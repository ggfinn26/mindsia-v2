@extends('layouts.dashboard')

@section('title', 'Sosialisasi Saya')

@section('content')
@php
    $statusColors = [
        'draft'     => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]',
        'scheduled' => 'bg-blue-100 text-blue-700',
        'completed' => 'bg-green-100 text-green-700',
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
        'confirmed' => 'bg-green-100 text-green-700',
    ];
    $esStatusLabels = [
        'assigned'  => 'Belum Selesai',
        'confirmed' => 'Sudah Dikonfirmasi',
    ];
@endphp

<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[color:var(--dash-text-heading)]">Sosialisasi Saya</h1>
            <p class="text-sm text-[color:var(--dash-text-muted)] mt-1">Daftar sosialisasi yang Anda ditugaskan</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($socializations->isEmpty())
        <div class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-2xl shadow-sm p-12 text-center">
            <span class="material-symbols-outlined text-4xl text-gray-300">groups</span>
            <p class="mt-3 text-[color:var(--dash-text-muted)] text-sm">Belum ada sosialisasi yang ditugaskan ke Anda.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($socializations as $es)
                @php
                    $soc = $es->socialization;
                    $schedule = $soc->schedules->first();
                @endphp
                <a href="{{ route('my-socializations.show', $es) }}"
                    class="block bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl shadow-sm hover:shadow-md hover:border-indigo-200 transition-all p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="font-semibold text-[color:var(--dash-text-heading)] text-sm">
                                    {{ $soc->institution?->institution_name ?? $soc->location_name ?? '—' }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$soc->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' }}">
                                    {{ $statusLabels[$soc->status] ?? $soc->status }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $esStatusColors[$es->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' }}">
                                    {{ $esStatusLabels[$es->status] ?? $es->status }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-[color:var(--dash-text-muted)] flex-wrap">
                                @if ($schedule)
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">calendar_today</span>
                                        {{ \Carbon\Carbon::parse($schedule->schedule_date)->translatedFormat('d M Y') }}
                                        @if ($schedule->start_time)
                                            · {{ \Illuminate\Support\Str::substr($schedule->start_time, 0, 5) }}
                                            @if ($schedule->end_time)–{{ \Illuminate\Support\Str::substr($schedule->end_time, 0, 5) }}@endif
                                        @endif
                                    </span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">school</span>
                                    {{ $es->classes_obtained ?? 0 }} kelas
                                </span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-[color:var(--dash-text-faint)] shrink-0">chevron_right</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $socializations->links() }}
        </div>
    @endif
</div>
@endsection
