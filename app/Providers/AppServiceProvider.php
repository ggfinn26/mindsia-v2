<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\Branch;
use App\Models\BudgetEstimateItem;
use App\Models\ClassRoom;
use App\Models\Employee;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\Institution;
use App\Models\JobPermintaan;
use App\Models\JobPosting;
use App\Models\KpiBonusRule;
use App\Models\KpiBonusRuleTier;
use App\Models\MarketingBonusRule;
use App\Models\MarketingBonusRuleTier;
use App\Models\MemberSupportTicket;
use App\Models\Province;
use App\Models\Region;
use App\Models\ReimbursementItem;
use App\Models\SpecialBonusRule;
use App\Models\SpecialBonusRuleCondition;
use App\Models\Survey;
use App\Models\ToeflMedia;
use App\Models\ToeflTest;
use App\Observers\AuditObserver;
use App\Observers\Bonus\BonusChildObserver;
use App\Observers\Bonus\BonusRuleObserver;
use App\Observers\BudgetEstimateItemObserver;
use App\Observers\ClassRoomObserver;
use App\Observers\EmployeeWorkAttendanceLogObserver;
use App\Observers\JobPermintaanObserver;
use App\Observers\JobPostingObserver;
use App\Observers\Member\MemberSupportTicketObserver;
use App\Observers\ReimbursementItemObserver;
use App\Observers\SurveyObserver;
use App\Observers\ToeflMediaObserver;
use App\Observers\ToeflTestObserver;
use App\Repositories\Bonus\BonusRuleChangeHistoryRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers()->symbols());
        $this->registerGates();
        $this->registerObservers();
    }

    private function registerGates(): void
    {
        // No blanket bypass — all access controlled via granular Spatie permissions per role.
    }

    private function registerObservers(): void
    {
        foreach ([Province::class, Region::class, Area::class, Branch::class, Institution::class, Employee::class, ClassRoom::class] as $model) {
            $model::observe(AuditObserver::class);
        }
        ClassRoom::observe(ClassRoomObserver::class);
        EmployeeWorkAttendanceLog::observe(EmployeeWorkAttendanceLogObserver::class);
        $historyRepo = app(BonusRuleChangeHistoryRepository::class);
        MarketingBonusRule::observe(new BonusRuleObserver($historyRepo, 'marketing'));
        KpiBonusRule::observe(new BonusRuleObserver($historyRepo, 'kpi'));
        SpecialBonusRule::observe(new BonusRuleObserver($historyRepo, 'special'));
        MarketingBonusRuleTier::observe(new BonusChildObserver($historyRepo, 'marketing', 'marketing_bonus_rule_id', 'tier'));
        KpiBonusRuleTier::observe(new BonusChildObserver($historyRepo, 'kpi', 'kpi_bonus_rule_id', 'tier'));
        SpecialBonusRuleCondition::observe(new BonusChildObserver($historyRepo, 'special', 'special_bonus_rule_id', 'condition'));
        MemberSupportTicket::observe(MemberSupportTicketObserver::class);
        JobPermintaan::observe(JobPermintaanObserver::class);
        Survey::observe(SurveyObserver::class);
        ToeflTest::observe(ToeflTestObserver::class);
        ToeflMedia::observe(ToeflMediaObserver::class);
        JobPosting::observe(JobPostingObserver::class);
        BudgetEstimateItem::observe(BudgetEstimateItemObserver::class);
        ReimbursementItem::observe(ReimbursementItemObserver::class);
    }
}
