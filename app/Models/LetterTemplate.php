<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LetterTemplate extends Model
{
    protected $fillable = [
        'template_code',
        'template_name',
        'letter_category',
        'letter_number_format',
        'telegram_file_id',
        'is_active',
        'created_by_employee_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function generatedLetters(): HasMany
    {
        return $this->hasMany(OutLetterViaGenerate::class, 'letter_template_id');
    }
}
