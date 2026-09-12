<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberSupportTicketReply extends Model
{
    protected $fillable = [
        'member_support_ticket_id',
        'employee_id',
        'member_id',
        'reply',
        'telegram_attachment_id',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(MemberSupportTicket::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberData::class, 'member_id');
    }
}
