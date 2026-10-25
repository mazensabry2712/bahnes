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

    $this->actingAs($staff)->get(route('admin.clients.index'))->assertOk();
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
