<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProspectiveMemberStatusHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'prospective_member_id',
        'previous_status',
        'new_status',
        'changed_by_employee_id',
        'change_reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function prospectiveMember(): BelongsTo
    {
        return $this->belongsTo(ProspectiveMember::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'changed_by_employee_id');
    }
}
