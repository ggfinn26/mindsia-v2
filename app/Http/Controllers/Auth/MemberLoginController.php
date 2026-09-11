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

            // Per flow.md member-login: cek is_active, email_verified_at via middleware
            if (! $member->is_active) {
                return redirect()->route('member.dashboard')->with('info', 'Akun sedang menunggu aktivasi admin.');
            }

            $member->update(['last_login_at' => now()]);

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
