@extends('layouts.dashboard')

@section('title', 'Tambah Indikator KPI')
@section('header_title', 'Tambah Indikator')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('kpi.templates.show', $template) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke {{ $template->template_name }}
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">KPI - Template</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Tambah Indikator</h1>
    </div>

    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Data Indikator</h2>
        </div>
        <form method="POST" action="{{ route('kpi.templates.indicators.store', $template) }}"
              class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Kode Indikator <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="indicator_code" value="{{ old('indicator_code') }}"
                           required maxlength="100" placeholder="HADIR_RATE"
                           class="w-full px-4 py-2.5 text-sm rounded-lg uppercase focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">ID unik huruf kapital, dipakai sistem. Contoh: <span class="font-mono">HADIR_RATE</span>, <span class="font-mono">MPI_SCORE</span>.</p>
                    @error('indicator_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Nama Indikator <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="indicator_name" value="{{ old('indicator_name') }}"
                           required maxlength="255"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">Nama yang tampil ke pegawai saat mengisi evaluasi.</p>
                    @error('indicator_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Satuan</label>
                    <input type="text" name="unit" value="{{ old('unit') }}" maxlength="50"
                           placeholder="%, hari, sesi..."
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">Label angka hasil evaluasi. Contoh: <span class="font-mono">%</span>, <span class="font-mono">hari</span>, <span class="font-mono">sesi</span>.</p>
                    @error('unit')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Target Nilai <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="target_value" value="{{ old('target_value') }}"
                           required step="0.01"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">Nilai ideal yang ingin dicapai (100%, 20 sesi, dst).</p>
                    @error('target_value')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Bobot (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="weight" value="{{ old('weight') }}"
                           required min="0" max="100" step="0.01"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">Porsi indikator ini dari total KPI. Semua bobot harus berjumlah 100%.</p>
                    @error('weight')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Urutan <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="sequence_number" value="{{ old('sequence_number', $nextSequence) }}"
                           required min="1"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">Urutan tampil di form evaluasi. Angka kecil tampil lebih atas.</p>
                    @error('sequence_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                    Sumber Data Otomatis
                    <span class="text-xs font-normal ml-1" style="color: var(--dash-text-faint);">(kosongkan jika diisi manual)</span>
                </label>
                <select name="data_source_type"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Manual (diisi oleh evaluator)</option>
                    @foreach ($allowedSources as $source)
                        <option value="{{ $source }}" @selected(old('data_source_type') === $source)>{{ $sourceLabels[$source] ?? $source }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">Indikator otomatis diisi dari data sistem saat evaluasi dibuat.</p>
                @error('data_source_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Deskripsi</label>
                <textarea name="description" rows="2"
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" id="is_active"
                       @checked(old('is_active', true))
                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="text-sm font-medium" style="color: var(--dash-text-body);">Aktif</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">add</span>Tambah Indikator
                </button>
                <a href="{{ route('kpi.templates.show', $template) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
