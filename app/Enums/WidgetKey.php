<?php

namespace App\Enums;

enum WidgetKey: string
{
    // Financial & Business
    case Revenue = 'revenue';
    case Profit = 'profit';
    case CostControl = 'cost_control';

    // Operations & Compliance
    case CabangPerformance = 'cabang_performance';
    case AttendanceSummary = 'attendance_summary';
    case ClassScheduleUtilization = 'class_schedule_utilization';
    case FacilityStatus = 'facility_status';
    case InventoryStatus = 'inventory_status';
    case BranchRentContracts = 'branch_rent_contracts';
    case AttendanceCompliance = 'attendance_compliance';
    case ContractStatus = 'contract_status';
    case AlertMerah = 'alert_merah';

    // HR & Recruitment
    case SdmHeadcount = 'sdm_headcount';
    case PayrollSummary = 'payroll_summary';
    case RecruitmentPipeline = 'recruitment_pipeline';
    case EmployeeDevelopment = 'employee_development';
    case JobPostings = 'job_postings';
    case CandidateScreening = 'candidate_screening';
    case InterviewSchedule = 'interview_schedule';
    case OnboardingStatus = 'onboarding_status';
    case HiringMetrics = 'hiring_metrics';
    case EmployeeDirectory = 'employee_directory';
    case LeaveAttendance = 'leave_attendance';
    case CompliancePolicies = 'compliance_policies';

    // Marketing & Growth
    case MarketingRevenue = 'marketing_revenue';
    case MemberAcquisition = 'member_acquisition';
    case LeadStatus = 'lead_status';
    case MarketingTeamPerformance = 'marketing_team_performance';
    case SocializationEffectiveness = 'socialization_effectiveness';
    case MarketingKpiSnapshot = 'marketing_kpi_snapshot';

    // Finance & Administrative
    case BudgetVsActual = 'budget_vs_actual';
    case CashFlow = 'cash_flow';
    case BranchFinancialPerformance = 'branch_financial_performance';
    case ProgramFinancialPerformance = 'program_financial_performance';
    case CostAnalysis = 'cost_analysis';
    case FinancialForecasting = 'financial_forecasting';
    case InvoiceBillManagement = 'invoice_bill_management';
    case PaymentStatus = 'payment_status';
    case ExpenseTracking = 'expense_tracking';
    case AccountReconciliation = 'account_reconciliation';
    case FinancialReports = 'financial_reports';
    case AreaPerformance = 'area_performance';
    case BranchPerformanceComparison = 'branch_performance_comparison';
    case BranchPerformance = 'branch_performance';
    case SocializationSchedule = 'socialization_schedule';

    // Personal Performance
    case MyKpiAchievement = 'my_kpi_achievement';
    case MySalesPerformance = 'my_sales_performance';
    case MySocializationActivity = 'my_socialization_activity';
    case MyTargetStatus = 'my_target_status';
    case MyRanking = 'my_ranking';
    case MyCommissionIncentive = 'my_commission_incentive';
    case MyPerformanceHistory = 'my_performance_history';
    case MyClasses = 'my_classes';
    case MySessionSchedule = 'my_session_schedule';
    case MySessionAttendance = 'my_session_attendance';
    case MyAssessmentTasks = 'my_assessment_tasks';
    case MyStudentProgress = 'my_student_progress';
    case MyAttendanceRecord = 'my_attendance_record';
    case MyPerformanceSummary = 'my_performance_summary';
    case SupportTicket = 'support_ticket';

    // Analytics & Insight
    case MemberGrowth = 'member_growth';
    case RankingProgram = 'ranking_program';
    case Comparable = 'comparable';

    // System & Administration
    case SystemAuditLog = 'system_audit_log';
    case ActiveUsers = 'active_users';

    public function label(): string
    {
        return match ($this) {
            self::Revenue => 'Revenue',
            self::Profit => 'Profit',
            self::CostControl => 'Cost Control',
            self::CabangPerformance => 'Cabang Performance',
            self::AttendanceSummary => 'Attendance Summary',
            self::ClassScheduleUtilization => 'Class Schedule & Utilization',
            self::FacilityStatus => 'Facility Status',
            self::InventoryStatus => 'Inventory Status',
            self::BranchRentContracts => 'Branch Rent Contracts',
            self::AttendanceCompliance => 'Attendance Compliance',
            self::ContractStatus => 'Contract Status',
            self::AlertMerah => 'Alert Merah',
            self::SdmHeadcount => 'SDM Headcount',
            self::PayrollSummary => 'Payroll Summary',
            self::RecruitmentPipeline => 'Recruitment Pipeline',
            self::EmployeeDevelopment => 'Employee Development',
            self::JobPostings => 'Job Postings',
            self::CandidateScreening => 'Candidate Screening',
            self::InterviewSchedule => 'Interview Schedule',
            self::OnboardingStatus => 'Onboarding Status',
            self::HiringMetrics => 'Hiring Metrics',
            self::EmployeeDirectory => 'Employee Directory',
            self::LeaveAttendance => 'Leave & Attendance',
            self::CompliancePolicies => 'Compliance & Policies',
            self::MarketingRevenue => 'Marketing Revenue',
            self::MemberAcquisition => 'Member Acquisition',
            self::LeadStatus => 'Lead Status',
            self::MarketingTeamPerformance => 'Marketing Team Performance',
            self::SocializationEffectiveness => 'Socialization Effectiveness',
            self::MarketingKpiSnapshot => 'Marketing KPI Snapshot',
            self::BudgetVsActual => 'Budget vs Actual',
            self::CashFlow => 'Cash Flow',
            self::BranchFinancialPerformance => 'Branch Financial Performance',
            self::ProgramFinancialPerformance => 'Program Financial Performance',
            self::CostAnalysis => 'Cost Analysis',
            self::FinancialForecasting => 'Financial Forecasting',
            self::InvoiceBillManagement => 'Invoice & Bill Management',
            self::PaymentStatus => 'Payment Status',
            self::ExpenseTracking => 'Expense Tracking',
            self::AccountReconciliation => 'Account Reconciliation',
            self::FinancialReports => 'Financial Reports',
            self::AreaPerformance => 'Area Performance',
            self::BranchPerformanceComparison => 'Branch Performance Comparison',
            self::BranchPerformance => 'Branch Performance',
            self::SocializationSchedule => 'Socialization Schedule',
            self::MyKpiAchievement => 'My KPI Achievement',
            self::MySalesPerformance => 'My Sales Performance',
            self::MySocializationActivity => 'My Socialization Activity',
            self::MyTargetStatus => 'My Target Status',
            self::MyRanking => 'My Ranking',
            self::MyCommissionIncentive => 'My Commission & Incentive',
            self::MyPerformanceHistory => 'My Performance History',
            self::MyClasses => 'My Classes',
            self::MySessionSchedule => 'My Session Schedule',
            self::MySessionAttendance => 'My Session Attendance',
            self::MyAssessmentTasks => 'My Assessment Tasks',
            self::MyStudentProgress => 'My Student Progress',
            self::MyAttendanceRecord => 'My Attendance Record',
            self::MyPerformanceSummary => 'My Performance Summary',
            self::SupportTicket => 'Support Ticket',
            self::MemberGrowth => 'Member Growth',
            self::RankingProgram => 'Ranking Program',
            self::Comparable => 'Comparable',
            self::SystemAuditLog => 'System Audit Log',
            self::ActiveUsers => 'Active Users',
        };
    }

    public function category(): string
    {
        return match ($this) {
            self::Revenue, self::Profit, self::CostControl => 'financial',
            self::CabangPerformance, self::AttendanceSummary, self::ClassScheduleUtilization,
            self::FacilityStatus, self::InventoryStatus, self::BranchRentContracts,
            self::AttendanceCompliance, self::ContractStatus, self::AlertMerah => 'operations',
            self::SdmHeadcount, self::PayrollSummary, self::RecruitmentPipeline,
            self::EmployeeDevelopment, self::JobPostings, self::CandidateScreening,
            self::InterviewSchedule, self::OnboardingStatus, self::HiringMetrics,
            self::EmployeeDirectory, self::LeaveAttendance, self::CompliancePolicies => 'hr',
            self::MarketingRevenue, self::MemberAcquisition, self::LeadStatus,
            self::MarketingTeamPerformance, self::SocializationEffectiveness,
            self::MarketingKpiSnapshot => 'marketing',
            self::BudgetVsActual, self::CashFlow, self::BranchFinancialPerformance,
            self::ProgramFinancialPerformance, self::CostAnalysis, self::FinancialForecasting,
            self::InvoiceBillManagement, self::PaymentStatus, self::ExpenseTracking,
            self::AccountReconciliation, self::FinancialReports, self::AreaPerformance,
            self::BranchPerformanceComparison, self::BranchPerformance, self::SocializationSchedule => 'finance_admin',
            self::MyKpiAchievement, self::MySalesPerformance, self::MySocializationActivity,
            self::MyTargetStatus, self::MyRanking, self::MyCommissionIncentive,
            self::MyPerformanceHistory, self::MyClasses, self::MySessionSchedule,
            self::MySessionAttendance, self::MyAssessmentTasks, self::MyStudentProgress,
            self::MyAttendanceRecord, self::MyPerformanceSummary, self::SupportTicket => 'personal',
            self::MemberGrowth, self::RankingProgram, self::Comparable => 'analytics',
            self::SystemAuditLog, self::ActiveUsers => 'system',
        };
    }

    public function isExportable(): bool
    {
        return ! in_array($this, [self::AlertMerah]);
    }

    public function icon(): string
    {
        return match ($this) {
            // Financial & Business
            self::Revenue => 'payments',
            self::Profit => 'trending_up',
            self::CostControl => 'account_balance_wallet',
            // Operations & Compliance
            self::CabangPerformance => 'location_city',
            self::AttendanceSummary => 'schedule',
            self::ClassScheduleUtilization => 'school',
            self::FacilityStatus => 'build',
            self::InventoryStatus => 'inventory_2',
            self::BranchRentContracts => 'real_estate_agent',
            self::AttendanceCompliance => 'rule',
            self::ContractStatus => 'description',
            self::AlertMerah => 'warning',
            // HR & Recruitment
            self::SdmHeadcount => 'groups',
            self::PayrollSummary => 'receipt_long',
            self::RecruitmentPipeline => 'person_add',
            self::EmployeeDevelopment => 'school',
            self::JobPostings => 'work',
            self::CandidateScreening => 'filter_list',
            self::InterviewSchedule => 'event',
            self::OnboardingStatus => 'how_to_reg',
            self::HiringMetrics => 'analytics',
            self::EmployeeDirectory => 'badge',
            self::LeaveAttendance => 'event_busy',
            self::CompliancePolicies => 'gavel',
            // Marketing & Growth
            self::MarketingRevenue => 'campaign',
            self::MemberAcquisition => 'person_add',
            // Analytics & Insight
            self::MemberGrowth => 'show_chart',
            self::RankingProgram => 'leaderboard',
            self::Comparable => 'compare_arrows',
            // System & Administration
            self::SystemAuditLog => 'visibility',
            self::ActiveUsers => 'group',
            default => 'dashboard',
        };
    }
}
