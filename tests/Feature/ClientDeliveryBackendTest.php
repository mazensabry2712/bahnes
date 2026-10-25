<?php

use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\DeliveryProject;
use App\Models\ProjectActivity;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('stores client documents privately and requires document permissions to download them', function () {
    Storage::fake('local');
    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Document Client',
        'email' => 'document-client@example.com',
        'preferred_locale' => 'en',
    ]);

    $this->actingAs($admin)->post(route('admin.clients.documents.store', $client), [
        'document' => UploadedFile::fake()->create('signed-contract.pdf', 12, 'application/pdf'),
        'category' => 'contract',
        'description' => 'Signed service agreement',
    ])->assertRedirect();

    $document = ClientDocument::query()->firstOrFail();
    Storage::disk('local')->assertExists($document->storage_path);
    expect($document->original_name)->toBe('signed-contract.pdf')
        ->and($document->category)->toBe('contract');

    $staffRole = \App\Models\Role::query()->create([
        'name' => 'Client viewer without documents',
        'slug' => 'client-viewer-without-documents',
        'permissions' => ['clients.view'],
    ]);
    $staff = User::factory()->create(['is_admin' => false, 'role_id' => $staffRole->id]);

    $this->actingAs($staff)->get(route('admin.client-documents.download', $document))->assertForbidden();

    $this->actingAs($admin)->get(route('admin.client-documents.download', $document))
        ->assertOk()
        ->assertHeader('Cache-Control', 'private, no-store')
        ->assertHeader('X-Content-Type-Options', 'nosniff');

    $this->actingAs($admin)->delete(route('admin.client-documents.destroy', $document))->assertRedirect();
    Storage::disk('local')->assertMissing($document->storage_path);
    $this->assertDatabaseMissing('client_documents', ['id' => $document->id]);
});

it('rejects executable and oversized private client documents', function () {
    Storage::fake('local');
    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Invalid Document Client',
        'email' => 'invalid-document@example.com',
        'preferred_locale' => 'en',
    ]);

    $this->actingAs($admin)->post(route('admin.clients.documents.store', $client), [
        'document' => UploadedFile::fake()->create('payload.php', 2, 'application/x-php'),
        'category' => 'contract',
    ])->assertSessionHasErrors(['document']);

    expect(ClientDocument::query()->count())->toBe(0);
});

it('tracks project tasks and milestones with validated completion state', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Task Client',
        'email' => 'task-client@example.com',
        'preferred_locale' => 'en',
    ]);
    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Task project',
        'status' => 'in_progress',
        'progress_percent' => 40,
        'reference' => 'BH-PRJ-900001',
    ]);

    $this->actingAs($admin)->post(route('admin.delivery-projects.tasks.store', $project), [
        'title' => 'Approve architecture',
        'status' => 'completed',
        'is_milestone' => '1',
        'due_date' => '2026-10-25',
        'sort_order' => 1,
        'internal_notes' => 'Internal review only',
    ])->assertRedirect();

    $task = ProjectTask::query()->where('title', 'Approve architecture')->firstOrFail();
    expect($task->is_milestone)->toBeTrue()
        ->and($task->status)->toBe('completed')
        ->and($task->completed_at)->not->toBeNull();

    $this->actingAs($admin)->get(route('admin.delivery-projects.tasks.index', $project))
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Approve architecture')
        ->assertJsonPath('data.0.is_milestone', true);

    $this->actingAs($admin)->put(route('admin.project-tasks.update', $task), [
        'title' => 'Approve architecture',
        'status' => 'not-a-status',
        'is_milestone' => '1',
        'sort_order' => 1,
    ])->assertSessionHasErrors(['status']);

    $this->actingAs($admin)->delete(route('admin.project-tasks.archive', $task))->assertRedirect();
    expect(ProjectTask::query()->whereKey($task->id)->exists())->toBeFalse();
});

it('records audit history for client project and task changes', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.clients.store'), [
        'name' => 'Audit Client',
        'email' => 'audit-client@example.com',
        'preferred_locale' => 'en',
        'agreed_services' => 'Website development',
        'commercial_summary' => 'Fixed-scope engagement',
        'internal_notes' => 'Private internal context',
    ])->assertRedirect();

    $client = Client::query()->where('email', 'audit-client@example.com')->firstOrFail();
    $this->actingAs($admin)->post(route('admin.delivery-projects.store', $client), [
        'title' => 'Audit Project',
        'status' => 'planning',
        'progress_percent' => 0,
        'blocker' => 'Waiting for access',
        'delivery_notes' => 'Staging environment first',
    ])->assertRedirect();

    $project = DeliveryProject::query()->where('title', 'Audit Project')->firstOrFail();

    expect($project->reference)->toBe('BH-PRJ-'.str_pad((string) $project->id, 6, '0', STR_PAD_LEFT))
        ->and($client->agreed_services)->toBe('Website development')
        ->and($client->commercial_summary)->toBe('Fixed-scope engagement')
        ->and(ProjectActivity::query()->where('client_id', $client->id)->where('event_key', 'client.created')->exists())->toBeTrue()
        ->and(ProjectActivity::query()->where('delivery_project_id', $project->id)->where('event_key', 'project.created')->exists())->toBeTrue();

    $this->actingAs($admin)->get(route('admin.clients.activity', $client))
        ->assertOk()
        ->assertJsonPath('data.0.event_key', 'project.created');
});

it('prevents archiving a client while active delivery projects remain', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Active Client',
        'email' => 'active-client@example.com',
        'preferred_locale' => 'en',
    ]);
    DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Active delivery',
        'status' => 'in_progress',
        'progress_percent' => 50,
    ]);

    $this->actingAs($admin)->delete(route('admin.clients.archive', $client))
        ->assertSessionHasErrors(['client']);

    expect($client->fresh())->not->toBeNull();
});
