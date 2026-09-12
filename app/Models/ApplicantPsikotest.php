<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantPsikotest extends Model
{
    protected $table = 'applicant_psikotests';

    protected $fillable = [
        'job_application_id',
        'psikotest_link',
        'psikotest_invited_at',
        'psikotest_date',
        'psikotest_score',
        'psikotest_notes',
    ];

    protected $casts = [
        'psikotest_invited_at' => 'datetime',
        'psikotest_date' => 'date',
        'psikotest_score' => 'integer',
    ];

    // psikotest_link: link eksternal yang dikirim HR ke pelamar
    // score diisi manual oleh HR setelah pelamar selesai

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }
}
