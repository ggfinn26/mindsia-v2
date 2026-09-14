<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSocializationHistory extends Model
{
    protected $fillable = [
        'employee_socialization_id',
        'employee_id',
        'socialization_id',
        'employee_name_snapshot',
        'socialization_date_snapshot',
        'location_name_snapshot',
        'classes_obtained',
        'prospective_members_count',
        'snapshot_at',
    ];

    protected $casts = [
        'socialization_date_snapshot' => 'date',
        'snapshot_at' => 'datetime',
    ];

    public function employeeSocialization(): BelongsTo
    {
        return $this->belongsTo(EmployeeSocialization::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function socialization(): BelongsTo
    {
        return $this->belongsTo(Socialization::class);
    }
}
