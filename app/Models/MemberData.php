<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MemberData extends Model
{
    use HasFactory;

    protected $table = 'members_data';

    protected $fillable = [
        'full_name',
        'gender',
        'birthdate',
        'whatsapp_number',
        'email',
        'instagram',
        'father_name',
        'mother_name',
        'father_occupation',
        'mother_occupation',
        'father_whatsapp',
        'mother_whatsapp',
        'address',
        'institution_id',
        'program_id',
        'referred_by_employee_id',
        'activation_status',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'referred_by_employee_id');
    }

    public function account(): HasOne
    {
        return $this->hasOne(MemberAccount::class, 'members_data_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(MemberRegistration::class, 'members_data_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(MemberSupportTicket::class, 'member_id');
    }

    public function npsResponses(): HasMany
    {
        return $this->hasMany(MemberNpsResponse::class, 'member_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(MemberNotification::class, 'member_id');
    }
}
