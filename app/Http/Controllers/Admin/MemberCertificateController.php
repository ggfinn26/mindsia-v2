<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\UpdateMemberCertificateRequest;
use App\Models\MemberCertificate;
use Illuminate\Http\RedirectResponse;

class MemberCertificateController extends Controller
{
    public function update(UpdateMemberCertificateRequest $request, MemberCertificate $memberCertificate): RedirectResponse
    {
        $memberCertificate->update($request->validated());

        return back()->with('success', 'Data sertifikat berhasil diperbarui.');
    }
}
