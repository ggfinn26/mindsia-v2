<?php

namespace App\Providers;

use App\Models\ClassRoom;
use App\Policies\ClassroomCurriculumPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        ClassRoom::class => ClassroomCurriculumPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
