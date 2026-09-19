<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiDocument extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'kpi_template_id',
        'employee_kpi_evaluation_id',
        'document_type',
        'title',
        'description',
        'telegram_file_id',
        'storage_path',
        'original_name',
        'uploaded_by_employee_id',
        'uploaded_at',
    ];

    protected $casts = ['uploaded_at' => 'datetime'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(KpiTemplate::class, 'kpi_template_id');
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(EmployeeKpiEvaluation::class, 'employee_kpi_evaluation_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by_employee_id');
    }
}
