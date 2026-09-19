<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ActivityPhoto extends Model
{
    protected $fillable = [
        'section',
        'title',
        'caption',
        'telegram_file_id',
        'file_path',
        'storage_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeVisible(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeForSection(Builder $query, string $section): void
    {
        $query->where('section', $section)->where('is_active', true)->orderBy('sort_order');
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->storage_path) {
            return Storage::url($this->storage_path);
        }

        if ($this->telegram_file_id) {
            return route('file.serve', $this->telegram_file_id);
        }

        return $this->file_path ? Storage::url($this->file_path) : '';
    }

    /** Alias so welcome.blade.php $gallery->image keeps working */
    public function getImageAttribute(): string
    {
        return $this->photo_url;
    }
}
