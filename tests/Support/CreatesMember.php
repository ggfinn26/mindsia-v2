<?php

namespace Tests\Support;

use App\Models\MemberAccount;
use App\Models\MemberData;

trait CreatesMember
{
    /**
     * Buat MemberData + MemberAccount secara atomik.
     *
     * @param  array<string, mixed>  $accountAttrs Override untuk MemberAccount
     * @param  array<string, mixed>  $dataAttrs    Override untuk MemberData
     */
    protected function createMember(array $accountAttrs = [], array $dataAttrs = []): MemberAccount
    {
        $email = $accountAttrs['email'] ?? 'test.member.' . uniqid() . '@mindsia.test';

        $memberData = MemberData::create(array_merge([
            'full_name' => 'Test Member',
            'email' => $email,
            'whatsapp_number' => '08' . rand(100000000, 999999999),
        ], $dataAttrs));

        return MemberAccount::create(array_merge([
            'members_data_id' => $memberData->id,
            'email' => $email,
            'password' => 'password',
            'email_verified_at' => now(),
            'is_active' => true,
        ], $accountAttrs));
    }
}
