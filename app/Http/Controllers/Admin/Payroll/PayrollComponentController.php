<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\StorePayrollComponentRequest;
use App\Http\Requests\Payroll\UpdatePayrollComponentRequest;
use App\Models\PayrollComponent;
use App\Repositories\Payroll\PayrollComponentRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PayrollComponentController extends Controller
{
    public function __construct(
        private readonly PayrollComponentRepository $repo,
    ) {}

    public function index(): View
    {
        return view('admin.payroll.components.index', [
            'components' => $this->repo->all(),
        ]);
    }

    public function store(StorePayrollComponentRequest $request): RedirectResponse
    {
        $this->repo->create($request->validated());

        return redirect()->route('payroll.components.index')->with('success', 'Komponen gaji berhasil ditambahkan.');
    }

    public function update(UpdatePayrollComponentRequest $request, PayrollComponent $component): RedirectResponse
    {
        $this->repo->update($component, $request->validated());

        return redirect()->route('payroll.components.index')->with('success', 'Komponen gaji berhasil diperbarui.');
    }

    public function destroy(PayrollComponent $component): RedirectResponse
    {
        if ($this->repo->hasPayrollHistory($component)) {
            $this->repo->update($component, ['is_active' => false]);

            return redirect()->route('payroll.components.index')->with('success', 'Komponen gaji dinonaktifkan (sudah digunakan di payroll).');
        }

        $component->delete();

        return redirect()->route('payroll.components.index')->with('success', 'Komponen gaji berhasil dihapus.');
    }
}
