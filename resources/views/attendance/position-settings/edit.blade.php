@extends('layouts.dashboard')

@section('title', 'Edit Pengaturan Absensi Per Posisi - MINDSIA')
@section('header_title', 'Pengaturan Absensi Per Posisi')

@section('content')
<h1 class="sr-only">Pengaturan Absensi Per Posisi</h1>
<div class="max-w-2xl mx-auto pt-8 sm:pt-16 space-y-4">

    @if($errors->any())
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        <div class="px-6 py-4 border-b flex items-center gap-2.5"
             style="border-color: var(--dash-card-border);">
            <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">
                {{ $position->position_name }}
            </h3>
        </div>

        <form method="POST" action="{{ route('attendance.position-settings.update', $position) }}" class="px-6 py-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Work schedule attendance --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: var(--dash-text-muted);">Absensi Jadwal Kerja</p>
                <div class="space-y-3">
                    <label class="flex items-center justify-between gap-4 p-4 rounded-xl cursor-pointer"
                           style="border: 1px solid var(--dash-card-border);">
                        <div>
                            <p class="text-sm font-medium" style="color: var(--dash-text-heading);">Wajib absen manual</p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Posisi ini harus check-in dan check-out setiap hari kerja</p>
                        </div>
                        <input type="hidden" name="is_required" value="0">
                        <input type="checkbox" name="is_required" value="1"
                               {{ old('is_required', $setting->is_required ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded">
                    </label>
                </div>
            </div>

            {{-- Session attendance --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: var(--dash-text-muted);">Absensi Sesi Kelas</p>
                <label class="flex items-center justify-between gap-4 p-4 rounded-xl cursor-pointer"
                       style="border: 1px solid var(--dash-card-border);">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--dash-text-heading);">Wajib absen sesi</p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Posisi ini harus check-in/out di setiap sesi kelas yang dijadwalkan</p>
                    </div>
                    <input type="hidden" name="is_session_required" value="0">
                    <input type="checkbox" name="is_session_required" value="1"
                           {{ old('is_session_required', $setting->is_session_required ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 rounded">
                </label>
            </div>

            {{-- Location scope --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: var(--dash-text-muted);">Scope Lokasi Absensi</p>
                <div class="space-y-2">
                    @foreach([
                        'branch'   => ['Cabang Sendiri', 'Hanya bisa absen dalam radius cabang yang di-assign'],
                        'area'     => ['Semua Cabang di Area', 'Bisa absen di cabang mana saja dalam area yang sama'],
                        'anywhere' => ['Bebas (Remote OK)', 'Bisa absen dari lokasi mana saja, tanpa batasan radius'],
                    ] as $value => [$label, $desc])
                        <label class="flex items-center gap-3 p-4 rounded-xl cursor-pointer"
                               style="border: 1px solid var(--dash-card-border);">
                            <input type="radio" name="attendance_scope" value="{{ $value }}"
                                   {{ old('attendance_scope', $setting->attendance_scope ?? 'branch') === $value ? 'checked' : '' }}
                                   class="w-4 h-4">
                            <div>
                                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $label }}</p>
                                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $desc }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Early check-in window --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: var(--dash-text-muted);">Toleransi Check-In Lebih Awal</p>
                <div class="flex items-center gap-3">
                    <input type="number" name="early_checkin_minutes"
                           value="{{ old('early_checkin_minutes', $setting->early_checkin_minutes) }}"
                           min="0" max="480" placeholder="—"
                           class="w-28 px-3 py-2 rounded-xl text-sm"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    <span class="text-sm" style="color: var(--dash-text-muted);">menit sebelum jam masuk (kosong = bebas kapan saja)</span>
                </div>
            </div>

            {{-- Early check-out window --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: var(--dash-text-muted);">Toleransi Check-Out Lebih Awal</p>
                <div class="flex items-center gap-3">
                    <input type="number" name="early_checkout_minutes"
                           value="{{ old('early_checkout_minutes', $setting->early_checkout_minutes) }}"
                           min="0" max="480" placeholder="—"
                           class="w-28 px-3 py-2 rounded-xl text-sm"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    <span class="text-sm" style="color: var(--dash-text-muted);">menit sebelum jam pulang (kosong = harus tepat waktu)</span>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-base">save</span>
                    Simpan
                </button>
                <a href="{{ route('attendance.position-settings.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
