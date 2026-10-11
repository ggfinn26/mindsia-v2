@extends('layouts.dashboard')

@section('title', 'Kelola Foto Landing Page')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8" x-data="{ showModal: false, deleteUrl: '' }" @keydown.escape.window="showModal = false">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Foto Landing Page</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola foto galeri beranda dan company profile.</p>
        </div>
        @can('landing.photo.manage')
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
                        @can('landing.photo.manage')
                        <a href="{{ route('admin.landing.photos.edit', $photo) }}"
                           class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors">
                            <span class="material-symbols-outlined text-[16px]">edit</span>
                        </a>
                        @endcan
                        @can('landing.photo.manage')
                        <button type="button"
                            @click="deleteUrl = '{{ route('admin.landing.photos.destroy', $photo) }}'; showModal = true"
                            class="p-1.5 text-red-500 hover:bg-red-50 rounded transition-colors">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none" @click.self="showModal = false">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-600 text-[20px]">delete</span>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Hapus Foto</h3>
                    <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showModal = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                <form :action="deleteUrl" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
