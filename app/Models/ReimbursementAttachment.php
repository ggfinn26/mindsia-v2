<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReimbursementAttachment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'reimbursement_item_id', 'file_id', 'file_name', 'file_type',
    ];

    public function reimbursementItem(): BelongsTo
    {
        return $this->belongsTo(ReimbursementItem::class);
    }
}
