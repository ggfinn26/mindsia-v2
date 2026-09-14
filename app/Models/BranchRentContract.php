<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchRentContract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'area_id',
        'owner_name',
        'owner_phone',
        'rent_amount',
        'down_payment',
        'termin_count',
        'rent_period',
        'start_date',
        'end_date',
        'status',
        'notes',
        'created_by_employee_id',
    ];

    protected $casts = [
        'rent_amount' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function termins(): HasMany
    {
        return $this->hasMany(BranchRentTermin::class, 'contract_id')->orderBy('termin_number');
    }

    public function terminAmountPerCycle(): float
    {
        return ($this->rent_amount - $this->down_payment) / $this->termin_count;
    }
}
