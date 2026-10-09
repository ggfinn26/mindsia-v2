@extends('layouts.dashboard')

@section('title', 'Edit Foto Landing Page')

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.landing.photos.index', ['section' => $photo->section]) }}"
           class="text-sm text-blue-600 hover:underline flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
        </a>
        <h1 class="text-2xl font-bold text-[color:var(--dash-text-heading)] mt-3">Edit Foto</h1>
    </div>

    <form method="POST" action="{{ route('admin.landing.photos.update', $photo) }}" enctype="multipart/form-data"
          class="bg-[var(--dash-card-bg)] rounded-2xl border border-[color:var(--dash-card-border)] p-6 space-y-5 shadow-sm">
        @csrf @method('PUT')

        {{-- Current photo --}}
        <div>
            <p class="text-sm font-medium text-[color:var(--dash-text-body)] mb-2">Foto Saat Ini</p>
            <img src="{{ $photo->photo_url }}" alt="{{ $photo->title }}"
                 class="w-full max-h-48 object-cover rounded-lg border border-[color:var(--dash-card-border)]">
        </div>

        <div>
            <label class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Ganti Foto</label>
            <input type="file" name="photo" accept="image/webp"
                   class="block w-full text-sm text-[color:var(--dash-text-body)] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100">
            <p class="mt-1 text-xs text-[color:var(--dash-text-faint)]">Kosongkan jika tidak ingin mengubah foto. Hanya .webp, maks 5MB.</p>
            @error('photo')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Judul</label>
            <input type="text" name="title" value="{{ old('title', $photo->title) }}" maxlength="150"
                   class="w-full border border-[color:var(--dash-input-border)] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Caption</label>
            <textarea name="caption" rows="2"
                      class="w-full border border-[color:var(--dash-input-border)] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('caption', $photo->caption) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Urutan</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $photo->sort_order) }}" min="0"
                       class="w-full border border-[color:var(--dash-input-border)] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end pb-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $photo->is_active ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm font-medium text-[color:var(--dash-text-body)]">Aktif</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.landing.photos.index', ['section' => $photo->section]) }}"
               class="px-4 py-2.5 border border-[color:var(--dash-input-border)] text-[color:var(--dash-text-body)] rounded-lg text-sm font-medium hover:bg-[var(--dash-nav-hover-bg)] transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
