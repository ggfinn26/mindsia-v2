<?php

namespace App\Http\Controllers\Admin\SystemAccess;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionManagementController extends Controller
{
    public function index(): View
    {
        $positions = Position::with(['role.permissions'])->orderBy('position_name')->get();

        $usersWithDirect = User::with('permissions')
            ->whereHas('permissions')
            ->orderBy('name')
            ->get();

        $allPermissions = Permission::orderBy('name')->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0]);

        return view('system-access.permissions.index', compact(
            'positions',
            'usersWithDirect',
            'allPermissions',
        ));
    }

    public function editPosition(Position $position): View
    {
        $position->load('role.permissions');

        $allPermissions = Permission::orderBy('name')->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0]);

        $assigned = $position->role?->permissions->pluck('id')->toArray() ?? [];

        return view('system-access.permissions.edit-position', compact(
            'position',
            'allPermissions',
            'assigned',
        ));
    }

    public function updatePosition(Request $request, Position $position): RedirectResponse
    {
        // superadmin tidak boleh edit permission posisi Super Admin (self-lock)
        abort_if($position->role?->name === 'Super Admin', 403);

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $permissionIds = $validated['permissions'] ?? [];
        $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();

        $role = $position->role;
        abort_if(! $role, 404, 'Posisi ini tidak memiliki role.');

        $role->syncPermissions($permissions);

        // Invalidate cache semua user dengan role ini
        User::role($role->name)->each(fn ($u) => $u->forgetCachedPermissions());

        return redirect()
            ->route('system.permissions.index')
            ->with('success', 'Permission posisi "'.$position->position_name.'" berhasil diperbarui.');
    }

    public function editUser(User $user): View
    {
        // superadmin tidak boleh edit direct permission dirinya sendiri
        abort_if(auth()->id() === $user->id, 403);

        $user->load('permissions');

        $allPermissions = Permission::orderBy('name')->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0]);

        $assigned = $user->permissions->pluck('id')->toArray();

        return view('system-access.permissions.edit-user', compact(
            'user',
            'allPermissions',
            'assigned',
        ));
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        abort_if(auth()->id() === $user->id, 403);
        // hanya Super Admin yang boleh assign direct permission
        abort_unless(auth()->user()->hasRole('Super Admin'), 403);

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $permissionIds = $validated['permissions'] ?? [];
        $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();

        $user->syncPermissions($permissions);
        $user->forgetCachedPermissions();

        return redirect()
            ->route('system.permissions.index')
            ->with('success', 'Direct permission untuk "'.$user->name.'" berhasil diperbarui.');
    }
}
