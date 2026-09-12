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
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(MemberRegistration::class);
    }
}
