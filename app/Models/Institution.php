<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenjang_institution',
        'institution_name',
        'regions_id',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'regions_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(MemberData::class, 'institution_id');
    }
}
