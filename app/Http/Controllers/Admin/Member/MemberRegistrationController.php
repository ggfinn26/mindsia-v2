<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberRegistrationRequest;
use App\Http\Requests\Member\UpdateMemberRegistrationGraduationRequest;
use App\Http\Requests\Member\UpdateMemberRegistrationPaymentStatusRequest;
use App\Models\MemberData;
use App\Models\MemberRegistration;
use App\Models\Program;
use App\Repositories\Member\MemberRegistrationRepository;
use App\Services\Member\MemberRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MemberRegistrationController extends Controller
{
    public function __construct(
        private readonly MemberRegistrationService $service,
        private readonly MemberRegistrationRepository $repo,
    ) {}

    public function create(MemberData $member): View
    {
        $programs = Program::where('is_active', true)->orderBy('program_name')->get(['id', 'program_name']);

        return view('admin.member.registrations.create', compact('member', 'programs'));
    }

    public function store(StoreMemberRegistrationRequest $request, MemberData $member): RedirectResponse
    {
        $data = array_merge($request->validated(), [
            'employee_id' => $request->user()->employee?->id ?? $request->user()->id,
        ]);

        $registration = $this->service->register($member, $data);

        return redirect()->route('registrations.show', $registration)->with('success', 'Registrasi berhasil dibuat.');
    }

    public function show(MemberRegistration $registration): View
    {
        return view('admin.member.registrations.show', [
            'registration' => $this->repo->findWithDetails($registration->id),
        ]);
    }

    public function updateGraduation(UpdateMemberRegistrationGraduationRequest $request, MemberRegistration $registration): RedirectResponse
    {
        $this->repo->updateGraduation($registration, $request->validated('graduation_status'));

        return redirect()->back()->with('success', 'Status kelulusan berhasil diperbarui.');
    }

    public function updatePaymentStatus(UpdateMemberRegistrationPaymentStatusRequest $request, MemberRegistration $registration): RedirectResponse
    {
        $registration->update(['payment_status' => $request->validated('payment_status')]);

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
