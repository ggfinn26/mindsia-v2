<?php

namespace Database\Seeders;

use App\Enums\WidgetKey;
use App\Models\DashboardWidgetConfig;
use App\Models\Position;
use App\Models\User;
use App\Models\UserDashboardWidgetCustomization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DeveloperWidgetPermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * Seed widget permissions and configure all dashboard widgets for Developer position.
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Ensure Role 'Developer' exists
        $role = Role::firstOrCreate(['name' => 'Developer', 'guard_name' => 'web']);

        // 3. Ensure Position 'Developer' exists
        $position = Position::firstOrCreate(
            ['position_name' => 'Developer'],
            [
                'role_id' => $role->id,
                'hierarchy_order' => 1,
            ]
        );

        if (! $position->role_id) {
            $position->update(['role_id' => $role->id]);
        }

        // 4. Ensure all widget permissions exist and collect their names
        $allWidgetCases = WidgetKey::cases();
        $widgetPermissionNames = [];

        foreach ($allWidgetCases as $case) {
            $permName = 'dashboard.widget.'.$case->value.'.view';
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            $widgetPermissionNames[] = $permName;
        }

        // 5. Grant all permissions (including all widget permissions) to Developer role
        $allPermissions = Permission::pluck('name')->toArray();
        $role->syncPermissions($allPermissions);

        // 6. Give ALL widgets to Developer position in DashboardWidgetConfig
        $order = 1;
        foreach ($allWidgetCases as $case) {
            DashboardWidgetConfig::updateOrCreate(
                [
                    'position_id' => $position->id,
                    'widget_key' => $case->value,
                ],
                [
                    'order' => $order++,
                    'is_enabled' => true,
                ]
            );
        }

        // 7. Clear any disabled customizations for Developer users so all widgets are shown
        $devUsers = User::role('Developer')->get();
        if ($devUsers->isNotEmpty()) {
            UserDashboardWidgetCustomization::whereIn('user_id', $devUsers->pluck('id'))
                ->where('is_enabled', false)
                ->update(['is_enabled' => true]);
        }

        // 8. Invalidate permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command?->info('Berhasil: '.count($allWidgetCases).' widget dan permission widget diberikan ke posisi Developer.');
    }
}
