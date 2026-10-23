@extends('layouts.dashboard')

@section('title', 'Buat Job Posting')
@section('header_title', 'Buat Job Posting')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{
        manualItems: {{ json_encode(old('requirements_manual', [])) }},
        addItem() { this.manualItems.push({ criteria_name: '', criteria_desc: '' }) },
        removeItem(i) { this.manualItems.splice(i, 1) }
     }">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('recruitment.postings.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Posting
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Rekrutmen</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Buat Job Posting</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Dari permintaan: <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $permintaan->detail?->position?->position_name ?? '-' }}</span>
            &bull; {{ $permintaan->branch?->branch_name ?? '-' }}
        </p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="p-4 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('recruitment.postings.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="job_permintaan_id" value="{{ $permintaan->id }}">
        <input type="hidden" name="position_id" value="{{ $permintaan->detail?->position_id }}">

        {{-- Card 1: Informasi Posting --}}
        <div class="rounded-2xl p-6 sm:p-7 shadow-sm space-y-5"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
                <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Informasi Posting</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Rincian teks lowongan yang akan ditampilkan ke publik</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Judul Posting <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="Contoh: Lowongan Person In Charge Cabang Cilegon"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                @error('title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Deskripsi Pekerjaan <span class="text-red-500">*</span>
                </label>
                <textarea name="job_description" rows="4" required
                          placeholder="Gambaran umum peran dan lingkungan kerja..."
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('job_description') }}</textarea>
                @error('job_description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Tanggung Jawab Utama <span class="text-red-500">*</span>
                </label>
                <textarea name="job_responsibilities" rows="4" required
                          placeholder="Daftar tanggung jawab dan tugas posisi ini..."
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('job_responsibilities') }}</textarea>
                @error('job_responsibilities')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Kualifikasi Publik <span class="text-red-500">*</span>
                </label>
                <textarea name="job_requirements_text" rows="4" required
                          placeholder="Kualifikasi yang ditampilkan di portal karir publik..."
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('job_requirements_text') }}</textarea>
                @error('job_requirements_text')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Tanggal Penutupan (opsional)
                </label>
                <input type="date" name="closing_date" value="{{ old('closing_date') }}"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                <p class="mt-1 text-xs" style="color: var(--dash-text-muted);">Kosongkan jika lowongan dibuka tanpa batas waktu tertentu.</p>
                @error('closing_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Card 2: Kriteria Otomatis --}}
        <div class="rounded-2xl p-6 sm:p-7 shadow-sm space-y-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
                <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Kriteria Otomatis Sistem</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Digunakan sistem untuk evaluasi awal pemenuhan syarat pelamar.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Usia Minimum</label>
                    <input type="number" name="requirements_auto[minimum_age]" min="15" max="99"
                           value="{{ old('requirements_auto.minimum_age', $permintaan->requirementsAuto?->minimum_age) }}"
                           placeholder="Contoh: 21"
                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Usia Maksimum</label>
                    <input type="number" name="requirements_auto[maximum_age]" min="15" max="99"
                           value="{{ old('requirements_auto.maximum_age', $permintaan->requirementsAuto?->maximum_age) }}"
                           placeholder="Contoh: 35"
                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Min. Pengalaman (thn)</label>
                    <input type="number" name="requirements_auto[minimum_year_experience]" min="0"
                           value="{{ old('requirements_auto.minimum_year_experience', $permintaan->requirementsAuto?->minimum_year_experience) }}"
                           placeholder="Contoh: 1"
                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Min. Pendidikan</label>
                    <select name="requirements_auto[minimum_education_level]"
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Tidak ada minimum</option>
                        @foreach(['sd','smp','sma','d1','d2','d3','d4','s1','s2','s3'] as $level)
                            <option value="{{ $level }}"
                                @selected(old('requirements_auto.minimum_education_level', $permintaan->requirementsAuto?->minimum_education_level) === $level)>
                                {{ strtoupper($level) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Card 3: Kriteria Manual --}}
        <div class="rounded-2xl p-6 sm:p-7 shadow-sm space-y-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 gap-3"
                 style="border-bottom: 1px solid var(--dash-card-border);">
                <div>
                    <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Kriteria Manual</h2>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Pemeriksaan kualifikasi spesifik yang dinilai langsung oleh tim HR.</p>
                </div>
                <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors shrink-0"
                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Kriteria
                </button>
            </div>

            @if($permintaan->requirementsManual->count())
                <div class="p-3 rounded-xl text-xs font-medium"
                     style="background-color: color-mix(in srgb, #d97706 10%, transparent); border: 1px solid color-mix(in srgb, #d97706 25%, transparent); color: #b45309;">
                    Permintaan ini telah memiliki {{ $permintaan->requirementsManual->count() }} kriteria bawaan. Tambahan baru akan digabungkan.
                </div>
            @endif

            <div class="space-y-3">
                <template x-for="(item, i) in manualItems" :key="i">
                    <div class="flex items-start gap-3 p-3.5 rounded-xl"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <div class="flex-1 space-y-2">
                            <input type="text" :name="`requirements_manual[${i}][criteria_name]`"
                                   x-model="item.criteria_name"
                                   placeholder="Nama kriteria (contoh: Kemampuan Bahasa Inggris)"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'" />
                            <textarea :name="`requirements_manual[${i}][criteria_desc]`"
                                      x-model="item.criteria_desc" rows="2"
                                      placeholder="Deskripsi atau standar penilaian kriteria..."
                                      class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                      style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                      onfocus="this.style.borderColor='var(--dash-primary)'"
                                      onblur="this.style.borderColor='var(--dash-input-border)'"></textarea>
                        </div>
                        <button type="button" @click="removeItem(i)"
                                class="inline-flex items-center justify-center p-2 rounded-lg text-white transition-opacity hover:opacity-90 shrink-0 mt-1"
                                style="background-color: var(--dash-danger, #dc2626);">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </div>
                </template>

                <template x-if="manualItems.length === 0">
                    <p class="text-xs text-center py-4" style="color: var(--dash-text-muted);">
                        Belum ada kriteria manual tambahan. Klik "+ Tambah Kriteria" untuk menambahkan.
                    </p>
                </template>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('recruitment.postings.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">close</span>
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Simpan sebagai Draft
            </button>
        </div>
    </form>
</div>
@endsection
