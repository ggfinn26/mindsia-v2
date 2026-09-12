<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosting extends Model
{
    protected $table = 'job_postings';

    protected $fillable = [
        'job_permintaan_id',
        'branch_id',
        'position_id',
        'title',
        'job_description',
        'job_responsibilities',
        'job_requirements_text',
        'status',
        'publish_date',
        'closing_date',
        'created_by_employee_id',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'closing_date' => 'date',
    ];

    // status: draft | published | closed
    const STATUS_DRAFT = 'draft';

    const STATUS_PUBLISHED = 'published';

    const STATUS_CLOSED = 'closed';

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(JobPermintaan::class, 'job_permintaan_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}
