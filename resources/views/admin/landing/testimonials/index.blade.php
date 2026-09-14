@extends('layouts.app')

@section('title', 'Kelola Testimoni')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Testimoni Landing Page</h1>
        @can('landing.testimonial.create')
        <div class="flex gap-2">
            <a href="{{ route('admin.landing.testimonials.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Testimoni
            </a>
        </div>
        @endcan
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @if($testimonials->isEmpty())
    <div class="p-12 border-2 border-dashed border-gray-200 rounded-xl text-center text-gray-400">
        Belum ada testimoni. Klik "Tambah Testimoni" untuk memulai.
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tipe</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Konten</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($testimonials as $testi)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $testi->type->value === 'image' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $testi->type->value === 'image' ? 'Gambar' : 'Teks' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $testi->name }}</td>
                    <td class="px-4 py-3 text-gray-500 max-w-xs">
                        @if($testi->isImage())
                            <img src="{{ $testi->image_url }}" alt="{{ $testi->name }}" class="h-10 w-16 object-cover rounded">
                        @else
                            <span class="line-clamp-2">{{ $testi->quote }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $testi->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $testi->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-1">
                            @can('landing.testimonial.update')
                            <a href="{{ route('admin.landing.testimonials.edit', $testi) }}"
                               class="px-3 py-1.5 text-blue-600 hover:bg-blue-50 rounded text-xs font-medium transition-colors">Edit</a>
                            @endcan
                            @can('landing.testimonial.delete')
                            <form method="POST" action="{{ route('admin.landing.testimonials.destroy', $testi) }}"
                                  onsubmit="return confirm('Hapus testimoni ini?')">
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
