@extends('layouts.dashboard')

@section('title', 'Absen Masuk/Keluar - MINDSIA')
@section('header_title', 'Kehadiran Harian')

@section('content')
<div class="max-w-4xl mx-auto pt-8 sm:pt-16 space-y-4"
     x-data="attendanceCamera({ csrfToken: '{{ csrf_token() }}' })">

    @if($errors->any())
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Action card — horizontal layout (V1 style) --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-8 sm:p-10 flex flex-col sm:flex-row items-center justify-between gap-6">

            {{-- Left: date, time, status --}}
            <div x-data="{ t: '{{ now()->translatedFormat('H:i') }}', d: '{{ now()->translatedFormat('l, d F Y') }}' }"
                 x-init="
                    setInterval(() => t = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }), 1000);
                    d = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                 ">
                <h2 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);" x-text="t">{{ now()->translatedFormat('H:i') }}</h2>
                <p class="text-sm" style="color: var(--dash-text-muted);" x-text="d">{{ now()->translatedFormat('l, d F Y') }}</p>

                <div class="mt-4 text-sm" style="color: var(--dash-text-body);">
                    Status Hari Ini:
                    @if(!$today || !$today->check_in)
                        <span class="font-medium" style="color: var(--dash-text-heading);">Belum Check-In</span>
                    @elseif($today->check_in && !$today->check_out)
                        <span class="font-medium" style="color: #059669;">Sudah Check-In pukul {{ \Carbon\Carbon::parse($today->check_in)->format('H:i') }}</span>
                    @else
                        <span class="font-medium" style="color: var(--dash-primary);">Selesai (In: {{ \Carbon\Carbon::parse($today->check_in)->format('H:i') }}, Out: {{ \Carbon\Carbon::parse($today->check_out)->format('H:i') }})</span>
                    @endif
                </div>
            </div>

            {{-- Right: action button --}}
            <div class="w-full sm:w-auto">
                @if($scheduleRequired === false)
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium"
                         style="background-color:#ccfbf1; color:#0f766e; border:1px solid #99f6e4;">
                        <span class="material-symbols-outlined text-base">check_circle</span>
                        Kehadiran Anda otomatis tercatat
                    </div>
                @elseif(!$today || !$today->check_in)
                    <button type="button"
                            @click="open('check-in', '{{ route('work-attendance.check-in') }}')"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-lg">login</span>
                        Catat Check-In
                    </button>
                @elseif($today->check_in && !$today->check_out)
                    <button type="button"
                            @click="open('check-out', '{{ route('work-attendance.check-out') }}')"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <span class="material-symbols-outlined text-lg">logout</span>
                        Catat Check-Out
                    </button>
                @else
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium"
                         style="border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                        Absensi Hari Ini Selesai
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Inline camera — appears when button clicked (x-show via showModal) --}}
    <div x-show="showModal" x-cloak
         class="rounded-2xl p-6"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @include('attendance._inline-camera')
    </div>


</div>
@endsection
