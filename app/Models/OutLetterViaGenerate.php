<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutLetterViaGenerate extends Model
{
    protected $table = 'out_letter_via_generate';

    protected $fillable = [
        'letter_template_id',
        'branch_id',
        'letter_type',
        'letter_number',
        'letter_date',
        'recipient',
        'subject',
        'telegram_file_id',
        'storage_path',
        'payload',
        'status',
        'signer_employee_id',
        'signer_name_snapshot',
        'signer_title_snapshot',
        'created_by_employee_id',
        'published_by_employee_id',
        'published_at',
        'notes',
    ];

    protected $casts = [
        'letter_date' => 'date',
        'payload' => 'array',
        'published_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(LetterTemplate::class, 'letter_template_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'signer_employee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'published_by_employee_id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
}
