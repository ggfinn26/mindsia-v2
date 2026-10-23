@extends('layouts.dashboard')

@section('title', 'Edit Pemimpin')
@section('header_title', 'Edit Pemimpin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Header Pola B3 --}}
    <div>
        <a href="{{ route('admin.landing.leaders.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Struktur Kepemimpinan
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Landing Page</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Pemimpin</h1>
        <p class="text-sm mt-1"
           style="color: var(--dash-text-muted);">Perbarui informasi pemimpin atau ganti foto profil.</p>
    </div>

    {{-- Form Card --}}
    <div class="rounded-2xl p-6 sm:p-8"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="POST" action="{{ route('admin.landing.leaders.update', $leader) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Photo Preview if exists --}}
            @if($leader->photo_url)
            <div>
                <p class="text-sm font-medium mb-2" style="color: var(--dash-text-body);">Foto Saat Ini</p>
                <div class="w-20 h-20 rounded-full overflow-hidden border"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <img src="{{ $leader->photo_url }}" alt="{{ $leader->name }}" class="w-full h-full object-cover">
                </div>
            </div>
            @endif

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $leader->name) }}" maxlength="150" required
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jabatan --}}
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Jabatan / Posisi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $leader->title) }}" maxlength="100" required
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                @error('title')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ganti Foto --}}
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Ganti Foto
                </label>
                <input type="file" name="photo" accept="image/webp"
                       class="block w-full text-sm rounded-lg file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold transition-all cursor-pointer"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'">
                <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);">Kosongkan jika tidak ingin mengubah. Hanya format .webp, maksimal 5MB.</p>
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
                    <input type="number" name="sort_order" value="{{ old('sort_order', $leader->sort_order) }}" min="0"
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
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $leader->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-blue-600 focus:ring-0 cursor-pointer">
                        <span class="text-sm font-medium" style="color: var(--dash-text-body);">Status Aktif (Tampilkan di web)</span>
                    </label>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4"
                 style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('admin.landing.leaders.index') }}"
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
