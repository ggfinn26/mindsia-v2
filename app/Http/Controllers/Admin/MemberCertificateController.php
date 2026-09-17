<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\UpdateMemberCertificateRequest;
use App\Models\MemberCertificate;
use App\Models\MemberRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemberCertificateController extends Controller
{
    public function store(Request $request, MemberRegistration $registration): RedirectResponse
    {
        abort_unless($request->user()->can('class.manage'), 403);
        abort_if($registration->certificate()->exists(), 422, 'Sertifikat sudah ada untuk registrasi ini.');

        MemberCertificate::create([
            'member_registration_id' => $registration->id,
            'certificate_number' => $request->input('certificate_number'),
            'certificate_available' => $request->boolean('certificate_available', true) ? 'available' : 'not_available',
            'certificate_hardcopy' => $request->boolean('certificate_hardcopy', false),
            'certificate_taken' => $request->boolean('certificate_taken', false) ? 'taken' : 'not_taken',
        ]);

        return back()->with('success', 'Sertifikat berhasil dibuat.');
    }

    public function update(UpdateMemberCertificateRequest $request, MemberCertificate $memberCertificate): RedirectResponse
    {
        $memberCertificate->update($request->validated());

        return back()->with('success', 'Data sertifikat berhasil diperbarui.');
    }
}
