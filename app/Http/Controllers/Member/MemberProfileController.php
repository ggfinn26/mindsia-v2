<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberProfileController extends Controller
{
    public function show(): View
    {
        return view('member.profile.show', [
            'member' => auth('member')->user()->memberData,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $memberData = auth('member')->user()->memberData;

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'instagram' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'father_whatsapp' => ['nullable', 'string', 'max:20'],
            'mother_whatsapp' => ['nullable', 'string', 'max:20'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('members_data', 'email')->ignore($memberData->id)],
        ]);

        $memberData->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
