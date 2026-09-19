<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ApplicantAccount;
use App\Models\MemberAccount;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        // pending_verification takes priority: a new registration must be able to verify
        // even if a different user is already authenticated in the same session.
        if ($this->hasPendingUnverified()) {
            return view('auth.verify-email');
        }

        foreach (self::GUARD_MAP as $guard => [, $redirectRoute]) {
            if ($user = auth($guard)->user()) {
                return $user->hasVerifiedEmail()
                    ? redirect()->route($redirectRoute)
                    : view('auth.verify-email');
            }
        }

        return redirect()->route('login');
    }

    public function submitOtp(Request $request): RedirectResponse
    {
        $request->validate(['otp' => 'required|digits:6']);

        [$account, $guard] = $this->resolveAccount();

        if (! $account) {
            return redirect()->route('login');
        }

        $cacheKey = "email_otp_{$guard}_{$account->getKey()}";

        // pull() = get + delete in one call; prevents two concurrent requests
        // from both reading the same OTP before either deletes it (TOCTOU).
        $stored = Cache::pull($cacheKey);

        if (! $stored || ! hash_equals($stored, $request->input('otp'))) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.']);
        }
        $account->markEmailAsVerified();
        event(new Verified($account));
        auth($guard)->login($account);
        session()->forget('pending_verification');

        return redirect()->route('register.success')->with('success', 'Email berhasil diverifikasi!');
    }

    public function send(Request $request): RedirectResponse
    {
        [$account, $guard] = $this->resolveAccount();

        if (! $account) {
            return redirect()->route('login');
        }

        if ($account->hasVerifiedEmail()) {
            return back()->with('success', 'Email sudah diverifikasi.');
        }

        $account->sendEmailVerificationNotification();

        if (! auth($guard)->check()) {
            session(['pending_verification' => ['guard' => $guard, 'id' => $account->getKey()]]);
        }

        return back()->with('status', 'verification-link-sent');
    }

    private function resolveAccount(): array
    {
        // pending_verification takes priority over authenticated user — a new registration
        // must be verifiable even when a different user is already logged in.
        $pending = session('pending_verification');

        if (
            $pending &&
            isset($pending['guard'], $pending['id']) &&
            isset(self::GUARD_MAP[$pending['guard']])
        ) {
            $guard = $pending['guard'];
            [$modelClass] = self::GUARD_MAP[$guard];
            $account = $modelClass::find($pending['id']);

            if ($account && ! $account->hasVerifiedEmail()) {
                return [$account, $guard];
            }
        }

        foreach (self::GUARD_MAP as $guard => [$modelClass]) {
            if ($user = auth($guard)->user()) {
                return [$user, $guard];
            }
        }

        return [null, null];
    }

    private function hasPendingUnverified(): bool
    {
        $pending = session('pending_verification');

        if (! $pending || ! isset($pending['guard'], $pending['id']) || ! isset(self::GUARD_MAP[$pending['guard']])) {
            return false;
        }

        [$modelClass] = self::GUARD_MAP[$pending['guard']];
        $account = $modelClass::find($pending['id']);

        return $account !== null && ! $account->hasVerifiedEmail();
    }
}
