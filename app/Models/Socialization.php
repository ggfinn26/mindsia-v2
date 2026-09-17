<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Socialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'area_id',
        'institution_id',
        'location_name',
        'partner_fee_amount',
        'partner_fee_status',
        'partner_fee_due_date',
        'partner_fee_paid_at',
        'status',
        'created_by_employee_id',
    ];

    protected $casts = [
        'partner_fee_amount' => 'decimal:2',
        'partner_fee_due_date' => 'date',
        'partner_fee_paid_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';

    const STATUS_SCHEDULED = 'scheduled';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(SocializationSchedule::class);
    }

    public function employeeSocializations(): HasMany
    {
        return $this->hasMany(EmployeeSocialization::class);
    }

    public function prospectiveMembers(): HasMany
    {
        return $this->hasMany(ProspectiveMember::class);
    }
}
