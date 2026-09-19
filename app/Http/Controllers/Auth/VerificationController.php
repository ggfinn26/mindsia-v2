<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ApplicantAccount;
use App\Models\MemberAccount;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    private const GUARD_MAP = [
        'web' => [User::class, 'dashboard'],
        'member' => [MemberAccount::class, 'member.dashboard'],
        'applicant' => [ApplicantAccount::class, 'applicant.dashboard'],
    ];

    public function notice(): View|RedirectResponse
    {
        if ($user = auth('web')->user()) {
            return $user->hasVerifiedEmail() ? redirect()->route('dashboard') : view('auth.verify-email');
        }

        if ($user = auth('member')->user()) {
            return $user->hasVerifiedEmail() ? redirect()->route('member.dashboard') : view('auth.verify-email');
        }

        if ($user = auth('applicant')->user()) {
            return $user->hasVerifiedEmail() ? redirect()->route('applicant.dashboard') : view('auth.verify-email');
        }

        return view('auth.verify-email');
    }

    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Link verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        $guard = $request->query('guard');

        if (! isset(self::GUARD_MAP[$guard])) {
            abort(404, 'Link verifikasi tidak valid.');
        }

        [$modelClass, $redirectRoute] = self::GUARD_MAP[$guard];

        $account = $modelClass::find($id);

        if (! $account || ! hash_equals(sha1($account->getEmailForVerification()), $hash)) {
            abort(404, 'Akun tidak ditemukan untuk link verifikasi ini.');
        }

        if ($account->hasVerifiedEmail()) {
            return redirect()->intended(route($redirectRoute))->with('success', 'Email sudah diverifikasi sebelumnya.');
        }

        if ($account->markEmailAsVerified()) {
            event(new Verified($account));
        }

        auth($guard)->login($account);

        return redirect()->route('register.success')->with('success', 'Email berhasil diverifikasi!');
    }

    public function send(Request $request): RedirectResponse
    {
        $user = auth('web')->user() ?? auth('member')->user() ?? auth('applicant')->user();

        if (! $user) {
            abort(403, 'Anda harus login untuk mengirim ulang link verifikasi.');
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email sudah diverifikasi.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Link verifikasi telah dikirim ke email Anda.');
    }
}
