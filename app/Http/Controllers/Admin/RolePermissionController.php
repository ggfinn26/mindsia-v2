<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function showUserRoles(User $user): View
    {
        $user->load('roles', 'permissions');
        $allRoles = Role::all();
        return view('admin.users.roles', compact('user', 'allRoles'));
    }

    public function assignRole(Request $request, User $user): RedirectResponse
    {
        $request->validate(['roles' => ['array', 'exists:roles,id']]);

        $user->syncRoles($request->input('roles', []));
        auth()->user()->forgetCachedPermissions();

        return back()->with('success', 'Role berhasil diperbarui.');
    }

    public function assignPermission(Request $request, User $user): RedirectResponse
    {
        $request->validate(['permissions' => ['array', 'exists:permissions,id']]);

        $user->syncPermissions($request->input('permissions', []));
        auth()->user()->forgetCachedPermissions();

        return back()->with('success', 'Permission berhasil diperbarui.');
    }
}
