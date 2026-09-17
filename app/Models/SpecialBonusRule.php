<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class SpecialBonusRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'rule_code',
        'rule_name',
        'scope_type',
        'role_id',
        'position_id',
        'employee_id',
        'reward_type',
        'reward_value',
        'reward_basis',
        'condition_mode',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'reward_value' => 'decimal:2',
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

    public function conditions(): HasMany
    {
        return $this->hasMany(SpecialBonusRuleCondition::class);
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
