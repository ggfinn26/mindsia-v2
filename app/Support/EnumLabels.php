<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Label Indonesia terpusat untuk nilai enum yang ditampilkan ke pengguna.
 *
 * Pakai di Blade: {{ \App\Support\EnumLabels::get('attendance_status', $log->status) }}
 * Nilai yang belum terdaftar jatuh ke humanize() - minimal tidak pernah bocor underscore.
 */
class EnumLabels
{
    /** @var array<string, array<string, string>> */
    public const MAP = [
        // Pembayaran (member, payroll, facility)
        'payment_status' => [
            'paid' => 'Lunas',
            'paid_full' => 'Lunas',
            'full_paid' => 'Lunas',
            'lunas' => 'Lunas',
            'partial' => 'Cicilan',
            'installment' => 'Cicilan',
            'installments' => 'Cicilan',
            'unpaid' => 'Belum Bayar',
            'pending' => 'Menunggu',
            'overdue' => 'Terlambat',
            'failed' => 'Gagal',
        ],

        // Kehadiran kerja / sesi
        'attendance_status' => [
            'absent' => 'Tidak Hadir',
            'checked_in' => 'Sudah Absen Masuk',
            'present' => 'Hadir',
            'sick' => 'Sakit',
            'permission' => 'Izin',
            'leave' => 'Cuti',
            'holiday' => 'Libur',
            'late' => 'Terlambat',
            'excused' => 'Berizin',
        ],

        // Jenis pengajuan cuti
        'leave_type' => [
            'permission' => 'Izin',
            'sick' => 'Sakit',
            'leave' => 'Cuti',
        ],

        // Status pengajuan (cuti, permintaan, approval)
        'request_status' => [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            'processed' => 'Diproses',
            'failed' => 'Gagal',
            'skipped' => 'Dilewati',
        ],

        // Jenis kepegawaian (recruitment)
        'employment_type' => [
            'full_time' => 'Penuh Waktu',
            'part_time' => 'Paruh Waktu',
            'contract' => 'Kontrak',
            'internship' => 'Magang',
            'freelance' => 'Lepas',
            'probation' => 'Masa Percobaan',
        ],
    ];

    public static function get(string $group, ?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return self::MAP[$group][$value] ?? self::humanize($value);
    }

    /** Fallback: buang underscore + Title Case supaya tidak pernah menampilkan snake_case mentah. */
    public static function humanize(string $value): string
    {
        return Str::title(str_replace('_', ' ', $value));
    }
}
