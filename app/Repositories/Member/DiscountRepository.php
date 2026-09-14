<?php

namespace App\Repositories\Member;

use App\Models\Discount;
use App\Models\DiscountProgram;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DiscountRepository
{
    public function all(): Collection
    {
        return Discount::withCount('registrations')->get();
    }

    public function findByCode(string $code): ?Discount
    {
        return Discount::where('discount_code', $code)->first();
    }

    public function store(array $data): Discount
    {
        return Discount::create($data);
    }

    public function update(Discount $discount, array $data): Discount
    {
        $discount->update($data);

        return $discount->fresh();
    }

    public function assignPrograms(Discount $discount, array $programIds): void
    {
        DB::transaction(function () use ($discount, $programIds) {
            DiscountProgram::where('discount_id', $discount->id)->delete();

            foreach ($programIds as $programId) {
                DiscountProgram::create(['discount_id' => $discount->id, 'program_id' => $programId]);
            }
        });
    }

    public function delete(Discount $discount): void
    {
        if ($discount->registrations()->exists()) {
            throw new \RuntimeException('Diskon sudah digunakan dalam registrasi, tidak bisa dihapus.');
        }

        $discount->delete();
    }
}
