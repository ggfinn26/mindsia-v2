<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreMemberRequest;
use App\Models\MemberAccount;
use App\Models\MemberData;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MemberRegisterController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.member-register');
    }

    public function register(StoreMemberRequest $request): RedirectResponse
    {
        // Per flow.md member-self-register (line 874): form data dasar + akun
        // Submit → is_active=false, email verifikasi

        $memberData = MemberData::create($request->validated([
            'full_name',
            'whatsapp_number',
            'birth_date',
            'gender',
            'address',
            'city',
        ]));

        $member = MemberAccount::create([
            'members_data_id' => $memberData->id,
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
        ]);

        // is_active = false per flow (line 882)
        $memberData->update(['is_active' => false]);

        // Kirim email verifikasi (expired 1 jam per flow line 882)
        $member->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with('success', 'Silakan verifikasi email untuk melanjutkan.');
    }
}
