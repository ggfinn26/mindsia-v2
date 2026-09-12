<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class MarketingBonusRule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'rule_code',
        'rule_name',
        'scope_type',
        'role_id',
        'position_id',
        'employee_id',
        'bonus_basis',
        'revenue_basis',
        'reward_basis',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(MarketingBonusRuleTier::class);
    }

    public function scopeForEmployee(Builder $query, int $employeeId, int $positionId, int $roleId): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) use ($employeeId, $positionId, $roleId) {
                $q->where('scope_type', 'global')
                    ->orWhere(fn ($q) => $q->where('scope_type', 'role')->where('role_id', $roleId))
                    ->orWhere(fn ($q) => $q->where('scope_type', 'position')->where('position_id', $positionId))
                    ->orWhere(fn ($q) => $q->where('scope_type', 'employee')->where('employee_id', $employeeId));
            });
    }
}
