<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. telegram_files — tambah kolom backup tracking
        Schema::table('telegram_files', function (Blueprint $table) {
            $table->string('storage_path', 500)->nullable()->after('file_path');
            $table->string('backup_status', 20)->default('pending')->after('storage_path');
            $table->unsignedTinyInteger('retry_count')->default(0)->after('backup_status');
            $table->text('last_error')->nullable()->after('retry_count');
            $table->timestamp('last_attempted_at')->nullable()->after('last_error');
            $table->index('backup_status');
        });

        // 2. Letter domain
        Schema::table('letter_templates', fn (Blueprint $table) => $table->string('storage_path', 500)->nullable()->after('telegram_file_id'));
        Schema::table('in_letters', fn (Blueprint $table) => $table->string('storage_path', 500)->nullable()->after('telegram_file_id'));
        Schema::table('out_letters_via_upload', fn (Blueprint $table) => $table->string('storage_path', 500)->nullable()->after('telegram_file_id'));
        Schema::table('out_letter_via_generate', fn (Blueprint $table) => $table->string('storage_path', 500)->nullable()->after('telegram_file_id'));
        Schema::table('sop_documents', fn (Blueprint $table) => $table->string('storage_path', 500)->nullable()->after('telegram_file_id'));

        // 3. Employee / HR domain
        Schema::table('employee_warning_letters', fn (Blueprint $table) => $table->string('storage_path', 500)->nullable()->after('telegram_file_id'));
        Schema::table('kpi_documents', fn (Blueprint $table) => $table->string('storage_path', 500)->nullable()->after('telegram_file_id'));
        Schema::table('facility_ticket_attachments', fn (Blueprint $table) => $table->string('storage_path', 500)->nullable()->after('telegram_file_id'));
        Schema::table('employee_payroll_slips', fn (Blueprint $table) => $table->string('storage_path', 500)->nullable()->after('telegram_file_id'));

        // 4. Attendance — selfie fields (variant naming)
        Schema::table('employee_work_attendance_logs', function (Blueprint $table) {
            $table->string('check_in_selfie_path', 500)->nullable()->after('check_in_selfie_telegram_file_id');
            $table->string('check_out_selfie_path', 500)->nullable()->after('check_out_selfie_telegram_file_id');
        });
        Schema::table('employee_session_attendance_logs', function (Blueprint $table) {
            $table->string('check_in_selfie_path', 500)->nullable()->after('check_in_selfie_telegram_file_id');
            $table->string('check_out_selfie_path', 500)->nullable()->after('check_out_selfie_telegram_file_id');
        });

        // 5. Leave requests — attachment (variant naming)
        Schema::table('employee_leave_requests', fn (Blueprint $table) => $table->string('attachment_path', 500)->nullable()->after('attachment_telegram_file_id'));

        // 6. Landing pages
        Schema::table('activity_photos', fn (Blueprint $table) => $table->string('storage_path', 500)->nullable()->after('telegram_file_id'));
        Schema::table('landing_leaders', fn (Blueprint $table) => $table->string('storage_path', 500)->nullable()->after('telegram_file_id'));

        // 7. Payroll payments — different field name (telegram_proof_id)
        Schema::table('employee_payroll_payments', fn (Blueprint $table) => $table->string('proof_path', 500)->nullable()->after('telegram_proof_id'));
    }

    public function down(): void
    {
        Schema::table('telegram_files', function (Blueprint $table) {
            $table->dropIndex(['backup_status']);
            $table->dropColumn(['storage_path', 'backup_status', 'retry_count', 'last_error', 'last_attempted_at']);
        });

        Schema::table('letter_templates', fn (Blueprint $table) => $table->dropColumn('storage_path'));
        Schema::table('in_letters', fn (Blueprint $table) => $table->dropColumn('storage_path'));
        Schema::table('out_letters_via_upload', fn (Blueprint $table) => $table->dropColumn('storage_path'));
        Schema::table('out_letter_via_generate', fn (Blueprint $table) => $table->dropColumn('storage_path'));
        Schema::table('sop_documents', fn (Blueprint $table) => $table->dropColumn('storage_path'));
        Schema::table('employee_warning_letters', fn (Blueprint $table) => $table->dropColumn('storage_path'));
        Schema::table('kpi_documents', fn (Blueprint $table) => $table->dropColumn('storage_path'));
        Schema::table('facility_ticket_attachments', fn (Blueprint $table) => $table->dropColumn('storage_path'));
        Schema::table('employee_payroll_slips', fn (Blueprint $table) => $table->dropColumn('storage_path'));

        Schema::table('employee_work_attendance_logs', function (Blueprint $table) {
            $table->dropColumn(['check_in_selfie_path', 'check_out_selfie_path']);
        });
        Schema::table('employee_session_attendance_logs', function (Blueprint $table) {
            $table->dropColumn(['check_in_selfie_path', 'check_out_selfie_path']);
        });

        Schema::table('employee_leave_requests', fn (Blueprint $table) => $table->dropColumn('attachment_path'));
        Schema::table('activity_photos', fn (Blueprint $table) => $table->dropColumn('storage_path'));
        Schema::table('landing_leaders', fn (Blueprint $table) => $table->dropColumn('storage_path'));
        Schema::table('employee_payroll_payments', fn (Blueprint $table) => $table->dropColumn('proof_path'));
    }
};
