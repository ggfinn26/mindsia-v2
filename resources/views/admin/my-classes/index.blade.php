@extends('layouts.dashboard')

@section('title', 'Kelas Saya')
@section('header_title', 'Kelas Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Akademik</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kelas Saya</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Sesi dan jadwal dari kelas yang Anda ampu sebagai tutor.
            </p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Tanggal & Waktu</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Kelas & Program</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Materi</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                            style="color: var(--dash-text-muted);">Presensi Siswa</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($schedules as $schedule)
                        @php
                            $isToday = $schedule->schedule_date?->isToday();
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border); {{ $isToday ? 'background-color: color-mix(in srgb, var(--dash-primary) 6%, transparent);' : '' }}"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='{{ $isToday ? 'color-mix(in srgb, var(--dash-primary) 6%, transparent)' : 'transparent' }}'">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-medium text-xs {{ $isToday ? 'font-semibold' : '' }}"
                                          style="color: {{ $isToday ? 'var(--dash-primary)' : 'var(--dash-text-heading)' }};">
                                        {{ $schedule->schedule_date?->format('d M Y') }}
                                    </span>
                                    @if($isToday)
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-full uppercase tracking-wider"
                                              style="background-color: color-mix(in srgb, var(--dash-primary) 15%, transparent); color: var(--dash-primary);">
                                            Hari ini
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] font-mono mt-0.5" style="color: var(--dash-text-muted);">
                                    {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                </p>
                            </td>
                            <td class="px-4 py-3 font-medium">
                                <a href="{{ route('classrooms.show', $schedule->classRoom) }}"
                                   class="font-semibold block hover:underline transition-colors text-xs"
                                   style="color: var(--dash-primary-text);">
                                    {{ $schedule->classRoom?->class_name ?? '-' }}
                                </a>
                                <p class="text-[11px] mt-0.5 flex items-center gap-1" style="color: var(--dash-text-muted);">
                                    <span>{{ $schedule->classRoom?->program?->program_name ?? '-' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $schedule->classRoom?->branch?->branch_name ?? '-' }}</span>
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs max-w-xs" style="color: var(--dash-text-body);">
                                <span class="line-clamp-2" title="{{ $schedule->material_taught ?? '-' }}">
                                    {{ $schedule->material_taught ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="inline-flex items-center justify-center gap-1">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-xs font-semibold rounded-md"
                                          style="background-color: color-mix(in srgb, #16a34a 12%, transparent); color: #16a34a;" title="Hadir">
                                        <span class="text-[10px] opacity-75">H:</span>{{ $schedule->present_count }}
                                    </span>
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-xs font-semibold rounded-md"
                                          style="background-color: color-mix(in srgb, var(--dash-danger) 12%, transparent); color: var(--dash-danger);" title="Absen">
                                        <span class="text-[10px] opacity-75">A:</span>{{ $schedule->absent_count }}
                                    </span>
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-xs font-semibold rounded-md"
                                          style="background-color: color-mix(in srgb, #d97706 12%, transparent); color: #d97706;" title="Terlambat">
                                        <span class="text-[10px] opacity-75">T:</span>{{ $schedule->late_count }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <a href="{{ route('classrooms.show', $schedule->classRoom) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                    Buka
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">school</span>
                                <h3 class="font-jakarta text-base font-semibold" style="color: var(--dash-text-heading);">Anda belum mengampu kelas aktif apapun.</h3>
                                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Jadwal sesi akan muncul setelah Anda ditugaskan pada kelas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
