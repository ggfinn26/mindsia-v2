<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToeflSession extends Model
{
    protected $table = 'toefl_sessions';

    protected $fillable = [
        'members_data_id',
        'toefl_test_id',
        'status',
        'listening_started_at',
        'structure_started_at',
        'reading_started_at',
        'listening_submitted_at',
        'structure_submitted_at',
        'reading_submitted_at',
        'score_listening',
        'score_structure',
        'score_reading',
        'score_total',
        'started_at',
        'completed_at',
        'guest_name',
        'guest_email',
        'guest_whatsapp',
        'guest_instagram',
        'guest_institution',
        'guest_city',
    ];

    protected $casts = [
        'listening_started_at' => 'datetime',
        'structure_started_at' => 'datetime',
        'reading_started_at' => 'datetime',
        'listening_submitted_at' => 'datetime',
        'structure_submitted_at' => 'datetime',
        'reading_submitted_at' => 'datetime',
        'score_listening' => 'integer',
        'score_structure' => 'integer',
        'score_reading' => 'integer',
        'score_total' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS_COMPLETED = 'completed';

    const STATUS_EXPIRED = 'expired';

    public function isGuest(): bool
    {
        return $this->members_data_id === null;
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberData::class, 'members_data_id');
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(ToeflTest::class, 'toefl_test_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ToeflAnswer::class, 'toefl_session_id');
    }
}
