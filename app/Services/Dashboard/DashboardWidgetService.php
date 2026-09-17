<?php

namespace App\Services\Dashboard;

use App\Enums\WidgetKey;
use App\Models\DashboardWidgetConfig;
use App\Models\User;
use App\Models\UserDashboardWidgetCustomization;
use App\Services\Dashboard\Widgets\AccountReconciliationWidget;
use App\Services\Dashboard\Widgets\ActiveUsersWidget;
use App\Services\Dashboard\Widgets\AlertMerahWidget;
use App\Services\Dashboard\Widgets\AreaPerformanceWidget;
use App\Services\Dashboard\Widgets\AttendanceComplianceWidget;
use App\Services\Dashboard\Widgets\AttendanceSummaryWidget;
use App\Services\Dashboard\Widgets\BranchFinancialPerformanceWidget;
use App\Services\Dashboard\Widgets\BranchPerformanceComparisonWidget;
use App\Services\Dashboard\Widgets\BranchPerformanceWidget;
use App\Services\Dashboard\Widgets\BranchRentContractsWidget;
use App\Services\Dashboard\Widgets\BudgetVsActualWidget;
use App\Services\Dashboard\Widgets\CabangPerformanceWidget;
use App\Services\Dashboard\Widgets\CandidateScreeningWidget;
use App\Services\Dashboard\Widgets\CashFlowWidget;
use App\Services\Dashboard\Widgets\ClassScheduleUtilizationWidget;
use App\Services\Dashboard\Widgets\ComparableWidget;
use App\Services\Dashboard\Widgets\CompliancePoliciesWidget;
use App\Services\Dashboard\Widgets\ContractStatusWidget;
use App\Services\Dashboard\Widgets\CostAnalysisWidget;
use App\Services\Dashboard\Widgets\CostControlWidget;
use App\Services\Dashboard\Widgets\EmployeeDevelopmentWidget;
use App\Services\Dashboard\Widgets\EmployeeDirectoryWidget;
use App\Services\Dashboard\Widgets\ExpenseTrackingWidget;
use App\Services\Dashboard\Widgets\FacilityStatusWidget;
use App\Services\Dashboard\Widgets\FinancialForecastingWidget;
use App\Services\Dashboard\Widgets\FinancialReportsWidget;
use App\Services\Dashboard\Widgets\HiringMetricsWidget;
use App\Services\Dashboard\Widgets\InterviewScheduleWidget;
use App\Services\Dashboard\Widgets\InventoryStatusWidget;
use App\Services\Dashboard\Widgets\InvoiceBillManagementWidget;
use App\Services\Dashboard\Widgets\JobPostingsWidget;
use App\Services\Dashboard\Widgets\LeadStatusWidget;
use App\Services\Dashboard\Widgets\LeaveAttendanceWidget;
use App\Services\Dashboard\Widgets\MarketingKpiSnapshotWidget;
use App\Services\Dashboard\Widgets\MarketingRevenueWidget;
use App\Services\Dashboard\Widgets\MarketingTeamPerformanceWidget;
use App\Services\Dashboard\Widgets\MemberAcquisitionWidget;
use App\Services\Dashboard\Widgets\MemberGrowthWidget;
use App\Services\Dashboard\Widgets\MyAssessmentTasksWidget;
use App\Services\Dashboard\Widgets\MyAttendanceRecordWidget;
use App\Services\Dashboard\Widgets\MyClassesWidget;
use App\Services\Dashboard\Widgets\MyCommissionIncentiveWidget;
use App\Services\Dashboard\Widgets\MyKpiAchievementWidget;
use App\Services\Dashboard\Widgets\MyPerformanceHistoryWidget;
use App\Services\Dashboard\Widgets\MyPerformanceSummaryWidget;
use App\Services\Dashboard\Widgets\MyRankingWidget;
use App\Services\Dashboard\Widgets\MySalesPerformanceWidget;
use App\Services\Dashboard\Widgets\MySessionAttendanceWidget;
use App\Services\Dashboard\Widgets\MySessionScheduleWidget;
use App\Services\Dashboard\Widgets\MySocializationActivityWidget;
use App\Services\Dashboard\Widgets\MyStudentProgressWidget;
use App\Services\Dashboard\Widgets\MyTargetStatusWidget;
use App\Services\Dashboard\Widgets\OnboardingStatusWidget;
use App\Services\Dashboard\Widgets\PaymentStatusWidget;
use App\Services\Dashboard\Widgets\PayrollSummaryWidget;
use App\Services\Dashboard\Widgets\ProfitWidget;
use App\Services\Dashboard\Widgets\ProgramFinancialPerformanceWidget;
use App\Services\Dashboard\Widgets\RankingProgramWidget;
use App\Services\Dashboard\Widgets\RecruitmentPipelineWidget;
use App\Services\Dashboard\Widgets\RevenueWidget;
use App\Services\Dashboard\Widgets\SdmHeadcountWidget;
use App\Services\Dashboard\Widgets\SocializationEffectivenessWidget;
use App\Services\Dashboard\Widgets\SocializationScheduleWidget;
use App\Services\Dashboard\Widgets\SupportTicketWidget;
use App\Services\Dashboard\Widgets\SystemAuditLogWidget;
use Illuminate\Support\Collection;

class DashboardWidgetService
{
    /** @var array<string, WidgetDataProviderInterface> */
    private array $providers = [];

    public function __construct()
    {
        // Financial & Business
        $this->registerProvider(new RevenueWidget);
        $this->registerProvider(new ProfitWidget);
        $this->registerProvider(new CostControlWidget);

        // Operations & Compliance
        $this->registerProvider(new CabangPerformanceWidget);
        $this->registerProvider(new AttendanceSummaryWidget);
        $this->registerProvider(new ClassScheduleUtilizationWidget);
        $this->registerProvider(new FacilityStatusWidget);
        $this->registerProvider(new InventoryStatusWidget);
        $this->registerProvider(new BranchRentContractsWidget);
        $this->registerProvider(new AttendanceComplianceWidget);
        $this->registerProvider(new ContractStatusWidget);
        $this->registerProvider(new AlertMerahWidget);

        // HR & Recruitment
        $this->registerProvider(new SdmHeadcountWidget);
        $this->registerProvider(new PayrollSummaryWidget);
        $this->registerProvider(new RecruitmentPipelineWidget);
        $this->registerProvider(new EmployeeDevelopmentWidget);
        $this->registerProvider(new JobPostingsWidget);
        $this->registerProvider(new CandidateScreeningWidget);
        $this->registerProvider(new InterviewScheduleWidget);
        $this->registerProvider(new OnboardingStatusWidget);
        $this->registerProvider(new HiringMetricsWidget);
        $this->registerProvider(new EmployeeDirectoryWidget);
        $this->registerProvider(new LeaveAttendanceWidget);
        $this->registerProvider(new CompliancePoliciesWidget);

        // Marketing & Growth
        $this->registerProvider(new MarketingRevenueWidget);
        $this->registerProvider(new MemberAcquisitionWidget);
        $this->registerProvider(new LeadStatusWidget);
        $this->registerProvider(new MarketingTeamPerformanceWidget);
        $this->registerProvider(new SocializationEffectivenessWidget);
        $this->registerProvider(new MarketingKpiSnapshotWidget);

        // Finance & Administrative
        $this->registerProvider(new BudgetVsActualWidget);
        $this->registerProvider(new CashFlowWidget);
        $this->registerProvider(new BranchFinancialPerformanceWidget);
        $this->registerProvider(new ProgramFinancialPerformanceWidget);
        $this->registerProvider(new CostAnalysisWidget);
        $this->registerProvider(new FinancialForecastingWidget);
        $this->registerProvider(new InvoiceBillManagementWidget);
        $this->registerProvider(new PaymentStatusWidget);
        $this->registerProvider(new ExpenseTrackingWidget);
        $this->registerProvider(new AccountReconciliationWidget);
        $this->registerProvider(new FinancialReportsWidget);
        $this->registerProvider(new AreaPerformanceWidget);
        $this->registerProvider(new BranchPerformanceComparisonWidget);
        $this->registerProvider(new BranchPerformanceWidget);
        $this->registerProvider(new SocializationScheduleWidget);

        // Personal Performance
        $this->registerProvider(new MyKpiAchievementWidget);
        $this->registerProvider(new MySalesPerformanceWidget);
        $this->registerProvider(new MySocializationActivityWidget);
        $this->registerProvider(new MyTargetStatusWidget);
        $this->registerProvider(new MyRankingWidget);
        $this->registerProvider(new MyCommissionIncentiveWidget);
        $this->registerProvider(new MyPerformanceHistoryWidget);
        $this->registerProvider(new MyClassesWidget);
        $this->registerProvider(new MySessionScheduleWidget);
        $this->registerProvider(new MySessionAttendanceWidget);
        $this->registerProvider(new MyAssessmentTasksWidget);
        $this->registerProvider(new MyStudentProgressWidget);
        $this->registerProvider(new MyAttendanceRecordWidget);
        $this->registerProvider(new MyPerformanceSummaryWidget);
        $this->registerProvider(new SupportTicketWidget);

        // Analytics & Insight
        $this->registerProvider(new MemberGrowthWidget);
        $this->registerProvider(new RankingProgramWidget);
        $this->registerProvider(new ComparableWidget);

        // System & Administration
        $this->registerProvider(new SystemAuditLogWidget);
        $this->registerProvider(new ActiveUsersWidget);
    }

    public function registerProvider(WidgetDataProviderInterface $provider): void
    {
        $this->providers[$provider->getKey()] = $provider;
    }

    public function getWidgetsForUser(User $user): Collection
    {
        $positionId = $user->employee?->currentStatus?->position_id;

        if (! $positionId) {
            return collect();
        }

        // Load user-level overrides (order + is_enabled) keyed by widget_key
        $userOverrides = UserDashboardWidgetCustomization::where('user_id', $user->id)
            ->get()
            ->keyBy('widget_key');

        return DashboardWidgetConfig::with('position')
            ->where('position_id', $positionId)
            ->get()
            ->map(function (DashboardWidgetConfig $config) use ($userOverrides) {
                $override = $userOverrides->get($config->widget_key);

                // User override wins over position config
                $isEnabled = $override ? $override->is_enabled : $config->is_enabled;
                $order = $override?->order ?? $config->order;

                return [
                    'key' => $config->widget_key,
                    'label' => $this->getLabel($config->widget_key),
                    'category' => $this->getCategory($config->widget_key),
                    'icon' => $this->getIcon($config->widget_key),
                    'order' => $order,
                    'is_enabled' => $isEnabled,
                    'is_exportable' => $this->isExportable($config->widget_key),
                    'custom_settings' => $config->custom_settings,
                ];
            })
            ->filter(fn (array $w) => $w['is_enabled'])
            ->sortBy('order')
            ->values();
    }

    public function getWidgetData(string $widgetKey, ?int $branchId = null): array
    {
        $provider = $this->providers[$widgetKey] ?? null;

        if (! $provider) {
            return ['error' => "Widget provider not found for key: {$widgetKey}"];
        }

        // Personal + real-time widgets bypass cache entirely (auth-scoped / always fresh)
        if ($this->shouldSkipCache($widgetKey)) {
            return $provider->getData($branchId);
        }

        $cacheKey = $this->cacheKey($widgetKey, $branchId);

        // Write-through: on hit return cached value; on miss fetch + write to cache
        $cached = cache()->get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $data = $provider->getData($branchId);
        cache()->put($cacheKey, $data, now()->addMinutes(15));

        return $data;
    }

    /**
     * Write-through refresh: force fetch fresh data and overwrite cache immediately.
     * Call this after writes that affect widget data.
     */
    public function refreshWidgetCache(string $widgetKey, ?int $branchId = null): void
    {
        $provider = $this->providers[$widgetKey] ?? null;

        if (! $provider || $this->shouldSkipCache($widgetKey)) {
            return;
        }

        $data = $provider->getData($branchId);
        cache()->put($this->cacheKey($widgetKey, $branchId), $data, now()->addMinutes(15));
    }

    /** Bust cache for a specific widget + branch (e.g. after bulk data import). */
    public function invalidateWidgetCache(string $widgetKey, ?int $branchId = null): void
    {
        cache()->forget($this->cacheKey($widgetKey, $branchId));
    }

    private function cacheKey(string $widgetKey, ?int $branchId): string
    {
        return "widget:{$widgetKey}:branch:{$branchId}";
    }

    private function shouldSkipCache(string $widgetKey): bool
    {
        // ponytail: personal (auth-scoped) + real-time widgets never cached
        return str_starts_with($widgetKey, 'my_')
            || in_array($widgetKey, ['active_users', 'alert_merah', 'system_audit_log']);
    }

    public function getWidgetExportData(string $widgetKey, ?int $branchId = null): array
    {
        $provider = $this->providers[$widgetKey] ?? null;

        if (! $provider) {
            return [];
        }

        return $provider->getExportData($branchId);
    }

    public function updateWidgetOrder(User $user, array $widgetOrders): void
    {
        foreach ($widgetOrders as $widgetKey => $order) {
            UserDashboardWidgetCustomization::updateOrCreate(
                ['user_id' => $user->id, 'widget_key' => $widgetKey],
                ['order' => (int) $order],
            );
        }
    }

    public function toggleWidget(User $user, string $widgetKey, bool $isEnabled): void
    {
        UserDashboardWidgetCustomization::updateOrCreate(
            ['user_id' => $user->id, 'widget_key' => $widgetKey],
            ['is_enabled' => $isEnabled],
        );
    }

    public function isExportable(string $widgetKey): bool
    {
        $enum = WidgetKey::tryFrom($widgetKey);

        return $enum?->isExportable() ?? false;
    }

    public function getLabel(string $widgetKey): string
    {
        $enum = WidgetKey::tryFrom($widgetKey);

        return $enum?->label() ?? ucfirst(str_replace('_', ' ', $widgetKey));
    }

    public function getCategory(string $widgetKey): string
    {
        $enum = WidgetKey::tryFrom($widgetKey);

        return $enum?->category() ?? 'general';
    }

    public function getIcon(string $widgetKey): string
    {
        $enum = WidgetKey::tryFrom($widgetKey);

        return $enum?->icon() ?? 'dashboard';
    }
}
