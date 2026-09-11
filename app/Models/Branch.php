<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'areas_id',
        'branch_name',
        'code_branches',
        'address',
        'gmaps_url',
        'whatsapp',
        'instagram',
        'latitude',
        'longitude',
        'radius_meters',
        'is_active',
        'ma_pic_employee_id',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'areas_id');
    }

    public function picEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'ma_pic_employee_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function programQuotas(): HasMany
    {
        return $this->hasMany(BranchProgramQuota::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
