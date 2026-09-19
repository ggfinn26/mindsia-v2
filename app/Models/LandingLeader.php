<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LandingLeader extends Model
{
    protected $table = 'landing_leaders';

    protected $fillable = [
        'name',
        'title',
        'telegram_file_id',
        'storage_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeVisible(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->storage_path) {
            return Storage::url($this->storage_path);
        }

        if (! $this->telegram_file_id) {
            return null;
        }

        if (str_starts_with($this->telegram_file_id, 'http')) {
            return $this->telegram_file_id;
        }

        return route('file.serve', $this->telegram_file_id);
    }
}
