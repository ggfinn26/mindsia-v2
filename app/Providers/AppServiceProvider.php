<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Province;
use App\Models\Region;
use App\Observers\AuditObserver;
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
        foreach ([Province::class, Region::class, Area::class, Branch::class] as $model) {
            $model::observe(AuditObserver::class);
        }
    }
}
