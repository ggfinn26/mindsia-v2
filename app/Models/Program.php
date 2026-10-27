<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_name',
        'slug',
        'program_code',
        'program_price',
        'admin_fee',
        'admin_fee_mode',
        'admin_fee_timing',
        'program_description',
        'tagline',
        'badge',
        'target_level',
        'duration',
        'facilities',
        'benefits',
        'schedule',
        'faqs',
        'gallery',
        'image_path',
        'receipt_template_path',
        'is_active',
    ];

    protected $casts = [
        'program_price' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'facilities' => 'array',
        'benefits' => 'array',
        'schedule' => 'array',
        'faqs' => 'array',
        'gallery' => 'array',
    ];

    /**
     * Resolve stored gallery paths to public URLs for display.
     *
     * @return list<string>
     */
    public function getGalleryUrlsAttribute(): array
    {
        return collect($this->gallery ?? [])
            ->map(fn ($path) => Storage::url($path))
            ->all();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (Program $program) {
            if (empty($program->slug) && ! empty($program->program_name)) {
                $program->slug = Str::slug($program->program_name);
            }
        });
    }

    // Landing page accessors — map DB columns to view-expected properties
    public function getCodeAttribute(): string
    {
        return $this->program_code;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image_path ? Storage::url($this->image_path) : 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=800&auto=format&fit=crop';
    }

    /**
     * Short plain-text teaser for cards. Strips the rich-text formatting stored
     * in program_description so markup never leaks into card UI.
     */
    public function getTaglineAttribute(): ?string
    {
        if (! empty($this->attributes['tagline'])) {
            return $this->attributes['tagline'];
        }

        $withSpaces = preg_replace('/<[^>]+>/', ' ', (string) $this->program_description);
        $plain = trim(preg_replace('/\s+/', ' ', html_entity_decode($withSpaces)));

        return $plain === '' ? null : Str::limit($plain, 160);
    }

    public function getIsFeaturedAttribute(): bool
    {
        return false;
    }

    public function getLevelLabelAttribute(): ?string
    {
        return null;
    }

    public function curriculum(): HasOne
    {
        return $this->hasOne(Curriculum::class);
    }

    public function branchQuotas(): HasMany
    {
        return $this->hasMany(BranchProgramQuota::class);
    }

    public function paymentPlans(): HasMany
    {
        return $this->hasMany(ProgramPaymentPlan::class);
    }

    public function activePaymentPlans(): HasMany
    {
        return $this->hasMany(ProgramPaymentPlan::class)->where('is_active', true);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(MemberRegistration::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(ClassRoom::class);
    }
}
