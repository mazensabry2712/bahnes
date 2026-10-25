<?php

use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\DeliveryProject;
use App\Models\ProjectActivity;
use App\Models\ProjectTask;
use App\Models\Role;
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
    $this->actingAs($staff)->post(route('admin.clients.documents.store', $client), [
        'document' => UploadedFile::fake()->create('unauthorized.pdf', 2, 'application/pdf'),
        'category' => 'contract',
    ])->assertForbidden();
    expect(ClientDocument::query()->count())->toBe(1);

    $this->actingAs($admin)->get(route('admin.client-documents.download', $document))
        ->assertOk()
        ->assertHeader('Cache-Control', 'no-store, private')
        ->assertHeader('X-Content-Type-Options', 'nosniff');

    $this->actingAs($admin)->delete(route('admin.client-documents.destroy', $document))->assertRedirect();
    Storage::disk('local')->assertMissing($document->storage_path);
    $this->assertDatabaseMissing('client_documents', ['id' => $document->id]);
});

it('rejects unsupported and oversized private client documents', function () {
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

    $this->actingAs($admin)->post(route('admin.clients.documents.store', $client), [
        'document' => UploadedFile::fake()->create('oversized-contract.pdf', 21000, 'application/pdf'),
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
    $viewerRole = Role::query()->create([
        'name' => 'Task viewer only',
        'slug' => 'task-viewer-only',
        'permissions' => ['project_tasks.view'],
    ]);
    $viewer = User::factory()->create(['is_admin' => false, 'role_id' => $viewerRole->id]);
    $this->actingAs($viewer)->post(route('admin.delivery-projects.tasks.store', $project), [
        'title' => 'Unauthorized task',
        'status' => 'todo',
    ])->assertForbidden();
    expect(ProjectTask::query()->where('title', 'Unauthorized task')->exists())->toBeFalse();

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
        ->assertJsonFragment(['event_key' => 'project.created']);
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


it('archives delivery projects without hard-deleting their history', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Archive Project Client',
        'email' => 'archive-project@example.com',
        'preferred_locale' => 'en',
    ]);
    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Archived delivery',
        'status' => 'completed',
        'progress_percent' => 100,
        'reference' => 'BH-PRJ-900002',
    ]);

    $this->actingAs($admin)->delete(route('admin.delivery-projects.archive', $project))
        ->assertRedirect(route('admin.clients.show', $client));

    expect(DeliveryProject::query()->whereKey($project->id)->exists())->toBeFalse()
        ->and(DeliveryProject::withTrashed()->whereKey($project->id)->exists())->toBeTrue()
        ->and(ProjectActivity::query()->where('delivery_project_id', $project->id)->where('event_key', 'project.archived')->exists())->toBeTrue();
});


it('never writes private internal notes into client or project activity payloads', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.clients.store'), [
        'name' => 'Private Audit Client',
        'email' => 'private-audit@example.com',
        'preferred_locale' => 'en',
        'internal_notes' => 'CLIENT_SECRET_INTERNAL_NOTE',
    ])->assertRedirect();

    $client = Client::query()->where('email', 'private-audit@example.com')->firstOrFail();
    $created = ProjectActivity::query()
        ->where('client_id', $client->id)
        ->where('event_key', 'client.created')
        ->firstOrFail();

    expect($created->new_values)->not->toHaveKey('internal_notes')
        ->and(json_encode($created->new_values))->not->toContain('CLIENT_SECRET_INTERNAL_NOTE');

    $this->actingAs($admin)->put(route('admin.clients.update', $client), [
        'name' => $client->name,
        'email' => $client->email,
        'preferred_locale' => 'en',
        'internal_notes' => 'UPDATED_CLIENT_SECRET',
    ])->assertRedirect();

    $updated = ProjectActivity::query()
        ->where('client_id', $client->id)
        ->where('event_key', 'client.updated')
        ->firstOrFail();

    expect($updated->old_values)->not->toHaveKey('internal_notes')
        ->and($updated->new_values)->not->toHaveKey('internal_notes')
        ->and(json_encode([$updated->old_values, $updated->new_values]))->not->toContain('UPDATED_CLIENT_SECRET');

    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Audit protected project',
        'status' => 'in_progress',
        'progress_percent' => 20,
    ]);

    $this->actingAs($admin)->post(route('admin.delivery-projects.tasks.store', $project), [
        'title' => 'Private task',
        'status' => 'todo',
        'internal_notes' => 'TASK_SECRET_INTERNAL_NOTE',
    ])->assertRedirect();

    $task = ProjectTask::query()->where('title', 'Private task')->firstOrFail();

    $this->actingAs($admin)->put(route('admin.project-tasks.update', $task), [
        'title' => $task->title,
        'status' => 'in_progress',
        'sort_order' => 0,
        'internal_notes' => 'UPDATED_TASK_SECRET',
    ])->assertRedirect();

    $taskActivity = ProjectActivity::query()
        ->where('delivery_project_id', $project->id)
        ->where('event_key', 'project.task_updated')
        ->firstOrFail();

    expect($taskActivity->old_values)->not->toHaveKey('internal_notes')
        ->and($taskActivity->new_values)->not->toHaveKey('internal_notes')
        ->and(json_encode([$taskActivity->old_values, $taskActivity->new_values]))->not->toContain('UPDATED_TASK_SECRET');
});


it('rejects assigning clients projects and tasks to accounts without admin access', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $ordinaryUser = User::factory()->create(['is_admin' => false, 'role_id' => null]);
    $client = Client::query()->create([
        'name' => 'Assignment Validation Client',
        'email' => 'assignment-validation@example.com',
        'preferred_locale' => 'en',
    ]);
    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Assignment validation project',
        'status' => 'planning',
        'progress_percent' => 0,
    ]);

    $this->actingAs($admin)->put(route('admin.clients.update', $client), [
        'name' => $client->name,
        'email' => $client->email,
        'preferred_locale' => 'en',
        'account_manager_id' => $ordinaryUser->id,
    ])->assertSessionHasErrors(['account_manager_id']);

    $this->actingAs($admin)->put(route('admin.delivery-projects.update', $project), [
        'title' => $project->title,
        'status' => 'planning',
        'progress_percent' => 0,
        'owner_id' => $ordinaryUser->id,
    ])->assertSessionHasErrors(['owner_id']);

    $this->actingAs($admin)->post(route('admin.delivery-projects.tasks.store', $project), [
        'title' => 'Unauthorized assignee task',
        'status' => 'todo',
        'assigned_to' => $ordinaryUser->id,
    ])->assertSessionHasErrors(['assigned_to']);

    expect($client->fresh()->account_manager_id)->toBeNull()
        ->and($project->fresh()->owner_id)->toBeNull()
        ->and(ProjectTask::query()->where('title', 'Unauthorized assignee task')->exists())->toBeFalse();
});


it('preserves task completion timestamps and clears them when reopened', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Task completion client',
        'email' => 'task-completion@example.com',
        'preferred_locale' => 'en',
    ]);
    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Task completion project',
        'status' => 'in_progress',
        'progress_percent' => 60,
    ]);

    $this->actingAs($admin)->post(route('admin.delivery-projects.tasks.store', $project), [
        'title' => 'Completed task',
        'status' => 'completed',
    ])->assertRedirect();

    $task = ProjectTask::query()->where('title', 'Completed task')->firstOrFail();
    $completedAt = $task->completed_at->toDateTimeString();

    $this->actingAs($admin)->put(route('admin.project-tasks.update', $task), [
        'title' => 'Completed task updated',
        'status' => 'completed',
        'sort_order' => 1,
    ])->assertRedirect();

    expect($task->fresh()->completed_at->toDateTimeString())->toBe($completedAt);

    $this->actingAs($admin)->put(route('admin.project-tasks.update', $task), [
        'title' => 'Completed task updated',
        'status' => 'todo',
        'sort_order' => 1,
    ])->assertRedirect();

    expect($task->fresh()->completed_at)->toBeNull();
});


it('keeps the document record when private storage refuses deletion', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Storage failure client',
        'email' => 'storage-failure@example.com',
        'preferred_locale' => 'en',
    ]);
    $document = ClientDocument::query()->create([
        'client_id' => $client->id,
        'uploaded_by' => $admin->id,
        'original_name' => 'contract.pdf',
        'storage_path' => 'client-documents/'.$client->id.'/contract.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 1024,
        'category' => 'contract',
    ]);

    $disk = Mockery::mock();
    $disk->shouldReceive('delete')->once()->with($document->storage_path)->andReturnFalse();
    Storage::shouldReceive('disk')->once()->with('local')->andReturn($disk);

    $this->actingAs($admin)
        ->delete(route('admin.client-documents.destroy', $document))
        ->assertRedirect()
        ->assertSessionHasErrors(['document']);

    expect(ClientDocument::query()->whereKey($document->id)->exists())->toBeTrue();
    expect(ProjectActivity::query()->where('event_key', 'client.document_deleted')->exists())->toBeFalse();
});
