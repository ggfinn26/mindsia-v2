@extends('layouts.dashboard')

@section('title', 'Kehadiran Harian')
@section('header_title', 'Kehadiran Harian')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="attendanceCamera({ csrfToken: '{{ csrf_token() }}' })">

    {{-- Page Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Kehadiran</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kehadiran Harian</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <a href="{{ route('work-attendance.my-recap') }}"
           class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors shrink-0"
           style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[16px]">history</span>
            Rekap Absensi
        </a>
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

    {{-- Two-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-[420px_1fr] gap-5 items-start">

        {{-- ══════ KIRI: ACTION CARD ══════ --}}
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

            @if($scheduleRequired === false)
            {{-- OTOMATIS --}}
            <div class="px-5 py-10 text-center">
                <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
                     style="background-color: #f0fdf4;">
                    <span class="material-symbols-outlined text-[28px]" style="color: #059669;">check_circle</span>
                </div>
                <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">Kehadiran Anda otomatis tercatat</p>
                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Tidak perlu check-in manual untuk jadwal ini.</p>
            </div>

            @elseif(!$today || !$today->check_in)
            {{-- BELUM CHECK-IN --}}
            <div class="px-5 pt-5 pb-4" style="background-color: #0f172a;">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider mb-2"
                              style="background-color: rgba(251,191,36,0.15); border: 1px solid rgba(251,191,36,0.2); color: #fbbf24;">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                            Belum Check-In
                        </span>
                        <p class="text-4xl font-extrabold text-white font-jakarta tracking-tight" id="live-clock">{{ now()->format('H:i') }}</p>
                        <p class="text-xs mt-1 font-mono" style="color: #64748b;">{{ now()->format('d/m/Y') }}</p>
                    </div>
                    <div class="relative w-10 h-10 mt-1 shrink-0">
                        <div class="absolute inset-0 rounded-full border-2 animate-ping opacity-40"
                             style="border-color: #fbbf24;"></div>
                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                             style="background-color: rgba(251,191,36,0.15);">
                            <span class="material-symbols-outlined text-[22px]" style="color: #fbbf24;">schedule</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol check-in (saat kamera belum dibuka) --}}
            <div x-show="!showModal" class="p-5">
                <button type="button"
                        @click="open('check-in', '{{ route('work-attendance.check-in') }}')"
                        class="w-full inline-flex items-center justify-center gap-2 py-3.5 text-sm font-bold text-white rounded-xl transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[20px]">login</span>
                    Catat Check-In
                </button>
                <div class="mt-4 space-y-2.5">
                    <p class="text-[11px] font-bold uppercase tracking-wider"
                       style="color: var(--dash-text-faint);">Cara Check-In</p>
                    @foreach([
                        ['location_on', 'Aktifkan GPS', 'Pastikan lokasi HP aktif dan akurat'],
                        ['photo_camera', 'Izinkan Kamera', 'Klik "Izinkan" saat browser meminta akses'],
                        ['face', 'Posisikan Wajah', 'Hadapkan wajah ke kamera'],
                        ['touch_app', 'Tap Tombol', 'Tap "Catat Check-In" untuk mulai'],
                    ] as [$icon, $title, $desc])
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[16px] mt-0.5 shrink-0"
                              style="color: var(--dash-primary);">{{ $icon }}</span>
                        <div>
                            <p class="text-xs font-semibold" style="color: var(--dash-text-body);">{{ $title }}</p>
                            <p class="text-[11px]" style="color: var(--dash-text-faint);">{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Kamera inline (setelah tombol diklik) --}}
            <div x-show="showModal" x-cloak class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">Foto Selfie Check-In</p>
                    <button type="button" @click="close()"
                            class="text-xs font-medium"
                            style="color: var(--dash-text-muted);">Batal</button>
                </div>
                @include('attendance._inline-camera')
            </div>

            @elseif($today->check_in && !$today->check_out)
            {{-- SUDAH CHECK-IN, BELUM CHECK-OUT --}}
            <div class="px-5 pt-5 pb-4" style="background-color: #0f172a;">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider mb-2"
                              style="background-color: rgba(52,211,153,0.15); border: 1px solid rgba(52,211,153,0.2); color: #34d399;">
                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: #34d399;"></span>
                            Check-In
                        </span>
                        <p class="text-4xl font-extrabold text-white font-jakarta tracking-tight" id="live-clock">{{ now()->format('H:i') }}</p>
                        <p class="text-xs mt-1 font-mono" style="color: #64748b;">
                            Masuk: <span style="color: #34d399; font-weight: 600;">{{ $today->check_in->format('H:i') }}</span>
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                         style="background-color: rgba(52,211,153,0.15);">
                        <span class="material-symbols-outlined text-[22px]" style="color: #34d399;">check_circle</span>
                    </div>
                </div>
            </div>

            {{-- Tombol check-out --}}
            <div x-show="!showModal" class="p-5">
                <div class="flex items-center gap-3 rounded-xl px-4 py-3 mb-4"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                         style="background-color: #f0fdf4;">
                        <span class="material-symbols-outlined text-[16px]" style="color: #059669;">login</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-widest"
                           style="color: var(--dash-text-faint);">Check-In Tercatat</p>
                        <p class="text-sm font-bold font-mono" style="color: var(--dash-text-heading);">{{ $today->check_in->format('H:i') }}</p>
                    </div>
                    @if($today->check_in_distance_m !== null)
                    <div class="text-right">
                        <p class="text-[10px]" style="color: var(--dash-text-faint);">Jarak</p>
                        <p class="text-sm font-semibold font-mono" style="color: var(--dash-text-body);">{{ number_format($today->check_in_distance_m) }}m</p>
                    </div>
                    @endif
                </div>
                <button type="button"
                        @click="open('check-out', '{{ route('work-attendance.check-out') }}')"
                        class="w-full inline-flex items-center justify-center gap-2 py-3.5 text-sm font-bold text-white rounded-xl transition-opacity hover:opacity-90"
                        style="background-color: #059669;">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    Catat Check-Out
                </button>
            </div>

            {{-- Kamera inline check-out --}}
            <div x-show="showModal" x-cloak class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">Foto Selfie Check-Out</p>
                    <button type="button" @click="close()"
                            class="text-xs font-medium"
                            style="color: var(--dash-text-muted);">Batal</button>
                </div>
                @include('attendance._inline-camera')
            </div>

            @else
            {{-- SELESAI --}}
            <div class="px-5 pt-5 pb-4" style="background-color: #0f172a;">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider mb-2"
                      style="background-color: rgba(6,182,212,0.15); border: 1px solid rgba(6,182,212,0.2); color: #22d3ee;">
                    <span class="material-symbols-outlined text-[12px]">verified</span>
                    Absensi Selesai
                </span>
                <p class="text-2xl font-extrabold text-white font-jakarta">Hari ini tercatat!</p>
                <p class="text-xs mt-1 font-mono" style="color: #64748b;">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="p-5 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl px-4 py-3.5"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <p class="text-[10px] font-bold uppercase tracking-widest mb-1"
                           style="color: var(--dash-text-faint);">Check-In</p>
                        <p class="text-2xl font-extrabold font-mono" style="color: var(--dash-text-heading);">{{ $today->check_in->format('H:i') }}</p>
                        @if($today->check_in_distance_m !== null)
                        <p class="text-[11px] mt-1 font-mono" style="color: var(--dash-text-faint);">{{ number_format($today->check_in_distance_m) }}m dari kantor</p>
                        @endif
                    </div>
                    <div class="rounded-xl px-4 py-3.5"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <p class="text-[10px] font-bold uppercase tracking-widest mb-1"
                           style="color: var(--dash-text-faint);">Check-Out</p>
                        <p class="text-2xl font-extrabold font-mono" style="color: var(--dash-text-heading);">{{ $today->check_out->format('H:i') }}</p>
                        @if($today->check_out_distance_m !== null)
                        <p class="text-[11px] mt-1 font-mono" style="color: var(--dash-text-faint);">{{ number_format($today->check_out_distance_m) }}m dari kantor</p>
                        @endif
                    </div>
                </div>

                @php
                    $duration = $today->check_in && $today->check_out
                        ? $today->check_in->diffInMinutes($today->check_out)
                        : null;
                    $hours = $duration ? intdiv($duration, 60) : 0;
                    $mins  = $duration ? $duration % 60 : 0;
                @endphp
                @if($duration)
                <div class="rounded-xl px-4 py-3"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] font-semibold" style="color: var(--dash-text-muted);">Durasi Kerja</p>
                        <p class="text-sm font-bold font-mono" style="color: var(--dash-text-heading);">{{ $hours }}j {{ $mins }}m</p>
                    </div>
                    <div class="h-1.5 rounded-full overflow-hidden"
                         style="background-color: var(--dash-card-border);">
                        <div class="h-full rounded-full transition-all"
                             style="width: {{ min(100, round($hours / 8 * 100)) }}%; background-color: var(--dash-primary);"></div>
                    </div>
                    <p class="text-[10px] mt-1" style="color: var(--dash-text-faint);">Target 8 jam / hari</p>
                </div>
                @endif

                @if($today->late_minutes > 0)
                <div class="flex items-center gap-2.5 rounded-xl px-3 py-2.5"
                     style="background-color: #fef3c7; border: 1px solid #fde68a;">
                    <span class="material-symbols-outlined text-[18px]" style="color: #d97706;">schedule</span>
                    <p class="text-xs font-semibold" style="color: #92400e;">Terlambat {{ $today->late_minutes }} menit</p>
                </div>
                @endif
            </div>
            @endif

        </div>
        {{-- ══════ END KIRI ══════ --}}

        {{-- ══════ KANAN: HISTORI ══════ --}}
        <div class="space-y-4">
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-5 py-4 flex items-center justify-between"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Riwayat 7 Hari Terakhir</h2>
                    <a href="{{ route('work-attendance.my-recap') }}"
                       class="text-[11px] font-semibold"
                       style="color: var(--dash-primary);">Semua rekap</a>
                </div>

                @if($recentLogs->count())
                <div class="divide-y" style="border-color: var(--dash-card-border);">
                    @foreach($recentLogs as $log)
                    @php
                        $isToday = $log->attendance_date->isToday();
                        $statusConfig = match($log->status) {
                            'present'    => ['dot' => '#22c55e', 'bg' => '#f0fdf4', 'text' => '#166534', 'label' => 'Hadir'],
                            'checked_in' => ['dot' => '#3b82f6', 'bg' => '#eff6ff', 'text' => '#1d4ed8', 'label' => 'Sedang Hadir'],
                            'leave'      => ['dot' => '#a855f7', 'bg' => '#faf5ff', 'text' => '#7e22ce', 'label' => 'Cuti'],
                            'sick'       => ['dot' => '#f59e0b', 'bg' => '#fffbeb', 'text' => '#92400e', 'label' => 'Sakit'],
                            'permission' => ['dot' => '#06b6d4', 'bg' => '#ecfeff', 'text' => '#155e75', 'label' => 'Izin'],
                            'holiday'    => ['dot' => '#14b8a6', 'bg' => '#f0fdfa', 'text' => '#134e4a', 'label' => 'Libur'],
                            'absent'     => ['dot' => '#ef4444', 'bg' => '#fef2f2', 'text' => '#991b1b', 'label' => 'Alpha'],
                            default      => ['dot' => '#94a3b8', 'bg' => '#f8fafc', 'text' => '#475569', 'label' => \App\Support\EnumLabels::get('attendance_status', $log->status)],
                        };
                    @endphp
                    <div class="flex items-center gap-3 px-5 py-3 transition-colors"
                         onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                         onmouseout="this.style.backgroundColor='transparent'">
                        <div class="shrink-0 text-center w-9">
                            <p class="text-[9px] font-bold uppercase tracking-widest"
                               style="color: var(--dash-text-faint);">{{ $log->attendance_date->format('D') }}</p>
                            <p class="text-base font-extrabold font-mono leading-tight"
                               style="color: {{ $isToday ? 'var(--dash-primary)' : 'var(--dash-text-heading)' }};">{{ $log->attendance_date->format('d') }}</p>
                        </div>
                        <div class="w-2 h-2 rounded-full shrink-0"
                             style="background-color: {{ $statusConfig['dot'] }};"></div>
                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold"
                              style="background-color: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['text'] }};">
                            {{ $statusConfig['label'] }}
                        </span>
                        <div class="ml-auto text-right">
                            <p class="text-xs font-medium font-mono"
                               style="color: var(--dash-text-body);">{{ $log->check_in?->format('H:i') ?? '-' }}</p>
                            <p class="text-[10px] font-mono"
                               style="color: var(--dash-text-faint);">{{ $log->check_out?->format('H:i') ?? '-' }}</p>
                        </div>
                        @if(($log->late_minutes ?? 0) > 0)
                        <span class="text-[10px] font-semibold shrink-0"
                              style="color: #d97706;">+{{ $log->late_minutes }}m</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="px-5 py-10 text-center">
                    <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                          style="color: var(--dash-text-faint);">calendar_month</span>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada riwayat kehadiran.</p>
                    <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Absensi pertama Anda akan muncul di sini.</p>
                </div>
                @endif
            </div>
        </div>
        {{-- ══════ END KANAN ══════ --}}

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const clock = document.getElementById('live-clock');
    if (!clock) { return; }
    const tick = () => {
        const now = new Date();
        clock.textContent = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
    };
    tick();
    setInterval(tick, 1000);
});
</script>
@endsection
