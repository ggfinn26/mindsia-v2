@extends('layouts.dashboard')

@section('title', 'Kelola Foto Landing Page')
@section('header_title', 'Kelola Foto Landing Page')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{ showModal: false, deleteUrl: '' }"
     @keydown.escape.window="showModal = false">

    {{-- Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Landing Page</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Foto Landing Page</h1>
            <p class="text-sm mt-1"
               style="color: var(--dash-text-muted);">Kelola foto galeri beranda dan company profile.</p>
        </div>
        @can('landing.photo.manage')
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.landing.categories.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[18px]">sell</span>
                Kelola Kategori
            </a>
            <a href="{{ route('admin.landing.photos.create', ['section' => $section]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add_photo_alternate</span>
                Tambah Foto
            </a>
        </div>
        @endcan
    </div>

    {{-- Section Tabs --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.landing.photos.index', ['section' => 'company']) }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
           @if($section === 'company')
           style="background-color: var(--dash-primary); color: #ffffff;"
           @else
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
           @endif>
            Galeri Perusahaan
        </a>
        <a href="{{ route('admin.landing.photos.index', ['section' => 'home']) }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
           @if($section === 'home')
           style="background-color: var(--dash-primary); color: #ffffff;"
           @else
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
           @endif>
            Galeri Beranda
        </a>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Content --}}
    @if($photos->isEmpty())
    <div class="p-12 text-center rounded-2xl"
         style="background-color: var(--dash-card-bg); border: 2px dashed var(--dash-card-border);">
        <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
              style="color: var(--dash-text-faint);">photo_library</span>
        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
            Belum ada foto. Klik "Tambah Foto" untuk memulai.
        </p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($photos as $photo)
        <div class="rounded-2xl transition-all duration-200 group flex flex-col"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="aspect-[4/3] rounded-t-2xl overflow-hidden"
                 style="background-color: var(--dash-nav-hover-bg);">
                <img src="{{ $photo->photo_url }}" alt="{{ $photo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            </div>
            <div class="p-4 flex flex-col justify-between flex-grow">
                <div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold mb-1.5"
                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-primary-text); border: 1px solid var(--dash-card-border);">
                        <span class="material-symbols-outlined text-[12px]">sell</span>
                        {{ $photo->category?->name ?? 'Umum' }}
                    </span>
                    <p class="text-sm font-semibold truncate" style="color: var(--dash-text-heading);">
                        {{ $photo->title ?: '(Tanpa judul)' }}
                    </p>
                    @if($photo->caption)
                    <p class="text-xs line-clamp-2 mt-1" style="color: var(--dash-text-muted);">
                        {{ $photo->caption }}
                    </p>
                    @endif
                </div>
                <div class="flex items-center justify-between mt-4 pt-3"
                     style="border-top: 1px solid var(--dash-card-border);">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $photo->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $photo->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    <div class="flex items-center gap-1.5">
                        @can('landing.photo.manage')
                        <a href="{{ route('admin.landing.photos.edit', $photo) }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                           title="Edit Foto">
                            <span class="material-symbols-outlined text-[14px]">edit</span>
                            Edit
                        </a>
                        @endcan
                        @can('landing.photo.manage')
                        <button type="button"
                                @click="deleteUrl = '{{ route('admin.landing.photos.destroy', $photo) }}'; showModal = true"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-danger, #dc2626);"
                                title="Hapus Foto">
                            <span class="material-symbols-outlined text-[14px]">delete</span>
                            Hapus
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Delete Confirmation Modal (Alpine.js - zero native confirm) --}}
    <template x-teleport="body">
        <div x-show="showModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showModal" @click.away="showModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        {{-- Header modal (rounded-t-2xl) --}}
                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Foto?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Foto yang dihapus tidak dapat dipulihkan. Apakah Anda yakin ingin melanjutkan?
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer modal (rounded-b-2xl) --}}
                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <form :action="deleteUrl" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                    Ya, Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
