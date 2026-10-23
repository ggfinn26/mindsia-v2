@extends('layouts.dashboard')

@section('title', 'Kelola Testimoni')
@section('header_title', 'Kelola Testimoni')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{ showModal: false, deleteUrl: '', deleteName: '' }"
     @keydown.escape.window="showModal = false">

    {{-- Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Landing Page</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Testimoni Landing Page</h1>
            <p class="text-sm mt-1"
               style="color: var(--dash-text-muted);">Kelola ulasan alumni dan siswa untuk ditampilkan di landing page.</p>
        </div>
        @can('landing.testimonial.manage')
        <a href="{{ route('admin.landing.testimonials.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Testimoni
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

    {{-- Content --}}
    @if($testimonials->isEmpty())
    <div class="p-12 text-center rounded-2xl"
         style="background-color: var(--dash-card-bg); border: 2px dashed var(--dash-card-border);">
        <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
              style="color: var(--dash-text-faint);">format_quote</span>
        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
            Belum ada testimoni. Klik "Tambah Testimoni" untuk memulai.
        </p>
    </div>
    @else
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Tipe</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Nama</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Konten</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($testimonials as $testi)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $testi->type->value === 'image' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $testi->type->value === 'image' ? 'Gambar' : 'Teks' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 font-semibold" style="color: var(--dash-text-heading);">
                            {{ $testi->name }}
                            @if($testi->program || $testi->city)
                            <p class="text-xs font-normal mt-0.5" style="color: var(--dash-text-muted);">
                                {{ $testi->program }}{{ $testi->program && $testi->city ? ' - ' : '' }}{{ $testi->city }}
                            </p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 max-w-sm" style="color: var(--dash-text-body);">
                            @if($testi->isImage())
                                <div class="h-12 w-20 rounded-lg overflow-hidden border"
                                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                                    <img src="{{ $testi->image_url }}" alt="{{ $testi->name }}" class="h-full w-full object-cover">
                                </div>
                            @else
                                <span class="line-clamp-2 text-xs leading-relaxed">{{ $testi->quote }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $testi->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $testi->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                @can('landing.testimonial.manage')
                                <a href="{{ route('admin.landing.testimonials.edit', $testi) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                                   title="Edit Testimoni">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                    Edit
                                </a>
                                @endcan
                                @can('landing.testimonial.manage')
                                <button type="button"
                                        @click="deleteUrl = '{{ route('admin.landing.testimonials.destroy', $testi) }}'; deleteName = '{{ addslashes($testi->name) }}'; showModal = true"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);"
                                        title="Hapus Testimoni">
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
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Testimoni?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Hapus testimoni dari <span class="font-semibold" style="color: var(--dash-text-heading);" x-text="deleteName"></span>? Tindakan ini tidak dapat dipulihkan.
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
