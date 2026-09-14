<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicketAssignmentRule extends Model
{
    protected $fillable = [
        'category',
        'branch_id',
        'position_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
