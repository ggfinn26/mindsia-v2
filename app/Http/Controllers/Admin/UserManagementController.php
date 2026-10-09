<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('employee', 'roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($qEmp) use ($search) {
                      $qEmp->where('full_name', 'like', "%{$search}%")
                           ->orWhere('employee_code', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('status')) {
            $status = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $status);
        }

        $sort = $request->input('sort', 'created_at');
        $dir = $request->input('dir', 'desc');

        if (in_array($sort, ['name', 'email', 'is_active', 'last_login_at', 'created_at'])) {
            $query->orderBy($sort, $dir === 'desc' ? 'desc' : 'asc');
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->pluck('name');

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show(User $user): View
    {
        $user->load('employee', 'roles');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $user->load('roles');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:employee_accounts,email,'.$user->id],
        ]);

        $user->update($request->only('name', 'email'));

        return redirect()->route('users.show', $user)->with('success', 'User berhasil diperbarui.');
    }

    public function showResetForm(User $user): View
    {
        return view('admin.users.reset-password', compact('user'));
    }

    public function reset(AdminResetPasswordRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'password' => $request->validated('password'),
            'must_change_password' => true,
        ]);

        return redirect()->route('users.show', $user)->with('success', 'Password user berhasil direset.');
    }
}
