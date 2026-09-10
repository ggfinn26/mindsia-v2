<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ApplicantLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicantLoginController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.applicant-login');
    }

    public function login(ApplicantLoginRequest $request): RedirectResponse
    {
        if ($request->authenticate()) {
            $request->session()->regenerate();

            // Per flow.md applicant-login (line 90): cek email_verified_at post-attempt
            if (!auth('applicant')->user()->email_verified_at) {
                return redirect()->route('verification.notice');
            }

            return redirect()->route('applicant.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        auth('applicant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/karir');
    }
}
