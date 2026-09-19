<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\TelegramStorageService;
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
        $memberAccount = auth('member')->user();

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

        // GAP-145: If email is being changed, require re-verification
        $oldEmail = $memberData->email;
        $newEmail = $validated['email'] ?? null;

        $memberData->update($validated);

        if ($newEmail && $newEmail !== $oldEmail) {
            // Sync email to MemberAccount and reset verification status
            $memberAccount->update([
                'email' => $newEmail,
                'email_verified_at' => null,
            ]);
            $memberAccount->sendEmailVerificationNotification();

            return back()->with('success', 'Profil berhasil diperbarui. Email verifikasi telah dikirim ke alamat email baru Anda.');
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // GAP-144: Avatar upload endpoint
    public function uploadAvatar(Request $request): RedirectResponse
    {
        $memberData = auth('member')->user()->memberData;

        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $file = $request->file('avatar');
        $storagePath = app(TelegramStorageService::class)->uploadPhoto(
            $file,
            'Avatar '.$memberData->full_name,
        );

        $memberData->update(['telegram_photo_id' => $storagePath]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
