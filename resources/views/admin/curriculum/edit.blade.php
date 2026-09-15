@extends('layouts.dashboard')

@section('title', 'Edit ' . $curriculum->curriculum_name)

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Kurikulum</h1>
        <a href="{{ route('curriculums.show', $curriculum) }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">Kembali</a>
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

    <form action="{{ route('curriculums.update', $curriculum) }}" method="POST" class="bg-white shadow sm:rounded-lg">
        @csrf
        @method('PUT')

        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                <select name="program_id" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}" {{ $program->id === $curriculum->program_id ? 'selected' : '' }}>
                            {{ $program->program_name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Program tidak dapat diubah (relasi 1:1)</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kurikulum</label>
                <input type="text" name="curriculum_name" required value="{{ old('curriculum_name', $curriculum->curriculum_name) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                @error('curriculum_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $curriculum->description) }}</textarea>
            </div>

            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $curriculum->is_active) ? 'checked' : '' }} class="rounded" />
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3 justify-end">
            <a href="{{ route('curriculums.show', $curriculum) }}" class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </div>
    </form>

    <!-- Manage Sessions Section -->
    <div class="bg-white shadow sm:rounded-lg mt-6">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">📅 Sesi Pertemuan Mingguan</h2>
        </div>
        <div class="px-6 py-4">
            @if ($curriculum->sessions->isEmpty())
                <p class="text-gray-500 text-sm py-6 text-center">Belum ada sesi. Lihat detail kurikulum untuk menambah sesi.</p>
            @else
                <div class="space-y-3">
                    @foreach ($curriculum->sessions->sortBy('sort_order') as $session)
                        <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900 text-sm">Minggu {{ $session->session_number }}: {{ $session->session_title }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $session->items->count() }} item materi</p>
                            </div>
                            <a href="{{ route('curriculums.show', $curriculum) . '#session-' . $session->id }}"
                               class="px-3 py-1.5 text-blue-600 hover:bg-blue-50 rounded text-sm">Kelola</a>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('curriculums.show', $curriculum) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                    + Tambah Sesi / Kelola Konten
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
