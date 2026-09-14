<?php

namespace App\Repositories\Toefl;

use App\Models\ToeflTest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class ToeflTestRepository
{
    public function list(): LengthAwarePaginator
    {
        return ToeflTest::with('createdBy')
            ->latest()
            ->paginate(20);
    }

    public function create(array $data): ToeflTest
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        return ToeflTest::create($data);
    }

    public function update(ToeflTest $test, array $data): void
    {
        abort_unless($test->status === ToeflTest::STATUS_DRAFT, 422, 'Hanya test draft yang bisa diedit.');

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $test->update($data);
    }

    public function publish(ToeflTest $test, int $publishedByEmployeeId): void
    {
        abort_unless($test->status === ToeflTest::STATUS_DRAFT, 422, 'Test sudah published.');
        abort_unless(
            $test->questions()->where('is_active', true)->exists(),
            422,
            'Test harus memiliki minimal satu soal aktif.'
        );

        $test->update([
            'status' => ToeflTest::STATUS_PUBLISHED,
            'published_by_employee_id' => $publishedByEmployeeId,
            'published_at' => now(),
        ]);
    }

    public function verifyPassword(ToeflTest $test, ?string $password): bool
    {
        if ($test->password === null) {
            return true;
        }

        return $password !== null && Hash::check($password, $test->password);
    }
}
