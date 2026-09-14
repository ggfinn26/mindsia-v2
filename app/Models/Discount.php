<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    protected $fillable = [
        'discount_code',
        'is_active',
        'discount_type',
        'discount_nominal',
        'discount_quota',
        'starts_at',
        'expired_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_nominal' => 'integer',
        'discount_quota' => 'integer',
        'starts_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function programs(): HasMany
    {
        return $this->hasMany(DiscountProgram::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(MemberRegistration::class);
    }

    public function isValidForProgram(int $programId): bool
    {
        if (! $this->programs()->exists()) {
            return true;
        }

        return $this->programs()->where('program_id', $programId)->exists();
    }
}
