<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToeflMedia extends Model
{
    protected $table = 'toefl_media';

    protected $fillable = [
        'media_type',
        'original_name',
        'file_path',
        'uploaded_by_employee_id',
    ];

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by_employee_id');
    }
}
