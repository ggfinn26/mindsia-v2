<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingTargetGlobalDefault extends Model
{
    protected $fillable = ['position_id', 'classes_target', 'omzet_target', 'created_by_employee_id'];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
