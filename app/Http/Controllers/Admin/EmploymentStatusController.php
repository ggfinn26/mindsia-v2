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
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
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

    public function index(Request $request): View
    {
        $today = now()->toDateString();
        $soon = now()->addDays(30)->toDateString();

        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $typeFilter = $request->input('type');
        $sort = $request->input('sort', 'end_date_asc');

        $query = Employee::query()
            ->where('is_active', true)
            ->with(['currentStatus.position'])
            ->whereHas('currentStatus', function ($q) use ($typeFilter) {
                if ($typeFilter) {
                    $q->where('type_employment', $typeFilter);
                }
            });

        $employees = $query->get();

        if ($search) {
            $searchLower = strtolower($search);
            $employees = $employees->filter(function ($e) use ($searchLower) {
                return str_contains(strtolower($e->full_name), $searchLower);
            });
        }

        if ($statusFilter) {
            $employees = $employees->filter(function ($e) use ($statusFilter) {
                $status = $e->currentStatus;
                $expiring = $status?->contract_end_date && $status->contract_end_date->lte(now()->addDays(30));

                if ($statusFilter === 'expiring') {
                    return $expiring;
                }
                if ($statusFilter === 'permanent') {
                    return empty($status?->contract_end_date);
                }
                if ($statusFilter === 'active') {
                    return ! empty($status?->contract_end_date) && ! $expiring;
                }

                return true;
            });
        }

        if ($sort === 'name_asc') {
            $employees = $employees->sortBy(fn ($e) => strtolower($e->full_name));
        } elseif ($sort === 'name_desc') {
            $employees = $employees->sortByDesc(fn ($e) => strtolower($e->full_name));
        } elseif ($sort === 'posisi_asc') {
            $employees = $employees->sortBy(fn ($e) => strtolower($e->currentStatus?->position?->position_name ?? 'zzz'));
        } elseif ($sort === 'posisi_desc') {
            $employees = $employees->sortByDesc(fn ($e) => strtolower($e->currentStatus?->position?->position_name ?? ''));
        } elseif ($sort === 'tipe_asc') {
            $employees = $employees->sortBy(fn ($e) => strtolower($e->currentStatus?->type_employment ?? 'zzz'));
        } elseif ($sort === 'tipe_desc') {
            $employees = $employees->sortByDesc(fn ($e) => strtolower($e->currentStatus?->type_employment ?? ''));
        } elseif ($sort === 'status_asc') {
            $employees = $employees->sortBy(fn ($e) => $e->currentStatus?->contract_end_date ?? '9999-12-31');
        } elseif ($sort === 'status_desc') {
            $employees = $employees->sortByDesc(fn ($e) => $e->currentStatus?->contract_end_date ?? '0000-01-01');
        } elseif ($sort === 'end_date_desc') {
            $employees = $employees->sortByDesc(fn ($e) => $e->currentStatus?->contract_end_date ?? '0000-01-01');
        } else {
            $employees = $employees->sortBy(fn ($e) => $e->currentStatus?->contract_end_date ?? '9999-12-31');
        }

        $page = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $employees = new LengthAwarePaginator(
            $employees->forPage($page, $perPage)->values(),
            $employees->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        return view('admin.employees.employment-status.index', compact('employees', 'today', 'soon'));
    }

    public function create(Employee $employee): View
    {
        $positions = $this->positionRepository->all();

        return view('admin.employees.employment-status.create', compact('employee', 'positions'));
    }

    public function store(EmploymentStatusRequest $request, Employee $employee): RedirectResponse
    {
        $employee->load('currentStatus.position');

        if ($employee->currentStatus !== null) {
            $status = $employee->currentStatus;
            $posisi = $status->position?->position_name ?? '—';
            $akhir = $status->contract_end_date
                ? Carbon::parse($status->contract_end_date)->translatedFormat('d F Y')
                : 'tidak ditentukan';

            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Kontrak karyawan atas nama {$employee->full_name} masih berlaku (berakhir {$akhir}) sebagai {$posisi}. Ubah atau putus kontrak terlebih dahulu.");
        }

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
            'position_id' => $validated['position_id'],
            'contract_end_date' => $validated['contract_end_date'] ?? null,
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
        $status->update(['contract_end_date' => $request->validated()['new_end_date']]);

        return redirect()
            ->route('employees.show', $status->employee)
            ->with('success', 'Kontrak karyawan berhasil diperpanjang.');
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

        $status->update([
            'contract_end_date' => Carbon::parse($validated['contract_start_date'])->subDay(),
        ]);

        $employee->employmentStatuses()->create([
            'type_employment' => $status->type_employment,
            'position_id' => $validated['position_id'],
            'contract_start_date' => $validated['contract_start_date'],
            'contract_end_date' => $validated['contract_end_date'] ?? null,
            'setup_incomplete' => false,
        ]);

        $this->employeeAuthService->syncRoleFromCurrentStatus($employee);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Posisi karyawan berhasil diubah.');
    }
}
