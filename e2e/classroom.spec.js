import { test, expect } from "@playwright/test";
import { execSync } from "child_process";

test.use({ storageState: "e2e/.auth.json" });

// Reset: satu kelas planned+member, satu kelas planned tanpa member
test.beforeAll(() => {
    execSync(
        `php artisan tinker --execute '
            use App\\Models\\ClassRoom;
            use App\\Models\\MemberClass;
            ClassRoom::query()->delete();
            $c1 = ClassRoom::create([
                "program_id" => 4, "branch_id" => 1, "class_name" => "Kelas Test Utama",
                "day_of_week" => "Senin", "week_count" => 4,
                "start_date" => "2026-11-03", "start_time_primary" => "08:00",
                "end_time_primary" => "10:00", "status" => "planned",
            ]);
            MemberClass::create(["class_id" => $c1->id, "member_registration_id" => 8, "start_date" => "2026-11-03", "status" => "active"]);
            ClassRoom::create([
                "program_id" => 4, "branch_id" => 1, "class_name" => "Kelas Kosong",
                "day_of_week" => "Selasa", "week_count" => 4,
                "start_date" => "2026-11-04", "start_time_primary" => "10:00",
                "end_time_primary" => "12:00", "status" => "planned",
            ]);
            echo "seeded\n";
        '`,
        { cwd: "/Users/rismaniswaty/Documents/MINDSIA-V2/mindsia-v2" },
    );
});

test.afterAll(() => {
    execSync(
        `php artisan tinker --execute 'App\\Models\\ClassRoom::whereIn("class_name", ["Kelas Test Utama", "Kelas Kosong", "Kelas Test Edit"])->delete(); echo "cleaned\n";'`,
        { cwd: "/Users/rismaniswaty/Documents/MINDSIA-V2/mindsia-v2" },
    );
});

function getClassId(name) {
    const out = execSync(
        `php artisan tinker --execute 'echo App\\Models\\ClassRoom::where("class_name", "${name}")->value("id");'`,
        { cwd: "/Users/rismaniswaty/Documents/MINDSIA-V2/mindsia-v2" },
    )
        .toString()
        .trim();
    return out.split("\n").pop();
}

// ── S1: Edit kelas planned ──────────────────────────────────────────────────
test("S1 edit kelas planned - ubah nama berhasil", async ({ page }) => {
    const id = getClassId("Kelas Test Utama");
    await page.goto(`/classrooms/${id}/edit`);
    await expect(page.locator("h1")).toContainText("Edit Kelas");

    await page.fill('input[name="class_name"]', "Kelas Test Edit");
    await page.click('button[type="submit"]');

    await expect(page.locator(".bg-green-100")).toContainText("berhasil diperbarui");
    await expect(page.locator("h1")).toContainText("Kelas Test Edit");
});

// ── S2: Tombol Edit tidak muncul di active ──────────────────────────────────
test("S2 tombol edit tidak ada di kelas active", async ({ page }) => {
    execSync(
        `php artisan tinker --execute 'App\\Models\\ClassRoom::where("class_name","Kelas Test Edit")->update(["status"=>"active"]); echo "done";'`,
        { cwd: "/Users/rismaniswaty/Documents/MINDSIA-V2/mindsia-v2" },
    );
    const id = getClassId("Kelas Test Edit");
    await page.goto(`/classrooms/${id}`);

    await expect(page.locator('a[href*="/edit"]')).toHaveCount(0);
    await expect(page.locator("button", { hasText: "Aktifkan" })).toHaveCount(0);
    await expect(page.locator("button", { hasText: "Batalkan Kelas" })).toBeVisible();

    // Reset ke planned untuk test berikutnya
    execSync(
        `php artisan tinker --execute 'App\\Models\\ClassRoom::where("class_name","Kelas Test Edit")->update(["status"=>"planned"]); echo "done";'`,
        { cwd: "/Users/rismaniswaty/Documents/MINDSIA-V2/mindsia-v2" },
    );
});

// ── S3: Hapus kelas planned tanpa member ────────────────────────────────────
test("S3 hapus kelas planned tanpa member berhasil", async ({ page }) => {
    const id = getClassId("Kelas Kosong");
    await page.goto(`/classrooms/${id}`);

    // Hapus via form submit
    await page.evaluate((classId) => {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = `/classrooms/${classId}`;
        form.innerHTML = `<input name="_token" value="${document.querySelector("meta[name=csrf-token]")?.content ?? ""}">
                          <input name="_method" value="DELETE">`;
        document.body.appendChild(form);
        form.submit();
    }, id);

    await page.waitForURL("**/classrooms**");
    await expect(page.locator(".bg-green-100")).toContainText("berhasil dihapus");
});

// ── S4: Hapus kelas planned dengan member — ditolak ─────────────────────────
test("S4 hapus kelas planned dengan member ditolak", async ({ page }) => {
    const id = getClassId("Kelas Test Edit");
    await page.goto(`/classrooms/${id}`);

    await page.evaluate((classId) => {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = `/classrooms/${classId}`;
        form.innerHTML = `<input name="_token" value="${document.querySelector("meta[name=csrf-token]")?.content ?? ""}">
                          <input name="_method" value="DELETE">`;
        document.body.appendChild(form);
        form.submit();
    }, id);

    await expect(page.locator(".bg-red-100, [class*=error]")).toContainText("planned tanpa member");
});

// ── S5: Hapus kelas active — ditolak ────────────────────────────────────────
test("S5 hapus kelas active ditolak", async ({ page }) => {
    execSync(
        `php artisan tinker --execute 'App\\Models\\ClassRoom::where("class_name","Kelas Test Edit")->update(["status"=>"active"]); echo "done";'`,
        { cwd: "/Users/rismaniswaty/Documents/MINDSIA-V2/mindsia-v2" },
    );
    const id = getClassId("Kelas Test Edit");
    await page.goto(`/classrooms/${id}`);

    await page.evaluate((classId) => {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = `/classrooms/${classId}`;
        form.innerHTML = `<input name="_token" value="${document.querySelector("meta[name=csrf-token]")?.content ?? ""}">
                          <input name="_method" value="DELETE">`;
        document.body.appendChild(form);
        form.submit();
    }, id);

    await expect(page.locator(".bg-red-100, [class*=error]")).toContainText("planned tanpa member");

    // Reset ke planned
    execSync(
        `php artisan tinker --execute 'App\\Models\\ClassRoom::where("class_name","Kelas Test Edit")->update(["status"=>"planned"]); echo "done";'`,
        { cwd: "/Users/rismaniswaty/Documents/MINDSIA-V2/mindsia-v2" },
    );
});

// ── S6a: Aktivasi kelas planned → active ────────────────────────────────────
test("S6a aktivasi kelas planned ke active berhasil", async ({ page }) => {
    // Seed jadwal dulu (butuh schedules untuk activate)
    execSync(
        `php artisan tinker --execute '
            use App\\Models\\ClassRoom; use App\\Models\\ClassSchedule;
            $c = ClassRoom::where("class_name","Kelas Test Edit")->first();
            ClassSchedule::create(["class_id"=>$c->id,"schedule_date"=>"2026-11-03","start_time"=>"08:00","end_time"=>"10:00"]);
            echo "done";
        '`,
        { cwd: "/Users/rismaniswaty/Documents/MINDSIA-V2/mindsia-v2" },
    );
    const id = getClassId("Kelas Test Edit");
    await page.goto(`/classrooms/${id}`);

    await expect(page.locator("button", { hasText: "Aktifkan" })).toBeVisible();
    await page.locator("button", { hasText: "Aktifkan" }).click();
    // Confirm modal
    await page.locator("button", { hasText: "Ya, Lanjutkan" }).click();

    await expect(page.locator(".bg-green-100")).toContainText("berhasil diaktifkan");
    await expect(page.locator("text=Aktif")).toBeVisible();
});

// ── S6b: Nonaktifkan member ─────────────────────────────────────────────────
test("S6b nonaktifkan member dari kelas berhasil", async ({ page }) => {
    const id = getClassId("Kelas Test Edit");
    await page.goto(`/classrooms/${id}`);

    const deactBtn = page.locator("button", { hasText: "Nonaktifkan" }).first();
    await expect(deactBtn).toBeVisible();
    await deactBtn.click();
    await page.locator("button", { hasText: "Ya, Lanjutkan" }).click();

    await expect(page.locator(".bg-green-100")).toContainText("berhasil dinonaktifkan");
});

// ── S6c: Cancel kelas ───────────────────────────────────────────────────────
test("S6c cancel kelas berhasil", async ({ page }) => {
    const id = getClassId("Kelas Test Edit");
    await page.goto(`/classrooms/${id}`);

    await expect(page.locator("button", { hasText: "Batalkan Kelas" })).toBeVisible();
    await page.locator("button", { hasText: "Batalkan Kelas" }).click();
    await page.locator("button", { hasText: "Ya, Lanjutkan" }).click();

    await expect(page.locator(".bg-green-100")).toContainText("berhasil dibatalkan");
});
