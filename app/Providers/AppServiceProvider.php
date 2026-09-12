<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\Employee;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\Institution;
use App\Models\JobPermintaan;
use App\Models\KpiBonusRule;
use App\Models\MarketingBonusRule;
use App\Models\MemberSupportTicket;
use App\Models\Province;
use App\Models\Region;
use App\Models\SpecialBonusRule;
use App\Observers\AuditObserver;
use App\Observers\Bonus\BonusRuleObserver;
use App\Observers\ClassRoomObserver;
use App\Observers\EmployeeWorkAttendanceLogObserver;
use App\Observers\JobPermintaanObserver;
use App\Observers\Member\MemberSupportTicketObserver;
use App\Repositories\Bonus\BonusRuleChangeHistoryRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        $this->registerGates();
        $this->registerObservers();
    }

    private function registerGates(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('BOARD_OF_DIRECTORS')) {
                return true;
            }
        });
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
        MemberSupportTicket::observe(MemberSupportTicketObserver::class);
        JobPermintaan::observe(JobPermintaanObserver::class);
        // TODO: register JobPostingObserver once NotificationDispatchService (notification domain) is implemented
    }
}
