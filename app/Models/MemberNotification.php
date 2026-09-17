<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberNotification extends Model
{
    use HasFactory;
    protected $fillable = [
        'member_id',
        'subject',
        'message',
        'status',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberData::class, 'member_id');
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', 'unread');
    }
}
