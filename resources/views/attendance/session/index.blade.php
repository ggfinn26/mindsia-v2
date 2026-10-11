@extends('layouts.dashboard')

@section('title', 'Absensi Sesi Kelas - MINDSIA')
@section('header_title', 'Absensi Sesi Kelas')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{ ...attendanceCamera({ csrfToken: '{{ csrf_token() }}' }), activeSessionId: null }">

    {{-- Page Header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Personal Dashboard</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Absensi Sesi Kelas</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Jadwal mengajar Anda hari ini: {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Session Cards --}}
    @forelse($sessions ?? [] as $session)
    @php
        $log = $session->attendanceLog;
        $classSchedule = $session->classSchedule;
        $classRoom = $classSchedule?->classRoom;
        $isDone = $log && $log->status === 'present';
        $isActive = $log && $log->status === 'checked_in';
        $isPending = !$log || $log->status === 'absent';
    @endphp

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        {{-- Dark header strip — hanya tampil saat kamera aktif sesi ini --}}
        <div x-show="activeSessionId === {{ $session->id }} && showModal"
             x-cloak
             class="px-5 pt-5 pb-4"
             style="background-color: #0f172a;">
            <div class="flex items-start justify-between">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider mb-2"
                          style="background-color: rgba(var(--dash-primary-rgb, 85,134,219),0.2); border: 1px solid rgba(var(--dash-primary-rgb, 85,134,219),0.3); color: #93c5fd;">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                        Foto Selfie
                    </span>
                    <p class="text-lg font-bold text-white font-jakarta">{{ $classRoom?->name ?? 'Kelas' }}</p>
                    @if($classSchedule)
                    <p class="text-xs mt-0.5 font-mono" style="color: #64748b;">
                        {{ \Carbon\Carbon::parse($classSchedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($classSchedule->end_time)->format('H:i') }}
                    </p>
                    @endif
                </div>
                <button type="button"
                        @click="activeSessionId = null; close()"
                        class="text-xs font-medium mt-1"
                        style="color: #64748b;">
                    Batal
                </button>
            </div>
        </div>

        {{-- Info sesi (selalu tampil kecuali saat kamera aktif) --}}
        <div x-show="!(activeSessionId === {{ $session->id }} && showModal)"
             class="px-5 py-4 flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate"
                   style="color: var(--dash-text-heading);">{{ $classRoom?->name ?? 'Kelas Tidak Diketahui' }}</p>
                @if($classSchedule)
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                    {{ \Carbon\Carbon::parse($classSchedule->schedule_date)->translatedFormat('d M Y') }}
                    &bull;
                    {{ \Carbon\Carbon::parse($classSchedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($classSchedule->end_time)->format('H:i') }}
                </p>
                @endif
                {{-- Waktu hadir --}}
                @if($log && $log->check_in)
                <p class="text-xs mt-1 font-mono" style="color: var(--dash-text-faint);">
                    In {{ \Carbon\Carbon::parse($log->check_in)->format('H:i') }}
                    @if($log->check_out)
                        &bull; Out {{ \Carbon\Carbon::parse($log->check_out)->format('H:i') }}
                    @endif
                </p>
                @endif
            </div>
            <div class="flex items-center gap-2 shrink-0">
                {{-- Status badge --}}
                @if($isDone)
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700">Selesai</span>
                @elseif($isActive)
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-700">Sedang Mengajar</span>
                @elseif($log && $log->status === 'absent')
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-red-50 text-red-700">Alpha</span>
                @else
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Belum Absen</span>
                @endif

                {{-- Action button --}}
                @if($isPending)
                    <button type="button"
                            @click="activeSessionId = {{ $session->id }}; open('check-in', '{{ route('session-attendance.check-in', $session->id) }}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[14px]">login</span>
                        Check-In
                    </button>
                @elseif($isActive)
                    <button type="button"
                            @click="activeSessionId = {{ $session->id }}; open('check-out', '{{ route('session-attendance.check-out', $session->id) }}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90"
                            style="background-color: #059669;">
                        <span class="material-symbols-outlined text-[14px]">logout</span>
                        Check-Out
                    </button>
                @else
                    <span class="inline-flex items-center gap-1 text-xs"
                          style="color: var(--dash-text-faint);">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                        Selesai
                    </span>
                @endif
            </div>
        </div>

        {{-- Inline camera — tampil saat tombol diklik --}}
        <div x-show="activeSessionId === {{ $session->id }} && showModal"
             x-cloak
             class="px-5 pb-5">
            @include('attendance._inline-camera')
        </div>

    </div>
    @empty

    {{-- Empty state --}}
    <div class="rounded-2xl px-5 py-20 text-center"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
              style="color: var(--dash-text-faint);">calendar_month</span>
        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Tidak ada jadwal hari ini.</p>
        <p class="text-sm mt-1" style="color: var(--dash-text-faint);">Anda tidak memiliki jadwal kelas untuk mengajar pada hari ini.</p>
    </div>

    @endforelse

</div>
@endsection
