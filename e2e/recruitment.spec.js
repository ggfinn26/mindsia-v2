import { test, expect } from "@playwright/test";
import { execSync } from "child_process";

const BASE = "http://localhost:8000";
const ARTISAN = (cmd) =>
    execSync(`php artisan tinker --no-interaction <<'PHP'\n${cmd}\nPHP`, {
        cwd: "/Users/rismaniswaty/Documents/MINDSIA-V2/mindsia-v2",
    }).toString();

const TEST_EMAIL = "playwright.applicant@example.com";
const TEST_WA = "81234567890";
const TEST_PASS = "Password1@";
const HRD_EMAIL = "chro@mindsia.id";
const HRD_PASS = "Password1@";

// ── NOTE ──────────────────────────────────────────────────────────────────────
// GAP: /karir route masih hardcoded dummy (English Tutor).
// Portal karir belum terhubung ke DB job_postings.
// Job posting real (PIC Cilegon) hanya bisa diakses via /applicant/lowongan.
// ─────────────────────────────────────────────────────────────────────────────

test.beforeAll(() => {
    // Assign recruitment permissions ke role 2 (BOARD) dan 4 (HRD)
    ARTISAN(`
        $permIds = DB::table('permissions')->where('name', 'like', 'recruitment%')->pluck('id');
        foreach ([2, 4] as $roleId) {
            foreach ($permIds as $permId) {
                DB::table('role_has_permissions')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permId]
                );
            }
        }
        echo "perms: " . $permIds->count() . " assigned\\n";
    `);

    // Pastikan job posting id=1 published
    ARTISAN(`
        DB::table('job_postings')->where('id', 1)->update(['status' => 'published', 'closing_date' => now()->addDays(30)]);
        echo "posting published\\n";
    `);

    // Bersihkan test applicant sebelumnya
    ARTISAN(`
        $acc = DB::table('applicant_accounts')->where('email', '${TEST_EMAIL}')->first();
        if ($acc) {
            $md = DB::table('applicants_master_data')->where('applicant_account_id', $acc->id)->first();
            if ($md) {
                DB::table('job_applications')->where('applicant_id', $md->id)->delete();
                DB::table('applicants_master_data')->where('id', $md->id)->delete();
            }
            DB::table('applicant_accounts')->where('id', $acc->id)->delete();
        }
        echo "cleaned\\n";
    `);
});

test.afterAll(() => {
    ARTISAN(`
        $acc = DB::table('applicant_accounts')->where('email', '${TEST_EMAIL}')->first();
        if ($acc) {
            $md = DB::table('applicants_master_data')->where('applicant_account_id', $acc->id)->first();
            if ($md) {
                DB::table('job_applications')->where('applicant_id', $md->id)->delete();
                DB::table('applicants_master_data')->where('id', $md->id)->delete();
            }
            DB::table('applicant_accounts')->where('id', $acc->id)->delete();
        }
        echo "afterAll cleanup done\\n";
    `);
});

async function loginEmployee(page, email, password) {
    await page.goto(`${BASE}/employee/login`);
    await page.fill('input[name="email"]', email);
    await page.fill('input[name="password"]', password);
    await page.click('button[type="submit"]');
    await page.waitForURL("**/dashboard**");
}

async function loginApplicant(page, email, password) {
    await page.goto(`${BASE}/applicant/login`);
    await page.fill('input[name="email"]', email);
    await page.fill('input[name="password"]', password);
    await page.click('button[type="submit"]');
    await page.waitForURL("**/applicant/dashboard**", { timeout: 10000 });
}

// ─── Flow 1: applicant-register ───────────────────────────────────────────────

test("R-1: Portal karir publik tampil lowongan (hardcoded dummy)", async ({ page }) => {
    await page.goto(`${BASE}/karir`);
    await expect(page).toHaveTitle(/MINDSIA/i);
    // Portal karir saat ini menampilkan data hardcoded (GAP: belum pakai DB)
    await expect(page.locator("body")).toContainText("English Tutor");
    await expect(page.locator("body")).toContainText("Posisi Terbuka");
});

test("R-2: Detail lowongan (dummy) bisa dibuka", async ({ page }) => {
    await page.goto(`${BASE}/karir/1`);
    // GAP: karir/1 masih hardcoded dummy, bukan data DB
    await expect(page.locator("body")).toContainText("English Tutor (Full-Time)");
    await expect(page.locator("body")).toContainText("Lamar Posisi Ini Sekarang");
});

test("R-3: Register akun pelamar baru berhasil", async ({ page }) => {
    await page.goto(`${BASE}/applicant/register`);

    await page.fill('input[name="full_name"]', "Playwright Pelamar");
    await page.fill('input[name="whatsapp_number"]', TEST_WA);
    await page.fill('input[name="email"]', TEST_EMAIL);
    await page.selectOption('select[name="gender"]', "male");
    await page.fill('input[name="password"]', TEST_PASS);
    await page.fill('input[name="password_confirmation"]', TEST_PASS);

    await page.click('button[type="submit"]');

    // Register redirect ke verification.notice (email verify required)
    await page.waitForURL(/(verification|dashboard|login)/, { timeout: 15000 });

    // Verify email via tinker (bypass Mailpit dalam test)
    ARTISAN(`
        DB::table('applicant_accounts')
            ->where('email', '${TEST_EMAIL}')
            ->update(['email_verified_at' => now()]);
        echo "email verified\\n";
    `);

    // Konfirmasi akun terbuat
    const acc = ARTISAN(`
        $acc = DB::table('applicant_accounts')->where('email', '${TEST_EMAIL}')->first();
        echo $acc ? "found:" . $acc->id : "not found";
    `);
    expect(acc).toContain("found:");
});

test("R-4: Login applicant berhasil setelah register", async ({ page }) => {
    await loginApplicant(page, TEST_EMAIL, TEST_PASS);
    await expect(page).toHaveURL(/applicant\/dashboard/);
});

test("R-5: Profil applicant bisa dilihat", async ({ page }) => {
    await loginApplicant(page, TEST_EMAIL, TEST_PASS);
    await page.goto(`${BASE}/applicant/profil`);
    await expect(page.locator("body")).toContainText("Playwright Pelamar");
});

// ─── Flow 2: job-application-and-stage ────────────────────────────────────────

test("J-1: Applicant lihat lowongan PIC via /applicant/lowongan", async ({ page }) => {
    await loginApplicant(page, TEST_EMAIL, TEST_PASS);
    await page.goto(`${BASE}/applicant/lowongan`);
    // Lowongan real dari DB muncul
    await expect(page.locator("body")).toContainText("Person In Charge");
});

test("J-2: Applicant apply ke lowongan berhasil", async ({ page }) => {
    await loginApplicant(page, TEST_EMAIL, TEST_PASS);
    await page.goto(`${BASE}/applicant/lowongan`);

    // Cari tombol Lamar di form yang action-nya mengandung /apply/
    const lamarForm = page.locator('form[action*="/apply/"]');
    await expect(lamarForm).toBeVisible({ timeout: 5000 });
    await lamarForm.locator('button[type="submit"]').click();

    // Redirect ke detail lamaran applicant
    await page.waitForURL(/applicant\/applications\/\d+/, { timeout: 10000 });
    await expect(page.locator("body")).toContainText(/lamaran|applied|person in charge/i);
});

test("J-3: Riwayat lamaran applicant tampil", async ({ page }) => {
    await loginApplicant(page, TEST_EMAIL, TEST_PASS);
    await page.goto(`${BASE}/applicant/lamaran`);
    await expect(page.locator("body")).toContainText("Person In Charge");
});

test("J-4: HRD lihat list aplikasi — pelamar baru muncul", async ({ page }) => {
    await loginEmployee(page, HRD_EMAIL, HRD_PASS);
    await page.goto(`${BASE}/recruitment/applications`);
    await expect(page.locator("body")).toContainText("Playwright Pelamar");
    await expect(page.locator("body")).toContainText("Applied");
});

test("J-5: HRD buka detail lamaran — profil tampil benar", async ({ page }) => {
    await loginEmployee(page, HRD_EMAIL, HRD_PASS);
    await page.goto(`${BASE}/recruitment/applications`);

    const detailLink = page.locator("tr").filter({ hasText: "Playwright Pelamar" }).locator("a:has-text('Detail')");
    await detailLink.click();
    await page.waitForURL(/recruitment\/applications\/\d+/);

    await expect(page.locator("body")).toContainText("Playwright Pelamar");
    await expect(page.locator("body")).toContainText("Applied");
    await expect(page.locator("button:has-text('Mulai Psikotest')")).toBeVisible();
});

test("J-6: HRD mulai psikotest — status jadi Screening", async ({ page }) => {
    await loginEmployee(page, HRD_EMAIL, HRD_PASS);
    await page.goto(`${BASE}/recruitment/applications`);

    const detailLink = page.locator("tr").filter({ hasText: "Playwright Pelamar" }).locator("a:has-text('Detail')");
    await detailLink.click();
    await page.waitForURL(/recruitment\/applications\/\d+/);

    await page.locator("button:has-text('Mulai Psikotest')").click();
    await page.waitForSelector('input[name="psikotest_date"]', { state: "visible" });

    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    await page.fill('input[name="psikotest_date"]', tomorrow.toISOString().split("T")[0]);

    await page.locator('form[action*="psikotest"] button[type="submit"]').click();
    await page.waitForURL(/recruitment\/applications\/\d+/, { timeout: 10000 });

    await expect(page.locator("body")).toContainText("Screening");
    await expect(page.locator("button:has-text('Jadwalkan Interview')")).toBeVisible();
});

test("J-7: HRD jadwalkan interview — status jadi Interview", async ({ page }) => {
    await loginEmployee(page, HRD_EMAIL, HRD_PASS);
    await page.goto(`${BASE}/recruitment/applications`);

    const detailLink = page.locator("tr").filter({ hasText: "Playwright Pelamar" }).locator("a:has-text('Detail')");
    await detailLink.click();
    await page.waitForURL(/recruitment\/applications\/\d+/);

    const jadwalBtn = page.locator("button:has-text('Jadwalkan Interview')");
    // Skip jika belum di tahap screening
    if (!(await jadwalBtn.isVisible())) {
        test.skip(true, "Belum di tahap screening — jalankan J-6 dulu");
        return;
    }

    await jadwalBtn.click();
    await page.waitForSelector('select[name="interview_type"]', { state: "visible" });

    await page.selectOption('select[name="interview_type"]', "offline");

    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    // datetime-local format: YYYY-MM-DDTHH:MM
    const datetimeStr = nextWeek.toISOString().slice(0, 16);
    await page.fill('input[name="scheduled_at"]', datetimeStr);
    await page.fill('input[name="location"]', "Kantor Pusat MINDSIA");

    // Pilih interviewer
    await page.selectOption('select[name="interviewer_employee_id"]', { index: 1 });

    await page.locator('form[action*="interviews"] button[type="submit"]').click();
    await page.waitForURL(/recruitment\/applications\/\d+/, { timeout: 10000 });

    await expect(page.locator("body")).toContainText("Interview");
    await expect(page.locator("body")).toContainText("Kantor Pusat MINDSIA");
});

test("J-8: HRD reject lamaran dari list — status jadi Rejected", async ({ page }) => {
    // Buat second applicant untuk di-reject supaya tidak ganggu J-6/J-7
    ARTISAN(`
        DB::table('applicant_accounts')->where('email', 'playwright.reject@example.com')->delete();
        $accId = DB::table('applicant_accounts')->insertGetId([
            'email' => 'playwright.reject@example.com',
            'password' => bcrypt('Password1@'),
            'email_verified_at' => now(),
            'created_at' => now(), 'updated_at' => now()
        ]);
        $mdId = DB::table('applicants_master_data')->insertGetId([
            'applicant_account_id' => $accId,
            'full_name' => 'PW Reject Test',
            'email' => 'playwright.reject@example.com',
            'whatsapp_number' => '81999999991',
            'created_at' => now(), 'updated_at' => now()
        ]);
        DB::table('job_applications')->insert([
            'applicant_id' => $mdId,
            'job_posting_id' => 1,
            'application_source' => 'job_posting',
            'status' => 'applied',
            'applied_at' => now(),
            'created_at' => now(), 'updated_at' => now()
        ]);
        echo "reject-test applicant created\\n";
    `);

    await loginEmployee(page, HRD_EMAIL, HRD_PASS);
    await page.goto(`${BASE}/recruitment/applications`);

    const row = page.locator("tr").filter({ hasText: "PW Reject Test" });
    await expect(row).toBeVisible({ timeout: 5000 });

    await row.locator("button:has-text('Tolak')").click();

    // Isi alasan di modal
    await page.waitForSelector("textarea[name='reason']", { state: "visible" });
    await page.fill("textarea[name='reason']", "Tidak memenuhi kualifikasi minimum");

    // Klik tombol merah "Tolak" di dalam form modal
    await page.locator('[x-ref="confirmForm"] button[type="submit"]').click();

    await page.waitForURL(/recruitment\/applications/, { timeout: 10000 });

    // Cek status berubah
    const rejectedRow = page.locator("tr").filter({ hasText: "PW Reject Test" });
    await expect(rejectedRow).toContainText("Rejected");

    // Cleanup
    ARTISAN(`
        $acc = DB::table('applicant_accounts')->where('email', 'playwright.reject@example.com')->first();
        if ($acc) {
            $md = DB::table('applicants_master_data')->where('applicant_account_id', $acc->id)->first();
            if ($md) { DB::table('job_applications')->where('applicant_id', $md->id)->delete(); DB::table('applicants_master_data')->where('id', $md->id)->delete(); }
            DB::table('applicant_accounts')->where('id', $acc->id)->delete();
        }
        echo "cleaned\\n";
    `);
});
