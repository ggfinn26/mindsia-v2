@extends('layouts.dashboard')

@section('title', 'Edit Aturan Jadwal Kerja - MINDSIA')
@section('header_title', 'Aturan Jadwal Kerja')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('work-schedule-rules.index') }}" class="dash-back inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-medium mb-3 transition-colors"
           style="border-color: var(--dash-card-border); color: var(--dash-text-muted); background-color: var(--dash-card-bg);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Aturan Jadwal Kerja
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Presensi</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Aturan Jadwal Kerja</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui parameter jam kerja untuk {{ $rule->setting_name }}.
        </p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
            <div class="flex-1">
                <p class="font-bold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm border"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center gap-2 border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">edit_calendar</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Aturan: {{ $rule->setting_name }}</h2>
        </div>

        <form method="POST" action="{{ route('work-schedule-rules.update', $rule) }}"
              x-data="{ isRequired: {{ old('is_required', $rule->is_required) ? 'true' : 'false' }} }">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">
                {{-- Nama Aturan --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Nama Aturan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="setting_name" value="{{ old('setting_name', $rule->setting_name) }}" required
                           placeholder="Contoh: Jadwal Standar, Jadwal Tutor"
                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('setting_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jam Mulai & Selesai --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="isRequired">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Jam Mulai Kerja <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="start_time" value="{{ old('start_time', $rule->start_time ? \Carbon\Carbon::parse($rule->start_time)->format('H:i') : '') }}"
                               :required="isRequired"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('start_time')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Jam Selesai Kerja <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="end_time" value="{{ old('end_time', $rule->end_time ? \Carbon\Carbon::parse($rule->end_time)->format('H:i') : '') }}"
                               :required="isRequired"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('end_time')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Jam Istirahat --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Jam Istirahat Mulai
                        </label>
                        <input type="time" name="break_start_time" value="{{ old('break_start_time', $rule->break_start_time ? \Carbon\Carbon::parse($rule->break_start_time)->format('H:i') : '') }}"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('break_start_time')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Jam Istirahat Selesai
                        </label>
                        <input type="time" name="break_end_time" value="{{ old('break_end_time', $rule->break_end_time ? \Carbon\Carbon::parse($rule->break_end_time)->format('H:i') : '') }}"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('break_end_time')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Toleransi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Toleransi Keterlambatan (menit) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="late_tolerance_minutes" value="{{ old('late_tolerance_minutes', $rule->late_tolerance_minutes) }}"
                               min="0" max="120" required
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('late_tolerance_minutes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Toleransi Pulang Cepat (menit) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="early_leave_tolerance_minutes" value="{{ old('early_leave_tolerance_minutes', $rule->early_leave_tolerance_minutes) }}"
                               min="0" max="120" required
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('early_leave_tolerance_minutes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Hari Kerja --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Hari Kerja <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(kosong = semua hari berlaku)</span>
                    </label>
                    @php
                        $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                        $activeDays = old('work_days', $rule->workDays->pluck('day_of_week')->toArray());
                    @endphp
                    <div class="flex flex-wrap gap-2 pt-1" x-data="{ selectedDays: {{ json_encode((array)$activeDays) }}.map(String) }">
                        @foreach($days as $num => $label)
                            <label class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border text-xs font-medium cursor-pointer transition-colors"
                                   :style="selectedDays.includes('{{ $num }}')
                                       ? 'background-color: var(--dash-nav-hover-bg); border-color: var(--dash-primary); color: var(--dash-primary-text);'
                                       : 'background-color: var(--dash-card-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);'">
                                <input type="checkbox" name="work_days[]" value="{{ $num }}"
                                       class="sr-only"
                                       @change="if ($event.target.checked) { if (!selectedDays.includes('{{ $num }}')) selectedDays.push('{{ $num }}') } else { selectedDays = selectedDays.filter(d => d !== '{{ $num }}') }"
                                       {{ in_array($num, (array)$activeDays) ? 'checked' : '' }}>
                                <span class="material-symbols-outlined text-[16px]"
                                      x-text="selectedDays.includes('{{ $num }}') ? 'check_box' : 'check_box_outline_blank'"></span>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('work_days')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pengaturan Status & Kewajiban --}}
                <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center justify-between p-4 rounded-xl border cursor-pointer transition-colors"
                           style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);"
                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">Presensi Wajib</p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Pegawai wajib melakukan check-in dan check-out</p>
                        </div>
                        <input type="hidden" name="is_required" value="0">
                        <input type="checkbox" name="is_required" value="1"
                               {{ old('is_required', $rule->is_required) ? 'checked' : '' }}
                               @change="isRequired = $event.target.checked"
                               class="w-4 h-4 rounded cursor-pointer"
                               style="accent-color: var(--dash-primary);">
                    </label>

                    <label class="flex items-center justify-between p-4 rounded-xl border cursor-pointer transition-colors"
                           style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);"
                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">Status Aktif</p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Aturan jadwal kerja dapat diterapkan ke pegawai</p>
                        </div>
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $rule->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded cursor-pointer"
                               style="accent-color: var(--dash-primary);">
                    </label>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 flex items-center justify-end gap-3 border-t"
                 style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('work-schedule-rules.show', $rule) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
