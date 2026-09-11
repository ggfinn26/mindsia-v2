<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractExtendOffer extends Model
{
    protected $fillable = [
        'employment_status_id',
        'current_end_date',
        'proposed_end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'current_end_date' => 'date',
        'proposed_end_date' => 'date',
    ];

    public function employmentStatus(): BelongsTo
    {
        return $this->belongsTo(EmploymentStatus::class);
    }
}
