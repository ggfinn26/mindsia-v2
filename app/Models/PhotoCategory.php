<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhotoCategory extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function photos(): HasMany
    {
        return $this->hasMany(ActivityPhoto::class, 'category_id');
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }
}
