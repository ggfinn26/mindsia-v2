<?php

namespace Database\Factories;

use App\Models\TelegramFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TelegramFile>
 */
class TelegramFileFactory extends Factory
{
    protected $model = TelegramFile::class;

    public function definition(): array
    {
        return [
            'telegram_file_id' => 'pending_'.uniqid(),
            'original_filename' => fake()->word().'.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(1024, 5242880),
            'file_path' => 'documents/'.fake()->word().'.pdf',
            'storage_path' => 'documents/'.fake()->word().'.pdf',
            'entity_type' => fake()->randomElement(['document', 'photo', 'payroll_slip', 'warning_letter', 'kpi_document']),
            'entity_id' => null,
            'stored_at' => 'group_storage',
            'backup_status' => 'pending',
            'retry_count' => 0,
            'last_error' => null,
            'last_attempted_at' => null,
        ];
    }

    public function uploaded(): static
    {
        return $this->state(fn (array $attributes) => [
            'telegram_file_id' => 'AgAC'.fake()->regexify('[A-Za-z0-9_-]{80}'),
            'backup_status' => 'uploaded',
            'last_attempted_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'backup_status' => 'failed',
            'retry_count' => fake()->numberBetween(1, 3),
            'last_error' => 'Telegram API error: 429 Too Many Requests',
            'last_attempted_at' => now()->subMinutes(5),
        ]);
    }
}
