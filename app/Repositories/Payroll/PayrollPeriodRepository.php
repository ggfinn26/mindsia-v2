<?php

namespace App\Repositories\Payroll;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class PayrollPeriodRepository
{
    public function all(): Collection
    {
        return PayrollPeriod::orderByDesc('period_year')->orderByDesc('period_month')->get();
    }

    public function findById(int $id): PayrollPeriod
    {
        return PayrollPeriod::findOrFail($id);
    }

    public function findByMonthYear(int $month, int $year): ?PayrollPeriod
    {
        return PayrollPeriod::where('period_month', $month)->where('period_year', $year)->first();
    }

    public function create(array $data): PayrollPeriod
    {
        if (empty($data['pay_date'])) {
            $data['pay_date'] = Carbon::createFromDate($data['period_year'], $data['period_month'], 3)->addMonth()->toDateString();
        }

        return PayrollPeriod::create($data);
    }

    public function update(PayrollPeriod $period, array $data): PayrollPeriod
    {
        $period->update($data);

        return $period;
    }

    public function advanceStatus(PayrollPeriod $period, ?Employee $confirmedBy = null): PayrollPeriod
    {
        $next = match ($period->status) {
            'draft' => 'review',
            'review' => 'finalized',
            default => throw new \LogicException('Periode sudah finalized.'),
        };

        $update = ['status' => $next];

        if ($next === 'finalized' && $confirmedBy) {
            $update['confirmed_by_employee_id'] = $confirmedBy->id;
            $update['confirmed_at'] = now();
        }

        $period->update($update);

        return $period;
    }

    public function revertToDraft(PayrollPeriod $period): PayrollPeriod
    {
        $period->update([
            'status' => 'draft',
            'confirmed_by_employee_id' => null,
            'confirmed_at' => null,
        ]);

        return $period;
    }
}
