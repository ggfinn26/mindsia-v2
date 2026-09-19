<?php

namespace App\Models;

use App\Enums\TestimonialType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LandingTestimonial extends Model
{
    protected $table = 'landing_testimonials';

    protected $fillable = [
        'type',
        'name',
        'quote',
        'program',
        'city',
        'telegram_file_id',
        'file_path',
        'member_review_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'type' => TestimonialType::class,
        'is_active' => 'boolean',
    ];

    public function scopeVisible(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    public function memberReview(): BelongsTo
    {
        return $this->belongsTo(MemberReview::class);
    }

    public function isImage(): bool
    {
        return $this->type === TestimonialType::Image;
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }

        return $this->telegram_file_id
            ? route('file.serve', $this->telegram_file_id)
            : null;
    }
}
