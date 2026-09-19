@extends('layouts.dashboard')

@section('title', 'Tambah Program')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Program</h1>
        <a href="{{ route('programs.index') }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('programs.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow sm:rounded-lg">
        @csrf
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Program</label>
                <input type="text" name="program_code" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       value="{{ old('program_code') }}" placeholder="Contoh: TOEFL-INTENSIVE" />
                @error('program_code')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Program</label>
                <input type="text" name="program_name" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       value="{{ old('program_name') }}" placeholder="Contoh: TOEFL Intensive" />
                @error('program_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Program</label>
                <input type="number" name="program_price" required min="0" step="0.01"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       value="{{ old('program_price') }}" placeholder="0" />
                @error('program_price')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Program (Opsional)</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                @error('image')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                <textarea name="program_description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('program_description') }}</textarea>
                @error('program_description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded" />
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3 justify-end">
            <a href="{{ route('programs.index') }}" class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
