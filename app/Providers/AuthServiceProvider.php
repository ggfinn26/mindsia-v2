<?php

namespace App\Providers;

use App\Models\ClassRoom;
use App\Models\Employee;
use App\Models\ResignRequest;
use App\Policies\ClassroomCurriculumPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\ResignRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        ClassRoom::class => ClassroomCurriculumPolicy::class,
        Employee::class => EmployeePolicy::class,
        ResignRequest::class => ResignRequestPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
