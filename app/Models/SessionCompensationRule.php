<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

class SessionCompensationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_code',
        'rule_name',
        'scope_type',
        'role_id',
        'position_id',
        'employee_id',
        'amount_per_session',
        'sessions_per_month_basis',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'amount_per_session' => 'decimal:2',
        'sessions_per_month_basis' => 'integer',
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
