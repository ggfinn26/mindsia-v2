<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_name',
        'program_code',
        'program_price',
        'admin_fee',
        'admin_fee_mode',
        'admin_fee_timing',
        'program_description',
        'is_active',
    ];

    protected $casts = [
        'program_price' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Landing page accessors — map DB columns to view-expected properties
    public function getCodeAttribute(): string
    {
        return $this->program_code;
    }

    public function getTaglineAttribute(): ?string
    {
        return $this->program_description;
    }

    public function getIsFeaturedAttribute(): bool
    {
        return false;
    }

    public function getLevelLabelAttribute(): ?string
    {
        return null;
    }

    public function curriculum(): HasOne
    {
        return $this->hasOne(Curriculum::class);
    }

    public function branchQuotas(): HasMany
    {
        return $this->hasMany(BranchProgramQuota::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(ClassRoom::class);
    }
}
