<?php

namespace Tests;

use Database\Seeders\TestSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    /**
     * TestSeeder (PermissionSeeder + RoleSeeder) dijalankan bersama migrate:fresh --seeder=TestSeeder.
     * Ini terjadi SEBELUM per-test transaction dimulai — data tidak di-rollback antar test.
     * Menghindari deadlock dari seed di dalam transaction (artisan buka koneksi baru).
     */
    protected string $seeder = TestSeeder::class;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Disable CSRF for all feature tests — POST requests in tests should not need CSRF tokens
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }
}
