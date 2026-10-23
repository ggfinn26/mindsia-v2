@extends('layouts.dashboard')

@section('title', 'Edit Job Permintaan')
@section('header_title', 'Edit Job Permintaan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('recruitment.permintaan.show', $jobPermintaan) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Detail Permintaan
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Rekrutmen</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Job Permintaan</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui data kebutuhan permintaan tenaga kerja berstatus draft.
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

    {{-- Form Card --}}
    <div class="rounded-2xl p-6 sm:p-7 shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('recruitment.permintaan.update', $jobPermintaan) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Posisi yang Diminta <span class="text-red-500">*</span>
                </label>
                <select name="detail[position_id]" required
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Pilih Posisi</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}"
                            @selected(old('detail.position_id', $jobPermintaan->detail?->position_id) == $position->id)>
                            {{ $position->position_name }}
                        </option>
                    @endforeach
                </select>
                @error('detail.position_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Jenis Permintaan <span class="text-red-500">*</span>
                    </label>
                    <select name="detail[request_type]" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Jenis Permintaan</option>
                        <option value="replacement"
                            @selected(old('detail.request_type', $jobPermintaan->detail?->request_type) === 'replacement')>
                            Penggantian (Replacement)
                        </option>
                        <option value="new_position"
                            @selected(old('detail.request_type', $jobPermintaan->detail?->request_type) === 'new_position')>
                            Posisi Baru (New Position)
                        </option>
                    </select>
                    @error('detail.request_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Tipe Pekerjaan <span class="text-red-500">*</span>
                    </label>
                    @php $empType = old('detail.employment_type', $jobPermintaan->detail?->employment_type); @endphp
                    <select name="detail[employment_type]" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Tipe Pekerjaan</option>
                        <option value="kontrak" @selected($empType === 'kontrak')>Kontrak</option>
                        <option value="tetap" @selected($empType === 'tetap')>Tetap</option>
                        <option value="magang" @selected($empType === 'magang')>Magang</option>
                        <option value="paruh_waktu" @selected($empType === 'paruh_waktu')>Paruh Waktu</option>
                    </select>
                    @error('detail.employment_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Jumlah yang Dibutuhkan <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="detail[headcount]" min="1" required
                           value="{{ old('detail.headcount', $jobPermintaan->detail?->headcount ?? 1) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('detail.headcount')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Jumlah Saat Ini (opsional)
                    </label>
                    <input type="number" name="detail[current_headcount]" min="0"
                           value="{{ old('detail.current_headcount', $jobPermintaan->detail?->current_headcount) }}"
                           placeholder="Isi jika ada"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('detail.current_headcount')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Target Tanggal Mulai (opsional)
                </label>
                <input type="date" name="detail[target_start_date]"
                       value="{{ old('detail.target_start_date', $jobPermintaan->detail?->target_start_date?->format('Y-m-d')) }}"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                @error('detail.target_start_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Deskripsi Pekerjaan (opsional)
                </label>
                <textarea name="detail[job_description]" rows="4"
                          placeholder="Uraikan tugas dan tanggung jawab..."
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('detail.job_description', $jobPermintaan->detail?->job_description) }}</textarea>
                @error('detail.job_description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-3 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('recruitment.permintaan.show', $jobPermintaan) }}"
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
