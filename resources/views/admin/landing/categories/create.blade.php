@extends('layouts.dashboard')

@section('title', 'Tambah Kategori Foto')
@section('header_title', 'Tambah Kategori Foto')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    <div>
        <a href="{{ route('admin.landing.categories.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Kategori
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Landing Page</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Tambah Kategori</h1>
    </div>

    <div class="rounded-2xl p-6 sm:p-8"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="POST" action="{{ route('admin.landing.categories.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" maxlength="100" required
                       placeholder="Contoh: Rakernas 2026"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Urutan Tampilan
                </label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);">Angka kecil tampil lebih dulu di chip filter.</p>
                @error('sort_order')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4"
                 style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('admin.landing.categories.index') }}"
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
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
