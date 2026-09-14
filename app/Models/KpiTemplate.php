<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiTemplate extends Model
{
    protected $fillable = [
        'template_code',
        'template_name',
        'position_id',
        'description',
        'is_active',
        'created_by_employee_id',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(KpiTemplateIndicator::class)->orderBy('sequence_number');
    }

    public function activeIndicators(): HasMany
    {
        return $this->hasMany(KpiTemplateIndicator::class)
            ->where('is_active', true)
            ->orderBy('sequence_number');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(EmployeeKpiEvaluation::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(KpiDocument::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
