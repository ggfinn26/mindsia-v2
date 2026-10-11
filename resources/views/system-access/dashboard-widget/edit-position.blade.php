@extends('layouts.dashboard')

@section('title', 'Widget Dashboard: ' . $position->position_name)
@section('header_title', 'Konfigurasi Widget Dashboard')

@section('content')
@php
    $categoryLabel = [
        'financial'    => 'Finansial & Bisnis',
        'operations'   => 'Operasional',
        'hr'           => 'HR & Rekrutmen',
        'marketing'    => 'Marketing & Growth',
        'finance_admin'=> 'Finance & Administrasi',
        'personal'     => 'Personal Performance',
        'analytics'    => 'Analitik & Insight',
        'system'       => 'Sistem & Administrasi',
    ];
    $totalAvailable = $widgetKeys->flatten()->count();
    $activeCount = $assignedConfigs->where('is_enabled', true)->count();
@endphp

<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header & Back Link --}}
    <div>
        <a href="{{ route('system.dashboard-widgets.index') }}" class="dash-back inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-medium mb-3 transition-colors"
           style="border-color: var(--dash-border-subtle); color: var(--dash-text-muted); background-color: var(--dash-card-bg);">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Widget Dashboard
        </a>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $position->position_name }}</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            @if ($position->role)
                Role: <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $position->role->name }}</span> ·
            @endif
            {{ $activeCount }} dari {{ $totalAvailable }} widget aktif untuk posisi ini
        </p>
    </div>

    <form method="POST" action="{{ route('system.dashboard-widgets.position.update', $position) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            @foreach ($widgetKeys as $category => $keys)
                @php
                    $categoryHasActive = $keys->contains(fn($wk) => isset($assignedConfigs[$wk->value]) && $assignedConfigs[$wk->value]->is_enabled);
                    $categoryActiveCount = $keys->filter(fn($wk) => isset($assignedConfigs[$wk->value]) && $assignedConfigs[$wk->value]->is_enabled)->count();
                @endphp
                <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);"
                     x-data="{ open: {{ $categoryHasActive ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                        class="flex w-full items-center justify-between border-b px-5 py-3.5 text-left transition-colors"
                        style="border-color: var(--dash-border-subtle); background-color: var(--dash-page-bg);">
                        <div class="flex items-center gap-2">
                            <h2 class="font-jakarta text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">
                                {{ $categoryLabel[$category] ?? ucfirst($category) }}
                            </h2>
                            @if ($categoryActiveCount > 0)
                                <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $categoryActiveCount }}/{{ $keys->count() }}</span>
                            @else
                                <span class="text-xs" style="color: var(--dash-text-muted);">{{ $keys->count() }} tersedia</span>
                            @endif
                        </div>
                        <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-text-muted);" x-text="open ? 'expand_less' : 'expand_more'"></span>
                    </button>
                    <div x-show="open" class="divide-y px-5 py-2" style="border-color: var(--dash-border-subtle);">
                        <div class="flex justify-end gap-3 py-2">
                            <button type="button"
                                @click="$el.closest('[x-data]').querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = true)"
                                class="text-xs font-semibold hover:underline" style="color: var(--dash-primary);">Pilih semua</button>
                            <button type="button"
                                @click="$el.closest('[x-data]').querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false)"
                                class="text-xs font-semibold hover:underline" style="color: var(--dash-text-muted);">Hapus semua</button>
                        </div>
                        @foreach ($keys as $wk)
                            @php
                                $cfg = $assignedConfigs[$wk->value] ?? null;
                                $isEnabled = $cfg ? (bool) $cfg->is_enabled : false;
                                $order = $cfg ? $cfg->order : 1;
                            @endphp
                            <div class="flex items-center justify-between gap-4 py-3 transition-colors hover:bg-slate-50/70" x-data="{ checked: {{ $isEnabled ? 'true' : 'false' }} }">
                                <label class="flex cursor-pointer items-start gap-3 min-w-0 flex-1">
                                    <input type="checkbox" name="widgets[{{ $wk->value }}][enabled]" value="1"
                                        x-model="checked"
                                        class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">{{ $wk->icon() }}</span>
                                            <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $wk->label() }}</p>
                                        </div>
                                        <p class="font-mono text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $wk->value }}</p>
                                    </div>
                                </label>
                                <div class="flex items-center gap-2 shrink-0" x-show="checked">
                                    <span class="text-xs" style="color: var(--dash-text-muted);">Urutan:</span>
                                    <input type="number" name="widgets[{{ $wk->value }}][order]" value="{{ $order }}" min="1" max="99"
                                        class="w-16 rounded-lg text-xs text-center py-1.5 transition-colors focus:ring-1"
                                        style="border-color: var(--dash-border-subtle); background-color: var(--dash-card-bg); color: var(--dash-text-heading);">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="sticky bottom-0 mt-6 flex justify-end gap-3 border-t p-4 rounded-2xl shadow-lg"
             style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);">
            <a href="{{ route('system.dashboard-widgets.index') }}"
                class="inline-flex min-h-10 items-center rounded-lg border px-5 text-sm font-semibold transition-colors hover:bg-slate-100/70"
                style="border-color: var(--dash-border-subtle); color: var(--dash-text-heading);">Batal</a>
            <button type="submit"
                class="inline-flex min-h-10 items-center rounded-lg px-6 text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
                style="background-color: var(--dash-primary);">Simpan Widget</button>
        </div>
    </form>
</div>
@endsection
