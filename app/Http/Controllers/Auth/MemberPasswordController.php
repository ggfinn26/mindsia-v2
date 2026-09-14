<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MemberChangePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MemberPasswordController extends Controller
{
    public function showChangeForm(): View
    {
        return view('member.change-password');
    }

    public function update(MemberChangePasswordRequest $request): RedirectResponse
    {
        $request->user('member')->update([
            'password' => $request->validated('password'),
        ]);

        return back()->with('status', 'Password berhasil diubah.');
    }
}
