<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToeflTest extends Model
{
    protected $table = 'toefl_tests';

    protected $fillable = [
        'test_name',
        'status',
        'password',
        'is_trial',
        'listening_time_limit',
        'structure_time_limit',
        'reading_time_limit',
        'created_by_employee_id',
        'published_by_employee_id',
        'published_at',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'is_trial' => 'boolean',
        'listening_time_limit' => 'integer',
        'structure_time_limit' => 'integer',
        'reading_time_limit' => 'integer',
        'published_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';

    const STATUS_PUBLISHED = 'published';

    const DEFAULT_TIME_LIMIT = [
        'listening' => 35,
        'structure' => 25,
        'reading' => 55,
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'published_by_employee_id');
    }

    public function passages(): HasMany
    {
        return $this->hasMany(ToeflPassage::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ToeflQuestion::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ToeflSession::class);
    }
}
