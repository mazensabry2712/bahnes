<?php

use App\Models\Role;
use App\Models\User;

it('allows staff only the modules and actions assigned to their role', function () {
    $role = Role::query()->create([
        'name' => 'Client reader',
        'slug' => 'client-reader',
        'permissions' => ['clients.view'],
    ]);
    $staff = User::factory()->create([
        'is_admin' => false,
        'role_id' => $role->id,
    ]);

    $this->post(route('admin.authenticate'), [
        'email' => $staff->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($staff);
    $this->get(route('admin.clients.index'))->assertOk();
    $this->actingAs($staff)->get(route('admin.clients.create'))->assertForbidden();
    $this->actingAs($staff)->get(route('admin.access.index'))->assertForbidden();

    expect($staff->hasPermissionTo('clients.view'))->toBeTrue()
        ->and($staff->hasPermissionTo('clients.create'))->toBeFalse()
        ->and($staff->hasPermissionTo('cms.products.update'))->toBeFalse();
});

it('allows super admins to access all modules without an assigned role', function () {
    $admin = User::factory()->create(['is_admin' => true, 'role_id' => null]);

    expect($admin->hasPermissionTo('clients.view'))->toBeTrue()
        ->and($admin->hasPermissionTo('access.manage'))->toBeTrue();

    $this->actingAs($admin)->get(route('admin.access.index'))->assertOk();
});


it('prevents role-less staff from using the dashboard and blocks staff from changing roles', function () {
    $role = Role::query()->create([
        'name' => 'Read only',
        'slug' => 'read-only',
        'permissions' => ['clients.view'],
    ]);
    $admin = User::factory()->create(['is_admin' => true]);
    $staff = User::factory()->create(['is_admin' => false, 'role_id' => $role->id]);
    $otherRole = Role::query()->create([
        'name' => 'Other role',
        'slug' => 'other-role',
        'permissions' => [],
    ]);

    $this->actingAs($staff)->get(route('admin.dashboard'))->assertOk();
    $this->actingAs($staff)->post(route('admin.access.roles.store'), [
        'name' => 'Escalated role',
        'permissions' => ['clients.view', 'clients.update'],
    ])->assertForbidden();

    $this->actingAs($staff)->put(route('admin.access.users.role.update', $admin), [
        'role_id' => $otherRole->id,
    ])->assertForbidden();

    expect(Role::query()->where('name', 'Escalated role')->exists())->toBeFalse()
        ->and($admin->fresh()->is_admin)->toBeTrue()
        ->and($admin->fresh()->role_id)->toBeNull();
});

it('rejects unknown permissions and invalid role assignments', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $role = Role::query()->create([
        'name' => 'Limited',
        'slug' => 'limited',
        'permissions' => ['clients.view'],
    ]);

    $this->actingAs($admin)->post(route('admin.access.roles.store'), [
        'name' => 'Invalid role',
        'permissions' => ['clients.view', 'system.root'],
    ])->assertSessionHasErrors(['permissions.1']);

    $this->actingAs($admin)->post(route('admin.access.users.store'), [
        'name' => 'Staff Member',
        'email' => 'staff@example.com',
        'password' => 'StrongPassword-123',
        'password_confirmation' => 'StrongPassword-123',
        'role_id' => 999999,
    ])->assertSessionHasErrors(['role_id']);

    expect(Role::query()->where('name', 'Invalid role')->exists())->toBeFalse()
        ->and(User::query()->where('email', 'staff@example.com')->exists())->toBeFalse();

    $this->actingAs($admin)->put(route('admin.access.users.role.update', $role->users()->create([
        'name' => 'Role user',
        'email' => 'role-user@example.com',
        'password' => 'StrongPassword-123',
        'is_admin' => false,
    ])), [
        'role_id' => null,
    ])->assertRedirect(route('admin.access.index'));
});
