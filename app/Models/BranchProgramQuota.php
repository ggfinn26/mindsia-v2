<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchProgramQuota extends Model
{
    protected $table = 'branch_program_quotas';

    protected $fillable = [
        'branch_id',
        'program_id',
        'quota_limit',
    ];

    protected $casts = [
        'quota_limit' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
