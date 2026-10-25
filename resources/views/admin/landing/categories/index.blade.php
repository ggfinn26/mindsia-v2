@extends('layouts.dashboard')

@section('title', 'Kelola Kategori Foto')
@section('header_title', 'Kelola Kategori Foto')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{ showModal: false, deleteUrl: '' }"
     @keydown.escape.window="showModal = false">

    {{-- Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('admin.landing.photos.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Kelola Foto
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Landing Page</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kategori Foto</h1>
            <p class="text-sm mt-1"
               style="color: var(--dash-text-muted);">Kelompokkan foto galeri (mis. Rakernas 2026, PINE, English Camp).</p>
        </div>
        @can('landing.photo.manage')
        <a href="{{ route('admin.landing.categories.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Kategori
        </a>
        @endcan
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- Table --}}
    @if($categories->isEmpty())
    <div class="p-12 text-center rounded-2xl"
         style="background-color: var(--dash-card-bg); border: 2px dashed var(--dash-card-border);">
        <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
              style="color: var(--dash-text-faint);">sell</span>
        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
            Belum ada kategori. Klik "Tambah Kategori" untuk memulai.
        </p>
    </div>
    @else
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <th class="text-left font-semibold px-5 py-3" style="color: var(--dash-text-muted);">Nama Kategori</th>
                    <th class="text-left font-semibold px-5 py-3" style="color: var(--dash-text-muted);">Jumlah Foto</th>
                    <th class="text-left font-semibold px-5 py-3" style="color: var(--dash-text-muted);">Urutan</th>
                    <th class="text-right font-semibold px-5 py-3" style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr style="border-bottom: 1px solid var(--dash-card-border);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <td class="px-5 py-3 font-semibold" style="color: var(--dash-text-heading);">{{ $category->name }}</td>
                    <td class="px-5 py-3" style="color: var(--dash-text-body);">{{ $category->photos_count }} foto</td>
                    <td class="px-5 py-3" style="color: var(--dash-text-body);">{{ $category->sort_order }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('landing.photo.manage')
                            <a href="{{ route('admin.landing.categories.edit', $category) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">edit</span>
                                Edit
                            </a>
                            <button type="button"
                                    @click="deleteUrl = '{{ route('admin.landing.categories.destroy', $category) }}'; showModal = true"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-danger, #dc2626);">
                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                Hapus
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
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

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Kategori?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Kategori yang masih dipakai foto tidak dapat dihapus. Pindahkan foto ke kategori lain terlebih dahulu.
                                    </p>
                                </div>
                            </div>
                        </div>

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
