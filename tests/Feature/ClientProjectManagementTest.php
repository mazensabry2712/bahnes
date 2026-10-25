<?php

use App\Mail\ClientProjectUpdateMail;
use App\Models\Client;
use App\Models\DeliveryProject;
use App\Models\ProjectUpdate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
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
        'commercial_summary' => 'Internal commercial terms must never be emailed',
    ]);
    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Client website',
        'status' => 'in_progress',
        'progress_percent' => 40,
        'internal_notes' => 'Never email project internal notes',
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
            && ! str_contains($mail->render(), 'Never include this in the email')
            && ! str_contains($mail->render(), 'Internal commercial terms must never be emailed')
            && ! str_contains($mail->render(), 'Never email project internal notes');
    });

    expect($update->fresh()->email_sent_at)->not->toBeNull()
        ->and($update->fresh()->email_status)->toBe('sent')
        ->and($update->fresh()->email_attempts)->toBe(1);
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


it('forces completed projects to 100 percent and preserves the original completion timestamp', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Completion Client',
        'email' => 'completion@example.com',
        'preferred_locale' => 'en',
    ]);

    $this->actingAs($admin)->post(route('admin.delivery-projects.store', $client), [
        'title' => 'Already completed',
        'status' => 'completed',
        'progress_percent' => 15,
        'start_date' => '2026-10-01',
        'target_date' => '2026-10-20',
    ])->assertRedirect(route('admin.clients.show', $client));

    $project = DeliveryProject::query()->where('title', 'Already completed')->firstOrFail();
    expect($project->progress_percent)->toBe(100)
        ->and($project->completed_at)->not->toBeNull();

    $originalCompletion = $project->completed_at->toDateTimeString();

    $this->actingAs($admin)->put(route('admin.delivery-projects.update', $project), [
        'title' => $project->title,
        'status' => 'completed',
        'progress_percent' => 70,
        'start_date' => '2026-10-01',
        'target_date' => '2026-10-20',
    ])->assertRedirect(route('admin.delivery-projects.show', $project));

    expect($project->fresh()->progress_percent)->toBe(100)
        ->and($project->fresh()->completed_at->toDateTimeString())->toBe($originalCompletion);

    $this->actingAs($admin)->put(route('admin.delivery-projects.update', $project), [
        'title' => $project->title,
        'status' => 'planning',
        'progress_percent' => 10,
    ])->assertSessionHasErrors(['status']);

    expect($project->fresh()->status)->toBe('completed');

    $this->actingAs($admin)->put(route('admin.delivery-projects.update', $project), [
        'title' => $project->title,
        'status' => 'in_progress',
        'progress_percent' => 10,
    ])->assertRedirect(route('admin.delivery-projects.show', $project));

    expect($project->fresh()->status)->toBe('in_progress')
        ->and($project->fresh()->completed_at)->toBeNull();
});

it('records a failed client email attempt without marking the update as sent', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Mail Failure Client',
        'email' => 'mail-failure@example.com',
        'preferred_locale' => 'en',
    ]);
    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Mail failure project',
        'status' => 'in_progress',
        'progress_percent' => 25,
    ]);
    $update = ProjectUpdate::query()->create([
        'delivery_project_id' => $project->id,
        'author_id' => $admin->id,
        'summary' => 'A client-visible update',
        'client_visible' => true,
    ]);

    Mail::shouldReceive('to')->once()->with($client->email)->andThrow(new RuntimeException('SMTP unavailable'));

    $this->actingAs($admin)
        ->post(route('admin.project-updates.send', $update))
        ->assertRedirect()
        ->assertSessionHasErrors('email');

    expect($update->fresh()->email_attempts)->toBe(1)
        ->and($update->fresh()->email_status)->toBe('failed')
        ->and($update->fresh()->email_sent_at)->toBeNull()
        ->and($update->fresh()->email_error)->not->toBeNull();
});


it('does not send an update that is already in the sending state', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Concurrent Client',
        'email' => 'concurrent@example.com',
        'preferred_locale' => 'en',
    ]);
    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Concurrent project',
        'status' => 'in_progress',
        'progress_percent' => 35,
    ]);
    $update = ProjectUpdate::query()->create([
        'delivery_project_id' => $project->id,
        'author_id' => $admin->id,
        'summary' => 'Already sending',
        'client_visible' => true,
        'email_status' => 'sending',
        'updated_at' => now(),
    ]);

    Mail::fake();
    $this->withoutMiddleware(PreventRequestForgery::class);

    $this->actingAs($admin)
        ->post(route('admin.project-updates.send', $update))
        ->assertRedirect()
        ->assertSessionHasErrors('email');

    Mail::assertNothingOutgoing();
    expect($update->fresh()->email_attempts)->toBe(0);
});


it('enforces update and email-send permissions independently of view permissions', function () {
    $role = Role::query()->create([
        'name' => 'Project viewer',
        'slug' => 'project-viewer',
        'permissions' => ['clients.view', 'projects.view', 'project_updates.create'],
    ]);
    $staff = User::factory()->create(['is_admin' => false, 'role_id' => $role->id]);
    $client = Client::query()->create([
        'name' => 'Permission Client',
        'email' => 'permission-client@example.com',
        'preferred_locale' => 'en',
        'internal_notes' => 'Keep unchanged',
    ]);
    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Permission project',
        'status' => 'in_progress',
        'progress_percent' => 20,
        'internal_notes' => 'Project internal notes',
    ]);
    $update = ProjectUpdate::query()->create([
        'delivery_project_id' => $project->id,
        'author_id' => $staff->id,
        'summary' => 'Approved update',
        'client_visible' => true,
    ]);

    $this->actingAs($staff)->put(route('admin.clients.update', $client), [
        'name' => 'Tampered name',
        'email' => $client->email,
        'preferred_locale' => 'en',
    ])->assertForbidden();

    $this->actingAs($staff)->put(route('admin.delivery-projects.update', $project), [
        'title' => 'Tampered project',
        'status' => 'completed',
        'progress_percent' => 100,
    ])->assertForbidden();

    Mail::fake();
    $this->actingAs($staff)->post(route('admin.project-updates.send', $update))->assertForbidden();
    Mail::assertNothingOutgoing();

    expect($client->fresh()->name)->toBe('Permission Client')
        ->and($project->fresh()->title)->toBe('Permission project')
        ->and($update->fresh()->email_attempts)->toBe(0)
        ->and($update->fresh()->email_sent_at)->toBeNull();
});


it('recovers a client email left in sending state for more than ten minutes', function () {
    Mail::fake();

    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Recovered Mail Client',
        'email' => 'recovered-mail@example.com',
        'preferred_locale' => 'en',
    ]);
    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Recovered mail project',
        'status' => 'in_progress',
        'progress_percent' => 55,
    ]);
    $update = ProjectUpdate::query()->create([
        'delivery_project_id' => $project->id,
        'author_id' => $admin->id,
        'summary' => 'Retry after abandoned delivery',
        'client_visible' => true,
        'email_status' => 'sending',
        'email_attempts' => 1,
    ]);
    DB::table('project_updates')->where('id', $update->id)->update(['updated_at' => now()->subMinutes(11)]);

    $this->actingAs($admin)
        ->post(route('admin.project-updates.send', $update))
        ->assertRedirect()
        ->assertSessionHas('status', 'Client progress email sent.');

    Mail::assertSent(ClientProjectUpdateMail::class);
    expect($update->fresh()->email_status)->toBe('sent')
        ->and($update->fresh()->email_attempts)->toBe(2);
});


it('blocks production failover mailers that can silently log instead of delivering', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $client = Client::query()->create([
        'name' => 'Failover Guard Client',
        'email' => 'failover-guard@example.com',
        'preferred_locale' => 'en',
    ]);
    $project = DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Failover guard project',
        'status' => 'in_progress',
        'progress_percent' => 15,
    ]);
    $update = ProjectUpdate::query()->create([
        'delivery_project_id' => $project->id,
        'author_id' => $admin->id,
        'summary' => 'Must not be marked delivered by log fallback',
        'client_visible' => true,
    ]);

    $this->app->instance('env', 'production');
    config()->set('app.env', 'production');
    config()->set('mail.default', 'failover');
    config()->set('mail.mailers.failover.mailers', ['smtp', 'log']);
    Mail::fake();
    $this->withoutMiddleware(PreventRequestForgery::class);

    $this->actingAs($admin)
        ->post(route('admin.project-updates.send', $update))
        ->assertRedirect()
        ->assertSessionHasErrors('email');

    Mail::assertNothingOutgoing();
    expect($update->fresh()->email_attempts)->toBe(0)
        ->and($update->fresh()->email_status)->toBe('not_sent')
        ->and($update->fresh()->email_sent_at)->toBeNull();
});
