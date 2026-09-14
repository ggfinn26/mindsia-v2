<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProspectiveMember extends Model
{
    protected $fillable = [
        'socialization_id',
        'captured_by_employee_id',
        'member_id',
        'full_name',
        'whatsapp_number',
        'form_distributed_at',
        'instagram',
        'email',
        'gender',
        'institution_id',
        'institution_name',
        'status',
        'notes',
    ];

    protected $casts = [
        'form_distributed_at' => 'datetime',
    ];

    const STATUS_ALMOST = 'ALMOST';

    const STATUS_YES = 'YES';

    const STATUS_NO = 'NO';

    const STATUS_FIXED = 'FIXED';

    public function socialization(): BelongsTo
    {
        return $this->belongsTo(Socialization::class);
    }

    public function capturedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'captured_by_employee_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberData::class, 'member_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ProspectiveMemberStatusHistory::class);
    }
}
