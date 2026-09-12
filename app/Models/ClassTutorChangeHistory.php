<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassTutorChangeHistory extends Model
{
    protected $table = 'class_tutor_change_histories';

    const UPDATED_AT = null;

    protected $fillable = ['class_id', 'from_tutor_id', 'to_tutor_id', 'changed_by_employee_id', 'reason', 'changed_at'];

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function fromTutor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'from_tutor_id');
    }

    public function toTutor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'to_tutor_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'changed_by_employee_id');
    }
}
