<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToeflDirectionAudio extends Model
{
    protected $table = 'toefl_direction_audio';

    protected $fillable = [
        'part',
        'file_path',
        'uploaded_by_employee_id',
    ];

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by_employee_id');
    }
}
