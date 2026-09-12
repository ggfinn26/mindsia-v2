<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Auth: applicant_accounts (missing from auth domain migration)
        Schema::create('applicant_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        // Applicant profile data (FK to applicant_accounts)
        Schema::create('applicants_master_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_account_id')->nullable()->constrained('applicant_accounts')->onDelete('set null');
            $table->string('full_name');
            $table->string('email', 255)->nullable();
            $table->string('whatsapp_number', 50);
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('cv_path', 500)->nullable();
            $table->string('photo_path', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('applicants_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants_master_data')->onDelete('cascade');
            $table->string('institution_name');
            $table->enum('education_level', ['sd', 'smp', 'sma', 'd1', 'd2', 'd3', 'd4', 's1', 's2', 's3']);
            $table->string('major', 150);
            $table->decimal('gpa', 3, 2);
            $table->date('start_date');
            $table->date('graduation_date')->nullable();
            $table->timestamps();
        });

        Schema::create('applicants_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants_master_data')->onDelete('cascade');
            $table->string('course_name', 200);
            $table->string('issuer_name', 200);
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('certificate_path', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('applicants_work_experience', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants_master_data')->onDelete('cascade');
            $table->string('company_name', 200);
            $table->string('position', 200);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('reason_for_leaving')->nullable();
            $table->timestamps();
        });

        // Job Permintaan (job requisition from branch)
        Schema::create('job_permintaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('requested_by_employee_id')->constrained('employees')->onDelete('restrict');
            $table->enum('status', ['draft', 'pending_hr_review', 'pending_ops_approval', 'approved', 'rejected', 'fulfilled'])->default('draft');
            $table->timestamps();
        });

        Schema::create('job_permintaan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_permintaan_id')->unique()->constrained('job_permintaan')->onDelete('cascade');
            $table->foreignId('position_id')->constrained('positions')->onDelete('restrict');
            $table->string('employment_type', 50);
            $table->enum('request_type', ['new_position', 'replacement']);
            $table->unsignedInteger('headcount')->default(1);
            $table->unsignedInteger('current_headcount')->nullable();
            $table->text('job_description')->nullable();
            $table->date('target_start_date')->nullable();
            $table->timestamps();
        });

        Schema::create('job_permintaan_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_permintaan_id')->constrained('job_permintaan')->onDelete('cascade');
            $table->enum('approval_step', ['hrd_review', 'ops_approval']);
            $table->enum('decision', ['approved', 'rejected']);
            $table->foreignId('acted_by_employee_id')->constrained('employees')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamp('acted_at');
        });

        Schema::create('job_requirements_auto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_permintaan_id')->unique()->constrained('job_permintaan')->onDelete('cascade');
            $table->unsignedSmallInteger('minimum_age');
            $table->unsignedSmallInteger('maximum_age');
            $table->unsignedSmallInteger('minimum_year_experience');
            $table->string('minimum_education_level', 50);
            $table->timestamps();
        });

        Schema::create('job_requirements_manual', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_permintaan_id')->constrained('job_permintaan')->onDelete('cascade');
            $table->string('criteria_name', 100);
            $table->text('criteria_desc');
            $table->timestamps();

            $table->unique(['job_permintaan_id', 'criteria_name']);
        });

        // Job Postings (published job ads)
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_permintaan_id')->constrained('job_permintaan')->onDelete('restrict');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('position_id')->constrained('positions')->onDelete('restrict');
            $table->string('title');
            $table->text('job_description');
            $table->text('job_responsibilities');
            $table->text('job_requirements_text');
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->date('publish_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->foreignId('created_by_employee_id')->constrained('employees')->onDelete('restrict');
            $table->timestamps();
        });

        // Job Applications
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants_master_data')->onDelete('restrict');
            $table->foreignId('job_posting_id')->constrained('job_postings')->onDelete('restrict');
            $table->enum('application_source', ['job_posting', 'referral', 'walk_in', 'archive']);
            $table->enum('status', ['applied', 'screening', 'interview', 'offering', 'hired', 'rejected'])->default('applied');
            $table->timestamp('applied_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['applicant_id', 'job_posting_id']);
        });

        // Recruitment stage log (append-only)
        Schema::create('recruitment_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->onDelete('cascade');
            $table->enum('stage', ['applied', 'screening', 'interview_scheduled', 'evaluated', 'offering', 'hired', 'rejected']);
            $table->enum('result', ['waiting', 'passed', 'rejected']);
            $table->boolean('is_automatic')->default(false);
            $table->text('reason')->nullable();
            $table->foreignId('processed_by_employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('created_at');
        });

        // Psikotest
        Schema::create('applicant_psikotests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->unique()->constrained('job_applications')->onDelete('cascade');
            $table->string('psikotest_link', 500)->nullable();
            $table->timestamp('psikotest_invited_at')->nullable();
            $table->date('psikotest_date')->nullable();
            $table->unsignedSmallInteger('psikotest_score')->nullable();
            $table->text('psikotest_notes')->nullable();
            $table->timestamps();
        });

        // Interview scheduling
        Schema::create('applicant_interview_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->onDelete('cascade');
            $table->enum('interview_type', ['online', 'offline']);
            $table->dateTime('scheduled_at');
            $table->string('meeting_link', 500)->nullable();
            $table->string('location', 255)->nullable();
            $table->foreignId('interviewer_employee_id')->constrained('employees')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->foreignId('created_by_employee_id')->constrained('employees')->onDelete('restrict');
            $table->timestamps();
        });

        // Interview evaluation (1:1 per interview schedule)
        Schema::create('applicant_interview_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_schedule_id')->unique()->constrained('applicant_interview_schedules')->onDelete('cascade');
            $table->foreignId('job_application_id')->unique()->constrained('job_applications')->onDelete('cascade');
            $table->foreignId('evaluated_by_employee_id')->constrained('employees')->onDelete('restrict');
            $table->unsignedTinyInteger('score_education');
            $table->unsignedTinyInteger('score_experience');
            $table->unsignedTinyInteger('score_personality');
            $table->unsignedTinyInteger('score_communication');
            $table->unsignedTinyInteger('score_problem_solving');
            $table->unsignedTinyInteger('total_score');
            $table->enum('decision', ['rejected', 'reserve', 'accepted']);
            $table->decimal('current_salary', 15, 2)->nullable();
            $table->decimal('desired_salary', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Offering Letter
        Schema::create('offering_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->unique()->constrained('job_applications')->onDelete('cascade');
            $table->decimal('offered_salary', 15, 2);
            $table->decimal('agreed_salary', 15, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->enum('status', ['sent', 'negotiating', 'accepted', 'declined'])->default('sent');
            $table->enum('meeting_type', ['online', 'offline'])->nullable();
            $table->dateTime('meeting_at')->nullable();
            $table->string('meeting_link', 500)->nullable();
            $table->string('meeting_location', 255)->nullable();
            $table->timestamp('meeting_notified_at')->nullable();
            $table->string('pdf_path', 500)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Employee Onboarding
        Schema::create('employee_onboardings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->unique()->constrained('job_applications')->onDelete('restrict');
            $table->foreignId('offering_letter_id')->unique()->constrained('offering_letters')->onDelete('restrict');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('position_id')->constrained('positions')->onDelete('restrict');
            $table->string('employment_type', 50);
            $table->date('start_date');
            $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected', 'completed'])->default('draft');
            $table->foreignId('employee_id')->nullable()->unique()->constrained('employees')->onDelete('set null');
            $table->foreignId('reviewed_by_employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_onboardings');
        Schema::dropIfExists('offering_letters');
        Schema::dropIfExists('applicant_interview_evaluations');
        Schema::dropIfExists('applicant_interview_schedules');
        Schema::dropIfExists('applicant_psikotests');
        Schema::dropIfExists('recruitment_stages');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_postings');
        Schema::dropIfExists('job_requirements_manual');
        Schema::dropIfExists('job_requirements_auto');
        Schema::dropIfExists('job_permintaan_approvals');
        Schema::dropIfExists('job_permintaan_details');
        Schema::dropIfExists('job_permintaan');
        Schema::dropIfExists('applicants_work_experience');
        Schema::dropIfExists('applicants_courses');
        Schema::dropIfExists('applicants_education');
        Schema::dropIfExists('applicants_master_data');
        Schema::dropIfExists('applicant_accounts');
    }
};
