@extends('layouts.dashboard')

@section('title', 'Kehadiran & Nilai - MINDSIA')
@section('header_title', 'Kehadiran & Nilai')

@php
    $statusLabel = [
        'present'    => 'Hadir',
        'late'       => 'Terlambat',
        'absent'     => 'Tidak Hadir',
        'excused'    => 'Izin',
        'sick'       => 'Sakit',
        'unrecorded' => 'Belum Dicatat',
        'scheduled'  => 'Terjadwal',
    ];

    $statusStyle = [
        'present'    => 'background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid var(--dash-card-border);',
        'late'       => 'background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text); border: 1px solid var(--dash-card-border);',
        'absent'     => 'background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text); border: 1px solid var(--dash-card-border);',
        'excused'    => 'background-color: var(--dash-status-info-bg); color: var(--dash-status-info-text); border: 1px solid var(--dash-card-border);',
        'sick'       => 'background-color: rgba(168, 85, 247, 0.12); color: #9333ea; border: 1px solid var(--dash-card-border);',
        'unrecorded' => 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-faint); border: 1px solid var(--dash-card-border);',
        'scheduled'  => 'background-color: var(--dash-card-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);',
    ];
@endphp

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Portal Siswa</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Kehadiran & Nilai</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Rekap kehadiran dan nilai per pertemuan untuk semua kelas aktif Anda.
        </p>
    </div>

    @if($classes->isEmpty())
    {{-- Empty State Card --}}
    <div class="rounded-2xl p-12 flex flex-col items-center justify-center text-center shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4"
             style="background-color: var(--dash-status-info-bg);">
            <span class="material-symbols-outlined text-3xl" style="color: var(--dash-primary);">analytics</span>
        </div>
        <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Belum Ada Data</h3>
        <p class="text-sm max-w-sm mx-auto mb-6" style="color: var(--dash-text-muted);">
            Data tersedia setelah kelas aktif dan jadwal pertama ditambahkan.
        </p>
        <a href="{{ route('member.dashboard') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Dashboard
        </a>
    </div>

    @else
    @foreach($classes as $class)
    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        {{-- Class header --}}
        <div class="p-5 sm:p-6" style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                         style="background-color: var(--dash-status-info-bg);">
                        <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">menu_book</span>
                    </div>
                    <div>
                        <h3 class="font-bold font-jakarta leading-tight" style="color: var(--dash-text-heading);">
                            {{ $class['class_name'] }}
                        </h3>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                            {{ $class['program_name'] }} &middot; {{ $class['tutor_name'] }}
                        </p>
                    </div>
                </div>

                @if($class['summary']['total'] > 0)
                <div class="flex items-center gap-4 sm:gap-6 text-center">
                    <div>
                        <div class="text-lg font-bold font-jakarta"
                             style="color: {{ $class['summary']['rate'] >= 75 ? 'var(--dash-status-success-text)' : 'var(--dash-status-danger-text)' }};">
                            {{ $class['summary']['rate'] }}%
                        </div>
                        <div class="text-[11px] font-semibold" style="color: var(--dash-text-muted);">Kehadiran</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold font-jakarta" style="color: var(--dash-text-heading);">
                            {{ $class['summary']['attended'] }}
                        </div>
                        <div class="text-[11px] font-semibold" style="color: var(--dash-text-muted);">Hadir</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold font-jakarta" style="color: var(--dash-text-heading);">
                            {{ $class['summary']['absent'] }}
                        </div>
                        <div class="text-[11px] font-semibold" style="color: var(--dash-text-muted);">Tidak Hadir</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold font-jakarta" style="color: var(--dash-text-heading);">
                            {{ $class['summary']['total'] }}
                        </div>
                        <div class="text-[11px] font-semibold" style="color: var(--dash-text-muted);">Sesi</div>
                    </div>
                </div>
                @endif
            </div>

            @if($class['summary']['total'] > 0)
            <div class="mt-4">
                <div class="w-full rounded-full h-1.5 overflow-hidden"
                     style="background-color: var(--dash-nav-hover-bg);">
                    <div class="h-1.5 rounded-full transition-all"
                         style="width: {{ $class['summary']['rate'] }}%; background-color: {{ $class['summary']['rate'] >= 75 ? 'var(--dash-status-success-text)' : 'var(--dash-status-danger-text)' }};"></div>
                </div>
                @if($class['summary']['rate'] < 75)
                <p class="text-xs font-medium mt-1.5" style="color: var(--dash-status-danger-text);">
                    Kehadiran di bawah 75% - perhatikan syarat kelulusan.
                </p>
                @endif
            </div>
            @endif
        </div>

        {{-- Column headers --}}
        <div class="grid grid-cols-12 px-5 sm:px-6 py-2.5 text-xs font-bold uppercase tracking-wider"
             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
            <div class="col-span-1">#</div>
            <div class="col-span-5">Pertemuan</div>
            <div class="col-span-3">Materi</div>
            <div class="col-span-2 text-center">Status</div>
            <div class="col-span-1 text-right">Nilai</div>
        </div>

        {{-- Session rows --}}
        @if($class['sessions']->isEmpty())
        <div class="p-8 text-center text-sm" style="color: var(--dash-text-faint);">
            Belum ada jadwal pertemuan.
        </div>
        @else
        <div class="divide-y" style="border-color: var(--dash-card-border);">
            @foreach($class['sessions'] as $i => $session)
            @php $isFuture = $session['status'] === 'scheduled'; @endphp
            <div class="grid grid-cols-12 items-center px-5 sm:px-6 py-3.5 transition-colors {{ $isFuture ? 'opacity-60' : '' }}"
                 onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                 onmouseout="this.style.backgroundColor='transparent'">
                {{-- Number --}}
                <div class="col-span-1">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <span class="text-xs font-bold" style="color: var(--dash-text-muted);">{{ $i + 1 }}</span>
                    </div>
                </div>

                {{-- Date + time --}}
                <div class="col-span-5">
                    <div class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                        {{ $session['date']->translatedFormat('d M Y') }}
                    </div>
                    <div class="text-xs" style="color: var(--dash-text-muted);">{{ $session['time'] }}</div>
                    @if($session['notes'])
                    <div class="text-xs italic mt-0.5" style="color: var(--dash-text-muted);">{{ $session['notes'] }}</div>
                    @endif
                </div>

                {{-- Material --}}
                <div class="col-span-3">
                    <span class="text-xs line-clamp-2" style="color: var(--dash-text-body);">{{ $session['material'] ?? '-' }}</span>
                </div>

                {{-- Status badge --}}
                <div class="col-span-2 flex justify-center">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-md whitespace-nowrap shadow-xs"
                          style="{{ $statusStyle[$session['status']] ?? $statusStyle['unrecorded'] }}">
                        {{ $statusLabel[$session['status']] ?? '-' }}
                    </span>
                </div>

                {{-- Score --}}
                <div class="col-span-1 text-right">
                    @if($session['score'] !== null)
                        <span class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $session['score'] }}</span>
                    @else
                        <span class="text-xs" style="color: var(--dash-text-faint);">-</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
    @endforeach
    @endif

</div>
@endsection
