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
        foreach (self::GUARD_MAP as $guard => [, $redirectRoute]) {
            if ($user = auth($guard)->user()) {
                return $user->hasVerifiedEmail()
                    ? redirect()->route($redirectRoute)
                    : view('auth.verify-email');
            }
        }

        if (session()->has('pending_verification')) {
            return view('auth.verify-email');
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
        $stored = Cache::get($cacheKey);

        if (! $stored || ! hash_equals($stored, $request->input('otp'))) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.']);
        }

        Cache::forget($cacheKey);
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
        foreach (self::GUARD_MAP as $guard => [$modelClass]) {
            if ($user = auth($guard)->user()) {
                return [$user, $guard];
            }
        }

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

        return [null, null];
    }
}
