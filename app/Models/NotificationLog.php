<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'notification_template_id',
        'employee_id',
        'member_id',
        'notification_type',
        'channel',
        'payload',
        'notification_status',
        'attempts',
        'notification_sent_at',
        'last_attempt_at',
        'notification_error',
    ];

    protected $casts = [
        'payload' => 'array',
        'notification_sent_at' => 'datetime',
        'last_attempt_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'notification_template_id');
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
