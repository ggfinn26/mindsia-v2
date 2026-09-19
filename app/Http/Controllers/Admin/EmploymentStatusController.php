<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractChangePositionRequest;
use App\Http\Requests\ContractExtendRequest;
use App\Http\Requests\EmploymentStatusRequest;
use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Repositories\PositionRepository;
use App\Services\EmployeeAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmploymentStatusController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:contract.manage'),
        ];
    }

    public function __construct(
        private readonly PositionRepository $positionRepository,
        private readonly EmployeeAuthService $employeeAuthService,
    ) {}

    public function create(Employee $employee): View
    {
        $positions = $this->positionRepository->all();

        return view('admin.employees.employment-status.create', compact('employee', 'positions'));
    }

    public function store(EmploymentStatusRequest $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validated();
        $tab = $validated['tab'];

        if ($tab === 'cepat') {
            $this->storeCepat($request, $employee, $validated);
        } else {
            $this->storeLengkap($employee, $validated);
        }

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Kontrak karyawan berhasil dibuat.');
    }

    private function storeLengkap(Employee $employee, array $validated): void
    {
        $employee->employmentStatuses()->create([
            'type_employment' => $validated['type_employment'],
            'join_date' => $validated['join_date'],
            'position_id' => $validated['position_id'],
            'contract_start_date' => $validated['contract_start_date'],
            'contract_end_date' => $validated['contract_end_date'] ?? null,
            'setup_incomplete' => false,
        ]);

        $this->employeeAuthService->syncRoleFromCurrentStatus($employee);
    }

    private function storeCepat(EmploymentStatusRequest $request, Employee $employee, array $validated): void
    {
        $path = null;
        if ($request->hasFile('contract_file_path')) {
            $file = $request->file('contract_file_path');
            $path = $file->store('contracts', 'public');
        }

        $employee->employmentStatuses()->create([
            'type_employment' => $validated['type_employment'],
            'join_date' => $validated['join_date'],
            'position_id' => $validated['position_id'],
            'contract_file_path' => $path,
            'setup_incomplete' => true,
        ]);

        $this->employeeAuthService->syncRoleFromCurrentStatus($employee);
    }

    public function edit(EmploymentStatus $status): View
    {
        $positions = $this->positionRepository->all();

        return view('admin.employees.employment-status.edit', compact('status', 'positions'));
    }

    public function update(EmploymentStatusRequest $request, EmploymentStatus $status): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'type_employment' => $validated['type_employment'],
            'join_date' => $validated['join_date'],
            'position_id' => $validated['position_id'],
        ];

        if (array_key_exists('contract_start_date', $validated)) {
            $data['contract_start_date'] = $validated['contract_start_date'];
            $data['contract_end_date'] = $validated['contract_end_date'];
        }

        $status->update($data);

        return back()->with('success', 'Kontrak karyawan berhasil diperbarui.');
    }

    public function extendCreate(EmploymentStatus $status): View
    {
        return view('admin.employees.employment-status.extend', compact('status'));
    }

    public function extendStore(ContractExtendRequest $request, EmploymentStatus $status): RedirectResponse
    {
        $validated = $request->validated();

        $status->extendOffer()->updateOrCreate(
            ['employment_status_id' => $status->id],
            [
                'current_end_date' => $status->contract_end_date,
                'proposed_end_date' => $validated['proposed_end_date'],
                'status' => 'pending',
                'notes' => $validated['notes'],
            ]
        );

        return redirect()
            ->route('employees.show', $status->employee)
            ->with('success', 'Penawaran perpanjangan kontrak telah dibuat.');
    }

    public function extendAccept(EmploymentStatus $status): RedirectResponse
    {
        $offer = $status->extendOffer;
        abort_if(! $offer || $offer->status !== 'pending', 404);

        DB::transaction(function () use ($status, $offer) {
            $offer->update(['status' => 'accepted']);
            $status->update(['contract_end_date' => $offer->proposed_end_date]);
        });

        return redirect()
            ->route('employees.show', $status->employee)
            ->with('success', 'Perpanjangan kontrak disetujui.');
    }

    public function extendReject(EmploymentStatus $status): RedirectResponse
    {
        $offer = $status->extendOffer;
        abort_if(! $offer || $offer->status !== 'pending', 404);

        $offer->update(['status' => 'rejected']);

        return redirect()
            ->route('employees.show', $status->employee)
            ->with('success', 'Penawaran perpanjangan ditolak.');
    }

    public function changePositionCreate(EmploymentStatus $status): View
    {
        $positions = $this->positionRepository->all();

        return view('admin.employees.employment-status.change-position', compact('status', 'positions'));
    }

    public function changePositionStore(ContractChangePositionRequest $request, EmploymentStatus $status): RedirectResponse
    {
        $validated = $request->validated();

        $employee = $status->employee;

        $employee->employmentStatuses()->create([
            'type_employment' => $status->type_employment,
            'join_date' => $status->join_date,
            'position_id' => $validated['position_id'],
            'contract_start_date' => $validated['effective_date'],
            'contract_end_date' => $status->contract_end_date,
            'setup_incomplete' => false,
        ]);

        $this->employeeAuthService->syncRoleFromCurrentStatus($employee);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Posisi karyawan berhasil diubah.');
    }
}
