<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InLetter extends Model
{
    protected $table = 'in_letters';

    protected $fillable = [
        'branch_id',
        'sender_name',
        'letter_date',
        'receive_date',
        'letter_number',
        'subject',
        'telegram_file_id',
        'original_name',
        'mime_type',
        'pic_employee_id',
        'uploaded_by_employee_id',
        'notes',
    ];

    protected $casts = [
        'letter_date' => 'date',
        'receive_date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'pic_employee_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by_employee_id');
    }
}
