<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MemberLoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberLoginController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.member-login');
    }

    public function login(MemberLoginRequest $request): RedirectResponse|JsonResponse
    {
        if ($request->authenticate()) {
            $request->session()->regenerate();

            $member = auth('member')->user();

            if (! $member->is_active) {
                $message = 'Akun sedang menunggu aktivasi admin.';
                if ($request->expectsJson()) {
                    return response()->json(['redirect' => route('member.dashboard'), 'info' => $message]);
                }

                return redirect()->route('member.dashboard')->with('info', $message);
            }

            $member->update(['last_login_at' => now()]);

            if ($request->expectsJson()) {
                return response()->json(['redirect' => route('member.dashboard')]);
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

        return redirect()->route('member.landing');
    }
}
