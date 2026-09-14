@extends('layouts.app')

@section('title', 'Kelola Foto Landing Page')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Foto Landing Page</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola foto galeri beranda dan company profile.</p>
        </div>
        @can('landing.photo.create')
        <a href="{{ route('admin.landing.photos.create', ['section' => $section]) }}"
           class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            <span class="material-symbols-outlined text-[18px]">add_photo_alternate</span>
            Tambah Foto
        </a>
        @endcan
    </div>

    {{-- Section Tabs --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('admin.landing.photos.index', ['section' => 'company']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $section === 'company' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
            Galeri Perusahaan
        </a>
        <a href="{{ route('admin.landing.photos.index', ['section' => 'home']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $section === 'home' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
            Galeri Beranda
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @if($photos->isEmpty())
    <div class="p-12 border-2 border-dashed border-gray-200 rounded-xl text-center text-gray-400">
        Belum ada foto. Klik "Tambah Foto" untuk memulai.
    </div>
    @else
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($photos as $photo)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm group">
            <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                <img src="{{ $photo->photo_url }}" alt="{{ $photo->title }}" class="w-full h-full object-cover">
            </div>
            <div class="p-3">
                <p class="text-sm font-semibold text-gray-800 truncate">{{ $photo->title ?: '(Tanpa judul)' }}</p>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $photo->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $photo->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    <div class="flex gap-1">
                        @can('landing.photo.update')
                        <a href="{{ route('admin.landing.photos.edit', $photo) }}"
                           class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors">
                            <span class="material-symbols-outlined text-[16px]">edit</span>
                        </a>
                        @endcan
                        @can('landing.photo.delete')
                        <form method="POST" action="{{ route('admin.landing.photos.destroy', $photo) }}"
                              onsubmit="return confirm('Hapus foto ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded transition-colors">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
