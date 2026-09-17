<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeavePaySetting extends Model
{
    protected $fillable = [
        'leave_type',
        'is_paid',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
    ];
}
