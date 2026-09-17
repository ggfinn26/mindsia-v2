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
    public function index(): View
    {
        $users = User::with('employee', 'roles')->paginate(15);

        return view('admin.users.index', compact('users'));
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
