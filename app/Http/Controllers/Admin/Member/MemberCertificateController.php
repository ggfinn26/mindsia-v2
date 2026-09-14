<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberRegistration;
use Illuminate\Http\Request;

class MemberCertificateController extends Controller
{
    public function store(Request $request, MemberRegistration $registration)
    {
        $this->authorize('class.certificate.update');

        if ($registration->graduation_status !== 'LULUS') {
            return redirect()->back()->with('error', 'Sertifikat hanya bisa dikelola setelah member lulus.');
        }

        $validated = $request->validate([
            'certificate_number' => 'nullable|string|max:50',
            'certificate_available' => 'required|in:available,not_available',
            'certificate_hardcopy' => 'required|boolean',
            'certificate_taken' => 'required|in:taken,not_taken',
        ]);

        if (! $registration->certificate) {
            $validated['graduated_at'] = now();
            $registration->certificate()->create($validated);
        } else {
            $registration->certificate->update($validated);
        }

        return redirect()->back()->with('success', 'Status sertifikat berhasil diperbarui.');
    }
}
