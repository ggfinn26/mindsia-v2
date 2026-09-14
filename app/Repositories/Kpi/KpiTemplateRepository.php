<?php

namespace App\Repositories\Kpi;

use App\Models\KpiTemplate;
use App\Models\KpiTemplateIndicator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class KpiTemplateRepository
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return KpiTemplate::with('position')
            ->when($filters['position_id'] ?? null, fn ($q, $v) => $q->where('position_id', $v))
            ->when(isset($filters['is_active']), fn ($q) => $q->where('is_active', $filters['is_active']))
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('template_code', 'like', "%{$v}%")
                    ->orWhere('template_name', 'like', "%{$v}%");
            }))
            ->orderBy('template_name')
            ->paginate(25);
    }

    public function find(int $id): KpiTemplate
    {
        return KpiTemplate::with('position', 'indicators', 'createdBy')->findOrFail($id);
    }

    public function active(): Collection
    {
        return KpiTemplate::active()->with('position')->orderBy('template_name')->get();
    }

    public function create(array $data): KpiTemplate
    {
        return KpiTemplate::create($data);
    }

    public function update(KpiTemplate $template, array $data): KpiTemplate
    {
        $template->update($data);

        return $template;
    }

    public function createIndicator(KpiTemplate $template, array $data): KpiTemplateIndicator
    {
        return $template->indicators()->create($data);
    }

    public function updateIndicator(KpiTemplateIndicator $indicator, array $data): KpiTemplateIndicator
    {
        $indicator->update($data);

        return $indicator;
    }

    public function deleteIndicator(KpiTemplateIndicator $indicator): void
    {
        $indicator->update(['is_active' => false]);
    }
}
