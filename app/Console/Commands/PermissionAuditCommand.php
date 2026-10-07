<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;

class PermissionAuditCommand extends Command
{
    protected $signature = 'permission:audit {--url=http://localhost:8000}';

    protected $description = 'Audit semua permission: test without (expect 403) dan with (expect not 403) per route';

    /**
     * Route-level: permission -> GET route(s) tanpa path param.
     * Diambil dari route:list --json, hanya GET tanpa {param}.
     */
    private array $routeMap = [
        'attendance.adjustment.create' => ['/attendance-recaps'],
        'attendance.document_signature.manage' => ['/attendance/document-signature'],
        'attendance.holiday.manage' => ['/holidays'],
        'attendance.policy.manage' => ['/attendance-policies'],
        'attendance.rule.manage' => ['/attendance-rules'],
        'attendance.schedule_rule.manage' => ['/work-schedule-rules'],
        // auth.user.force_reset_password — route dengan path param, skip untuk GET test
        // 'auth.user.force_reset_password' => ['/users/{user}/reset-password'] -- path param, skip
        'class.manage' => ['/classrooms'],
        'contract.manage' => ['/employment-statuses'],
        'curriculum.curriculum.create' => ['/curriculums/create'],
        // curriculum.curriculum.update — tidak ada GET route khusus tanpa path param
        'curriculum.program.create' => ['/programs'],
        // curriculum.quota.create — juga via /programs (butuh curriculum.program.create untuk index)
        'curriculum.view' => ['/curriculums'],
        // kpi.grade_rule.delete — delete hanya via DELETE route dengan ID, tidak ada GET index terpisah
        'kpi.grade_rule.view' => ['/kpi/grade-rules'],
        'leave.pay_settings.manage' => ['/leave-pay-settings'],
        'member.support.manage' => ['/ticket-assignment-rules'],
        'position.manage' => ['/positions'],
        'strategic_alerts.view' => ['/admin/strategic-alert'],
        'system.api_key.create' => ['/system/api-keys/create'],
        // system.api_key.delete/update — hanya via routes dengan ID param, tidak ada GET index
        'system.api_key.view' => ['/system/api-keys'],
        'system.permission.manage' => ['/system/permissions'],
        // system.telegram_backup.delete/retry — action routes, bukan GET index
        'system.telegram_backup.view' => ['/system/telegram-backup'],
    ];

    public function handle(): int
    {
        $baseUrl = $this->option('url');

        $testUser = $this->setupTestUser();
        $this->info("Test user: {$testUser->email}");

        $cookie = $this->login($baseUrl, $testUser->email, 'AuditPass99!');
        if (! $cookie) {
            $this->error('Login gagal.');
            $testUser->delete();

            return 1;
        }
        $this->info("Login OK\n");

        $allPerms = Permission::orderBy('name')->pluck('name');
        $pass = 0;
        $fail = 0;
        $skip = 0;
        $failList = [];

        foreach ($allPerms as $permName) {
            $routes = $this->routeMap[$permName] ?? null;

            if (! $routes) {
                $this->line("  SKIP {$permName} — tidak ada route map");
                $skip++;

                continue;
            }

            $testUrl = $baseUrl.$routes[0];

            // Without permission — expect 403
            $statusWithout = $this->get($testUrl, $cookie);
            $withoutOk = $statusWithout === 403;

            // Grant permission
            $testUser->givePermissionTo($permName);
            $testUser->forgetCachedPermissions();
            $cookie = $this->login($baseUrl, $testUser->email, 'AuditPass99!');

            // With permission — expect not 403
            $statusWith = $this->get($testUrl, $cookie);
            $withOk = $statusWith !== 403;

            // Revoke
            $testUser->revokePermissionTo($permName);
            $testUser->forgetCachedPermissions();
            $cookie = $this->login($baseUrl, $testUser->email, 'AuditPass99!');

            $passed = $withoutOk && $withOk;
            $note = "without={$statusWithout} with={$statusWith}";

            if ($passed) {
                $this->line("  <fg=green>✓ PASS</> {$permName} — {$note}");
                $pass++;
            } else {
                $this->error("  ✗ FAIL {$permName} — {$note} — {$testUrl}");
                $fail++;
                $failList[] = "{$permName} ({$note}) → {$testUrl}";
            }
        }

        Employee::where('email', 'perm.audit.test@mindsia.internal')->forceDelete();
        $testUser->forceDelete();

        $this->newLine();
        $this->info('═══════════════════════════════════════');
        $this->info('HASIL AUDIT PERMISSION');
        $this->info('═══════════════════════════════════════');
        $this->info("PASS : {$pass}");
        $this->error("FAIL : {$fail}");
        $this->warn("SKIP : {$skip} (tidak ada route map — dicek di controller)");
        $this->info('TOTAL: '.($pass + $fail + $skip));

        if ($fail > 0) {
            $this->newLine();
            $this->error('FAIL detail:');
            foreach ($failList as $f) {
                $this->error("  {$f}");
            }
        }

        return $fail > 0 ? 1 : 0;
    }

    private function setupTestUser(): User
    {
        User::where('email', 'perm.audit.test@mindsia.internal')->forceDelete();
        Employee::where('email', 'perm.audit.test@mindsia.internal')->forceDelete();

        $employee = Employee::create([
            'employee_code' => 'AUDIT-TEST-001',
            'full_name' => 'Permission Audit Test',
            'gender' => 'L',
            'birthdate' => '1990-01-01',
            'email' => 'perm.audit.test@mindsia.internal',
            'whatsapp_number' => '08000000000',
            'is_hq' => true,
        ]);

        $user = User::create([
            'name' => 'Permission Audit Test',
            'email' => 'perm.audit.test@mindsia.internal',
            'password' => Hash::make('AuditPass99!'),
            'employee_id' => $employee->id,
        ]);

        // email_verified_at wajib via DB update (cast hashed bisa reject assignment)
        DB::table($user->getTable())
            ->where('id', $user->id)
            ->update(['email_verified_at' => now()]);

        $user->refresh();

        return $user;
    }

    private function login(string $baseUrl, string $email, string $password): ?string
    {
        // GET halaman login untuk CSRF token + session cookie awal
        $resp = Http::withOptions(['allow_redirects' => false])->get("{$baseUrl}/employee?view=login");

        $csrfToken = '';
        if (preg_match('/csrf-token[^>]*content="([^"]+)"/s', $resp->body(), $m)) {
            $csrfToken = $m[1];
        }

        $getCookies = $this->extractCookieMap($resp);

        // Login — server return JSON {redirect:...} dengan 200 saat berhasil
        $loginResp = Http::withHeaders([
            'Cookie' => implode('; ', array_values($getCookies)),
            'X-CSRF-TOKEN' => $csrfToken,
            'Referer' => "{$baseUrl}/employee?view=login",
            'Accept' => 'application/json',
        ])->withoutRedirecting()->post("{$baseUrl}/employee/login", [
            '_token' => $csrfToken,
            'email' => $email,
            'password' => $password,
        ]);

        if ($loginResp->status() !== 200 && $loginResp->status() !== 302) {
            return null;
        }

        $merged = array_merge($getCookies, $this->extractCookieMap($loginResp));

        return implode('; ', array_values($merged)) ?: null;
    }

    private function extractCookieMap(Response $resp): array
    {
        $cookies = [];
        foreach ($resp->headers() as $name => $values) {
            if (strtolower((string) $name) === 'set-cookie') {
                foreach ((array) $values as $v) {
                    $pair = explode(';', $v)[0];
                    $key = explode('=', $pair, 2)[0];
                    $cookies[$key] = $pair;
                }
            }
        }

        return $cookies;
    }

    private function parseCookies(Response $resp): string
    {
        return implode('; ', array_values($this->extractCookieMap($resp)));
    }

    private function get(string $url, string $cookie): int
    {
        try {
            return Http::withHeaders(['Cookie' => $cookie])
                ->withoutRedirecting()
                ->get($url)
                ->status();
        } catch (\Throwable) {
            return 0;
        }
    }
}
