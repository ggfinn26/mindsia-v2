<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    public const MAX_CLASS_NAME_LENGTH = 255;

    public const MAX_WEEK_COUNT = 52;

    public const MIN_WEEK_COUNT = 1;

    public const DAY_PAIRS = [
        'Senin' => 'Kamis',
        'Kamis' => 'Senin',
        'Selasa' => 'Jumat',
        'Jumat' => 'Selasa',
        'Rabu' => 'Sabtu',
        'Sabtu' => 'Rabu',
    ];

    public const PRIMARY_DAYS = ['Senin', 'Selasa', 'Rabu'];

    public const VALID_DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    protected $fillable = [
        'program_id',
        'branch_id',
        'class_name',
        'tutor_id',
        'day_of_week',
        'week_count',
        'start_date',
        'end_date',
        'start_time_primary',
        'end_time_primary',
        'start_time_secondary',
        'end_time_secondary',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'week_count' => 'integer',
    ];

    public function getDayOfWeek2Attribute(): ?string
    {
        return self::DAY_PAIRS[$this->day_of_week] ?? null;
    }

    public function canBeDeleted(): bool
    {
        return $this->status === 'planned' && ! $this->memberClasses()->exists();
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'tutor_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'class_id');
    }

    public function memberClasses(): HasMany
    {
        return $this->hasMany(MemberClass::class, 'class_id');
    }

    public function tutorChangeHistories(): HasMany
    {
        return $this->hasMany(ClassTutorChangeHistory::class, 'class_id');
    }

    public function tests(): HasMany
    {
        return $this->hasMany(ClassTest::class, 'class_id');
    }
}
