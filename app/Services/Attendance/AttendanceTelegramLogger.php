<?php

namespace App\Services\Attendance;

use App\Jobs\SendAttendanceTelegramLogJob;
use App\Models\Employee;
use App\Repositories\Notification\NotificationLogRepository;
use App\Repositories\Notification\NotificationTemplateRepository;
use App\Services\Notification\NotificationTemplateRendererService;
use Illuminate\Support\Facades\Log;

/**
 * Setiap absen masuk/keluar (kerja dan sesi) dikirim ke satu grup Telegram pusat lewat antrian.
 * Kegagalan apa pun di sini tidak boleh menggagalkan absensi; pesan yang gagal tetap ada di Log Notifikasi dan bisa dikirim ulang.
 */
class AttendanceTelegramLogger
{
    public const KEY_CHECK_IN = 'attendance.log.check_in';

    public const KEY_CHECK_OUT = 'attendance.log.check_out';

    private const DEFAULT_BODY = [
        self::KEY_CHECK_IN => "{{nama_pegawai}} telah melakukan absen {{jenis_absen}} pada jam {{jam}} dengan status {{status}}.\nStatus yang akan dikerjakan: {{catatan}}",
        self::KEY_CHECK_OUT => "{{nama_pegawai}} telah melakukan absen {{jenis_absen}} pada jam {{jam}} dengan status {{status}}.\nStatus yang sudah dikerjakan hari ini: {{catatan}}",
    ];

    public function __construct(
        private readonly NotificationTemplateRepository $templates,
        private readonly NotificationLogRepository $logs,
        private readonly NotificationTemplateRendererService $renderer,
    ) {}

    /**
     * @param  'masuk'|'keluar'  $arah
     * @param  'telat'|'tepat waktu'|'kecepatan'  $status
     * @param  string|null  $photoPath  path foto selfie di storage lokal
     * @param  string  $kegiatan  '' untuk absen kerja, 'sesi ' untuk absen sesi
     */
    public function send(Employee $employee, string $arah, string $status, ?string $notes, ?string $photoPath, string $kegiatan = ''): void
    {
        try {
            $chatId = config('services.telegram.attendance_chat_id');
            if (! $chatId) {
                return;
            }

            $key = $arah === 'masuk' ? self::KEY_CHECK_IN : self::KEY_CHECK_OUT;
            $template = $this->templates->findByKey($key);
            $payload = [
                'nama_pegawai' => $employee->full_name,
                'jenis_absen' => $kegiatan.$arah,
                'jam' => now()->format('H:i'),
                'status' => $status,
                'catatan' => trim((string) $notes) !== '' ? trim((string) $notes) : '-',
            ];

            $body = $template ? $this->renderer->render($template, $payload) : $this->renderDefault($key, $payload);

            $log = $this->logs->create([
                'notification_template_id' => $template?->id,
                'employee_id' => $employee->id,
                'member_id' => null,
                'notification_type' => 'telegram',
                'channel' => (string) $chatId,
                'payload' => ['kind' => 'attendance_log', 'body' => $body, 'photo_path' => $photoPath] + $payload,
                'notification_status' => 'pending',
                'attempts' => 0,
            ]);

            dispatch(new SendAttendanceTelegramLogJob($log));
        } catch (\Throwable $e) {
            report($e);
            Log::warning('Log absen ke Telegram gagal dibuat: '.$e->getMessage());
        }
    }

    /** @param array<string, string> $payload */
    private function renderDefault(string $key, array $payload): string
    {
        $body = self::DEFAULT_BODY[$key];
        foreach ($payload as $name => $value) {
            $body = str_replace('{{'.$name.'}}', $value, $body);
        }

        return $body;
    }
}
