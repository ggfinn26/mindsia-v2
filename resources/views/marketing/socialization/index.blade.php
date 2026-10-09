@extends('layouts.dashboard')

@section('title', 'Sosialisasi')
@section('header_title', 'Sosialisasi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Sosialisasi</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Kelola jadwal dan data prospek dari kegiatan sosialisasi.
            </p>
        </div>
        @can('marketing.socialization.create')
        <a href="{{ route('socializations.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Buat Sosialisasi
        </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #a7f3d0; background-color: #ecfdf5; color: #065f46;">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #fecaca; background-color: #fef2f2; color: #991b1b;">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Tab --}}
    @if ($canViewAll)
        <div class="flex gap-4 mb-2 border-b" style="border-color: var(--dash-card-border);">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'mine', 'page' => 1]) }}"
               class="px-2 py-3 text-sm font-semibold border-b-2 transition-colors focus:outline-none -mb-px
                    {{ $currentView === 'mine' ? 'border-[var(--dash-primary)]' : 'border-transparent hover:opacity-75' }}"
               style="{{ $currentView === 'mine' ? 'color: var(--dash-primary);' : 'color: var(--dash-text-muted);' }}">
                Milik Saya
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'all', 'page' => 1]) }}"
               class="px-2 py-3 text-sm font-semibold border-b-2 transition-colors focus:outline-none -mb-px
                    {{ $currentView === 'all' ? 'border-[var(--dash-primary)]' : 'border-transparent hover:opacity-75' }}"
               style="{{ $currentView === 'all' ? 'color: var(--dash-primary);' : 'color: var(--dash-text-muted);' }}">
                Semua
            </a>
        </div>
    @endif

    {{-- Filter --}}
    <div class="rounded-2xl p-4" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" action="{{ route('socializations.index') }}" class="flex flex-wrap items-end gap-4">
            @if(request()->has('view'))
                <input type="hidden" name="view" value="{{ request('view') }}">
            @endif
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Status</label>
                <select name="status" onchange="this.form.submit()" class="rounded-lg px-3 py-2 text-sm focus:outline-none w-48"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                    <option value="">Semua Status</option>
                    @php
                        $statusLabels = [
                            'draft'     => 'Draft',
                            'scheduled' => 'Terjadwal',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                        ];
                    @endphp
                    @foreach ($statusLabels as $val => $label)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if (request()->hasAny(['status', 'branch_id', 'area_id']))
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('socializations.index', ['view' => request('view')]) }}"
                       class="inline-flex items-center gap-1 text-xs font-medium transition-opacity hover:opacity-75"
                       style="color: var(--dash-text-muted);">
                        <span class="material-symbols-outlined text-[14px]">close</span>Reset
                    </a>
                </div>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Lokasi / Institusi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Cabang</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Prospek</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($socializations as $s)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $s->location_name }}</p>
                                @if ($s->institution)
                                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $s->institution->institution_name }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">
                                {{ $s->branch->branch_name ?? '-' }}
                            </td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">
                                {{ $s->schedules->min('schedule_date')?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $statusColors = [
                                        'draft'     => 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);',
                                        'scheduled' => 'background-color: #eff6ff; color: #1d4ed8;', // blue
                                        'completed' => 'background-color: #f0fdf4; color: #059669;', // emerald
                                        'cancelled' => 'background-color: #fef2f2; color: #991b1b;', // red
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold" style="{{ $statusColors[$s->status] ?? $statusColors['draft'] }}">
                                    {{ $statusLabels[$s->status] ?? $s->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center tabular-nums" style="color: var(--dash-text-body);">
                                {{ $s->prospective_members_count ?? $s->prospectiveMembers->count() }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('socializations.show', $s) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">campaign</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data sosialisasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($socializations->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $socializations->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
