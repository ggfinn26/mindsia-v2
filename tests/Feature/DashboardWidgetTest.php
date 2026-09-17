<?php

use App\Enums\WidgetKey;
use App\Models\DashboardWidgetConfig;
use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\User;
use App\Models\UserDashboardWidgetCustomization;
use App\Services\Dashboard\DashboardWidgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

// Helper: buat Position tanpa factory
function makePosition(): Position
{
    return Position::create(['position_name' => 'Test Position ' . uniqid()]);
}

// Helper: bind user → employee → employmentStatus → position
function bindUserPosition(User $user, Position $position): void
{
    $employee = Employee::factory()->create(['user_id' => $user->id]);
    EmploymentStatus::create(['employees_id' => $employee->id, 'position_id' => $position->id]);
}

// DW-01: Unauthenticated redirect
it('redirects unauthenticated user to login', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

// DW-02: Dashboard loads for authenticated user
it('renders dashboard for authenticated user', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewIs('dashboard.index');
});

// DW-03: Only enabled widgets returned from position config
it('getWidgetsForUser only returns enabled widgets', function () {
    $position = makePosition();
    $user = User::factory()->create(['email_verified_at' => now()]);
    bindUserPosition($user, $position);

    DashboardWidgetConfig::create(['position_id' => $position->id, 'widget_key' => 'revenue', 'order' => 1, 'is_enabled' => true]);
    DashboardWidgetConfig::create(['position_id' => $position->id, 'widget_key' => 'profit', 'order' => 2, 'is_enabled' => false]);

    $widgets = app(DashboardWidgetService::class)->getWidgetsForUser($user->fresh());

    expect($widgets)->toHaveCount(1)
        ->and($widgets->first()['key'])->toBe('revenue');
});

// DW-04: User override can hide a position-enabled widget
it('user customization can hide a position-enabled widget', function () {
    $position = makePosition();
    $user = User::factory()->create(['email_verified_at' => now()]);
    bindUserPosition($user, $position);

    DashboardWidgetConfig::create(['position_id' => $position->id, 'widget_key' => 'revenue', 'order' => 1, 'is_enabled' => true]);
    UserDashboardWidgetCustomization::create(['user_id' => $user->id, 'widget_key' => 'revenue', 'is_enabled' => false]);

    $widgets = app(DashboardWidgetService::class)->getWidgetsForUser($user->fresh());

    expect($widgets)->toHaveCount(0);
});

// DW-05: User override order wins over position config order
it('user customization order overrides position config order', function () {
    $position = makePosition();
    $user = User::factory()->create(['email_verified_at' => now()]);
    bindUserPosition($user, $position);

    DashboardWidgetConfig::create(['position_id' => $position->id, 'widget_key' => 'revenue', 'order' => 5, 'is_enabled' => true]);
    UserDashboardWidgetCustomization::create(['user_id' => $user->id, 'widget_key' => 'revenue', 'order' => 99, 'is_enabled' => true]);

    $widget = app(DashboardWidgetService::class)->getWidgetsForUser($user->fresh())->first();

    expect($widget['order'])->toBe(99);
});

// DW-06: widget-order route saves per-user order
it('POST widget-order saves user-level order', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->post(route('dashboard.widget-order'), ['orders' => ['revenue' => '3', 'profit' => '1']])
        ->assertRedirect();

    $this->assertDatabaseHas('user_dashboard_widget_customizations', [
        'user_id'    => $user->id,
        'widget_key' => 'revenue',
        'order'      => 3,
    ]);
});

// DW-07: widget-toggle route saves is_enabled override
it('POST widget-toggle saves user-level is_enabled', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->post(route('dashboard.widget-toggle'), ['widget_key' => 'revenue', 'is_enabled' => false])
        ->assertRedirect();

    $this->assertDatabaseHas('user_dashboard_widget_customizations', [
        'user_id'    => $user->id,
        'widget_key' => 'revenue',
        'is_enabled' => false,
    ]);
});

// DW-08: Write-through — miss writes to cache
it('getWidgetData writes to cache on cache miss', function () {
    Cache::flush();

    app(DashboardWidgetService::class)->getWidgetData('revenue', null);

    expect(Cache::has('widget:revenue:branch:'))->toBeTrue();
});

// DW-09: Write-through — refreshWidgetCache overwrites stale data
it('refreshWidgetCache overwrites existing cached data', function () {
    Cache::flush();
    Cache::put('widget:revenue:branch:', ['stale' => true], now()->addMinutes(15));

    app(DashboardWidgetService::class)->refreshWidgetCache('revenue', null);

    expect(Cache::get('widget:revenue:branch:'))->not->toHaveKey('stale');
});

// DW-10: invalidateWidgetCache busts cache entry
it('invalidateWidgetCache removes the cached entry', function () {
    Cache::put('widget:revenue:branch:', ['data' => 1], now()->addMinutes(15));

    app(DashboardWidgetService::class)->invalidateWidgetCache('revenue', null);

    expect(Cache::has('widget:revenue:branch:'))->toBeFalse();
});

// DW-11: Personal widgets (my_*) bypass cache
it('personal widgets are never written to cache', function () {
    Cache::flush();

    app(DashboardWidgetService::class)->getWidgetData('my_kpi_achievement', null);

    expect(Cache::has('widget:my_kpi_achievement:branch:'))->toBeFalse();
});

// DW-12: All WidgetKey enum cases have a registered provider (no "error" key in response)
it('all WidgetKey enum cases have a registered data provider', function () {
    $service = app(DashboardWidgetService::class);

    $missing = collect(WidgetKey::cases())
        ->filter(fn ($case) => isset($service->getWidgetData($case->value, null)['error']))
        ->map(fn ($case) => $case->value)
        ->values()
        ->all();

    expect($missing)->toBeEmpty('Missing providers: ' . implode(', ', $missing));
})->skip('Requires fully migrated test DB with all columns (run with production-like DB)');

// DW-13: toggleWidget upsert — second call updates existing row
it('toggleWidget upserts correctly on second call', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $service = app(DashboardWidgetService::class);

    $service->toggleWidget($user, 'revenue', true);
    $service->toggleWidget($user, 'revenue', false);

    expect(UserDashboardWidgetCustomization::where('user_id', $user->id)->where('widget_key', 'revenue')->count())->toBe(1)
        ->and(UserDashboardWidgetCustomization::where('user_id', $user->id)->where('widget_key', 'revenue')->value('is_enabled'))->toBeFalse();
});
