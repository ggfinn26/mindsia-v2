<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacilityTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'branch_id',
        'created_by_employee_id',
        'assigned_to_employee_id',
        'category',
        'priority',
        'title',
        'description',
        'status',
        'cost_amount',
    ];

    protected $casts = [
        'cost_amount' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to_employee_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(FacilityTicketStatusHistory::class)->orderBy('changed_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(FacilityTicketAttachment::class);
    }
}
