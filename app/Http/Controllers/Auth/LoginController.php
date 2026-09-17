<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLogin()
    {
        return redirect()->route('employee.landing', ['view' => 'login']);
    }

    public function login(LoginRequest $request): RedirectResponse|JsonResponse
    {
        if ($request->authenticate()) {
            $request->session()->regenerate();
            auth()->user()->update(['last_login_at' => now()]);

            if (! auth()->user()->hasVerifiedEmail()) {
                if ($request->expectsJson()) {
                    return response()->json(['redirect' => route('verification.notice')]);
                }

                return redirect()->route('verification.notice');
            }

            if ($request->expectsJson()) {
                return response()->json(['redirect' => route('dashboard')]);
            }

            return redirect()->route('dashboard');
        }

        if ($request->expectsJson()) {
            return response()->json(['errors' => ['email' => ['Email atau password salah.']]], 422);
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        auth('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
