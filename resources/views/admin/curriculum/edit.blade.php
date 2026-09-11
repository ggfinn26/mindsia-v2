@extends('layouts.app')

@section('title', 'Edit ' . $curriculum->curriculum_name)

@section('content')
    <div class="container py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Kurikulum</h1>
                <p class="text-gray-600 mt-2">{{ $curriculum->curriculum_name }}</p>
            </div>
            <a href="{{ route('curriculums.show', $curriculum) }}"
               class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                Kembali
            </a>
        </div>

        <form action="{{ route('curriculums.update', $curriculum) }}" method="POST" class="bg-white rounded-lg shadow p-8">
            @csrf
            @method('PUT')

            <div class="grid gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                    <select name="program_id" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" {{ $program->id === $curriculum->program_id ? 'selected' : '' }}>
                                {{ $program->program_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Program tidak dapat diubah</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kurikulum</label>
                    <input type="text" name="curriculum_name" required value="{{ $curriculum->curriculum_name }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                    @error('curriculum_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ $curriculum->description }}</textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ $curriculum->is_active ? 'checked' : '' }} class="rounded" />
                        <span class="text-sm text-gray-700">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6 flex gap-3 justify-end">
                <a href="{{ route('curriculums.show', $curriculum) }}" class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <!-- Manage Sessions Section -->
        <div class="bg-white rounded-lg shadow p-8 mt-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📅 Kelola Sesi</h2>

            @if ($curriculum->sessions->isEmpty())
                <p class="text-gray-600 text-center py-8">Belum ada sesi. Lihat detail kurikulum untuk menambah sesi.</p>
            @else
                <div class="space-y-4">
                    @foreach ($curriculum->sessions->sortBy('sort_order') as $session)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-bold text-gray-900">Sesi {{ $session->session_number }}: {{ $session->session_title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $session->items->count() }} item</p>
                                </div>
                                <a href="{{ route('curriculums.show', $curriculum) . '#session-' . $session->id }}"
                                   class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded">
                                    Kelola
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('curriculums.show', $curriculum) }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    + Tambah Sesi / Kelola Konten
                </a>
            </div>
        </div>
    </div>
@endsection
