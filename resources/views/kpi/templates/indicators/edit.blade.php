@extends('layouts.dashboard')

@section('title', 'Edit Indikator KPI')
@section('header_title', 'Edit Indikator')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('kpi.templates.show', $template) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke {{ $template->template_name }}
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">KPI - Template</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Indikator</h1>
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
        <form method="POST" action="{{ route('kpi.templates.indicators.update', [$template, $indicator]) }}"
              class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Kode Indikator <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="indicator_code"
                           value="{{ old('indicator_code', $indicator->indicator_code) }}"
                           required maxlength="100"
                           class="w-full px-4 py-2.5 text-sm rounded-lg uppercase focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">ID unik huruf kapital. Contoh: <span class="font-mono">HADIR_RATE</span>, <span class="font-mono">MPI_SCORE</span>.</p>
                    @error('indicator_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Nama Indikator <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="indicator_name"
                           value="{{ old('indicator_name', $indicator->indicator_name) }}"
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
                    <input type="text" name="unit"
                           value="{{ old('unit', $indicator->unit) }}"
                           maxlength="50"
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
                    <input type="number" name="target_value"
                           value="{{ old('target_value', $indicator->target_value) }}"
                           required step="0.01"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('target_value')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Bobot (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="weight"
                           value="{{ old('weight', $indicator->weight) }}"
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
                    <input type="number" name="sequence_number"
                           value="{{ old('sequence_number', $indicator->sequence_number) }}"
                           required min="1"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">Angka kecil tampil lebih atas di form evaluasi.</p>
                    @error('sequence_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Sumber Data Otomatis</label>
                <select name="data_source_type"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Manual (diisi oleh evaluator)</option>
                    @foreach ($allowedSources as $source)
                        <option value="{{ $source }}" @selected(old('data_source_type', $indicator->data_source_type) === $source)>{{ $sourceLabels[$source] ?? $source }}</option>
                    @endforeach
                </select>
                @error('data_source_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Deskripsi</label>
                <textarea name="description" rows="2"
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('description', $indicator->description) }}</textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" id="is_active"
                       @checked(old('is_active', $indicator->is_active))
                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="text-sm font-medium" style="color: var(--dash-text-body);">Aktif</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>Simpan Perubahan
                </button>
                <a href="{{ route('kpi.templates.show', $template) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</a>
            </div>
        </form>
    </div>

    {{-- Danger zone: nonaktifkan indikator --}}
    <div x-data="{ showConfirm: false }" class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid #fecaca;">
        <div class="px-6 py-4" style="border-bottom: 1px solid #fecaca; background-color: #fef2f2;">
            <h2 class="font-semibold text-sm text-red-700">Zona Berbahaya</h2>
        </div>
        <div class="px-6 py-5 flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">Nonaktifkan indikator ini</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Indikator yang nonaktif tidak muncul di evaluasi baru.</p>
            </div>
            <button type="button" @click="showConfirm = true"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                    style="background-color: #dc2626;">
                <span class="material-symbols-outlined text-[16px]">toggle_off</span>Nonaktifkan
            </button>
        </div>

        <template x-teleport="body">
            <div x-show="showConfirm" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
                <div x-show="showConfirm"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                     @click="showConfirm = false"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div x-show="showConfirm" @click.away="showConfirm = false"
                             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                            <div class="rounded-t-2xl px-6 py-5"
                                 style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                    </div>
                                    <div>
                                        <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Nonaktifkan Indikator?</h3>
                                        <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                            Indikator tidak akan muncul di evaluasi baru. Data evaluasi lama tidak terpengaruh.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showConfirm = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <form method="POST" action="{{ route('kpi.templates.indicators.destroy', [$template, $indicator]) }}" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                            style="background-color: #dc2626;">
                                        <span class="material-symbols-outlined text-[16px]">toggle_off</span>Ya, Nonaktifkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection
