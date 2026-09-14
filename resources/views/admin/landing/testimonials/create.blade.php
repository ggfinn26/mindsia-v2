@extends('layouts.app')

@section('title', 'Tambah Testimoni')

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.landing.testimonials.index') }}"
           class="text-sm text-blue-600 hover:underline flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-3">Tambah Testimoni</h1>
    </div>

    <form method="POST" action="{{ route('admin.landing.testimonials.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-xl border border-gray-200 p-6 space-y-5 shadow-sm"
          x-data="{ type: '{{ old('type', $prefill['type'] ?? 'text') }}' }">
        @csrf

        @isset($prefill['member_review_id'])
        <input type="hidden" name="member_review_id" value="{{ $prefill['member_review_id'] }}">
        <div class="p-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-xs">
            Pre-filled dari Member Review #{{ $prefill['member_review_id'] }}. Edit sebelum menyimpan.
        </div>
        @endisset

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Testimoni <span class="text-red-500">*</span></label>
            <div class="flex gap-3">
                <label class="flex-1 flex items-center gap-2 p-3 border rounded-lg cursor-pointer transition-colors"
                       :class="type === 'text' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                    <input type="radio" name="type" value="text" x-model="type" class="text-blue-600">
                    <span class="text-sm font-medium">Teks (Quote)</span>
                </label>
                <label class="flex-1 flex items-center gap-2 p-3 border rounded-lg cursor-pointer transition-colors"
                       :class="type === 'image' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                    <input type="radio" name="type" value="image" x-model="type" class="text-blue-600">
                    <span class="text-sm font-medium">Gambar (Flyer)</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $prefill['name'] ?? '') }}" maxlength="150" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div x-show="type === 'text'" x-transition>
            <label class="block text-sm font-medium text-gray-700 mb-1">Quote <span class="text-red-500">*</span></label>
            <textarea name="quote" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('quote', $prefill['quote'] ?? '') }}</textarea>
            @error('quote')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div x-show="type === 'text'" x-transition class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                <input type="text" name="program" value="{{ old('program', $prefill['program'] ?? '') }}" maxlength="100"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                <input type="text" name="city" value="{{ old('city') }}" maxlength="100"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div x-show="type === 'image'" x-transition>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto/Flyer <span class="text-red-500">*</span></label>
            <input type="file" name="photo" accept="image/webp"
                   class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100">
            <p class="mt-1 text-xs text-gray-400">Hanya format .webp, maksimal 5MB.</p>
            @error('photo')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end pb-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm font-medium text-gray-700">Aktif</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                Simpan Testimoni
            </button>
            <a href="{{ route('admin.landing.testimonials.index') }}"
               class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
