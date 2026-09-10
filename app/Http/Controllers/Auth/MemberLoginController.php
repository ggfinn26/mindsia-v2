<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MemberLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberLoginController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.member-login');
    }

    public function login(MemberLoginRequest $request): RedirectResponse
    {
        if ($request->authenticate()) {
            $request->session()->regenerate();

            $member = auth('member')->user();

            // 3 kondisi redirect per flow.md member-login (line 862)
            if (!$member->memberData->email_verified_at) {
                return redirect()->route('verification.notice');
            }

            if (!$member->memberData->is_active) {
                return redirect()->route('member.dashboard')->with('info', 'Akun sedang menunggu aktivasi admin.');
            }

            return redirect()->route('member.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        auth('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
