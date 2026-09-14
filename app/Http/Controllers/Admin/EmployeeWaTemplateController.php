<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\StoreEmployeeWaTemplateRequest;
use App\Models\EmployeeWaTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeWaTemplateController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('marketing.wa_template.manage'), 403);

        $templates = EmployeeWaTemplate::where('employee_id', $request->user()->employee->id)
            ->orderBy('template_name')
            ->get();

        return view('marketing.wa-template.index', compact('templates'));
    }

    public function store(StoreEmployeeWaTemplateRequest $request): RedirectResponse
    {
        EmployeeWaTemplate::create(array_merge($request->validated(), [
            'employee_id' => $request->user()->employee->id,
        ]));

        return back()->with('success', 'Template berhasil ditambahkan.');
    }

    public function update(StoreEmployeeWaTemplateRequest $request, EmployeeWaTemplate $employeeWaTemplate): RedirectResponse
    {
        abort_unless($employeeWaTemplate->employee_id === $request->user()->employee->id, 403);

        $employeeWaTemplate->update($request->validated());

        return back()->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(Request $request, EmployeeWaTemplate $employeeWaTemplate): RedirectResponse
    {
        abort_unless($employeeWaTemplate->employee_id === $request->user()->employee->id, 403);

        $employeeWaTemplate->delete();

        return back()->with('success', 'Template dihapus.');
    }
}
