@extends('layouts.dashboard')

@section('title', 'Widget Dashboard')
@section('header_title', 'Widget Dashboard')

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
@endphp

<div class="max-w-6xl mx-auto space-y-6" x-data="{ tab: 'positions' }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider font-jakarta" style="color: var(--dash-nav-section);">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight mt-1" style="color: var(--dash-text-heading);">Widget Dashboard</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Atur widget dashboard mana yang tampil untuk setiap posisi atau jabatan.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex border-b" style="border-color: var(--dash-border-subtle);">
        <button @click="tab = 'positions'"
            :class="tab === 'positions' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80'"
            :style="tab === 'positions' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);'"
            class="px-5 py-3 text-sm transition-colors">
            Widget per Posisi
        </button>
        <button @click="tab = 'catalog'"
            :class="tab === 'catalog' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80'"
            :style="tab === 'catalog' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);'"
            class="px-5 py-3 text-sm transition-colors">
            Katalog Semua Widget
        </button>
    </div>

    {{-- Tab 1: Widget per Posisi (Identik dengan Manajemen Permission) --}}
    <div x-show="tab === 'positions'">
        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);">
            <div class="border-b px-5 py-3.5" style="border-color: var(--dash-border-subtle); background-color: var(--dash-page-bg);">
                <p class="text-xs font-semibold" style="color: var(--dash-text-muted);">Pilih posisi di bawah dan klik "Kelola" untuk mengaktifkan widget dan mengatur urutan tampilannya.</p>
            </div>
            <div class="divide-y" style="border-color: var(--dash-border-subtle);">
                @forelse ($positions as $position)
                    @php
                        $activeWidgetsCount = $position->dashboardWidgetConfigs->where('is_enabled', true)->count();
                    @endphp
                    <div class="flex items-center justify-between gap-4 px-5 py-4 transition-colors hover:bg-slate-50/50">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $position->position_name }}</p>
                            <div class="mt-0.5 flex items-center gap-2">
                                @if ($position->role)
                                    <span class="font-mono text-xs" style="color: var(--dash-text-muted);">role: {{ $position->role->name }}</span>
                                    <span class="text-xs text-slate-300">·</span>
                                @endif
                                @if ($activeWidgetsCount > 0)
                                    <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $activeWidgetsCount }} widget aktif</span>
                                @else
                                    <span class="text-xs italic" style="color: var(--dash-text-muted);">Belum ada widget aktif</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('system.dashboard-widgets.position.edit', $position) }}"
                            class="inline-flex shrink-0 min-h-9 items-center rounded-lg border px-3 text-xs font-semibold transition-colors hover:bg-slate-100/70"
                            style="border-color: var(--dash-border-subtle); color: var(--dash-text-heading);">
                            <span class="material-symbols-outlined text-[15px] mr-1" style="color: var(--dash-primary);">tune</span> Kelola
                        </a>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center text-sm" style="color: var(--dash-text-muted);">Belum ada posisi.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tab 2: Katalog Semua Widget --}}
    <div x-show="tab === 'catalog'" style="display:none;" class="space-y-6">
        @foreach ($widgetKeys as $category => $keys)
            <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);">
                <div class="border-b px-5 py-3.5" style="border-color: var(--dash-border-subtle); background-color: var(--dash-page-bg);">
                    <h2 class="font-jakarta text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">
                        {{ $categoryLabel[$category] ?? \App\Support\EnumLabels::get('widget_category', $category) }}
                    </h2>
                </div>
                <div class="divide-y" style="border-color: var(--dash-border-subtle);">
                    @foreach ($keys as $wk)
                        @php
                            $assigned = $byWidget->get($wk->value, collect());
                        @endphp
                        <div class="flex items-start gap-4 px-5 py-4 transition-colors hover:bg-slate-50/50">
                            <div class="flex min-w-0 flex-1 items-start gap-3">
                                <span class="material-symbols-outlined mt-0.5 text-[20px]" style="color: var(--dash-primary);">{{ $wk->icon() }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $wk->label() }}</p>
                                    <p class="font-mono text-xs" style="color: var(--dash-text-muted);">{{ $wk->value }}</p>
                                    @if ($assigned->isEmpty())
                                        <p class="mt-1.5 text-xs italic" style="color: var(--dash-text-muted);">Belum di-assign ke posisi manapun</p>
                                    @else
                                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                                            @foreach ($assigned->sortBy('order') as $cfg)
                                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cfg->is_enabled ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500 line-through' }}">
                                                    {{ $cfg->position?->position_name ?? 'Posisi Terhapus' }}
                                                    <span class="text-[10px] opacity-75">#{{ $cfg->order }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
