@extends('layouts.dashboard')

@section('title', 'Tambah Testimoni')
@section('header_title', 'Tambah Testimoni')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Header Pola B3 --}}
    <div>
        <a href="{{ route('admin.landing.testimonials.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Kelola Testimoni
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Landing Page</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Tambah Testimoni</h1>
        <p class="text-sm mt-1"
           style="color: var(--dash-text-muted);">Tambahkan ulasan atau cerita pengalaman alumni baru.</p>
    </div>

    {{-- Form Card --}}
    <div class="rounded-2xl p-6 sm:p-8"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
         x-data="{ type: '{{ old('type', $prefill['type'] ?? 'text') }}' }">
        <form method="POST" action="{{ route('admin.landing.testimonials.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            @isset($prefill['member_review_id'])
            <input type="hidden" name="member_review_id" value="{{ $prefill['member_review_id'] }}">
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-xs font-medium"
                 style="background-color: #eff6ff; border-color: #bfdbfe; color: #1d4ed8;">
                <span class="material-symbols-outlined text-[18px]">info</span>
                <span>Pre-filled dari Member Review #{{ $prefill['member_review_id'] }}. Periksa dan sesuaikan sebelum menyimpan.</span>
            </div>
            @endisset

            {{-- Tipe Testimoni --}}
            <div>
                <label class="block text-sm font-medium mb-2" style="color: var(--dash-text-body);">
                    Tipe Testimoni <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3.5 rounded-xl cursor-pointer transition-all border"
                           :style="type === 'text' ? 'border-color: var(--dash-primary); background-color: var(--dash-nav-hover-bg);' : 'border-color: var(--dash-card-border); background-color: var(--dash-card-bg);'">
                        <input type="radio" name="type" value="text" x-model="type" class="text-blue-600 focus:ring-0">
                        <div>
                            <span class="text-sm font-semibold block" style="color: var(--dash-text-heading);">Teks (Kutipan Ulasan)</span>
                            <span class="text-xs" style="color: var(--dash-text-muted);">Format kutipan teks disertai nama dan program</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3.5 rounded-xl cursor-pointer transition-all border"
                           :style="type === 'image' ? 'border-color: var(--dash-primary); background-color: var(--dash-nav-hover-bg);' : 'border-color: var(--dash-card-border); background-color: var(--dash-card-bg);'">
                        <input type="radio" name="type" value="image" x-model="type" class="text-blue-600 focus:ring-0">
                        <div>
                            <span class="text-sm font-semibold block" style="color: var(--dash-text-heading);">Gambar (Flyer / Desain)</span>
                            <span class="text-xs" style="color: var(--dash-text-muted);">Format flyer gambar testimoni visual</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Nama Alumni / Siswa <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $prefill['name'] ?? '') }}" maxlength="150" required
                       placeholder="Contoh: Sarah Aulia"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Quote (Text Type) --}}
            <div x-show="type === 'text'" x-transition>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Kutipan Testimoni <span class="text-red-500">*</span>
                </label>
                <textarea name="quote" rows="3"
                          placeholder="Ceritakan pengalaman belajar di MINDSIA..."
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('quote', $prefill['quote'] ?? '') }}</textarea>
                @error('quote')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Program & Kota (Text Type) --}}
            <div x-show="type === 'text'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Program yang Diikuti
                    </label>
                    <input type="text" name="program" value="{{ old('program', $prefill['program'] ?? '') }}" maxlength="100"
                           placeholder="Contoh: Intensive English Camp"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('program')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Kota Domisili / Cabang
                    </label>
                    <input type="text" name="city" value="{{ old('city') }}" maxlength="100"
                           placeholder="Contoh: Bandung"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('city')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Foto / Flyer (Image Type) --}}
            <div x-show="type === 'image'" x-transition>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Berkas Foto / Flyer <span class="text-red-500">*</span>
                </label>
                <input type="file" name="photo" accept="image/webp"
                       class="block w-full text-sm rounded-lg file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold transition-all cursor-pointer"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'">
                <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);">Hanya format file .webp, ukuran maksimal 5MB.</p>
                @error('photo')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Urutan --}}
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Urutan Tampilan
                    </label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('sort_order')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status Aktif --}}
                <div class="flex items-center sm:pt-7">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-blue-600 focus:ring-0 cursor-pointer">
                        <span class="text-sm font-medium" style="color: var(--dash-text-body);">Status Aktif (Tampilkan di web)</span>
                    </label>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4"
                 style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('admin.landing.testimonials.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Simpan Testimoni
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
