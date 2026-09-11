@extends('layouts.app')

@section('title', 'Kelola Kurikulum')

@section('content')
    <div class="container py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Kurikulum</h1>
                <p class="text-gray-600 mt-2">Daftar semua kurikulum dan kontennya</p>
            </div>
            <a href="{{ route('curriculums.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Buat Kurikulum
            </a>
        </div>

        @if ($curriculums->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-600">Belum ada kurikulum. Mulai buat kurikulum baru.</p>
            </div>
        @else
            <div class="grid gap-6">
                @foreach ($curriculums as $curriculum)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $curriculum->curriculum_name }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">Program: {{ $curriculum->program->program_name }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $curriculum->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $curriculum->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            @if ($curriculum->description)
                                <p class="text-gray-700 mb-4">{{ $curriculum->description }}</p>
                            @endif

                            <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                                <span>📚 {{ $curriculum->sessions->count() }} sesi</span>
                                <span>📄 {{ $curriculum->sessions->sum(fn($s) => $s->items->count()) }} item</span>
                            </div>

                            <div class="flex gap-3 pt-4 border-t border-gray-200">
                                <a href="{{ route('curriculums.show', $curriculum) }}"
                                   class="px-3 py-2 text-blue-600 hover:bg-blue-50 rounded">
                                    Lihat Detail
                                </a>
                                <a href="{{ route('curriculums.edit', $curriculum) }}"
                                   class="px-3 py-2 text-blue-600 hover:bg-blue-50 rounded">
                                    Edit
                                </a>
                                <form action="{{ route('curriculums.destroy', $curriculum) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus kurikulum ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 text-red-600 hover:bg-red-50 rounded">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $curriculums->links() }}
            </div>
        @endif
    </div>
@endsection
