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
use Closure;
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

        // Register bonus observers using direct event listeners since BonusRuleObserver
        // has constructor parameters that Laravel's container can't auto-resolve.
        $marketingObserver = new BonusRuleObserver($historyRepo, 'marketing');
        $kpiObserver = new BonusRuleObserver($historyRepo, 'kpi');
        $specialObserver = new BonusRuleObserver($historyRepo, 'special');

        foreach (['created', 'updated', 'deleted'] as $event) {
            MarketingBonusRule::registerModelEvent($event, Closure::fromCallable([$marketingObserver, $event]));
            KpiBonusRule::registerModelEvent($event, Closure::fromCallable([$kpiObserver, $event]));
            SpecialBonusRule::registerModelEvent($event, Closure::fromCallable([$specialObserver, $event]));
        }

        $marketingTierObserver = new BonusChildObserver($historyRepo, 'marketing', 'marketing_bonus_rule_id', 'tier');
        $kpiTierObserver = new BonusChildObserver($historyRepo, 'kpi', 'kpi_bonus_rule_id', 'tier');
        $specialCondObserver = new BonusChildObserver($historyRepo, 'special', 'special_bonus_rule_id', 'condition');

        foreach (['created', 'updated', 'deleted'] as $event) {
            MarketingBonusRuleTier::registerModelEvent($event, Closure::fromCallable([$marketingTierObserver, $event]));
            KpiBonusRuleTier::registerModelEvent($event, Closure::fromCallable([$kpiTierObserver, $event]));
            SpecialBonusRuleCondition::registerModelEvent($event, Closure::fromCallable([$specialCondObserver, $event]));
        }
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
