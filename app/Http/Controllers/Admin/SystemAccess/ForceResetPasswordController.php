<?php

namespace App\Http\Controllers\Admin\SystemAccess;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemAccess\ForceResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ForceResetPasswordController extends Controller
{
    public function index(): View
    {
        return view('system-access.force-reset.index');
    }

    public function reset(ForceResetPasswordRequest $request): RedirectResponse
    {
        // Re-auth superadmin before mutating another user's password
        if (! Hash::check($request->validated('current_password'), $request->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password superadmin tidak cocok.',
            ]);
        }

        $user = User::findOrFail($request->validated('user_id'));
        $user->update([
            'password' => $request->validated('new_password'),
            'must_change_password' => true,
        ]);

        return redirect()->route('system.force-reset.index')->with('success', "Password user {$user->name} berhasil direset. Mereka wajib ganti password saat login berikutnya.");
    }
}
