<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutLetterViaUpload extends Model
{
    protected $table = 'out_letters_via_upload';

    protected $fillable = [
        'branch_id',
        'letter_type',
        'letter_number',
        'letter_date',
        'recipient',
        'subject',
        'telegram_file_id',
        'original_name',
        'mime_type',
        'uploaded_by_employee_id',
        'notes',
    ];

    protected $casts = [
        'letter_date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by_employee_id');
    }
}
