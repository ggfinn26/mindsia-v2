<?php

namespace Tests;

use Database\Seeders\TestSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    private static bool $seeded = false;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed once per process, outside the per-test transaction, so roles/permissions
        // survive DatabaseTransactions rollbacks between tests.
        if (! static::$seeded) {
            Artisan::call('db:seed', ['--class' => TestSeeder::class]);
            static::$seeded = true;
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Prevent real Telegram API calls (AuditObserver → TelegramLogService, etc.)
        // Key 'https://api.telegram.org/*' allows per-test overrides: re-calling
        // Http::fake() with the same key updates the value in-place so it stays first.
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        // Disable CSRF for all feature tests — POST requests in tests should not need CSRF tokens
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    /**
     * Replace all Http stubs with the given set (instead of appending).
     * Use when a test needs specific URL responses that would otherwise be
     * shadowed by the global Telegram fake registered in setUp().
     */
    protected function setHttpFakes(array $stubs): void
    {
        $factory = Http::getFacadeRoot();
        $prop = new \ReflectionProperty($factory, 'stubCallbacks');
        $prop->setAccessible(true);
        $prop->setValue($factory, new Collection);
        Http::fake($stubs);
    }
}
