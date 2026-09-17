<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmploymentStatus extends Model
{
    use HasFactory;
    protected $table = 'employment_status';

    protected $fillable = [
        'employees_id',
        'type_employment',
        'join_date',
        'contract_start_date',
        'contract_end_date',
        'position_id',
        'setup_incomplete',
        'contract_file_path',
    ];

    protected $casts = [
        'join_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function offBoarding(): HasOne
    {
        return $this->hasOne(OffBoardingStatus::class);
    }

    public function extendOffer(): HasOne
    {
        return $this->hasOne(ContractExtendOffer::class);
    }

    public function terminationChecklist(): HasMany
    {
        return $this->hasMany(TerminationChecklist::class);
    }
}
