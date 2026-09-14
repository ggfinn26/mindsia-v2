<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmployeeSocialization extends Model
{
    protected $fillable = [
        'employee_id',
        'socialization_id',
        'classes_obtained',
        'status',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function socialization(): BelongsTo
    {
        return $this->belongsTo(Socialization::class);
    }

    public function history(): HasOne
    {
        return $this->hasOne(EmployeeSocializationHistory::class);
    }
}
