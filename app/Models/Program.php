<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Program extends Model
{
    protected $fillable = [
        'program_name',
        'program_code',
        'program_price',
        'program_description',
        'is_active',
    ];

    protected $casts = [
        'program_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function curriculum(): HasOne
    {
        return $this->hasOne(Curriculum::class);
    }

    public function branchQuotas(): HasMany
    {
        return $this->hasMany(BranchProgramQuota::class);
    }
}
