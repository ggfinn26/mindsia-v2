<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // System & Access
            'system.permission.manage',
            'system.api_key.create',
            'system.api_key.update',
            'system.webhook.manage',
            'system.bot.manage',
            'system.notification_routing.manage',
            'auth.user.force_reset_password',

            // Organization
            'organization.province.create',
            'organization.province.update',
            'organization.province.delete',
            'organization.region.create',
            'organization.region.update',
            'organization.region.delete',
            'organization.area.create',
            'organization.area.update',
            'organization.area.delete',
            'organization.branch.create',
            'organization.branch.update',
            'organization.branch.delete',
            'organization.branch.assign_pic',
            'organization.branch.toggle_active',
            'organization.institution.create',
            'organization.institution.update',
            'organization.institution.delete',
            'organization.branch_transfer.review',

            // Employee
            'employee.view',
            'employee.create',
            'employee.update',
            'employee.delete',
            'employee.termination_checklist.review',

            // Recruitment
            'recruitment.job_permintaan.create',
            'recruitment.job_permintaan.update',
            'recruitment.job_permintaan.review',
            'recruitment.job_permintaan.approve',
            'recruitment.job_posting.create',
            'recruitment.job_posting.update',
            'recruitment.job_posting.publish',
            'recruitment.job_application.create',
            'recruitment.job_application.review',
            'recruitment.interview.create',
            'recruitment.interview.update',
            'recruitment.interview.delete',
            'recruitment.interview.evaluate',
            'recruitment.psikotest.create',
            'recruitment.psikotest.update',
            'recruitment.offering_letter.create',
            'recruitment.offering_letter.update',
            'recruitment.offering_letter.negotiate',
            'recruitment.offering_letter.export',
            'recruitment.onboarding.create',
            'recruitment.onboarding.update',
            'recruitment.onboarding.review',

            // Attendance
            'attendance.document_signature.manage',
            'attendance.adjustment.create',
            'attendance.leave.review',
            'attendance.schedule_rule.manage',
            'attendance.holiday.manage',
            'attendance.policy.manage',
            'attendance.policy.index',
            'attendance.policy.create',
            'attendance.policy.edit',
            'attendance.rule.manage',
            'attendance.rule.index',
            'attendance.rule.create',
            'attendance.rule.edit',
            'attendance.recap.view',

            // KPI
            'kpi.template.create',
            'kpi.template.update',
            'kpi.grade_rule.create',
            'kpi.grade_rule.update',
            'kpi.evaluator_assignment.create',
            'kpi.evaluator_assignment.update',
            'kpi.evaluation.create',
            'kpi.evaluation.update',
            'kpi.evaluation.finalize',
            'kpi.document.create',

            // Survey
            'survey.form.view',
            'survey.form.create',
            'survey.form.update',
            'survey.form.delete',
            'survey.assignment.create',
            'survey.result.view',

            // Payroll
            'payroll.component.create',
            'payroll.component.update',
            'payroll.employee_compensation.create',
            'payroll.employee_compensation.update',
            'payroll.session_compensation_rule.create',
            'payroll.session_compensation_rule.update',
            'payroll.period.create',
            'payroll.period.update',
            'payroll.period.generate',
            'payroll.period.pay',
            'payroll.slip.generate',
            'payroll.item.adjust',

            // Finance
            'finance.budget_estimate.view',
            'finance.budget_estimate.create',
            'finance.budget_estimate.ops_review',
            'finance.budget_estimate.send',
            'finance.reimbursement.view',
            'finance.reimbursement.create',
            'finance.reimbursement.review',
            'finance.reimbursement.pay',
            'finance.monthly_cost.view',
            'finance.monthly_cost.delete',
            'finance.period.lock',

            // Facility
            'facility.ticket.create',
            'facility.ticket.update',
            'facility.ticket.assign',
            'facility.ticket.review',
            'facility.ticket.resolve',
            'facility.inventory.create',
            'facility.inventory.update',
            'facility.inventory.dispose',
            'facility.rent_contract.create',
            'facility.rent_contract.update',
            'facility.rent_contract.mark_paid',

            // Letter
            'letter.template.create',
            'letter.template.update',
            'letter.generate.create',
            'letter.generate.update',
            'letter.generate.publish',
            'letter.upload.create',
            'letter.upload.update',
            'letter.in.create',
            'letter.in.update',
            'letter.sop.create',
            'letter.sop.update',

            // Notification
            'notification.template.create',
            'notification.template.update',
            'notification.template.delete',
            'notification.variable.create',
            'notification.variable.update',
            'notification.variable.delete',
            'notification.log.view',
            'notification.log.resend',
            'notification.send_manual',

            // Class
            'class.attendance.record',
            'class.session.update',
            'class.assessment.record',
            'class.progress.update',
            'class.test.create',
            'class.test.update',
            'class.test.delete',
            'class.test.score',
            'class.certificate.update',
            'class.graduate',
            'class.test.review_essay',

            // TOEFL
            'toefl.test.create',
            'toefl.test.update',
            'toefl.test.publish',
            'toefl.question.create',
            'toefl.question.update',
            'toefl.passage.create',
            'toefl.passage.update',
            'toefl.media.create',

            // Marketing
            'marketing.socialization.create',
            'marketing.socialization.update',
            'marketing.socialization.assign',
            'marketing.prospective_member.create',
            'marketing.prospective_member.update',
            'marketing.prospective.view_area',
            'marketing.prospective.view_national',
            'marketing.target.set',
            'marketing.ranking_rule.manage',
            'marketing.kpi.view',
            'marketing.view_own_performance',
            'marketing.wa_template.manage',
            'marketing.member_payment_statement.view',
            'marketing.monthly_revenue_data.view',
            'marketing.monthly_revenue_data.view_branch',
            'marketing.socialization.partner_fee.update',

            // Missing permissions from old seeder that might be needed by sidebar
            // Just general view permissions for sidebars
            'strategic_alerts.view',
            'position.view',
            'position.manage',
            'member.view',
            'curriculum.view',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }
}
