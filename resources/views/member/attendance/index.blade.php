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
    $statusColor = [
        'present'    => 'bg-green-50 text-green-700 border-green-200',
        'late'       => 'bg-amber-50 text-amber-700 border-amber-200',
        'absent'     => 'bg-red-50 text-red-600 border-red-200',
        'excused'    => 'bg-blue-50 text-blue-600 border-blue-200',
        'sick'       => 'bg-purple-50 text-purple-600 border-purple-200',
        'unrecorded' => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-faint)] border-[color:var(--dash-card-border)]',
        'scheduled'  => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-faint)] border-[color:var(--dash-card-border)]',
    ];
@endphp

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div>
        <h2 class="text-xl font-bold font-jakarta text-[color:var(--dash-text-heading)]">Kehadiran & Nilai</h2>
        <p class="text-sm text-[color:var(--dash-text-muted)] mt-0.5">Rekap kehadiran dan nilai per pertemuan untuk semua kelas aktif Anda.</p>
    </div>

    @if($classes->isEmpty())
    <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[color:var(--dash-card-border)] shadow-sm p-12 flex flex-col items-center justify-center text-center">
        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[#5586DB] text-3xl">analytics</span>
        </div>
        <h3 class="text-base font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-1">Belum Ada Data</h3>
        <p class="text-sm text-[color:var(--dash-text-muted)] max-w-xs mx-auto mb-5">Data tersedia setelah kelas aktif dan jadwal pertama ditambahkan.</p>
        <a href="{{ route('member.dashboard') }}"
           class="px-5 py-2 bg-[#5586DB] text-white text-sm font-semibold rounded-lg hover:bg-[#4370BC] transition-colors">
            Kembali ke Dashboard
        </a>
    </div>

    @else
    @foreach($classes as $class)
    <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[color:var(--dash-card-border)] shadow-sm overflow-hidden">

        {{-- Class header --}}
        <div class="p-5 sm:p-6 border-b border-[color:var(--dash-card-border)]">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#5586DB]/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#5586DB]" style="font-size:20px">menu_book</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-[color:var(--dash-text-heading)] font-jakarta leading-tight">{{ $class['class_name'] }}</h3>
                        <p class="text-xs text-[color:var(--dash-text-muted)] mt-0.5">{{ $class['program_name'] }} &middot; {{ $class['tutor_name'] }}</p>
                    </div>
                </div>

                @if($class['summary']['total'] > 0)
                <div class="flex items-center gap-4 sm:gap-6 text-center">
                    <div>
                        <div class="text-lg font-bold {{ $class['summary']['rate'] >= 75 ? 'text-green-600' : 'text-red-500' }}">
                            {{ $class['summary']['rate'] }}%
                        </div>
                        <div class="text-[11px] text-[color:var(--dash-text-faint)] font-medium">Kehadiran</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-[color:var(--dash-text-heading)]">{{ $class['summary']['attended'] }}</div>
                        <div class="text-[11px] text-[color:var(--dash-text-faint)] font-medium">Hadir</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-[color:var(--dash-text-heading)]">{{ $class['summary']['absent'] }}</div>
                        <div class="text-[11px] text-[color:var(--dash-text-faint)] font-medium">Tidak Hadir</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-[color:var(--dash-text-heading)]">{{ $class['summary']['total'] }}</div>
                        <div class="text-[11px] text-[color:var(--dash-text-faint)] font-medium">Sesi</div>
                    </div>
                </div>
                @endif
            </div>

            @if($class['summary']['total'] > 0)
            <div class="mt-4">
                <div class="w-full bg-[var(--dash-nav-hover-bg)] rounded-full h-1.5 overflow-hidden">
                    <div class="{{ $class['summary']['rate'] >= 75 ? 'bg-green-500' : 'bg-red-400' }} h-1.5 rounded-full transition-all"
                         style="width: {{ $class['summary']['rate'] }}%"></div>
                </div>
                @if($class['summary']['rate'] < 75)
                <p class="text-xs text-red-500 font-medium mt-1.5">Kehadiran di bawah 75% — perhatikan syarat kelulusan.</p>
                @endif
            </div>
            @endif
        </div>

        {{-- Column headers --}}
        <div class="grid grid-cols-12 px-5 sm:px-6 py-2 bg-[var(--dash-nav-hover-bg)] border-b border-[color:var(--dash-card-border)]">
            <div class="col-span-1"></div>
            <div class="col-span-5 text-xs font-bold text-[color:var(--dash-text-faint)] uppercase tracking-wide">Pertemuan</div>
            <div class="col-span-3 text-xs font-bold text-[color:var(--dash-text-faint)] uppercase tracking-wide">Materi</div>
            <div class="col-span-2 text-xs font-bold text-[color:var(--dash-text-faint)] uppercase tracking-wide text-center">Status</div>
            <div class="col-span-1 text-xs font-bold text-[color:var(--dash-text-faint)] uppercase tracking-wide text-right">Nilai</div>
        </div>

        {{-- Session rows --}}
        @if($class['sessions']->isEmpty())
        <div class="p-6 text-center text-sm text-[color:var(--dash-text-faint)]">Belum ada jadwal pertemuan.</div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($class['sessions'] as $i => $session)
            @php $isFuture = $session['status'] === 'scheduled'; @endphp
            <div class="grid grid-cols-12 items-center px-5 sm:px-6 py-3 {{ $isFuture ? 'opacity-50' : '' }}">
                {{-- Number --}}
                <div class="col-span-1">
                    <div class="w-6 h-6 rounded-full bg-[var(--dash-nav-hover-bg)] flex items-center justify-center">
                        <span class="text-xs font-bold text-[color:var(--dash-text-muted)]">{{ $i + 1 }}</span>
                    </div>
                </div>

                {{-- Date + time --}}
                <div class="col-span-5">
                    <div class="text-sm font-semibold text-[color:var(--dash-text-heading)]">
                        {{ $session['date']->translatedFormat('d M Y') }}
                    </div>
                    <div class="text-xs text-[color:var(--dash-text-faint)]">{{ $session['time'] }}</div>
                    @if($session['notes'])
                    <div class="text-xs text-[color:var(--dash-text-faint)] italic mt-0.5">{{ $session['notes'] }}</div>
                    @endif
                </div>

                {{-- Material --}}
                <div class="col-span-3">
                    <span class="text-xs text-[color:var(--dash-text-muted)] line-clamp-2">{{ $session['material'] ?? '—' }}</span>
                </div>

                {{-- Status badge --}}
                <div class="col-span-2 flex justify-center">
                    <span class="text-xs font-semibold px-2 py-1 rounded-md border whitespace-nowrap {{ $statusColor[$session['status']] ?? $statusColor['unrecorded'] }}">
                        {{ $statusLabel[$session['status']] ?? '-' }}
                    </span>
                </div>

                {{-- Score --}}
                <div class="col-span-1 text-right">
                    @if($session['score'] !== null)
                        <span class="text-sm font-bold text-[color:var(--dash-text-heading)]">{{ $session['score'] }}</span>
                    @else
                        <span class="text-xs text-gray-300">—</span>
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
