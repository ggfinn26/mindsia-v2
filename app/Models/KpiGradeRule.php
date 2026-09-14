<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiGradeRule extends Model
{
    protected $fillable = [
        'grade',
        'minimum_score',
        'maximum_score',
        'description',
        'is_active',
    ];

    protected $casts = [
        'minimum_score' => 'decimal:2',
        'maximum_score' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function gradeFor(float $score): ?string
    {
        $rule = static::active()
            ->where('minimum_score', '<=', $score)
            ->where(fn ($q) => $q
                ->whereNull('maximum_score')
                ->orWhere('maximum_score', '>=', $score)
            )
            ->first();

        return $rule?->grade;
    }
}
