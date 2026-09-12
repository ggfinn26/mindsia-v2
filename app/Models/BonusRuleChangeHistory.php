<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusRuleChangeHistory extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'bonus_type',
        'rule_id',
        'action',
        'old_values',
        'new_values',
        'changed_by_employee_id',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'changed_by_employee_id');
    }
}
