# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**MINDSIA v2** — HR/Employee Management system. 18 domains (attendance, auth, bonus, class, curriculum, employee, facility, finance, kpi, letter, marketing, member, notification, organization, payroll, recruitment, survey, toefl) built iteratively following architecture blueprint.

**Source of truth files** (outside mindsia-v2 repo, under `docs-rework/`):
- `@../docs-rework/flow.md` — **SOURCE OF TRUTH for all flows** — traces logic across domains, marks what stays/changes/breaks
- `@../docs-rework/schema.sql` — database schema (defines structure before code)
- `@../docs-rework/permission.md` — authorization model
- `@../docs-rework/sidebar-design.md` — UI/sidebar structure
- `@../docs-rework/docx-to-pdf.md` — document generation flows (Word→PDF)
- `@../docs-rework/DECISION-TELEGRAM-STORAGE.md` — architectural decision: Telegram as storage
- `@../docs-rework/dashboard-widgets.md` — dashboard component list
- `@../docs-rework/CHECKPOINT.md` — progress tracking per domain

**Domain skeleton** (each domain has skeleton under `docs-rework/{domain-name}/`):
- `Model/*.php` — entity spec + method intent
- `Controller/*.php` — controller skeleton
- `Helper/*.php` (Form Requests) — validation & authorization skeleton
- `.md` file — business rules, fields, relations, access control

## Architecture & Domain Navigation

**Entry points:**
- Models: `app/Models/` (domain-grouped)
- Controllers: `app/Http/Controllers/` (domain-grouped)
- Requests (Form Validation): `app/Http/Requests/` (domain/Helper folders)
- Routes: `routes/web.php` + API routes under `routes/`

**Before implementing in a domain:**
1. Read `docs-rework/<domain>/<domain>.md` — entity fields, relations, business rules, access control
2. Read `docs-rework/<domain>/Model/*.php` skeleton — method intent
3. Check `docs-rework/CHECKPOINT.md` — current phase (Markdown/Skeleton/Backend/Tests/Views)

Domains fully documented: check CHECKPOINT.md progress table for status.

## Development Workflow & Execution SOP

Setiap pengerjaan fitur, redesign, atau bugfix WAJIB mengikuti 7 tahapan berurutan berikut secara disiplin:

1. **Analisis Kebutuhan & Pola Seragam (Design Pattern Reuse):**
   - Baca spec di `docs-rework/flow.md` dan cek implementasi routes/controller.
   - Gunakan pola seragam yang sudah terbukti rapi (misal: **Pola Manajemen Permission**: Master Posisi di index dengan badge & tombol "Kelola" → Form Edit Accordion per domain/kategori dengan sticky footer & tombol pilih semua / hapus semua). Jangan ciptakan pola baru jika sudah ada pola sejenis yang disukai user.

2. **Backend & Route Architecture:**
   - Bangun relasi Eloquent terikat pada Model (misal: `$position->dashboardWidgetConfigs()`).
   - Letakkan route spesifik (misal: `/position/{position}`) **sebelum** resource/wildcard route (`/{widgetKey}`) agar tidak terjadi route parameter mismatch / 404.
   - Pastikan logic controller atomic dan input tersanitasi.

3. **Implementasi View & Standar DESIGN.md (Zero Slop):**
   - SEMUA warna wajib CSS variables (`var(--dash-*)`), mendukung multi-theme dan dark mode. Dilarang hardcode warna Tailwind (misal: `bg-blue-600`, `text-gray-900`, `bg-white`).
   - Cards wajib `rounded-2xl`, tombol wajib `rounded-lg` (8px), tombol danger solid merah `#dc2626` / `var(--dash-danger)`.
   - **Modal dialog card DILARANG `overflow-hidden`:** Wrapper modal card dilarang `overflow-hidden` agar dropdown/popover/select enhance tidak terpotong. Header `rounded-t-2xl`, footer `rounded-b-2xl`.
   - **Zero Native Dialogs:** DILARANG memakai `confirm()` atau `alert()` bawaan browser. Gunakan custom modal Alpine.js.
   - **Zero Em-Dash:** DILARANG memakai `—`. Gunakan hyphen biasa (` - `).
   - **Search Input:** Icon search dan input teks wajib memiliki jarak padding yang proporsional dan konsisten.
   - Tombol kembali wajib chip `.dash-back` dengan teks kontekstual.

4. **Automated Verification (Feature Test & Pint):**
   - Tulis Feature Test (Pest) untuk setiap endpoint/alur baru.
   - Jalankan `vendor/bin/pest <path-test>` dari direktori `mindsia-v2/` dan pastikan hijau.
   - Jalankan `vendor/bin/pint --dirty --format agent` untuk menjaga kerapian kode PHP.

5. **Sinkronisasi Dokumentasi & Flowchart (Wajib 6 Titik COMPLETE-CHARTS.HTML):**
   - Buat/update `.md` di `docs-rework/flowcharts/<flow-id>.md`.
   - Update `docs-rework/flowcharts/COMPLETE-CHARTS.HTML` di 6 titik: (1) Sidebar, (2) Tab 1 Pipeline `dc-*`, (3) Tab 2 Userflow `uf-*`, (4) Tab 3 Panduan Teks `guide-*`, (5) Script JS `FLOWS`, (6) Script JS array `DIAG_FLOWS` & `UF_FLOWS`.
   - Patuhi Mermaid rules: `graph TD`, hanya bentuk `()`, `[]`, `{}`, tanpa `:`, tanpa `\n`, tanpa cycle/backward edges (ganti dengan node `(Coba lagi)`).
   - Render `mermaid.render()` manual hanya saat container terlihat (`display !== 'none'`).

6. **Update Master Tracker & Checkpoint:**
   - Update `docs-rework/MANUAL-AND-FLOW-TRACKER.md` (tabel status, summary count, log flowchart).
   - Update `docs-rework/CHECKPOINT.md` (daftar view, backend fixes, status domain).
   - Update `flow.md` jika implementasi sengaja disederhanakan/diubah.

7. **Git Staging & Clean Commit:**
   - Stage perubahan relevan, bersihkan scratch scripts/temporary files, jangan commit auth tokens.
   - Commit pesan deskriptif konvensional (`feat(...)`, `fix(...)`).
   - Pastikan working tree selalu bersih.

## Framework & Tooling

**Laravel 8.3** + Vite/Tailwind frontend.

### Build & Dev

```bash
composer run dev   # Start dev server (Laravel's artisan dev)
npm run build      # Build Vite assets
npm run dev        # Watch mode (if running separately)
composer run setup # Full setup: install deps, migrate, build
```

### Testing

```bash
composer run test                    # Run full test suite
php artisan test --compact           # Run with compact output
php artisan test <path-or-filter>    # Run specific tests
vendor/bin/pest --filter=testName    # Pest runner directly
```

Framework uses **Pest** for testing. Create tests with `php artisan make:test --pest TestName` (no Feature/ prefix in name). See `.claude/skills/testing-best-practices/SKILL.md` for test structure and best practices.

### Code Style

**PHP**: Formatted by Laravel Pint. After editing PHP files, run:

```bash
vendor/bin/pint --dirty --format agent
```

AGENTS.md defines PHP style rules: constructor property promotion, type hints everywhere, TitleCase Enums, PHPDoc > inline comments, array shape types.

### Skills (Already Installed)

Activate when entering relevant domain:
- `@.claude/skills/laravel-best-practices/SKILL.md` — Laravel patterns, security, performance, database queries
- `@.claude/skills/testing-best-practices/SKILL.md` — test structure, naming, isolation, assertions
- `@.claude/skills/tailwindcss-development/SKILL.md` — Tailwind class composition
- `@.claude/skills/infer-conventions/SKILL.md` — identify project patterns from code

## Existing Guidelines

Full Laravel Boost guidelines in `@AGENTS.md` — includes Artisan, Tinker, model creation, routing, APIs, testing, Vite, Pint, migrations, Pest. Refer there for framework-specific patterns.

## Repository Etiquette

- Follow AGENTS.md: use Artisan for scaffolding (`php artisan make:*`), pass `--no-interaction`, confirm package versions before using APIs
- Use `database-query` Boost tool for read-only queries instead of manual SQL
- Use `search-docs` Boost tool for framework APIs and version-specific syntax
- Do not delete test files or models without approval — they are part of the application
- Commit message style: subject line under 70 chars, describe *why*, not what. Reference domain when relevant (e.g., "auth: add 2FA verification step")

## Dashboard Theme System

**Design source of truth:** `@../docs-rework/DESIGN.md` — palette, dials, token values, dan **full UI component system**. Baca sebelum menulis atau mengubah Blade view apapun.
**Mockup reference:** `docs-rework/mockup-palette.html` — 10 tab preview semua kombinasi tema.

**CSS vars:** prefix `--dash-*` di `resources/css/app.css`. Jangan hardcode warna dashboard — pakai vars.
**3 tema:** `theme-blue` (#5586DB, default karyawan), `theme-cyan` (#00AACC, alt karyawan), `theme-gold` (#DBB940, direksi — guard dinonaktifkan saat dev).
**2 mode:** `mode-light` (default), `mode-dark`. Applied via class di `<html>`: `theme-blue mode-light`.
**Switcher:** `dashTheme()` Alpine component di `app.js` — localStorage persistence. UI: 3 titik warna + toggle di `layouts/sidebar/footer.blade.php`.
**Komponen yang sudah pakai vars:** `nav-item.blade.php`, `nav-group.blade.php`, `sidebar/footer.blade.php`, `layouts/dashboard.blade.php`.

**Env gotcha:** `APP_URL` harus `http://localhost:8000` (dengan scheme+port) — tanpa ini `route()` generate URL tanpa port, fetch JS login gagal.

## Dashboard Blade View Rules (WAJIB)

Sebelum menulis view baru atau mengubah yang ada, baca `../docs-rework/DESIGN.md` bagian "Dashboard UI Component System". Rules ringkasnya:

### Anti-slop view rules
1. **Warna via `style=""` bukan Tailwind arbitrary** — `style="color: var(--dash-text-heading);"` bukan `class="text-[var(--dash-text-heading)]"`
2. **Hover pakai JS onmouseover** — bukan `hover:bg-[var(...)]` karena Tailwind hover tidak override inline style
3. **Focus input pakai JS onfocus/onblur** — bukan `focus:border-[var(--dash-primary)] focus:ring-1`
4. **Jangan `bg-white`, `border-gray-200`, `text-gray-900`** — pakai `var(--dash-card-bg)`, `var(--dash-card-border)`, `var(--dash-text-heading)`
5. **Setiap view wajib set** `@section('title')` dan `@section('header_title')`

### Komponen standar (pattern exact — jangan deviate)

**Page header (KEPUTUSAN terkunci):** List = pola A, Show/Create/Edit = pola B3 (back chip).

**Pola A — list page:**
```blade
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Domain</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Judul</h1>
        </div>
        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0" style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>Tambah
        </button>
    </div>
```

**Pola B3 — show/create/edit page (back chip kontekstual):**
```blade
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">
    <div>
        <a href="{{ route('...index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar X
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Domain</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Judul</h1>
    </div>
```
Back WAJIB chip `.dash-back` (sudah di app.css, hover via CSS) + teks kontekstual "Kembali ke [list]". JANGAN ikon panah telanjang. Judul halaman SELALU `<h1>` (bukan h2).

**Card/tabel wrapper:** `rounded-2xl overflow-hidden` + `style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"`

**Tabel header:** `style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);"` — bukan `bg-gray-50`

**Tabel row hover:** `onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'" onmouseout="this.style.backgroundColor='transparent'"` — wajib

**Tombol:** SEMUA `rounded-lg` (8px, keputusan terkunci — tidak ada rounded-xl lagi). Tabel inline `px-3 py-1.5 text-xs`, utama `px-4 py-2`. **Hapus:** solid `#dc2626` — BUKAN outline merah. Semua tombol WAJIB ikon.

**Ikon standar (1 label = 1 ikon):** Lihat=`visibility`, Edit=`edit`, Hapus=`delete`, Tambah/Buat=`add`, Kembali=`arrow_back`, Simpan=`save`, Kirim=`send`, Filter=`filter_list`, Reset=`restart_alt`, Aktif/Nonaktif=`toggle_on`/`toggle_off`, Download=`download`, Search field=`search`. Flash: success=`check_circle`, error=`error`, warning=`warning`.

**Input focus:** `onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'" onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"`. Select placeholder "Pilih ..." BUKAN "-- Pilih --".

**Search bar:** ikon `search` di kiri DALAM field (`pl-10` + absolute icon), wajib.

**Badge:** solid, TANPA dot.

**Empty state di tabel:** icon responsive (48px desktop / 40px tablet / 36px mobile) warna `var(--dash-text-faint)` + teks `text-sm font-semibold` warna `var(--dash-text-muted)` — WAJIB ada.

**Warna teks:** SELALU var — JANGAN `text-gray-*`/`text-slate-*` (gelap-di-dark, termasuk select/dropdown).

**Max-width:** list = `max-w-7xl`, show/detail = `max-w-4xl`, create/edit form = `max-w-3xl`

**Showcase:** `docs-rework/component-showcase.html` — referensi visual semua komponen + keputusan.

### Referensi terbaik (lihat dulu sebelum nulis)
- List table: `resources/views/admin/member/index.blade.php`
- CRUD actions: `resources/views/admin/users/index.blade.php`
- Detail/show: `resources/views/admin/member/discounts/show.blade.php`
- Form create: `resources/views/recruitment/permintaan/create.blade.php`

## Environment & Secrets

`.env` — not committed. Copy from `.env.example`. See `.env.example` for required keys.

Storage: `/storage` contains app data, logs, uploads (see `.gitignore`).

## Common Commands

```bash
# Artisan inspection
php artisan route:list              # List all routes
php artisan route:list --method=GET # Filter by HTTP method
php artisan config:show app.name    # Read config value
php artisan list                    # Discover available commands

# Database
php artisan migrate # Run pending migrations
php artisan tinker  # PHP REPL in app context (use single quotes)

# Development
php artisan serve   # Dev server (if not using artisan dev)
php artisan horizon # Queue dashboard (if Redis/queues enabled)
```

---

**Last updated: 2026-09-11** | Refer to AGENTS.md for full Laravel ecosystem guidance.

===

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

</laravel-boost-guidelines>
