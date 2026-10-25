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


it('rejects weak or mismatched passwords when provisioning an administrator', function () {
    $this->artisan('admin:create')
        ->expectsQuestion('Administrator name', 'New Admin')
        ->expectsQuestion('Administrator email', 'new-admin@example.com')
        ->expectsQuestion('Password (minimum 12 characters)', 'short')
        ->expectsOutput('The password must be at least 12 characters long.')
        ->assertExitCode(1);

    expect(User::query()->where('email', 'new-admin@example.com')->exists())->toBeFalse();

    $this->artisan('admin:create')
        ->expectsQuestion('Administrator name', 'New Admin')
        ->expectsQuestion('Administrator email', 'new-admin@example.com')
        ->expectsQuestion('Password (minimum 12 characters)', 'LongEnoughPassword-123')
        ->expectsQuestion('Confirm password', 'DifferentLongPassword-123')
        ->expectsOutput('The passwords do not match. No account was created.')
        ->assertExitCode(1);

    expect(User::query()->where('email', 'new-admin@example.com')->exists())->toBeFalse();
});

it('does not change an administrator password when the reset password is weak or mismatched', function () {
    $admin = User::factory()->create([
        'email' => 'safe-admin@example.com',
        'is_admin' => true,
        'password' => 'OriginalStrongPassword-123',
    ]);
    $originalHash = $admin->password;

    $this->artisan('admin:password-reset', ['email' => $admin->email])
        ->expectsQuestion('New password (minimum 12 characters)', 'short')
        ->expectsOutput('The password must be at least 12 characters long.')
        ->assertExitCode(1);

    expect($admin->fresh()->password)->toBe($originalHash);

    $this->artisan('admin:password-reset', ['email' => $admin->email])
        ->expectsQuestion('New password (minimum 12 characters)', 'NewStrongPassword-123')
        ->expectsQuestion('Confirm new password', 'NotTheSamePassword-123')
        ->expectsOutput('The passwords do not match. No changes were made.')
        ->assertExitCode(1);

    expect($admin->fresh()->password)->toBe($originalHash);
});

it('rejects a malformed administrator email and blank administrator name', function () {
    $this->artisan('admin:create')
        ->expectsQuestion('Administrator name', '')
        ->expectsOutput('Enter a name between 1 and 255 characters.')
        ->assertExitCode(1);

    $this->artisan('admin:create')
        ->expectsQuestion('Administrator name', 'New Admin')
        ->expectsQuestion('Administrator email', 'not-an-email')
        ->expectsOutput('Enter a valid email address.')
        ->assertExitCode(1);

    expect(User::query()->count())->toBe(0);
});
