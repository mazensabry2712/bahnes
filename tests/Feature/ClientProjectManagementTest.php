<?php

use App\Mail\ClientProjectUpdateMail;
use App\Models\Client;
use App\Models\DeliveryProject;
use App\Models\ProjectUpdate;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

it('allows an administrator to create a private client and delivery project', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.clients.store'), [
        'name' => 'Example Client',
        'company' => 'Example Ltd',
        'email' => 'client@example.com',
        'preferred_locale' => 'en',
        'internal_notes' => 'Staff-only contract context',
    ])->assertRedirect();

    $client = Client::query()->where('email', 'client@example.com')->firstOrFail();

    $this->actingAs($admin)->post(route('admin.delivery-projects.store', $client), [
        'title' => 'Client website',
        'status' => 'planning',
        'progress_percent' => 10,
        'start_date' => '2026-10-01',
        'target_date' => '2026-11-01',
    ])->assertRedirect(route('admin.clients.show', $client));

    $this->assertDatabaseHas('delivery_projects', [
        'client_id' => $client->id,
        'title' => 'Client website',
        'progress_percent' => 10,
    ]);
});

it('validates project progress and target date', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Example Client',
        'email' => 'client@example.com',
        'preferred_locale' => 'en',
    ]);

    $this->actingAs($admin)->post(route('admin.delivery-projects.store', $client), [
        'title' => 'Invalid project',
        'status' => 'not-a-status',
        'progress_percent' => 140,
        'start_date' => '2026-11-10',
        'target_date' => '2026-10-01',
    ])->assertSessionHasErrors(['status', 'progress_percent', 'target_date']);
});

it('emails only approved client-visible progress updates and records the send time', function () {
    Mail::fake();

    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Example Client',
        'email' => 'client@example.com',
        'preferred_locale' => 'en',
        'internal_notes' => 'Never include this in the email',
    ]);
    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Client website',
        'status' => 'in_progress',
        'progress_percent' => 40,
    ]);
    $internalUpdate = ProjectUpdate::query()->create([
        'delivery_project_id' => $project->id,
        'author_id' => $admin->id,
        'summary' => 'Internal note only',
        'client_visible' => false,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.project-updates.send', $internalUpdate))
        ->assertForbidden();

    Mail::assertNothingOutgoing();

    $update = ProjectUpdate::query()->create([
        'delivery_project_id' => $project->id,
        'author_id' => $admin->id,
        'summary' => 'Milestone completed',
        'completed_work' => 'Requirements review',
        'current_work' => 'Implementation',
        'next_step' => 'Begin QA',
        'client_visible' => true,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.project-updates.send', $update))
        ->assertRedirect()
        ->assertSessionHas('status', 'Client progress email sent.');

    Mail::assertSent(ClientProjectUpdateMail::class, function (ClientProjectUpdateMail $mail) use ($client, $update): bool {
        return $mail->hasTo($client->email)
            && $mail->update->is($update)
            && ! str_contains($mail->render(), 'Never include this in the email');
    });

    expect($update->fresh()->email_sent_at)->not->toBeNull();
});

it('prevents sending the same client update twice', function () {
    Mail::fake();

    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Example Client',
        'email' => 'client@example.com',
        'preferred_locale' => 'en',
    ]);
    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Client website',
        'status' => 'in_progress',
        'progress_percent' => 50,
    ]);
    $update = ProjectUpdate::query()->create([
        'delivery_project_id' => $project->id,
        'author_id' => $admin->id,
        'summary' => 'Milestone update',
        'client_visible' => true,
        'email_sent_at' => now(),
    ]);

    $this->actingAs($admin)
        ->post(route('admin.project-updates.send', $update))
        ->assertRedirect()
        ->assertSessionHas('status', 'This update was already emailed.');

    Mail::assertNothingOutgoing();
});
