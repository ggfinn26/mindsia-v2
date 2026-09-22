<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramFile extends Model
{
    use HasFactory;

    protected $table = 'telegram_files';

    protected $fillable = [
        'telegram_file_id',
        'original_filename',
        'mime_type',
        'file_size',
        'file_path',
        'storage_path',
        'entity_type',
        'entity_id',
        'stored_at',
        'backup_status',
        'retry_count',
        'last_error',
        'last_attempted_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'retry_count' => 'integer',
        'last_attempted_at' => 'datetime',
    ];

    public function isPending(): bool
    {
        return $this->backup_status === 'pending';
    }

    public function isUploaded(): bool
    {
        return $this->backup_status === 'uploaded';
    }

    public function isFailed(): bool
    {
        return $this->backup_status === 'failed';
    }

    public function markUploaded(): void
    {
        $this->update(['backup_status' => 'uploaded', 'last_attempted_at' => now()]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'backup_status' => 'failed',
            'retry_count' => $this->retry_count + 1,
            'last_error' => $error,
            'last_attempted_at' => now(),
        ]);
    }

    public function markPending(): void
    {
        $this->update(['backup_status' => 'pending', 'last_error' => null]);
    }
}
