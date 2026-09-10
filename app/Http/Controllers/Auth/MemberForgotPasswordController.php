<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class MemberForgotPasswordController extends Controller
{
    public function showForm(): View
    {
        return view('member.forgot-password');
    }

    public function sendResetLink(EmailRequest $request): RedirectResponse
    {
        $status = Password::broker('members')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => 'Reset link telah dikirim ke email.'])
            : back()->withErrors(['email' => 'Email tidak ditemukan.']);
    }
}
