<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TerminationChecklist extends Model
{
    use HasFactory;

    protected $table = 'termination_checklist';

    protected $fillable = [
        'employment_status_id',
        'item_name',
        'description',
        'is_completed',
        'completed_at',
        'completed_by',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function employmentStatus(): BelongsTo
    {
        return $this->belongsTo(EmploymentStatus::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function complete(User $user): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by' => $user->id,
        ]);
    }

    public function uncomplete(): void
    {
        $this->update([
            'is_completed' => false,
            'completed_at' => null,
            'completed_by' => null,
        ]);
    }
}
