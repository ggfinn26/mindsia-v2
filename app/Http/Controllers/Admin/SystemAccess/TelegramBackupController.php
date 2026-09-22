<?php

namespace App\Http\Controllers\Admin\SystemAccess;

use App\Http\Controllers\Controller;
use App\Jobs\PushTelegramBackupJob;
use App\Models\TelegramFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TelegramBackupController extends Controller
{
    public function index(): View
    {
        $files = TelegramFile::with([])
            ->orderByRaw("FIELD(backup_status, 'failed', 'pending', 'uploaded')")
            ->orderByDesc('last_attempted_at')
            ->paginate(20);

        $failedCount = TelegramFile::where('backup_status', 'failed')->count();
        $pendingCount = TelegramFile::where('backup_status', 'pending')->count();

        return view('system-access.telegram-backup.index', compact('files', 'failedCount', 'pendingCount'));
    }

    public function retry(TelegramFile $telegramFile): RedirectResponse
    {
        if ($telegramFile->isUploaded()) {
            return redirect()->route('system.telegram-backup.index')->with('info', 'File sudah berhasil di-backup.');
        }

        $telegramFile->markPending();

        PushTelegramBackupJob::dispatch(
            $telegramFile->id,
            $telegramFile->storage_path ?? $telegramFile->file_path,
            $telegramFile->original_filename ?? 'file',
            caption: "Retry: {$telegramFile->original_filename}",
        );

        return redirect()->route('system.telegram-backup.index')->with('success', 'Backup dijadwalkan ulang.');
    }

    public function retryAll(): RedirectResponse
    {
        $failed = TelegramFile::where('backup_status', 'failed')->get();

        if ($failed->isEmpty()) {
            return redirect()->route('system.telegram-backup.index')->with('info', 'Tidak ada backup yang gagal.');
        }

        foreach ($failed as $file) {
            $file->markPending();

            PushTelegramBackupJob::dispatch(
                $file->id,
                $file->storage_path ?? $file->file_path,
                $file->original_filename ?? 'file',
                caption: "Retry all: {$file->original_filename}",
            );
        }

        return redirect()->route('system.telegram-backup.index')->with('success', "{$failed->count()} backup dijadwalkan ulang.");
    }

    public function destroy(TelegramFile $telegramFile): RedirectResponse
    {
        $telegramFile->delete();

        return redirect()->route('system.telegram-backup.index')->with('success', 'Record backup dihapus.');
    }
}
