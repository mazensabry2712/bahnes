<?php

use App\Models\User;

it('shows the administrator login page to visitors', function () {
    $this->get(route('admin.login'))
        ->assertOk()
        ->assertSee('Administrator sign in');
});

it('redirects visitors away from the protected dashboard to login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

it('allows an administrator with valid credentials to sign in', function () {
    $admin = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => 'a-strong-test-password',
        'is_admin' => true,
    ]);

    $response = $this->post(route('admin.authenticate'), [
        'email' => 'ADMIN@example.com',
        'password' => 'a-strong-test-password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin);

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Content management')
        ->assertSee(route('admin.services.index'), false)
        ->assertSee(route('admin.projects.index'), false)
        ->assertSee(route('admin.products.index'), false)
        ->assertSee(route('admin.templates.index'), false)
        ->assertSee(route('admin.insights.index'), false)
        ->assertSee(route('admin.insight-categories.index'), false)
        ->assertSee(route('admin.inquiries.index'), false);
});

it('does not allow a regular user to sign in through the admin endpoint', function () {
    User::factory()->create([
        'email' => 'user@example.com',
        'password' => 'a-strong-test-password',
        'is_admin' => false,
    ]);

    $this->post(route('admin.authenticate'), [
        'email' => 'user@example.com',
        'password' => 'a-strong-test-password',
    ])
        ->assertRedirect()
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('forbids a signed-in regular user from viewing the admin dashboard', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('signs the administrator out and invalidates the session', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'));

    $this->assertGuest();
});


it('rate limits repeated administrator login attempts', function () {
    foreach (range(1, 5) as $attempt) {
        $this->post(route('admin.authenticate'), [
            'email' => 'missing-admin@example.com',
            'password' => 'incorrect-password',
        ])->assertRedirect();
    }

    $this->post(route('admin.authenticate'), [
        'email' => 'missing-admin@example.com',
        'password' => 'incorrect-password',
    ])->assertTooManyRequests();

    $this->assertGuest();
});
