<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    public function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($request->only('name', 'email'));

        return redirect()->route('users.show', $user)->with('success', 'User berhasil diperbarui.');
    }
}
