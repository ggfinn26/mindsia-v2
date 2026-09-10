<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ApplicantPasswordController extends Controller
{
    public function showChangeForm(): View
    {
        return view('applicant.change-password');
    }

    public function update(ChangePasswordRequest $request): RedirectResponse
    {
        $request->user('applicant')->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Password berhasil diubah.');
    }
}
