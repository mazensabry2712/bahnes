<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\ProjectActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AccessManagementController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->is_admin, 403);

        return view('admin.access.index', [
            'roles' => Role::query()->withCount('users')->orderBy('name')->get(),
            'users' => User::query()->with('role')->orderBy('name')->paginate(25),
            'permissions' => config('admin-permissions'),
        ]);
    }

    public function storeRole(Request $request): RedirectResponse
    {
        abort_unless($request->user()->is_admin, 403);

        $permissions = array_keys(config('admin-permissions'));
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', Rule::in($permissions)],
        ]);

        $role = Role::query()->create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(5)),
            'permissions' => array_values(array_unique($data['permissions'] ?? [])),
        ]);
        app(ProjectActivityLogger::class)->record(
            $request->user(), 'role.created', 'Staff role created.',
            null, null, null, ['role_id' => $role->getKey(), 'name' => $role->name, 'permissions' => $role->permissions],
        );

        return redirect()->route('admin.access.index')->with('status', 'Role created.');
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        abort_unless($request->user()->is_admin, 403);

        $permissions = array_keys(config('admin-permissions'));
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', Rule::in($permissions)],
        ]);

        $oldValues = ['name' => $role->name, 'permissions' => $role->permissions];
        $role->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(5)),
            'permissions' => array_values(array_unique($data['permissions'] ?? [])),
        ]);
        app(ProjectActivityLogger::class)->record(
            $request->user(), 'role.updated', 'Staff role permissions updated.',
            null, null, $oldValues, ['role_id' => $role->getKey(), 'name' => $role->name, 'permissions' => $role->permissions],
        );

        return redirect()->route('admin.access.index')->with('status', 'Role permissions updated.');
    }

    public function storeUser(Request $request): RedirectResponse
    {
        abort_unless($request->user()->is_admin, 403);

        // Normalize before unique validation so case variants cannot bypass the check
        // on databases whose unique-index collation is case-sensitive.
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'max:255', 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        $staff = User::query()->create([
            'name' => $data['name'],
            'email' => Str::lower($data['email']),
            'password' => Hash::make($data['password']),
            'is_admin' => false,
            'role_id' => $data['role_id'],
        ]);
        app(ProjectActivityLogger::class)->record(
            $request->user(), 'staff.created', 'Staff account created.',
            null, null, null, ['user_id' => $staff->getKey(), 'email' => $staff->email, 'role_id' => $staff->role_id],
        );

        return redirect()->route('admin.access.index')->with('status', 'Staff account created with the selected role.');
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->is_admin, 403);

        if ($user->is_admin) {
            return back()->withErrors(['role_id' => 'Super Admin accounts cannot be downgraded here.']);
        }

        $data = $request->validate([
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
        ]);

        $oldRoleId = $user->role_id;
        $user->forceFill(['role_id' => $data['role_id'] ?? null])->save();
        app(ProjectActivityLogger::class)->record(
            $request->user(), 'staff.role_updated', 'Staff access assignment updated.',
            null, null, ['user_id' => $user->getKey(), 'role_id' => $oldRoleId],
            ['user_id' => $user->getKey(), 'role_id' => $user->role_id],
        );

        return redirect()->route('admin.access.index')->with('status', 'Staff access updated.');
    }
}
