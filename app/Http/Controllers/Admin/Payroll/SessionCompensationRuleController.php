<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\StoreSessionCompensationRuleRequest;
use App\Http\Requests\Payroll\UpdateSessionCompensationRuleRequest;
use App\Models\SessionCompensationRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SessionCompensationRuleController extends Controller
{
    public function index(): View
    {
        return view('admin.payroll.session-rules.index', [
            'rules' => SessionCompensationRule::with(['role', 'position', 'employee'])->get(),
        ]);
    }

    public function store(StoreSessionCompensationRuleRequest $request): RedirectResponse
    {
        SessionCompensationRule::create($request->validated());

        return redirect()->route('payroll.session-rules.index')->with('success', 'Rule sesi berhasil ditambahkan.');
    }

    public function update(UpdateSessionCompensationRuleRequest $request, SessionCompensationRule $sessionRule): RedirectResponse
    {
        $sessionRule->update($request->validated());

        return redirect()->route('payroll.session-rules.index')->with('success', 'Rule sesi berhasil diperbarui.');
    }

    public function destroy(SessionCompensationRule $sessionRule): RedirectResponse
    {
        $sessionRule->update(['is_active' => false]);

        return redirect()->route('payroll.session-rules.index')->with('success', 'Rule sesi dinonaktifkan.');
    }
}
