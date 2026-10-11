@extends('layouts.dashboard')

@section('title', 'Tambah Test - ' . $classroom->class_name)
@section('header_title', 'Tambah Test')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6"
     x-data="{
         criteria: {{ old('criteria') ? json_encode(old('criteria')) : '[]' }},
         addCriterion() {
             this.criteria.push({ criteria_name: '', description: '' });
         }
     }"
     id="criteria-container">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('classrooms.show', $classroom) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke Kelas
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Akademik</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Tambah Test Baru</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Kelas: <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $classroom->class_name }}</span>
        </p>
    </div>

    {{-- Flash Errors --}}
    @if ($errors->any())
        <div class="p-4 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <div class="flex items-center gap-2 mb-2 font-semibold">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>Terdapat kesalahan pada formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <form action="{{ route('class-tests.store', $classroom) }}" method="POST"
          class="rounded-2xl border shadow-sm"
          style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        @csrf

        {{-- Card Header --}}
        <div class="px-6 py-4 rounded-t-2xl"
             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                Informasi Test
            </h2>
        </div>

        <div class="p-6 space-y-6">
            {{-- Test Name --}}
            <div>
                <label for="test_name" class="block text-sm font-medium mb-1.5"
                       style="color: var(--dash-text-body);">
                    Nama Test <span class="text-red-500">*</span>
                </label>
                <input id="test_name" type="text" name="test_name" required
                       value="{{ old('test_name') }}"
                       placeholder="Contoh: Pre-Test TOEFL Batch 5"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                @error('test_name')
                    <p class="text-xs mt-1" style="color: var(--dash-danger);">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Test Type --}}
                <div>
                    <label for="test_type" class="block text-sm font-medium mb-1.5"
                           style="color: var(--dash-text-body);">
                        Tipe Test <span class="text-red-500">*</span>
                    </label>
                    <select id="test_type" name="test_type" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Tipe Test</option>
                        <option value="pre_test" @selected(old('test_type') === 'pre_test')>Pre-Test</option>
                        <option value="post_test" @selected(old('test_type') === 'post_test')>Post-Test</option>
                    </select>
                    @error('test_type')
                        <p class="text-xs mt-1" style="color: var(--dash-danger);">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date --}}
                <div>
                    <label for="date" class="block text-sm font-medium mb-1.5"
                           style="color: var(--dash-text-body);">
                        Tanggal Test <span class="text-red-500">*</span>
                    </label>
                    <input id="date" type="date" name="date" required
                           value="{{ old('date') }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'" />
                    @error('date')
                        <p class="text-xs mt-1" style="color: var(--dash-danger);">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium mb-1.5"
                       style="color: var(--dash-text-body);">Deskripsi</label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Deskripsi singkat tentang pelaksanaan test ini..."
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs mt-1" style="color: var(--dash-danger);">{{ $message }}</p>
                @enderror
            </div>

            {{-- Scoring Criteria Section --}}
            <div class="pt-6 border-t" style="border-color: var(--dash-card-border);">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                            Kriteria Penilaian
                        </h3>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                            Tambahkan kriteria penilaian khusus untuk test ini.
                        </p>
                    </div>
                    <button type="button" @click="addCriterion()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-primary-text);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        Tambah Kriteria
                    </button>
                </div>

                <div class="mt-4 space-y-3">
                    <template x-for="(criterion, index) in criteria" :key="index">
                        <div class="p-4 rounded-xl border space-y-3"
                             style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                            <div class="flex items-start gap-3">
                                <div class="flex-1 space-y-3">
                                    <div>
                                        <label class="block text-xs font-semibold mb-1"
                                               style="color: var(--dash-text-body);">
                                            Nama Kriteria <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" :name="'criteria[' + index + '][criteria_name]'"
                                               x-model="criterion.criteria_name" required
                                               placeholder="Contoh: Listening Comprehension, Structure & Written Expression"
                                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                               onfocus="this.style.borderColor='var(--dash-primary)'"
                                               onblur="this.style.borderColor='var(--dash-input-border)'" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold mb-1"
                                               style="color: var(--dash-text-body);">
                                            Deskripsi Kriteria
                                        </label>
                                        <textarea :name="'criteria[' + index + '][description]'"
                                                  x-model="criterion.description" rows="2"
                                                  placeholder="Penjelasan rubrik atau panduan penilaian kriteria ini..."
                                                  class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                                  style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                  onfocus="this.style.borderColor='var(--dash-primary)'"
                                                  onblur="this.style.borderColor='var(--dash-input-border)'"></textarea>
                                    </div>
                                </div>
                                <button type="button" @click="criteria.splice(index, 1)"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-opacity hover:opacity-90 shrink-0 mt-6"
                                        style="background-color: #dc2626;"
                                        title="Hapus kriteria">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="criteria.length === 0"
                         class="text-center py-6 px-4 rounded-xl border border-dashed"
                         style="border-color: var(--dash-card-border); color: var(--dash-text-muted);">
                        <span class="material-symbols-outlined text-[32px] block mx-auto mb-1"
                              style="color: var(--dash-text-faint);">playlist_add</span>
                        <p class="text-xs">Belum ada kriteria. Klik "Tambah Kriteria" untuk menambahkan rubrik penilaian.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Footer --}}
        <div class="px-6 py-4 rounded-b-2xl flex items-center justify-end gap-3"
             style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
            <a href="{{ route('classrooms.show', $classroom) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Simpan Test
            </button>
        </div>
    </form>
</div>
@endsection
