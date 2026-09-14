<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityTicketStatusHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'facility_ticket_id',
        'from_status',
        'to_status',
        'changed_by_employee_id',
        'notes',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(FacilityTicket::class, 'facility_ticket_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'changed_by_employee_id');
    }
}
