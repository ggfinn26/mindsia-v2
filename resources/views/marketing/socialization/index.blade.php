@extends('layouts.dashboard')

@section('title', 'Sosialisasi - MINDSIA')
@section('header_title', 'Sosialisasi')

@section('content')
@php
    $statusColors = [
        'draft'     => 'bg-gray-100 text-gray-700',
        'scheduled' => 'bg-blue-100 text-blue-700',
        'completed' => 'bg-emerald-100 text-emerald-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
    $statusLabels = [
        'draft'     => 'Draft',
        'scheduled' => 'Terjadwal',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
@endphp
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Pemasaran</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Sosialisasi</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola kegiatan sosialisasi dan calon anggota baru.
            </p>
        </div>
        @can('marketing.socialization.create')
        <a href="{{ route('socializations.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Buat Sosialisasi
        </a>
        @endcan
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- Tab View --}}
    @if ($canViewAll)
        <div class="flex gap-2 border-b" style="border-color: var(--dash-card-border);">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'mine', 'page' => 1]) }}"
               class="px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors focus:outline-none -mb-px inline-flex items-center gap-1.5"
               style="{{ $currentView === 'mine' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'border-color: transparent; color: var(--dash-text-muted);' }}">
                <span class="material-symbols-outlined text-[16px]">person</span>
                Milik Saya
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'all', 'page' => 1]) }}"
               class="px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors focus:outline-none -mb-px inline-flex items-center gap-1.5"
               style="{{ $currentView === 'all' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'border-color: transparent; color: var(--dash-text-muted);' }}">
                <span class="material-symbols-outlined text-[16px]">group</span>
                Semua
            </a>
        </div>
    @endif

    {{-- Filter Card --}}
    <form method="GET" action="{{ route('socializations.index') }}"
          class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @if ($canViewAll && request('view'))
            <input type="hidden" name="view" value="{{ request('view') }}">
        @endif
        <div class="w-full sm:w-56">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Status</label>
            <select name="status"
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'"
                    onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach ($statusLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                Filter
            </button>
            @if (request()->hasAny(['status', 'branch_id', 'area_id']))
                <a href="{{ route('socializations.index', $canViewAll && request('view') ? ['view' => request('view')] : []) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Tabel Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Lokasi / Institusi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Cabang</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Calon Anggota</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($socializations as $s)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5">
                                <p class="font-medium" style="color: var(--dash-text-heading);">{{ $s->location_name }}</p>
                                @if ($s->institution)
                                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $s->institution->institution_name }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap" style="color: var(--dash-text-body);">
                                {{ $s->branch->branch_name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap" style="color: var(--dash-text-body);">
                                {{ $s->schedules->min('schedule_date')?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $statusColors[$s->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$s->status] ?? $s->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap" style="color: var(--dash-text-body);">
                                {{ $s->prospective_members_count ?? $s->prospectiveMembers->count() }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                <a href="{{ route('socializations.show', $s) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">campaign</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data sosialisasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($socializations->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $socializations->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
