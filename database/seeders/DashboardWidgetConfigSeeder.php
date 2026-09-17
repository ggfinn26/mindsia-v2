<?php

namespace Database\Seeders;

use App\Models\DashboardWidgetConfig;
use App\Models\Position;
use Illuminate\Database\Seeder;

class DashboardWidgetConfigSeeder extends Seeder
{
    private const POSITION_DEFAULTS = [
        'CEO' => [
            ['widget_key' => 'revenue', 'order' => 1],
            ['widget_key' => 'profit', 'order' => 2],
            ['widget_key' => 'alert_merah', 'order' => 3],
            ['widget_key' => 'cabang_performance', 'order' => 4],
            ['widget_key' => 'member_growth', 'order' => 5],
            ['widget_key' => 'ranking_program', 'order' => 6],
            ['widget_key' => 'comparable', 'order' => 7],
        ],
        'Superadmin' => [
            ['widget_key' => 'system_audit_log', 'order' => 1],
            ['widget_key' => 'active_users', 'order' => 2],
        ],
        'COO' => [
            ['widget_key' => 'alert_merah', 'order' => 1],
            ['widget_key' => 'cabang_performance', 'order' => 2],
            ['widget_key' => 'attendance_summary', 'order' => 3],
            ['widget_key' => 'class_schedule_utilization', 'order' => 4],
            ['widget_key' => 'facility_status', 'order' => 5],
            ['widget_key' => 'inventory_status', 'order' => 6],
            ['widget_key' => 'branch_rent_contracts', 'order' => 7],
            ['widget_key' => 'cost_control', 'order' => 8],
            ['widget_key' => 'comparable', 'order' => 9],
        ],
        'CMO' => [
            ['widget_key' => 'alert_merah', 'order' => 1],
            ['widget_key' => 'marketing_revenue', 'order' => 2],
            ['widget_key' => 'member_acquisition', 'order' => 3],
            ['widget_key' => 'marketing_team_performance', 'order' => 4],
            ['widget_key' => 'marketing_kpi_snapshot', 'order' => 5],
            ['widget_key' => 'comparable', 'order' => 6],
        ],
        'CHRO' => [
            ['widget_key' => 'alert_merah', 'order' => 1],
            ['widget_key' => 'sdm_headcount', 'order' => 2],
            ['widget_key' => 'payroll_summary', 'order' => 3],
            ['widget_key' => 'recruitment_pipeline', 'order' => 4],
            ['widget_key' => 'attendance_compliance', 'order' => 5],
            ['widget_key' => 'comparable', 'order' => 6],
        ],
        'CPO' => [
            ['widget_key' => 'alert_merah', 'order' => 1],
            ['widget_key' => 'program_financial_performance', 'order' => 2],
            ['widget_key' => 'ranking_program', 'order' => 3],
            ['widget_key' => 'member_growth', 'order' => 4],
            ['widget_key' => 'member_acquisition', 'order' => 5],
            ['widget_key' => 'class_schedule_utilization', 'order' => 6],
            ['widget_key' => 'comparable', 'order' => 7],
        ],
        'HR Recruitment' => [
            ['widget_key' => 'recruitment_pipeline', 'order' => 1],
            ['widget_key' => 'job_postings', 'order' => 2],
            ['widget_key' => 'candidate_screening', 'order' => 3],
            ['widget_key' => 'interview_schedule', 'order' => 4],
            ['widget_key' => 'onboarding_status', 'order' => 5],
            ['widget_key' => 'hiring_metrics', 'order' => 6],
            ['widget_key' => 'comparable', 'order' => 7],
        ],
        'HR Personalia' => [
            ['widget_key' => 'employee_directory', 'order' => 1],
            ['widget_key' => 'contract_status', 'order' => 2],
            ['widget_key' => 'payroll_summary', 'order' => 3],
            ['widget_key' => 'leave_attendance', 'order' => 4],
            ['widget_key' => 'employee_development', 'order' => 5],
            ['widget_key' => 'compliance_policies', 'order' => 6],
            ['widget_key' => 'comparable', 'order' => 7],
        ],
        'Dir Ops' => [
            ['widget_key' => 'alert_merah', 'order' => 1],
            ['widget_key' => 'cabang_performance', 'order' => 2],
            ['widget_key' => 'class_schedule_utilization', 'order' => 3],
            ['widget_key' => 'attendance_summary', 'order' => 4],
            ['widget_key' => 'facility_status', 'order' => 5],
            ['widget_key' => 'inventory_status', 'order' => 6],
            ['widget_key' => 'branch_rent_contracts', 'order' => 7],
            ['widget_key' => 'cost_control', 'order' => 8],
            ['widget_key' => 'sdm_headcount', 'order' => 9],
            ['widget_key' => 'comparable', 'order' => 10],
        ],
        'Finance Director' => [
            ['widget_key' => 'revenue', 'order' => 1],
            ['widget_key' => 'profit', 'order' => 2],
            ['widget_key' => 'budget_vs_actual', 'order' => 3],
            ['widget_key' => 'payroll_summary', 'order' => 4],
            ['widget_key' => 'cash_flow', 'order' => 5],
            ['widget_key' => 'branch_financial_performance', 'order' => 6],
            ['widget_key' => 'program_financial_performance', 'order' => 7],
            ['widget_key' => 'cost_analysis', 'order' => 8],
            ['widget_key' => 'financial_forecasting', 'order' => 9],
            ['widget_key' => 'comparable', 'order' => 10],
        ],
        'Finance General' => [
            ['widget_key' => 'payroll_summary', 'order' => 1],
            ['widget_key' => 'invoice_bill_management', 'order' => 2],
            ['widget_key' => 'payment_status', 'order' => 3],
            ['widget_key' => 'expense_tracking', 'order' => 4],
            ['widget_key' => 'budget_vs_actual', 'order' => 5],
            ['widget_key' => 'account_reconciliation', 'order' => 6],
            ['widget_key' => 'cash_flow', 'order' => 7],
            ['widget_key' => 'alert_merah', 'order' => 8],
            ['widget_key' => 'financial_reports', 'order' => 9],
            ['widget_key' => 'comparable', 'order' => 10],
        ],
        'Manager Area' => [
            ['widget_key' => 'alert_merah', 'order' => 1],
            ['widget_key' => 'area_performance', 'order' => 2],
            ['widget_key' => 'branch_performance_comparison', 'order' => 3],
            ['widget_key' => 'attendance_summary', 'order' => 4],
            ['widget_key' => 'class_schedule_utilization', 'order' => 5],
            ['widget_key' => 'member_acquisition', 'order' => 6],
            ['widget_key' => 'facility_status', 'order' => 7],
            ['widget_key' => 'socialization_schedule', 'order' => 8],
            ['widget_key' => 'cost_control', 'order' => 9],
            ['widget_key' => 'comparable', 'order' => 10],
        ],
        'PIC' => [
            ['widget_key' => 'alert_merah', 'order' => 1],
            ['widget_key' => 'branch_performance', 'order' => 2],
            ['widget_key' => 'attendance_summary', 'order' => 3],
            ['widget_key' => 'class_schedule_utilization', 'order' => 4],
            ['widget_key' => 'member_acquisition', 'order' => 5],
            ['widget_key' => 'facility_status', 'order' => 6],
            ['widget_key' => 'socialization_schedule', 'order' => 7],
            ['widget_key' => 'cost_control', 'order' => 8],
            ['widget_key' => 'comparable', 'order' => 9],
        ],
        'Marketing' => [
            ['widget_key' => 'my_kpi_achievement', 'order' => 1],
            ['widget_key' => 'my_sales_performance', 'order' => 2],
            ['widget_key' => 'my_socialization_activity', 'order' => 3],
            ['widget_key' => 'my_target_status', 'order' => 4],
            ['widget_key' => 'my_ranking', 'order' => 5],
            ['widget_key' => 'my_commission_incentive', 'order' => 6],
            ['widget_key' => 'my_performance_history', 'order' => 7],
            ['widget_key' => 'alert_merah', 'order' => 8],
        ],
        'Regular Tutor' => [
            ['widget_key' => 'my_classes', 'order' => 1],
            ['widget_key' => 'my_session_schedule', 'order' => 2],
            ['widget_key' => 'my_session_attendance', 'order' => 3],
            ['widget_key' => 'my_assessment_tasks', 'order' => 4],
            ['widget_key' => 'my_student_progress', 'order' => 5],
            ['widget_key' => 'my_attendance_record', 'order' => 6],
            ['widget_key' => 'my_performance_summary', 'order' => 7],
            ['widget_key' => 'support_ticket', 'order' => 8],
        ],
        'Official Tutor' => [
            ['widget_key' => 'my_kpi_achievement', 'order' => 1],
            ['widget_key' => 'my_sales_performance', 'order' => 2],
            ['widget_key' => 'my_socialization_activity', 'order' => 3],
            ['widget_key' => 'my_target_status', 'order' => 4],
            ['widget_key' => 'my_ranking', 'order' => 5],
            ['widget_key' => 'my_commission_incentive', 'order' => 6],
            ['widget_key' => 'my_performance_history', 'order' => 7],
            ['widget_key' => 'alert_merah', 'order' => 8],
            ['widget_key' => 'my_classes', 'order' => 9],
            ['widget_key' => 'my_session_schedule', 'order' => 10],
            ['widget_key' => 'my_session_attendance', 'order' => 11],
            ['widget_key' => 'my_assessment_tasks', 'order' => 12],
            ['widget_key' => 'my_student_progress', 'order' => 13],
            ['widget_key' => 'my_attendance_record', 'order' => 14],
            ['widget_key' => 'my_performance_summary', 'order' => 15],
            ['widget_key' => 'support_ticket', 'order' => 16],
        ],
    ];

    public function run(): void
    {
        foreach (self::POSITION_DEFAULTS as $positionName => $widgets) {
            $position = Position::where('position_name', $positionName)->first();

            if (! $position) {
                $this->command->warn("Position '{$positionName}' not found. Skipping.");

                continue;
            }

            foreach ($widgets as $widget) {
                DashboardWidgetConfig::updateOrCreate(
                    [
                        'position_id' => $position->id,
                        'widget_key' => $widget['widget_key'],
                    ],
                    [
                        'order' => $widget['order'],
                        'is_enabled' => true,
                    ],
                );
            }
        }

        $this->command->info('Dashboard widget configs seeded successfully.');
    }
}
