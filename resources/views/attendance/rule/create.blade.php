@extends('layouts.dashboard')

@section('title', 'Tambah Aturan - Kelola Sanksi Absen - MINDSIA')
@section('header_title', 'Kelola Sanksi Absen')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{
         actions: @json(old('actions', [['action_type' => '', 'action_order' => 1]])),
         addAction() {
             this.actions.push({ action_type: '', action_order: this.actions.length + 1 });
         },
         removeAction(i) {
             this.actions.splice(i, 1);
             this.actions.forEach((a, idx) => a.action_order = idx + 1);
         }
     }">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('attendance-rules.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Kelola Sanksi Absen
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Presensi</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Tambah Aturan Sanksi Absen</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Konfigurasikan kriteria pemicu pelanggaran kehadiran dan urutan eskalasi tindakan otomatis.
        </p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: var(--dash-danger);">
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

    {{-- Form --}}
    <form action="{{ route('attendance-rules.store') }}" method="POST">
        @csrf

        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm divide-y"
             style="border-color: var(--dash-card-border);">

            {{-- Bagian 1: Definisi Aturan --}}
            <div class="p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-2 border-b"
                     style="border-color: var(--dash-card-border);">
                    <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                    <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Definisi Aturan</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nama Aturan --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Nama Aturan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="rule_name" value="{{ old('rule_name') }}" required
                               placeholder="Contoh: Sanksi Absen Beruntun 3 Hari"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('rule_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tipe Absensi --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Tipe Absensi <span class="text-red-500">*</span>
                        </label>
                        <select name="attendance_type" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            <option value="work_schedule" {{ old('attendance_type') === 'work_schedule' ? 'selected' : '' }}>Kerja Harian</option>
                            <option value="session" {{ old('attendance_type') === 'session' ? 'selected' : '' }}>Sesi / Kelas</option>
                        </select>
                        @error('attendance_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Periode --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Periode Evaluasi <span class="text-red-500">*</span>
                        </label>
                        <select name="period_type" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            <option value="daily" {{ old('period_type') === 'daily' ? 'selected' : '' }}>Harian</option>
                            <option value="monthly" {{ old('period_type', 'monthly') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        </select>
                        @error('period_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenis Pemicu --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Jenis Pemicu Pelanggaran <span class="text-red-500">*</span>
                        </label>
                        <select name="trigger_type" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            <option value="consecutive_absence" {{ old('trigger_type') === 'consecutive_absence' ? 'selected' : '' }}>Absen Beruntun</option>
                            <option value="monthly_absence" {{ old('trigger_type') === 'monthly_absence' ? 'selected' : '' }}>Absen Bulanan</option>
                            <option value="monthly_late_count" {{ old('trigger_type') === 'monthly_late_count' ? 'selected' : '' }}>Jumlah Keterlambatan</option>
                            <option value="monthly_late_minutes" {{ old('trigger_type') === 'monthly_late_minutes' ? 'selected' : '' }}>Total Menit Terlambat</option>
                            <option value="daily_late" {{ old('trigger_type') === 'daily_late' ? 'selected' : '' }}>Terlambat Harian</option>
                        </select>
                        @error('trigger_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Operator & Nilai --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Kondisi Ambang Batas <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <select name="trigger_operator" required
                                        class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                                    @foreach(['=' => '=', '>' => '>', '>=' => '≥ (Lebih Dari Sama Dengan)', '<' => '<', '<=' => '≤ (Kurang Dari Sama Dengan)'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('trigger_operator', '>=') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="number" name="trigger_value" value="{{ old('trigger_value', 0) }}" min="0" step="0.01" required
                                       placeholder="Nilai angka"
                                       class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            </div>
                        </div>
                        @error('trigger_operator')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @error('trigger_value')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Status Aturan <span class="text-red-500">*</span>
                        </label>
                        <select name="is_active" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('is_active')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Bagian 2: Aksi Sanksi Bertingkat --}}
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b"
                     style="border-color: var(--dash-card-border);">
                    <div class="flex items-center gap-2.5">
                        <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                        <div>
                            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Urutan Tindakan Sanksi</h2>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Sanksi akan dijalankan berurutan berdasarkan prioritas urutan nomor.</p>
                        </div>
                    </div>
                    <button type="button" @click="addAction()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                            style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text); border: 1px solid var(--dash-card-border);">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        Tambah Aksi
                    </button>
                </div>

                <div class="space-y-3 pt-2">
                    <template x-for="(action, i) in actions" :key="i">
                        <div class="flex items-center gap-3 p-3.5 rounded-xl border transition-all"
                             style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold shrink-0"
                                  style="background-color: var(--dash-card-bg); color: var(--dash-primary-text); border: 1px solid var(--dash-card-border);"
                                  x-text="i + 1"></span>

                            <div class="flex-1">
                                <select :name="`actions[${i}][action_type]`" x-model="action.action_type" required
                                        class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                                    <option value="">Pilih Jenis Aksi...</option>
                                    <option value="notification">Notifikasi</option>
                                    <option value="warning_letter">Surat Peringatan</option>
                                    <option value="payroll_deduction">Potongan Gaji</option>
                                    <option value="mark_anomaly">Tandai Anomali</option>
                                    <option value="create_follow_up">Buat Tindak Lanjut</option>
                                </select>
                            </div>

                            <input type="hidden" :name="`actions[${i}][action_order]`" :value="i + 1">

                            <button type="button" @click="removeAction(i)" x-show="actions.length > 1"
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg transition-opacity hover:opacity-80 shrink-0"
                                    style="background-color: #fee2e2; color: var(--dash-danger);"
                                    title="Hapus Aksi">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </template>
                </div>

                @error('actions')
                    <p class="text-xs mt-1" style="color: var(--dash-danger);">{{ $message }}</p>
                @enderror
            </div>

            {{-- Footer Action Buttons --}}
            <div class="px-6 py-4 flex items-center justify-end gap-3 rounded-b-2xl"
                 style="background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('attendance-rules.index') }}"
                   class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Aturan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
