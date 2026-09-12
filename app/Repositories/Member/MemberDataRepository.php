<?php

namespace App\Repositories\Member;

use App\Models\MemberData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MemberDataRepository
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return MemberData::with(['institution', 'program'])
            ->when($filters['institution_id'] ?? null, fn ($q, $v) => $q->where('institution_id', $v))
            ->when($filters['program_id'] ?? null, fn ($q, $v) => $q->where('program_id', $v))
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('full_name', 'like', "%{$v}%")
                    ->orWhere('email', 'like', "%{$v}%")
                    ->orWhere('whatsapp_number', 'like', "%{$v}%");
            }))
            ->orderBy('full_name')
            ->paginate(25);
    }

    public function findWithDetails(int $id): MemberData
    {
        return MemberData::with(['institution', 'program', 'referredBy', 'account', 'registrations.payments'])
            ->findOrFail($id);
    }

    public function create(array $data): MemberData
    {
        return MemberData::create($data);
    }

    public function update(MemberData $member, array $data): MemberData
    {
        $member->update($data);

        return $member->fresh();
    }

    public function delete(MemberData $member): void
    {
        if ($member->registrations()->exists()) {
            throw new \RuntimeException('Member sudah memiliki registrasi, tidak bisa dihapus.');
        }

        $member->delete();
    }
}
