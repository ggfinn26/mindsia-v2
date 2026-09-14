@extends('layouts.app')

@section('title', 'Edit Testimoni')

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.landing.testimonials.index') }}"
           class="text-sm text-blue-600 hover:underline flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-3">Edit Testimoni</h1>
    </div>

    <form method="POST" action="{{ route('admin.landing.testimonials.update', $testimonial) }}" enctype="multipart/form-data"
          class="bg-white rounded-xl border border-gray-200 p-6 space-y-5 shadow-sm">
        @csrf @method('PUT')

        <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-500">
            Tipe testimoni: <strong class="text-gray-700">{{ $testimonial->type->value === 'image' ? 'Gambar' : 'Teks' }}</strong> (tidak dapat diubah setelah dibuat)
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name', $testimonial->name) }}" maxlength="150"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        @if($testimonial->isImage())
        @if($testimonial->image_url)
        <div>
            <p class="text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini</p>
            <img src="{{ $testimonial->image_url }}" alt="{{ $testimonial->name }}"
                 class="max-h-48 rounded-lg border border-gray-200">
        </div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Gambar</label>
            <input type="file" name="photo" accept="image/webp"
                   class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100">
            <p class="mt-1 text-xs text-gray-400">Kosongkan jika tidak ingin mengubah. Hanya .webp, maks 5MB.</p>
        </div>
        @else
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Quote</label>
            <textarea name="quote" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('quote', $testimonial->quote) }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                <input type="text" name="program" value="{{ old('program', $testimonial->program) }}" maxlength="100"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                <input type="text" name="city" value="{{ old('city', $testimonial->city) }}" maxlength="100"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end pb-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $testimonial->is_active ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm font-medium text-gray-700">Aktif</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.landing.testimonials.index') }}"
               class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
