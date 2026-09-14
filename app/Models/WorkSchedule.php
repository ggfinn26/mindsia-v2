<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $fillable = ['branch_id', 'role_id', 'start_time', 'end_time', 'late_tolerance_minutes'];
}
