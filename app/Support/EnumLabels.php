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

        // Reimbursement (nilai uppercase)
        'reimbursement_status' => [
            'DRAFT' => 'Draft',
            'PENDING_REVIEW' => 'Menunggu Review',
            'APPROVED' => 'Disetujui',
            'REJECTED' => 'Ditolak',
            'PAID' => 'Dibayar',
        ],

        // Estimasi anggaran cabang
        'budget_status' => [
            'draft' => 'Draft',
            'ops_review' => 'Review Operasional',
            'finance_review' => 'Review Keuangan',
            'accepted' => 'Diterima',
            'sent' => 'Terkirim',
            'inventory_draft' => 'Draft Inventaris',
        ],

        // Kategori biaya operasional cabang
        'cost_category' => [
            'sewa' => 'Sewa',
            'listrik' => 'Listrik',
            'air' => 'Air',
            'internet' => 'Internet',
            'gaji_non_employee' => 'Gaji Non-Karyawan',
            'peralatan' => 'Peralatan',
            'kebersihan' => 'Kebersihan',
            'keamanan' => 'Keamanan',
            'lainnya' => 'Lainnya',
        ],

        // Kategori item reimbursement
        'reimbursement_category' => [
            'transport' => 'Transport',
            'makan' => 'Makan',
            'akomodasi' => 'Akomodasi',
            'perlengkapan' => 'Perlengkapan',
            'lainnya' => 'Lainnya',
        ],

        // Tiket dukungan
        'ticket_category' => [
            'complaint' => 'Keluhan',
            'suggestion' => 'Saran',
            'question' => 'Pertanyaan',
        ],

        // Prioritas (tiket)
        'priority' => [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'urgent' => 'Mendesak',
        ],

        // Rekrutmen - jenis permintaan
        'request_type' => [
            'new_position' => 'Posisi Baru',
            'replacement' => 'Penggantian',
        ],

        // Rekrutmen - sumber lamaran
        'application_source' => [
            'job_posting' => 'Lowongan',
            'referral' => 'Referral',
            'walk_in' => 'Walk-in',
            'archive' => 'Arsip',
        ],

        // Rekrutmen - tahap/status lamaran
        'recruitment_stage' => [
            'applied' => 'Melamar',
            'screening' => 'Seleksi Berkas',
            'interview' => 'Wawancara',
            'interview_scheduled' => 'Wawancara Dijadwalkan',
            'evaluated' => 'Dievaluasi',
            'offering' => 'Penawaran',
            'hired' => 'Diterima',
            'rejected' => 'Ditolak',
        ],

        // Rekrutmen - status permintaan karyawan
        'job_request_status' => [
            'draft' => 'Draft',
            'pending_hr_review' => 'Menunggu Review HR',
            'pending_ops_approval' => 'Menunggu Persetujuan Ops',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'fulfilled' => 'Terpenuhi',
        ],

        // Fasilitas - status tiket
        'facility_ticket_status' => [
            'open' => 'Terbuka',
            'verified' => 'Terverifikasi',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
            'pending_review' => 'Menunggu Review',
            'approved' => 'Disetujui',
        ],

        // Kanal notifikasi / template
        'notification_channel' => [
            'email' => 'Email',
            'telegram' => 'Telegram',
            'whatsapp' => 'WhatsApp',
            'in-app' => 'Dalam Aplikasi',
            'in_app' => 'Dalam Aplikasi',
            'sms' => 'SMS',
        ],

        // Status pengiriman notifikasi
        'notification_status' => [
            'pending' => 'Menunggu',
            'sent' => 'Terkirim',
            'failed' => 'Gagal',
            'read' => 'Dibaca',
            'unread' => 'Belum Dibaca',
        ],

        // Jenis integrasi (bot, api key)
        'integration_type' => [
            'telegram' => 'Telegram',
            'whatsapp' => 'WhatsApp',
            'email' => 'Email',
            'convertapi' => 'ConvertAPI',
        ],

        // Status backup Telegram
        'backup_status' => [
            'ok' => 'Berhasil',
            'synced' => 'Tersinkron',
            'uploaded' => 'Terunggah',
            'pending' => 'Menunggu',
            'failed' => 'Gagal',
            'unknown' => 'Tidak Diketahui',
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
