<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberReview extends Model
{
    protected $fillable = [
        'member_registration_id',
        'rating',
        'review',
        'is_approved',
        'approved_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        // FK must be explicit: 'registration()' method name → auto-detect gives 'registration_id' (wrong)
        return $this->belongsTo(MemberRegistration::class, 'member_registration_id');
    }
}
