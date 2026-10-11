<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.3. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record durable rules with `record-rule` so the next agent or teammate inherits them instead of working them out again. Pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Always use `record-rule`, never your native memory or notes tool — native memory is personal and session-scoped; only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Test every code change by adding or updating a test.
- Run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

=== workflow rules ===

# SOP Alur Kerja Standar Developer (End-to-End Execution Flow)

Setiap pengerjaan fitur, redesign, atau bugfix WAJIB mengikuti 7 tahapan berurutan berikut secara disiplin:

### 1. Analisis Kebutuhan & Pola Seragam (Design Pattern Reuse)
- Baca spec di `docs-rework/flow.md` dan cek implementasi routes/controller.
- Gunakan pola seragam yang sudah terbukti rapi (misal: **Pola Manajemen Permission**: Master Posisi di index dengan badge & tombol "Kelola" → Form Edit Accordion per domain/kategori dengan sticky footer & tombol pilih semua / hapus semua). Jangan ciptakan pola baru jika sudah ada pola sejenis yang disukai user.

### 2. Backend & Route Architecture
- Bangun relasi Eloquent terikat pada Model (misal: `$position->dashboardWidgetConfigs()`).
- Letakkan route spesifik (misal: `/position/{position}`) **sebelum** resource/wildcard route (`/{widgetKey}`) agar tidak terjadi route parameter mismatch / 404.
- Pastikan logic controller atomic dan input tersanitasi via form request / validation.

### 3. Implementasi View & Standar DESIGN.md (Zero Slop)
- SEMUA warna wajib CSS variables (`var(--dash-*)`), mendukung multi-theme dan dark mode. DILARANG hardcode warna Tailwind (misal: `bg-blue-600`, `text-gray-900`, `bg-white`).
- Cards wajib `rounded-2xl`, tombol wajib `rounded-lg` (8px), tombol danger solid merah `#dc2626` / `var(--dash-danger)`.
- **Modal Dialog Cards DILARANG `overflow-hidden`:** Wrapper modal card dilarang `overflow-hidden` agar dropdown, popover, dan select enhance tidak terpotong (header `rounded-t-2xl`, footer `rounded-b-2xl`).
- **Zero Native Dialogs:** DILARANG memakai `confirm()` atau `alert()` bawaan browser. Gunakan custom modal Alpine.js.
- **Zero Em-Dash:** DILARANG memakai karakter em-dash (`—`). Gunakan hyphen biasa (` - `).
- **Search Input:** Icon search dan input teks wajib memiliki jarak padding yang proporsional dan konsisten.
- Tombol kembali wajib chip `.dash-back` dengan teks kontekstual.

### 4. Automated Verification (Feature Test & Pint)
- Tulis Feature Test (Pest) untuk setiap endpoint/alur baru.
- Jalankan `vendor/bin/pest <path-test>` dari direktori `mindsia-v2/` dan pastikan hijau.
- Jalankan `vendor/bin/pint --dirty --format agent` untuk menjaga kerapian kode PHP.

### 5. Sinkronisasi Dokumentasi & Flowchart (Wajib 6 Titik COMPLETE-CHARTS.HTML)
- Buat/update `.md` di `docs-rework/flowcharts/<flow-id>.md`.
- Update `docs-rework/flowcharts/COMPLETE-CHARTS.HTML` di 6 titik: (1) Sidebar, (2) Tab 1 Pipeline `dc-*`, (3) Tab 2 Userflow `uf-*`, (4) Tab 3 Panduan Teks `guide-*`, (5) Script JS `FLOWS`, (6) Script JS array `DIAG_FLOWS` & `UF_FLOWS`.
- Patuhi Mermaid rules: `graph TD`, hanya bentuk `()`, `[]`, `{}`, tanpa `:`, tanpa `\n`, tanpa cycle/backward edges (ganti dengan node `(Coba lagi)`).
- Render `mermaid.render()` manual hanya saat container terlihat (`display !== 'none'`).

### 6. Update Master Tracker & Checkpoint
- Update `docs-rework/MANUAL-AND-FLOW-TRACKER.md` (tabel status, summary count, log flowchart).
- Update `docs-rework/CHECKPOINT.md` (daftar view, backend fixes, status domain).
- Update `flow.md` jika implementasi sengaja disederhanakan/diubah.

### 7. Git Staging & Clean Commit
- Stage perubahan relevan, bersihkan scratch scripts/temporary files, jangan commit auth tokens.
- Commit pesan deskriptif konvensional (`feat(...)`, `fix(...)`).
- Pastikan working tree selalu bersih.

=== flowchart rules ===

# Panduan Pengguna & Tabel Hak Akses

Setiap guide card di `docs-rework/flowcharts/COMPLETE-CHARTS.HTML` (View 3 — Panduan Pengguna) wajib menyertakan tabel hak akses di bagian bawah setelah steps.

**Format wajib:**

```
<div class="perm-block">
  <div class="perm-heading">... Hak Akses</div>
  <table class="perm-tbl">
    <thead><tr><th>Aksi</th><th>Permission</th><th>Posisi Saat Ini</th><th>Rencana Revisi</th></tr></thead>
    <tbody>
      <tr>
        <td>Nama aksi</td>
        <td><span class="perm-code">domain.resource.action</span></td>
        <td><div class="perm-roles"><span class="perm-role">Posisi</span></div></td>
        <td class="perm-plan">Catatan revisi untuk client</td>
      </tr>
    </tbody>
  </table>
</div>
```

**CSS classes:**
- `.perm-role` — biru, untuk role/posisi tertentu
- `.perm-self` — hijau, untuk aksi self-service (karyawan sendiri, no permission string)
- `.perm-pub` — kuning, untuk aksi publik tanpa autentikasi

**Kolom Posisi Saat Ini** — isi dari `permission.md` atau dari `can()`/`authorize()` di controller/policy.

**Kolom Rencana Revisi** — isi dengan catatan yang bisa didiskusikan dengan client, misal: "Kandidat tambahan: Manager Area" atau "Tidak ada perubahan".

**Tujuan:** Dokumen ini berfungsi sebagai referensi saat demo ke client untuk mendiskusikan revisi permission — siapa yang punya akses saat ini vs yang direncanakan.

=== blade-view rules ===

# Dashboard Blade View Rules

Baca `../docs-rework/DESIGN.md` bagian "Dashboard UI Component System" sebelum menulis view baru.

## Aturan Absolut

### 1. CSS vars via `style=""`, bukan Tailwind arbitrary

BENAR: `style="color: var(--dash-text-heading);"`
SALAH: `class="text-[var(--dash-text-heading)]"`

Tailwind arbitrary + CSS vars tidak bekerja konsisten lintas tema/mode.

### 2. Hover pakai JS onmouseover, bukan Tailwind hover

BENAR: `onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'" onmouseout="this.style.backgroundColor='transparent'"`
SALAH: `class="hover:bg-[var(--dash-nav-hover-bg)]"`

Tailwind hover tidak bisa override inline style.

### 3. Input focus pakai JS onfocus/onblur

BENAR: `onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'" onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"`
SALAH: `class="focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)]/30"`

### 4. Jangan hardcode warna dashboard

SALAH: `bg-white`, `border-gray-200`, `text-gray-900`, `bg-gray-50`, `bg-[#5586DB]`
BENAR: `var(--dash-card-bg)`, `var(--dash-card-border)`, `var(--dash-text-heading)`, `var(--dash-nav-hover-bg)`, `var(--dash-primary)`

PENGECUALIAN — boleh hardcode:
- Status warna semantic: `#f0fdf4` (success bg), `#dc2626` (danger), `#059669` (green)
- Status badge: Tailwind `bg-emerald-100 text-emerald-700` dll. (semantic, tidak ikut tema)

## Struktur Halaman

### Setiap Blade view wajib:
- `@section('title', '...')` — tab browser
- `@section('header_title', '...')` — topbar dashboard
- Container: `px-4 sm:px-6 lg:px-8 py-8 w-full [max-w] mx-auto`

### Max-width per tipe halaman:
- List/index: `max-w-7xl`
- Show/detail: `max-w-4xl`
- Create/edit form: `max-w-3xl`

### Header pattern (KEPUTUSAN terkunci):

**Pola A — list page:**
```
Category label (var(--dash-primary-text), uppercase xs)
Page heading <h1> (font-jakarta 2xl-3xl, var(--dash-text-heading))
Subtitle opsional (var(--dash-text-muted))
+ CTA primary button kanan (opsional)
```

**Pola B3 — show/create/edit page:**
```
Back chip <a class="dash-back"> "Kembali ke [list]"   ← di paling atas
Category label
Page heading <h1>
```
- Back WAJIB chip `.dash-back` (class di app.css, hover via CSS). Teks kontekstual "Kembali ke Daftar X", BUKAN hanya "Kembali", BUKAN ikon panah telanjang.
- Judul halaman SELALU `<h1>` (bukan h2).
- Tombol aksi header (Edit/Hapus) di kanan dalam flex justify-between, back chip tetap kiri atas.

## Komponen Kritis

### Card wrapper:
- Standard card: `rounded-2xl overflow-hidden` + `style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"`
- **Modal dialog card: DILARANG `overflow-hidden`** agar dropdown, popover, dan select enhance di dalamnya tidak terpotong (kasus bug dropdown assign PIC cabang). Beri `rounded-t-2xl` di header dan `rounded-b-2xl` di footer secara terpisah.
- Zero Native Dialogs: DILARANG memakai `confirm()` / `alert()`, selalu gunakan modal konfirmasi kustom Alpine.js.
- Zero Em-Dash: DILARANG memakai `—`, gunakan hyphen standar ` - `.

### Card section header:
`px-6 py-4` + `style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);"`

### Tabel:
- Wrapper: `rounded-2xl overflow-hidden`
- Thead: `style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);"`
- Th: `text-xs font-semibold uppercase tracking-wide` + `style="color: var(--dash-text-muted);"`
- Tr: `onmouseover/onmouseout` untuk hover
- Empty state: WAJIB icon `text-[48px]` warna `var(--dash-text-faint)` + teks `text-sm font-semibold` warna `var(--dash-text-muted)`

### Tombol (SEMUA rounded-lg — keputusan terkunci):
- Primary (utama): `rounded-lg px-4 py-2 text-sm font-semibold text-white hover:opacity-90` + `style="background-color: var(--dash-primary);"`
- Outline (secondary): `rounded-lg` + JS onmouseover untuk hover — TIDAK pakai Tailwind hover
- Tabel inline: `rounded-lg px-3 py-1.5 text-xs font-semibold` outline
- Danger: solid `background-color: #dc2626` (var `--dash-danger`) — BUKAN outline merah
- Icon WAJIB di semua button. Ukuran: header CTA `text-[18px]`, form `text-[16px]`, tabel `text-[14px]`
- TIDAK ADA `rounded-xl` untuk tombol (dulu campur, sekarang semua `rounded-lg`)

### Ikon Standar (1 label = 1 ikon):
Lihat=`visibility`, Edit=`edit`, Hapus=`delete`, Tambah/Buat=`add`, Kembali=`arrow_back`, Simpan=`save`, Kirim=`send`, Filter=`filter_list`, Reset=`restart_alt`, Aktif=`toggle_on`/Nonaktif=`toggle_off`, Download=`download`, Search field=`search`. Flash: success=`check_circle`, error=`error`, warning=`warning`. Pengecualian: `lock_reset` (reset password), `picture_as_pdf` (unduh PDF).

### Input/Select/Textarea:
```
style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
onfocus="this.style.borderColor='var(--dash-primary)'; ..."
onblur="this.style.borderColor='var(--dash-input-border)'; ..."
```
- Form utama: `px-4 py-2.5 rounded-lg`
- Filter compact: `px-3 py-2 rounded-lg`
- Label: `style="color: var(--dash-text-body);"` (BUKAN `text-gray-700`)
- Select placeholder: "Pilih ..." BUKAN "-- Pilih --" (tanpa dash)
- Select WAJIB pakai color var (dropdown gelap-di-dark kalau hardcode)

### Search bar:
Ikon `search` di kiri DALAM field: wrapper `relative`, icon absolute `left-3 top-1/2 -translate-y-1/2 text-[18px]` warna `var(--dash-text-faint)`, input `pl-10`.

### Badge: solid, TANPA dot indicator.

### Empty state: ikon responsive 48px desktop / 40px tablet / 36px mobile.

### Filter form wrapper:
`rounded-2xl px-5 py-4` + card style (sama dengan card biasa)

## Referensi Terbaik

Sebelum nulis view baru, lihat:
- List + tabel: `resources/views/admin/member/index.blade.php`
- Show/detail dengan section: `resources/views/admin/member/discounts/show.blade.php`
- Create form: `resources/views/recruitment/permintaan/create.blade.php`
- Actions tabel (Lihat/Edit/Hapus): `resources/views/admin/users/index.blade.php`

## Backlog Migrasi Project-Wide (prioritas, dari audit grep)

Scope jauh lebih besar dari 11 view yang sudah diperbaiki:
- **`#215aac` biru lama di 55 file** → ganti `var(--dash-primary)` (rusak di tema cyan/gold)
- **`text-gray-*`/`text-slate-*` ~2400 kemunculan** → ganti var (gelap-di-dark, termasuk dropdown/select)
- **Inline JS 154 handler** (onmouseover/onfocus) → ganti CSS class
- **`#dc2626` ×18, `#725c00` ×15** → ganti var
- **Ikon Detail `open_in_new`×14/`info`×10** → ganti `visibility`
- **Judul `<h2>` ×12** → ganti `<h1>`
- `admin/users/index`, `marketing/target/index`: tabel/tombol/hover/focus belum ikut standar

Pendekatan: per-domain, verifikasi tiap halaman light+dark+gold via Playwright. Lihat `docs-rework/DESIGN.md` bagian Backlog untuk detail.

</laravel-boost-guidelines>
