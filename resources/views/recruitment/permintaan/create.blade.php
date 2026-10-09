@extends('layouts.dashboard')

@section('title', 'Buat Job Permintaan')
@section('header_title', 'Buat Job Permintaan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    <div class="flex items-start gap-3">
        <a href="{{ route('recruitment.permintaan.index') }}"
           class="mt-1 hover:opacity-70 transition-opacity shrink-0"
           style="color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Rekrutmen</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Buat Job Permintaan</h1>
        </div>
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

    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('recruitment.permintaan.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                    Posisi yang Diminta <span class="text-red-500">*</span>
                </label>
                <select name="detail[position_id]" required
                        class="w-full rounded-lg text-sm px-3 py-2.5 focus:outline-none transition-colors"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Pilih Posisi</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}" {{ old('detail.position_id') == $position->id ? 'selected' : '' }}>
                            {{ $position->position_name }}
                        </option>
                    @endforeach
                </select>
                @error('detail.position_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Jenis Permintaan <span class="text-red-500">*</span>
                    </label>
                    <select name="detail[request_type]" required
                            class="w-full rounded-lg text-sm px-3 py-2.5 focus:outline-none transition-colors"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Jenis</option>
                        <option value="replacement" {{ old('detail.request_type') === 'replacement' ? 'selected' : '' }}>Penggantian (Replacement)</option>
                        <option value="new_position" {{ old('detail.request_type') === 'new_position' ? 'selected' : '' }}>Posisi Baru (New Position)</option>
                    </select>
                    @error('detail.request_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Tipe Pekerjaan <span class="text-red-500">*</span>
                    </label>
                    <select name="detail[employment_type]" required
                            class="w-full rounded-lg text-sm px-3 py-2.5 focus:outline-none transition-colors"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Tipe</option>
                        <option value="kontrak"     {{ old('detail.employment_type') === 'kontrak'     ? 'selected' : '' }}>Kontrak</option>
                        <option value="tetap"       {{ old('detail.employment_type') === 'tetap'       ? 'selected' : '' }}>Tetap</option>
                        <option value="magang"      {{ old('detail.employment_type') === 'magang'      ? 'selected' : '' }}>Magang</option>
                        <option value="paruh_waktu" {{ old('detail.employment_type') === 'paruh_waktu' ? 'selected' : '' }}>Paruh Waktu</option>
                    </select>
                    @error('detail.employment_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Jumlah yang Dibutuhkan <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="detail[headcount]" min="1" value="{{ old('detail.headcount', 1) }}" required
                           class="w-full rounded-lg text-sm px-3 py-2.5 focus:outline-none transition-colors"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('detail.headcount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Jumlah Saat Ini <span style="color: var(--dash-text-faint);">(opsional)</span>
                    </label>
                    <input type="number" name="detail[current_headcount]" min="0" value="{{ old('detail.current_headcount') }}"
                           placeholder="Isi jika ada"
                           class="w-full rounded-lg text-sm px-3 py-2.5 focus:outline-none transition-colors"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('detail.current_headcount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                    Target Tanggal Mulai <span style="color: var(--dash-text-faint);">(opsional)</span>
                </label>
                <input type="date" name="detail[target_start_date]" value="{{ old('detail.target_start_date') }}"
                       class="w-full rounded-lg text-sm px-3 py-2.5 focus:outline-none transition-colors"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'" />
                @error('detail.target_start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                    Deskripsi Pekerjaan <span style="color: var(--dash-text-faint);">(opsional)</span>
                </label>
                <textarea name="detail[job_description]" rows="4"
                          placeholder="Uraikan tugas dan tanggung jawab..."
                          class="w-full rounded-lg text-sm px-3 py-2.5 focus:outline-none transition-colors"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('detail.job_description') }}</textarea>
                @error('detail.job_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2 flex justify-end gap-3" style="border-top: 1px solid var(--dash-card-border); padding-top: 1.25rem;">
                <a href="{{ route('recruitment.permintaan.index') }}"
                   class="px-5 py-2.5 text-sm font-semibold rounded-xl transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</a>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    Simpan sebagai Draft
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
