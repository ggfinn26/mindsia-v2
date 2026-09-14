<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberSupportTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'branch_id',
        'member_id',
        'subject',
        'message',
        'status',
        'telegram_attachment_id',
        'category',
        'priority',
        'assigned_employee_id',
        'resolved_at',
        'rating',
        'rating_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'rating' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberData::class, 'member_id');
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(MemberSupportTicketReply::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(MemberSupportTicketStatusHistory::class);
    }
}
