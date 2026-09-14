<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SopDocument extends Model
{
    protected $fillable = [
        'branch_id',
        'category',
        'title',
        'document_code',
        'version',
        'telegram_file_id',
        'effective_date',
        'visible_to',
        'is_active',
        'uploaded_by_employee_id',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'visible_to' => 'array',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by_employee_id');
    }

    public function isVisibleToRole(string $roleName): bool
    {
        if ($this->visible_to === null) {
            return true;
        }

        return in_array($roleName, $this->visible_to, true);
    }
}
