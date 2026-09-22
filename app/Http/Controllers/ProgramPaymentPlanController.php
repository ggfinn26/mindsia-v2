<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramPaymentPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProgramPaymentPlanController extends Controller
{
    public function store(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:cash,installment'],
            'installment_count' => ['nullable', 'integer', 'min:1', 'required_if:type,installment'],
            'dp_amount' => ['nullable', 'integer', 'min:0'],
            'per_installment_amount' => ['nullable', 'integer', 'min:0', 'required_if:type,installment'],
        ]);

        $program->paymentPlans()->create([
            'label' => $validated['label'],
            'type' => $validated['type'],
            'installment_count' => $validated['installment_count'] ?? null,
            'dp_amount' => $validated['dp_amount'] ?? 0,
            'per_installment_amount' => $validated['per_installment_amount'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Payment plan berhasil ditambahkan.');
    }

    public function update(Request $request, ProgramPaymentPlan $plan): RedirectResponse
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $plan->update(['is_active' => (bool) $request->is_active]);

        $action = $request->is_active ? 'diaktifkan' : 'diarsipkan';

        return back()->with('success', "Payment plan berhasil {$action}.");
    }

    public function destroy(ProgramPaymentPlan $plan): RedirectResponse
    {
        if ($plan->registrations()->exists()) {
            return back()->with('error', 'Payment plan tidak bisa dihapus karena sudah digunakan oleh registrasi.');
        }

        $plan->delete();

        return back()->with('success', 'Payment plan berhasil dihapus.');
    }
}
