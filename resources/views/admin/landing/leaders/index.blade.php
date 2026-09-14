@extends('layouts.app')

@section('title', 'Struktur Kepemimpinan')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Struktur Kepemimpinan</h1>
        @can('landing.leader.create')
        <a href="{{ route('admin.landing.leaders.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            Tambah Pemimpin
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @if($leaders->isEmpty())
    <div class="p-12 border-2 border-dashed border-gray-200 rounded-xl text-center text-gray-400">
        Belum ada data. Klik "Tambah Pemimpin" untuk memulai.
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Foto</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Jabatan</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Urutan</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($leaders as $leader)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden border border-gray-200">
                            @if($leader->photo_url)
                                <img src="{{ $leader->photo_url }}" alt="{{ $leader->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <span class="material-symbols-outlined text-[20px]">person</span>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $leader->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $leader->title }}</td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ $leader->sort_order }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $leader->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $leader->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-1">
                            @can('landing.leader.update')
                            <a href="{{ route('admin.landing.leaders.edit', $leader) }}"
                               class="px-3 py-1.5 text-blue-600 hover:bg-blue-50 rounded text-xs font-medium transition-colors">Edit</a>
                            @endcan
                            @can('landing.leader.delete')
                            <form method="POST" action="{{ route('admin.landing.leaders.destroy', $leader) }}"
                                  onsubmit="return confirm('Hapus {{ $leader->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-red-500 hover:bg-red-50 rounded text-xs font-medium transition-colors">Hapus</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
