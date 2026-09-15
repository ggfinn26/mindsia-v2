@extends('layouts.dashboard')

@section('title', 'Kelola Kurikulum')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Kelola Kurikulum</h1>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @if($curriculums->isEmpty())
        <div class="bg-white shadow sm:rounded-lg p-8 text-center">
            <span class="material-symbols-outlined text-gray-300 text-5xl mb-3">menu_book</span>
            <p class="text-gray-500">Belum ada kurikulum. Kurikulum dibuat dari halaman detail Program (relasi 1:1).</p>
            <a href="{{ route('programs.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-900 text-sm font-medium">Lihat daftar Program &rarr;</a>
        </div>
    @else
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kurikulum</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sesi Mingguan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Materi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($curriculums as $curriculum)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $curriculum->curriculum_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $curriculum->program->program_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $curriculum->sessions->count() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $curriculum->sessions->sum(fn($s) => $s->items->count()) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $curriculum->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $curriculum->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('curriculums.show', $curriculum) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                    <a href="{{ route('curriculums.edit', $curriculum) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form action="{{ route('curriculums.destroy', $curriculum) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus kurikulum ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada kurikulum.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($curriculums->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $curriculums->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
