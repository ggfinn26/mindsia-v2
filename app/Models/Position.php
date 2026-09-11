<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Position extends Model
{
    protected $fillable = [
        'position_name',
        'role_id',
        'hierarchy_order',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'position_permissions');
    }

    public function employmentStatuses(): HasMany
    {
        return $this->hasMany(EmploymentStatus::class);
    }

    public function canDelete(): bool
    {
        return $this->employmentStatuses()
            ->whereDoesntHave('offBoarding')
            ->doesntExist();
    }
}
