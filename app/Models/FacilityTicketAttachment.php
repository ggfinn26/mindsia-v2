<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityTicketAttachment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'facility_ticket_id',
        'telegram_file_id',
        'storage_path',
        'original_name',
        'mime_type',
        'uploaded_by_employee_id',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(FacilityTicket::class, 'facility_ticket_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by_employee_id');
    }
}
