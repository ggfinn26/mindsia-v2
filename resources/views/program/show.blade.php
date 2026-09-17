@extends('layouts.dashboard')

@section('title', $program->program_name)

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $program->program_name }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $program->program_code }} &middot; Rp {{ number_format($program->program_price, 0, ',', '.') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('programs.edit', $program) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Edit</a>
            <a href="{{ route('programs.index') }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">Kembali</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    <!-- Program Info -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $program->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $program->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $program->program_description ?: '-' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Curriculum Section (1:1) -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">📚 Kurikulum</h2>
            @if($program->curriculum)
                <a href="{{ route('curriculums.show', $program->curriculum) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Lihat Detail &rarr;</a>
            @else
                <a href="{{ route('curriculums.create') }}?program_id={{ $program->id }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-medium">Buat Kurikulum</a>
            @endif
        </div>
        <div class="px-6 py-4">
            @if($program->curriculum)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">{{ $program->curriculum->curriculum_name }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $program->curriculum->sessions->count() }} sesi &middot; {{ $program->curriculum->sessions->sum(fn($s) => $s->items->count()) }} item materi</p>
                    </div>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $program->curriculum->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $program->curriculum->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            @else
                <p class="text-gray-500 text-sm">Belum ada kurikulum untuk program ini. Buat kurikulum untuk mengelola materi dan sesi pertemuan.</p>
            @endif
        </div>
    </div>

    <!-- Branch Quota Section -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">📊 Kuota Cabang</h2>
            <span class="text-sm text-gray-500">{{ $program->branchQuotas->count() }} cabang</span>
        </div>

        @if($program->branchQuotas->isEmpty())
            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                Belum ada kuota cabang yang diset. Kelola kuota melalui menu Pengaturan Cabang.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cabang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuota</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($program->branchQuotas as $quota)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $quota->branch->branch_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $quota->quota_limit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
