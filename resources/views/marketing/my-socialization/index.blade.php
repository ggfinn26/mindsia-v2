@extends('layouts.dashboard')

@section('title', 'Sosialisasi Saya - MINDSIA')
@section('header_title', 'Sosialisasi Saya')

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
    $esStatusColors = [
        'assigned'  => 'bg-yellow-100 text-yellow-700',
        'confirmed' => 'bg-emerald-100 text-emerald-700',
    ];
    $esStatusLabels = [
        'assigned'  => 'Belum Selesai',
        'confirmed' => 'Sudah Dikonfirmasi',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Pemasaran</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Sosialisasi Saya</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Daftar penugasan kegiatan sosialisasi yang ditugaskan ke Anda.
        </p>
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

    @if ($socializations->isEmpty())
        <div class="rounded-2xl p-12 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">groups</span>
            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada sosialisasi yang ditugaskan ke Anda.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($socializations as $es)
                @php
                    $soc = $es->socialization;
                    $schedule = $soc->schedules->first();
                @endphp
                <a href="{{ route('my-socializations.show', $es) }}"
                    class="block rounded-2xl p-5 transition-colors"
                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-1.5">
                                <span class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                                    {{ $soc->institution?->institution_name ?? $soc->location_name ?? '-' }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$soc->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$soc->status] ?? $soc->status }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $esStatusColors[$es->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $esStatusLabels[$es->status] ?? $es->status }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 text-xs flex-wrap" style="color: var(--dash-text-muted);">
                                @if ($schedule)
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                        {{ \Carbon\Carbon::parse($schedule->schedule_date)->translatedFormat('d M Y') }}
                                        @if ($schedule->start_time)
                                            · {{ \Illuminate\Support\Str::substr($schedule->start_time, 0, 5) }}
                                            @if ($schedule->end_time) - {{ \Illuminate\Support\Str::substr($schedule->end_time, 0, 5) }}@endif
                                        @endif
                                    </span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">school</span>
                                    {{ $es->classes_obtained ?? 0 }} kelas
                                </span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-[20px] shrink-0" style="color: var(--dash-text-faint);">chevron_right</span>
                    </div>
                </a>
            @endforeach
        </div>

        @if ($socializations->hasPages())
            <div class="pt-2">
                {{ $socializations->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
