<?php

namespace App\Support;

class PermissionLabels
{
    /**
     * Returns ['label' => string, 'description' => string|null]
     * Falls back to formatted permission key if not found.
     */
    public static function get(string $permission): array
    {
        $map = self::map();

        if (isset($map[$permission])) {
            return $map[$permission];
        }

        // Fallback: prettify the key
        $parts = explode('.', $permission);
        $label = implode(' › ', array_map(fn ($p) => ucfirst(str_replace('_', ' ', $p)), $parts));

        return ['label' => $label, 'description' => null];
    }

    /** @return array<string, array{label: string, description: string|null}> */
    private static function map(): array
    {
        return [
            // ── Attendance ────────────────────────────────────────────
            'attendance.adjustment.create' => ['label' => 'Buat Penyesuaian Absensi', 'description' => null],
            'attendance.document_signature.manage' => ['label' => 'Kelola Tanda Tangan Dokumen Absensi', 'description' => null],
            'attendance.holiday.manage' => ['label' => 'Kelola Hari Libur', 'description' => null],
            'attendance.leave.approve_hr_staff' => ['label' => 'Setujui Cuti', 'description' => 'Hak approve cuti tingkat HR/Staff'],
            'attendance.leave.review' => ['label' => 'Tinjau Pengajuan Cuti', 'description' => null],
            'attendance.policy.create' => ['label' => 'Buat Kebijakan Absensi', 'description' => null],
            'attendance.policy.edit' => ['label' => 'Edit Kebijakan Absensi', 'description' => null],
            'attendance.policy.index' => ['label' => 'Lihat Daftar Kebijakan Absensi', 'description' => null],
            'attendance.policy.manage' => ['label' => 'Kelola Kebijakan Absensi', 'description' => 'Create + edit + delete'],
            'attendance.recap.view' => ['label' => 'Lihat Rekap Absensi', 'description' => null],
            'attendance.rule.create' => ['label' => 'Buat Aturan Absensi', 'description' => null],
            'attendance.rule.edit' => ['label' => 'Edit Aturan Absensi', 'description' => null],
            'attendance.rule.index' => ['label' => 'Lihat Daftar Aturan Absensi', 'description' => null],
            'attendance.rule.manage' => ['label' => 'Kelola Aturan Absensi', 'description' => 'Create + edit + delete'],
            'attendance.schedule_rule.manage' => ['label' => 'Kelola Aturan Jadwal Kerja', 'description' => null],

            // ── Auth ──────────────────────────────────────────────────
            'auth.user.force_reset_password' => ['label' => 'Paksa Reset Password Pengguna', 'description' => null],

            // ── Bonus ─────────────────────────────────────────────────
            'bonus.kpi-rule.create' => ['label' => 'Buat Aturan Bonus KPI', 'description' => null],
            'bonus.kpi-rule.delete' => ['label' => 'Hapus Aturan Bonus KPI', 'description' => null],
            'bonus.kpi-rule.update' => ['label' => 'Edit Aturan Bonus KPI', 'description' => null],
            'bonus.kpi-rule.view' => ['label' => 'Lihat Aturan Bonus KPI', 'description' => null],
            'bonus.marketing-rule.create' => ['label' => 'Buat Aturan Bonus Marketing', 'description' => null],
            'bonus.marketing-rule.delete' => ['label' => 'Hapus Aturan Bonus Marketing', 'description' => null],
            'bonus.marketing-rule.update' => ['label' => 'Edit Aturan Bonus Marketing', 'description' => null],
            'bonus.marketing-rule.view' => ['label' => 'Lihat Aturan Bonus Marketing', 'description' => null],
            'bonus.rule.history.view' => ['label' => 'Lihat Riwayat Perubahan Aturan Bonus', 'description' => null],
            'bonus.special-rule.create' => ['label' => 'Buat Aturan Bonus Khusus', 'description' => null],
            'bonus.special-rule.delete' => ['label' => 'Hapus Aturan Bonus Khusus', 'description' => null],
            'bonus.special-rule.update' => ['label' => 'Edit Aturan Bonus Khusus', 'description' => null],
            'bonus.special-rule.view' => ['label' => 'Lihat Aturan Bonus Khusus', 'description' => null],

            // ── Class ─────────────────────────────────────────────────
            'class.assessment.record' => ['label' => 'Catat Penilaian Kelas', 'description' => null],
            'class.attendance.record' => ['label' => 'Catat Kehadiran Kelas', 'description' => null],
            'class.certificate.update' => ['label' => 'Update Sertifikat Kelas', 'description' => null],
            'class.graduate' => ['label' => 'Luluskan Peserta Kelas', 'description' => null],
            'class.manage' => ['label' => 'Kelola Kelas', 'description' => 'Create + edit + delete kelas'],
            'class.member.assign' => ['label' => 'Daftarkan Member ke Kelas', 'description' => null],
            'class.progress.update' => ['label' => 'Update Progress Kelas', 'description' => null],
            'class.session.update' => ['label' => 'Update Sesi Kelas', 'description' => null],
            'class.test.create' => ['label' => 'Buat Ujian Kelas', 'description' => null],
            'class.test.delete' => ['label' => 'Hapus Ujian Kelas', 'description' => null],
            'class.test.review_essay' => ['label' => 'Koreksi Soal Essay', 'description' => null],
            'class.test.score' => ['label' => 'Input Nilai Ujian', 'description' => null],
            'class.test.update' => ['label' => 'Edit Ujian Kelas', 'description' => null],

            // ── Contract ──────────────────────────────────────────────
            'contract.manage' => ['label' => 'Kelola Kontrak Kerja', 'description' => 'Create + perpanjang + ubah posisi'],

            // ── Curriculum ────────────────────────────────────────────
            'curriculum.curriculum.create' => ['label' => 'Buat Kurikulum', 'description' => null],
            'curriculum.curriculum.update' => ['label' => 'Edit Kurikulum', 'description' => null],
            'curriculum.interactive.manage' => ['label' => 'Kelola Konten Interaktif', 'description' => null],
            'curriculum.interactive.result.view' => ['label' => 'Lihat Hasil Konten Interaktif', 'description' => null],
            'curriculum.item.create' => ['label' => 'Buat Item Kurikulum', 'description' => null],
            'curriculum.item.update' => ['label' => 'Edit Item Kurikulum', 'description' => null],
            'curriculum.program.create' => ['label' => 'Buat Program', 'description' => null],
            'curriculum.program.update' => ['label' => 'Edit Program', 'description' => null],
            'curriculum.quota.create' => ['label' => 'Buat Kuota Program Cabang', 'description' => null],
            'curriculum.quota.update' => ['label' => 'Edit Kuota Program Cabang', 'description' => null],
            'curriculum.session.create' => ['label' => 'Buat Sesi Kurikulum', 'description' => null],
            'curriculum.session.update' => ['label' => 'Edit Sesi Kurikulum', 'description' => null],
            'curriculum.view' => ['label' => 'Lihat Kurikulum', 'description' => null],

            // ── Dashboard Widgets ─────────────────────────────────────
            'dashboard.widget.account_reconciliation.view' => ['label' => 'Widget: Rekonsiliasi Akun', 'description' => null],
            'dashboard.widget.active_users.view' => ['label' => 'Widget: Pengguna Aktif', 'description' => null],
            'dashboard.widget.alert_merah.view' => ['label' => 'Widget: Alert Merah', 'description' => null],
            'dashboard.widget.area_performance.view' => ['label' => 'Widget: Performa Area', 'description' => null],
            'dashboard.widget.attendance_compliance.view' => ['label' => 'Widget: Kepatuhan Absensi', 'description' => null],
            'dashboard.widget.attendance_summary.view' => ['label' => 'Widget: Ringkasan Absensi', 'description' => null],
            'dashboard.widget.branch_financial_performance.view' => ['label' => 'Widget: Keuangan Cabang', 'description' => null],
            'dashboard.widget.branch_performance_comparison.view' => ['label' => 'Widget: Perbandingan Performa Cabang', 'description' => null],
            'dashboard.widget.branch_performance.view' => ['label' => 'Widget: Performa Cabang', 'description' => null],
            'dashboard.widget.branch_rent_contracts.view' => ['label' => 'Widget: Kontrak Sewa Cabang', 'description' => null],
            'dashboard.widget.budget_vs_actual.view' => ['label' => 'Widget: Budget vs Aktual', 'description' => null],
            'dashboard.widget.cabang_performance.view' => ['label' => 'Widget: Performa Cabang (Alt)', 'description' => null],
            'dashboard.widget.candidate_screening.view' => ['label' => 'Widget: Screening Kandidat', 'description' => null],
            'dashboard.widget.cash_flow.view' => ['label' => 'Widget: Arus Kas', 'description' => null],
            'dashboard.widget.class_schedule_utilization.view' => ['label' => 'Widget: Utilisasi Jadwal Kelas', 'description' => null],
            'dashboard.widget.comparable.view' => ['label' => 'Widget: Perbandingan Performa', 'description' => null],
            'dashboard.widget.compliance_policies.view' => ['label' => 'Widget: Kepatuhan Kebijakan', 'description' => null],
            'dashboard.widget.contract_status.view' => ['label' => 'Widget: Status Kontrak', 'description' => null],
            'dashboard.widget.cost_analysis.view' => ['label' => 'Widget: Analisis Biaya', 'description' => null],
            'dashboard.widget.cost_control.view' => ['label' => 'Widget: Pengendalian Biaya', 'description' => null],
            'dashboard.widget.employee_development.view' => ['label' => 'Widget: Pengembangan Karyawan', 'description' => null],
            'dashboard.widget.employee_directory.view' => ['label' => 'Widget: Direktori Karyawan', 'description' => null],
            'dashboard.widget.expense_tracking.view' => ['label' => 'Widget: Pelacakan Pengeluaran', 'description' => null],
            'dashboard.widget.facility_status.view' => ['label' => 'Widget: Status Fasilitas', 'description' => null],
            'dashboard.widget.financial_forecasting.view' => ['label' => 'Widget: Proyeksi Keuangan', 'description' => null],
            'dashboard.widget.financial_hub.view' => ['label' => 'Widget: Hub Keuangan', 'description' => null],
            'dashboard.widget.financial_reports.view' => ['label' => 'Widget: Laporan Keuangan', 'description' => null],
            'dashboard.widget.hiring_metrics.view' => ['label' => 'Widget: Metrik Rekrutmen', 'description' => null],
            'dashboard.widget.interview_schedule.view' => ['label' => 'Widget: Jadwal Interview', 'description' => null],
            'dashboard.widget.inventory_status.view' => ['label' => 'Widget: Status Inventaris', 'description' => null],
            'dashboard.widget.invoice_bill_management.view' => ['label' => 'Widget: Manajemen Invoice & Tagihan', 'description' => null],
            'dashboard.widget.job_postings.view' => ['label' => 'Widget: Lowongan Kerja', 'description' => null],
            'dashboard.widget.lead_status.view' => ['label' => 'Widget: Status Lead', 'description' => null],
            'dashboard.widget.leave_attendance.view' => ['label' => 'Widget: Cuti & Absensi', 'description' => null],
            'dashboard.widget.marketing_kpi_snapshot.view' => ['label' => 'Widget: Snapshot KPI Marketing', 'description' => null],
            'dashboard.widget.marketing_progress.view' => ['label' => 'Widget: Progress Marketing', 'description' => null],
            'dashboard.widget.marketing_revenue.view' => ['label' => 'Widget: Pendapatan Marketing', 'description' => null],
            'dashboard.widget.marketing_team_performance.view' => ['label' => 'Widget: Performa Tim Marketing', 'description' => null],
            'dashboard.widget.member_acquisition.view' => ['label' => 'Widget: Akuisisi Member', 'description' => null],
            'dashboard.widget.member_growth.view' => ['label' => 'Widget: Pertumbuhan Member', 'description' => null],
            'dashboard.widget.my_assessment_tasks.view' => ['label' => 'Widget: Tugas Penilaian Saya', 'description' => null],
            'dashboard.widget.my_attendance_record.view' => ['label' => 'Widget: Rekap Absensi Saya', 'description' => null],
            'dashboard.widget.my_classes.view' => ['label' => 'Widget: Kelas Saya', 'description' => null],
            'dashboard.widget.my_commission_incentive.view' => ['label' => 'Widget: Komisi & Insentif Saya', 'description' => null],
            'dashboard.widget.my_kpi_achievement.view' => ['label' => 'Widget: Pencapaian KPI Saya', 'description' => null],
            'dashboard.widget.my_performance_history.view' => ['label' => 'Widget: Riwayat Performa Saya', 'description' => null],
            'dashboard.widget.my_performance_summary.view' => ['label' => 'Widget: Ringkasan Performa Saya', 'description' => null],
            'dashboard.widget.my_ranking.view' => ['label' => 'Widget: Ranking Saya', 'description' => null],
            'dashboard.widget.my_sales_performance.view' => ['label' => 'Widget: Performa Penjualan Saya', 'description' => null],
            'dashboard.widget.my_session_attendance.view' => ['label' => 'Widget: Kehadiran Sesi Saya', 'description' => null],
            'dashboard.widget.my_session_schedule.view' => ['label' => 'Widget: Jadwal Sesi Saya', 'description' => null],
            'dashboard.widget.my_socialization_activity.view' => ['label' => 'Widget: Aktivitas Sosialisasi Saya', 'description' => null],
            'dashboard.widget.my_student_progress.view' => ['label' => 'Widget: Progress Siswa Saya', 'description' => null],
            'dashboard.widget.my_target_status.view' => ['label' => 'Widget: Status Target Saya', 'description' => null],
            'dashboard.widget.onboarding_status.view' => ['label' => 'Widget: Status Onboarding', 'description' => null],
            'dashboard.widget.payment_status.view' => ['label' => 'Widget: Status Pembayaran', 'description' => null],
            'dashboard.widget.payroll_summary.view' => ['label' => 'Widget: Ringkasan Payroll', 'description' => null],
            'dashboard.widget.profit.view' => ['label' => 'Widget: Profit', 'description' => null],
            'dashboard.widget.program_financial_performance.view' => ['label' => 'Widget: Keuangan per Program', 'description' => null],
            'dashboard.widget.ranking_program.view' => ['label' => 'Widget: Ranking Program', 'description' => null],
            'dashboard.widget.recruitment_pipeline.view' => ['label' => 'Widget: Pipeline Rekrutmen', 'description' => null],
            'dashboard.widget.revenue.view' => ['label' => 'Widget: Pendapatan', 'description' => null],
            'dashboard.widget.sdm_headcount.view' => ['label' => 'Widget: Headcount SDM', 'description' => null],
            'dashboard.widget.socialization_effectiveness.view' => ['label' => 'Widget: Efektivitas Sosialisasi', 'description' => null],
            'dashboard.widget.socialization_schedule.view' => ['label' => 'Widget: Jadwal Sosialisasi', 'description' => null],
            'dashboard.widget.support_ticket.view' => ['label' => 'Widget: Tiket Support', 'description' => null],
            'dashboard.widget.system_audit_log.view' => ['label' => 'Widget: Log Audit Sistem', 'description' => null],

            // ── Employee ──────────────────────────────────────────────
            'employee.create' => ['label' => 'Tambah Karyawan', 'description' => null],
            'employee.delete' => ['label' => 'Hapus Karyawan', 'description' => null],
            'employee.termination_checklist.review' => ['label' => 'Tinjau Checklist Pemutusan Kerja', 'description' => null],
            'employee.update' => ['label' => 'Edit Data Karyawan', 'description' => null],
            'employee.view' => ['label' => 'Lihat Data Karyawan', 'description' => null],

            // ── Facility ──────────────────────────────────────────────
            'facility.inventory.create' => ['label' => 'Tambah Inventaris', 'description' => null],
            'facility.inventory.dispose' => ['label' => 'Hapus/Dispose Inventaris', 'description' => null],
            'facility.inventory.update' => ['label' => 'Edit Inventaris', 'description' => null],
            'facility.rent_contract.create' => ['label' => 'Buat Kontrak Sewa', 'description' => null],
            'facility.rent_contract.mark_paid' => ['label' => 'Tandai Kontrak Sewa Lunas', 'description' => null],
            'facility.rent_contract.update' => ['label' => 'Edit Kontrak Sewa', 'description' => null],
            'facility.ticket.assign' => ['label' => 'Assign Tiket Fasilitas', 'description' => null],
            'facility.ticket.create' => ['label' => 'Buat Tiket Fasilitas', 'description' => null],
            'facility.ticket.resolve' => ['label' => 'Selesaikan Tiket Fasilitas', 'description' => null],
            'facility.ticket.review' => ['label' => 'Tinjau Tiket Fasilitas', 'description' => null],
            'facility.ticket.update' => ['label' => 'Edit Tiket Fasilitas', 'description' => null],

            // ── Finance ───────────────────────────────────────────────
            'finance.budget_estimate.create' => ['label' => 'Buat Estimasi Budget', 'description' => null],
            'finance.budget_estimate.ops_review' => ['label' => 'Review Ops Estimasi Budget', 'description' => null],
            'finance.budget_estimate.send' => ['label' => 'Kirim Estimasi Budget', 'description' => null],
            'finance.budget_estimate.view' => ['label' => 'Lihat Estimasi Budget', 'description' => null],
            'finance.monthly_cost.create' => ['label' => 'Input Biaya Bulanan', 'description' => null],
            'finance.monthly_cost.delete' => ['label' => 'Hapus Biaya Bulanan', 'description' => null],
            'finance.monthly_cost.view' => ['label' => 'Lihat Biaya Bulanan', 'description' => null],
            'finance.period.lock' => ['label' => 'Kunci Periode Keuangan', 'description' => 'Mencegah perubahan data setelah periode ditutup'],
            'finance.reimbursement.create' => ['label' => 'Ajukan Reimbursement', 'description' => null],
            'finance.reimbursement.pay' => ['label' => 'Bayar Reimbursement', 'description' => null],
            'finance.reimbursement.review' => ['label' => 'Tinjau Reimbursement', 'description' => null],
            'finance.reimbursement.view' => ['label' => 'Lihat Reimbursement', 'description' => null],

            // ── KPI ───────────────────────────────────────────────────
            'kpi.document.create' => ['label' => 'Buat Dokumen KPI', 'description' => null],
            'kpi.document.delete' => ['label' => 'Hapus Dokumen KPI', 'description' => null],
            'kpi.evaluation.create' => ['label' => 'Buat Evaluasi KPI', 'description' => null],
            'kpi.evaluation.finalize' => ['label' => 'Finalisasi Evaluasi KPI', 'description' => 'Mengunci hasil evaluasi agar tidak bisa diubah'],
            'kpi.evaluation.update' => ['label' => 'Edit Evaluasi KPI', 'description' => null],
            'kpi.evaluation.view_all' => ['label' => 'Lihat Semua Evaluasi KPI', 'description' => 'Termasuk milik karyawan lain'],
            'kpi.evaluator_assignment.create' => ['label' => 'Assign Evaluator KPI', 'description' => null],
            'kpi.evaluator_assignment.update' => ['label' => 'Ganti Evaluator KPI', 'description' => null],
            'kpi.grade_rule.create' => ['label' => 'Buat Aturan Grade KPI', 'description' => null],
            'kpi.grade_rule.delete' => ['label' => 'Hapus Aturan Grade KPI', 'description' => null],
            'kpi.grade_rule.update' => ['label' => 'Edit Aturan Grade KPI', 'description' => null],
            'kpi.grade_rule.view' => ['label' => 'Lihat Aturan Grade KPI', 'description' => null],
            'kpi.template.create' => ['label' => 'Buat Template KPI', 'description' => null],
            'kpi.template.update' => ['label' => 'Edit Template KPI', 'description' => null],

            // ── Letter ────────────────────────────────────────────────
            'letter.generate.create' => ['label' => 'Buat Surat (Generate)', 'description' => null],
            'letter.generate.publish' => ['label' => 'Terbitkan Surat', 'description' => null],
            'letter.generate.update' => ['label' => 'Edit Surat (Generate)', 'description' => null],
            'letter.in.create' => ['label' => 'Buat Surat Masuk', 'description' => null],
            'letter.in.update' => ['label' => 'Edit Surat Masuk', 'description' => null],
            'letter.sop.create' => ['label' => 'Buat SOP', 'description' => null],
            'letter.sop.update' => ['label' => 'Edit SOP', 'description' => null],
            'letter.template.create' => ['label' => 'Buat Template Surat', 'description' => null],
            'letter.template.manage' => ['label' => 'Kelola Template Surat', 'description' => null],
            'letter.template.update' => ['label' => 'Edit Template Surat', 'description' => null],
            'letter.upload.create' => ['label' => 'Upload Surat', 'description' => null],
            'letter.upload.update' => ['label' => 'Edit Surat Upload', 'description' => null],

            // ── Marketing ─────────────────────────────────────────────
            'marketing.kpi.view' => ['label' => 'Lihat KPI Marketing', 'description' => null],
            'marketing.member_payment_statement.view' => ['label' => 'Lihat Laporan Pembayaran Member', 'description' => null],
            'marketing.monthly_revenue_data.view' => ['label' => 'Lihat Data Pendapatan Bulanan (Semua)', 'description' => null],
            'marketing.monthly_revenue_data.view_branch' => ['label' => 'Lihat Data Pendapatan Bulanan (Cabang)', 'description' => null],
            'marketing.prospective_member.create' => ['label' => 'Tambah Calon Member', 'description' => null],
            'marketing.prospective_member.update' => ['label' => 'Edit Calon Member', 'description' => null],
            'marketing.prospective.view_area' => ['label' => 'Lihat Calon Member (Area)', 'description' => null],
            'marketing.prospective.view_national' => ['label' => 'Lihat Calon Member (Nasional)', 'description' => null],
            'marketing.ranking_rule.manage' => ['label' => 'Kelola Aturan Ranking Marketing', 'description' => null],
            'marketing.socialization.assign' => ['label' => 'Assign Sosialisasi', 'description' => null],
            'marketing.socialization.create' => ['label' => 'Buat Sosialisasi', 'description' => null],
            'marketing.socialization.partner_fee.update' => ['label' => 'Edit Biaya Mitra Sosialisasi', 'description' => null],
            'marketing.socialization.update' => ['label' => 'Edit Sosialisasi', 'description' => null],
            'marketing.target.set' => ['label' => 'Set Target Marketing', 'description' => null],
            'marketing.view_own_performance' => ['label' => 'Lihat Performa Sendiri', 'description' => null],
            'marketing.wa_template.manage' => ['label' => 'Kelola Template WhatsApp', 'description' => null],

            // ── Member ────────────────────────────────────────────────
            'member.manage' => ['label' => 'Kelola Member', 'description' => 'Create + edit + nonaktifkan'],
            'member.payment.manage' => ['label' => 'Kelola Pembayaran Member', 'description' => null],
            'member.support.manage' => ['label' => 'Kelola Tiket Support Member', 'description' => null],
            'member.view' => ['label' => 'Lihat Data Member', 'description' => null],

            // ── Notification ──────────────────────────────────────────
            'notification.log.resend' => ['label' => 'Kirim Ulang Notifikasi', 'description' => null],
            'notification.log.view' => ['label' => 'Lihat Log Notifikasi', 'description' => null],
            'notification.send_manual' => ['label' => 'Kirim Notifikasi Manual', 'description' => null],
            'notification.template.create' => ['label' => 'Buat Template Notifikasi', 'description' => null],
            'notification.template.delete' => ['label' => 'Hapus Template Notifikasi', 'description' => null],
            'notification.template.update' => ['label' => 'Edit Template Notifikasi', 'description' => null],
            'notification.variable.create' => ['label' => 'Buat Variabel Notifikasi', 'description' => null],
            'notification.variable.delete' => ['label' => 'Hapus Variabel Notifikasi', 'description' => null],
            'notification.variable.update' => ['label' => 'Edit Variabel Notifikasi', 'description' => null],

            // ── Organization ──────────────────────────────────────────
            'organization.area.create' => ['label' => 'Buat Area', 'description' => null],
            'organization.area.delete' => ['label' => 'Hapus Area', 'description' => null],
            'organization.area.update' => ['label' => 'Edit Area', 'description' => null],
            'organization.branch_transfer.review' => ['label' => 'Tinjau Mutasi Cabang', 'description' => null],
            'organization.branch.assign_pic' => ['label' => 'Assign PIC Cabang', 'description' => null],
            'organization.branch.create' => ['label' => 'Buat Cabang', 'description' => null],
            'organization.branch.delete' => ['label' => 'Hapus Cabang', 'description' => null],
            'organization.branch.toggle_active' => ['label' => 'Aktifkan/Nonaktifkan Cabang', 'description' => null],
            'organization.branch.update' => ['label' => 'Edit Cabang', 'description' => null],
            'organization.institution.create' => ['label' => 'Buat Institusi', 'description' => null],
            'organization.institution.delete' => ['label' => 'Hapus Institusi', 'description' => null],
            'organization.institution.update' => ['label' => 'Edit Institusi', 'description' => null],
            'organization.province.create' => ['label' => 'Buat Provinsi', 'description' => null],
            'organization.province.delete' => ['label' => 'Hapus Provinsi', 'description' => null],
            'organization.province.update' => ['label' => 'Edit Provinsi', 'description' => null],
            'organization.region.create' => ['label' => 'Buat Region', 'description' => null],
            'organization.region.delete' => ['label' => 'Hapus Region', 'description' => null],
            'organization.region.update' => ['label' => 'Edit Region', 'description' => null],

            // ── Payroll ───────────────────────────────────────────────
            'payroll.component.create' => ['label' => 'Buat Komponen Gaji', 'description' => null],
            'payroll.component.delete' => ['label' => 'Hapus Komponen Gaji', 'description' => null],
            'payroll.component.update' => ['label' => 'Edit Komponen Gaji', 'description' => null],
            'payroll.employee_compensation.create' => ['label' => 'Input Kompensasi Karyawan', 'description' => null],
            'payroll.employee_compensation.delete' => ['label' => 'Hapus Kompensasi Karyawan', 'description' => null],
            'payroll.employee_compensation.update' => ['label' => 'Edit Kompensasi Karyawan', 'description' => null],
            'payroll.item.adjust' => ['label' => 'Sesuaikan Item Payroll', 'description' => 'Override item gaji sebelum dibayar'],
            'payroll.period.create' => ['label' => 'Buat Periode Payroll', 'description' => null],
            'payroll.period.delete' => ['label' => 'Hapus Periode Payroll', 'description' => null],
            'payroll.period.generate' => ['label' => 'Generate Payroll', 'description' => 'Hitung gaji untuk semua karyawan dalam periode'],
            'payroll.period.pay' => ['label' => 'Tandai Payroll Dibayar', 'description' => null],
            'payroll.period.revert' => ['label' => 'Batalkan Payroll', 'description' => 'Kembalikan ke status draft'],
            'payroll.period.update' => ['label' => 'Edit Periode Payroll', 'description' => null],
            'payroll.period.view' => ['label' => 'Lihat Periode Payroll', 'description' => null],
            'payroll.session_compensation_rule.create' => ['label' => 'Buat Aturan Kompensasi Sesi', 'description' => null],
            'payroll.session_compensation_rule.delete' => ['label' => 'Hapus Aturan Kompensasi Sesi', 'description' => null],
            'payroll.session_compensation_rule.update' => ['label' => 'Edit Aturan Kompensasi Sesi', 'description' => null],
            'payroll.slip.generate' => ['label' => 'Generate Slip Gaji', 'description' => null],

            // ── Position ──────────────────────────────────────────────
            'position.manage' => ['label' => 'Kelola Posisi Jabatan', 'description' => 'Create + edit + delete posisi'],
            'position.view' => ['label' => 'Lihat Posisi Jabatan', 'description' => null],

            // ── Program ───────────────────────────────────────────────
            'program.payment_plan.manage' => ['label' => 'Kelola Rencana Pembayaran Program', 'description' => null],

            // ── Recruitment ───────────────────────────────────────────
            'recruitment.interview.create' => ['label' => 'Buat Jadwal Interview', 'description' => null],
            'recruitment.interview.delete' => ['label' => 'Hapus Jadwal Interview', 'description' => null],
            'recruitment.interview.evaluate' => ['label' => 'Input Evaluasi Interview', 'description' => null],
            'recruitment.interview.update' => ['label' => 'Edit Jadwal Interview', 'description' => null],
            'recruitment.job_application.create' => ['label' => 'Buat Lamaran (Manual)', 'description' => null],
            'recruitment.job_application.review' => ['label' => 'Tinjau Lamaran Kerja', 'description' => 'Screening, advance, reject'],
            'recruitment.job_permintaan.approve' => ['label' => 'Setujui Permintaan Rekrutmen', 'description' => null],
            'recruitment.job_permintaan.create' => ['label' => 'Buat Permintaan Rekrutmen', 'description' => null],
            'recruitment.job_permintaan.review' => ['label' => 'Tinjau Permintaan Rekrutmen', 'description' => null],
            'recruitment.job_permintaan.update' => ['label' => 'Edit Permintaan Rekrutmen', 'description' => null],
            'recruitment.job_posting.create' => ['label' => 'Buat Lowongan Kerja', 'description' => null],
            'recruitment.job_posting.publish' => ['label' => 'Terbitkan Lowongan Kerja', 'description' => null],
            'recruitment.job_posting.update' => ['label' => 'Edit Lowongan Kerja', 'description' => null],
            'recruitment.offering_letter.create' => ['label' => 'Buat Surat Penawaran', 'description' => null],
            'recruitment.offering_letter.export' => ['label' => 'Export Surat Penawaran', 'description' => null],
            'recruitment.offering_letter.negotiate' => ['label' => 'Negosiasi Surat Penawaran', 'description' => null],
            'recruitment.offering_letter.update' => ['label' => 'Edit Surat Penawaran', 'description' => null],
            'recruitment.onboarding.create' => ['label' => 'Buat Onboarding Karyawan Baru', 'description' => null],
            'recruitment.onboarding.review' => ['label' => 'Tinjau Onboarding', 'description' => null],
            'recruitment.onboarding.update' => ['label' => 'Edit Onboarding', 'description' => null],
            'recruitment.psikotest.create' => ['label' => 'Buat Psikotest', 'description' => null],
            'recruitment.psikotest.update' => ['label' => 'Edit Psikotest', 'description' => null],

            // ── Strategic Alerts ──────────────────────────────────────
            'strategic_alerts.view' => ['label' => 'Lihat Alert Strategis', 'description' => null],

            // ── Survey ────────────────────────────────────────────────
            'survey.assignment.create' => ['label' => 'Assign Survey ke Karyawan', 'description' => null],
            'survey.form.create' => ['label' => 'Buat Form Survey', 'description' => null],
            'survey.form.delete' => ['label' => 'Hapus Form Survey', 'description' => null],
            'survey.form.update' => ['label' => 'Edit Form Survey', 'description' => null],
            'survey.form.view' => ['label' => 'Lihat Form Survey', 'description' => null],
            'survey.result.view' => ['label' => 'Lihat Hasil Survey', 'description' => null],

            // ── System ────────────────────────────────────────────────
            'system.api_key.create' => ['label' => 'Buat API Key', 'description' => null],
            'system.api_key.delete' => ['label' => 'Hapus API Key', 'description' => null],
            'system.api_key.update' => ['label' => 'Edit API Key', 'description' => null],
            'system.api_key.view' => ['label' => 'Lihat API Key', 'description' => null],
            'system.bot.manage' => ['label' => 'Kelola Bot Telegram', 'description' => null],
            'system.notification_routing.manage' => ['label' => 'Kelola Routing Notifikasi', 'description' => null],
            'system.permission.manage' => ['label' => 'Kelola Permission Sistem', 'description' => 'Sangat sensitif — hanya Super Admin'],
            'system.telegram_backup.delete' => ['label' => 'Hapus Backup Telegram', 'description' => null],
            'system.telegram_backup.retry' => ['label' => 'Coba Ulang Backup Telegram', 'description' => null],
            'system.telegram_backup.view' => ['label' => 'Lihat Backup Telegram', 'description' => null],
            'system.webhook.manage' => ['label' => 'Kelola Webhook', 'description' => null],

            // ── TOEFL ─────────────────────────────────────────────────
            'toefl.media.create' => ['label' => 'Upload Media TOEFL', 'description' => null],
            'toefl.passage.create' => ['label' => 'Buat Passage TOEFL', 'description' => null],
            'toefl.passage.update' => ['label' => 'Edit Passage TOEFL', 'description' => null],
            'toefl.question.create' => ['label' => 'Buat Soal TOEFL', 'description' => null],
            'toefl.question.update' => ['label' => 'Edit Soal TOEFL', 'description' => null],
            'toefl.test.create' => ['label' => 'Buat Tes TOEFL', 'description' => null],
            'toefl.test.publish' => ['label' => 'Terbitkan Tes TOEFL', 'description' => null],
            'toefl.test.update' => ['label' => 'Edit Tes TOEFL', 'description' => null],
        ];
    }
}
