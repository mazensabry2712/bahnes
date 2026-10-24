<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates an administrator only through the command line', function () {
    $password = 'OnlyForThisTest-Password-123';

    $this->artisan('admin:create')
        ->expectsQuestion('Administrator name', 'Site Administrator')
        ->expectsQuestion('Administrator email', 'ADMIN@example.com')
        ->expectsQuestion('Password (minimum 12 characters)', $password)
        ->expectsQuestion('Confirm password', $password)
        ->expectsOutput('Administrator account created for admin@example.com.')
        ->assertExitCode(0);

    $admin = User::query()->where('email', 'admin@example.com')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->is_admin)->toBeTrue()
        ->and(Hash::check($password, $admin->password))->toBeTrue();
});

it('does not promote or overwrite an existing account when creating an administrator', function () {
    $user = User::factory()->create([
        'email' => 'existing@example.com',
        'is_admin' => false,
    ]);

    $this->artisan('admin:create')
        ->expectsQuestion('Administrator name', 'Existing User')
        ->expectsQuestion('Administrator email', 'existing@example.com')
        ->expectsOutput('An account with this email already exists. No changes were made.')
        ->assertExitCode(1);

    expect($user->fresh()->is_admin)->toBeFalse();
});

it('resets a password only for an existing administrator', function () {
    $admin = User::factory()->create([
        'email' => 'admin@example.com',
        'is_admin' => true,
    ]);
    $password = 'NewAdminPassword-1234';

    $this->artisan('admin:password-reset', ['email' => 'admin@example.com'])
        ->expectsQuestion('New password (minimum 12 characters)', $password)
        ->expectsQuestion('Confirm new password', $password)
        ->expectsOutput('Administrator password reset for admin@example.com.')
        ->assertExitCode(0);

    expect(Hash::check($password, $admin->fresh()->password))->toBeTrue();
});

it('does not reset a regular account password through the admin recovery command', function () {
    User::factory()->create([
        'email' => 'user@example.com',
        'is_admin' => false,
    ]);

    $this->artisan('admin:password-reset', ['email' => 'user@example.com'])
        ->expectsOutput('No administrator account was found for that email. No changes were made.')
        ->assertExitCode(1);
});
