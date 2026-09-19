<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\ReviewLeaveRequestRequest;
use App\Http\Requests\Attendance\StoreLeaveRequestRequest;
use App\Models\EmployeeLeaveRequest;
use App\Repositories\LeaveRequestRepository;
use App\Services\AttendancePolicyService;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function __construct(
        private readonly LeaveRequestRepository $repository,
        private readonly AttendancePolicyService $policyService,
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function index(): View
    {
        return view('attendance.leave.index', [
            'requests' => $this->repository->paginateForEmployee(auth()->user()->employee_id),
        ]);
    }

    public function create(): View
    {
        return view('attendance.leave.create');
    }

    public function store(StoreLeaveRequestRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('attachment');

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $uploaded = $this->telegramStorage->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'leave_attachment',
                auth()->user()->employee->id,
            );
            $data['attachment_telegram_file_id'] = $uploaded['file_id'];
            $data['attachment_path'] = $uploaded['file_id'];
        }

        $this->repository->create(auth()->user()->employee, $data);

        return redirect()->route('leave-requests.index')->with('success', 'Pengajuan izin berhasil dikirim.');
    }

    public function cancel(EmployeeLeaveRequest $leaveRequest): RedirectResponse
    {
        abort_unless($leaveRequest->employee_id === auth()->user()->employee_id, 403);
        abort_unless($leaveRequest->isPending(), 403, 'Hanya pengajuan pending yang bisa dibatalkan.');

        $this->repository->cancel($leaveRequest);

        return back()->with('success', 'Pengajuan dibatalkan.');
    }

    public function manage(): View
    {
        abort_unless(auth()->user()->can('attendance.leave.review'), 403);

        return view('attendance.leave.manage', [
            'requests' => $this->repository->paginateGlobal(
                request('branch_id'),
                request()->only('status'),
            ),
        ]);
    }

    public function approve(ReviewLeaveRequestRequest $request, EmployeeLeaveRequest $leaveRequest): RedirectResponse
    {
        $approver = auth()->user()->employee;

        abort_unless(
            $this->policyService->canApproveLeave($approver, $leaveRequest->employee),
            403,
            'Jabatan tidak cukup untuk menyetujui pengajuan ini.',
        );

        $this->repository->approve($leaveRequest, $approver);

        return back()->with('success', 'Pengajuan disetujui.');
    }

    public function reject(ReviewLeaveRequestRequest $request, EmployeeLeaveRequest $leaveRequest): RedirectResponse
    {
        $request->validate(['rejection_reason' => ['required', 'string']]);

        $approver = auth()->user()->employee;

        abort_unless(
            $this->policyService->canApproveLeave($approver, $leaveRequest->employee),
            403,
            'Jabatan tidak cukup untuk menolak pengajuan ini.',
        );

        $this->repository->reject($leaveRequest, $approver, $request->input('rejection_reason', ''));

        return back()->with('success', 'Pengajuan ditolak.');
    }
}
