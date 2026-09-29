<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\MemberAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class MemberResetPasswordController extends Controller
{
    public function showForm(Request $request, string $token): View|RedirectResponse
    {
        $email = $request->query('email', '');
        $user = MemberAccount::where('email', $email)->first();

        if (! $user || ! Password::broker('members')->tokenExists($user, $token)) {
            return redirect()->route('member.password.request')
                ->withErrors(['email' => 'Link reset password tidak valid atau sudah kedaluwarsa.']);
        }

        return view('member.reset-password', ['token' => $token]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::broker('members')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => $password])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('member.login')->with('status', 'Password berhasil direset.')
            : back()->withErrors(['email' => 'Gagal mereset password.']);
    }
}
