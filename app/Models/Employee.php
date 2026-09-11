<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'full_name',
        'gender',
        'birthdate',
        'email',
        'whatsapp_number',
        'image_path',
        'signature_image',
        'contract_file_path',
        'branch_id',
        'area_id',
        'region_id',
        'is_active',
    ];

    protected $hidden = ['signature_image'];

    protected $casts = [
        'birthdate' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function employmentStatuses(): HasMany
    {
        return $this->hasMany(EmploymentStatus::class, 'employees_id');
    }

    public function currentStatus(): HasOne
    {
        return $this->hasOne(EmploymentStatus::class, 'employees_id')
            ->doesntHave('offBoarding')
            ->latestOfMany('created_at');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function workHistories(): HasMany
    {
        return $this->hasMany(EmployeeWorkHistory::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInUserScope(Builder $query, ?\Illuminate\Foundation\Auth\User $user): Builder
    {
        if (! $user || ! $user->employee) {
            return $query;
        }

        if ($user->employee->branch_id) {
            return $query->where('branch_id', $user->employee->branch_id);
        }

        if ($user->employee->area_id) {
            return $query->where('area_id', $user->employee->area_id);
        }

        if ($user->employee->region_id) {
            return $query->where('region_id', $user->employee->region_id);
        }

        return $query; // BOARD or global access
    }
}
