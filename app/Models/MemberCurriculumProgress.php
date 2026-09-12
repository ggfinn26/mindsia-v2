<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberCurriculumProgress extends Model
{
    protected $table = 'member_curriculum_progress';

    protected $fillable = ['member_class_id', 'curriculum_item_id', 'status', 'started_at', 'completed_at', 'notes'];

    protected $casts = ['started_at' => 'datetime', 'completed_at' => 'datetime'];

    public function memberClass(): BelongsTo
    {
        return $this->belongsTo(MemberClass::class);
    }

    public function curriculumItem(): BelongsTo
    {
        return $this->belongsTo(CurriculumItem::class);
    }
}
