@extends('layouts.dashboard')

@section('title', 'Materi Pembelajaran')
@section('header_title', 'Materi Pembelajaran')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Kelas</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Materi Pembelajaran</h1>
        </div>
        <a href="{{ route('classes.learning-material.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">upload_file</span>Upload Materi
        </a>
    </div>

    @if (session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Judul Materi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Untuk Program / Kelas</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Tipe File</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Diunggah Oleh</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materials ?? [] as $material)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">{{ $material->title }}</td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $material->program?->name ?? '-' }}</td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ strtoupper($material->file_type ?? '-') }}</td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $material->uploader?->name ?? '-' }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('classes.learning-material.show', $material) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>Lihat
                                </a>
                                <a href="{{ route('classes.learning-material.edit', $material) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                </a>
                                <div x-data="{ showConfirm: false }" class="inline-block">
                                    <button type="button" @click="showConfirm = true"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                            style="background-color: #dc2626;">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                    </button>
                                    <template x-teleport="body">
                                        <div x-show="showConfirm" style="display: none;" class="relative z-50">
                                            <div x-show="showConfirm"
                                                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                                                 @click="showConfirm = false"></div>
                                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                                <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-0">
                                                    <div x-show="showConfirm" @click.away="showConfirm = false"
                                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                                                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                                                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                                        <div class="rounded-t-2xl px-6 py-5" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                                            <div class="flex items-start gap-3.5">
                                                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                                                    <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                                                </div>
                                                                <div>
                                                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Materi?</h3>
                                                                    <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Tindakan ini tidak dapat dibatalkan.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3" style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                                            <button type="button" @click="showConfirm = false"
                                                                    class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg"
                                                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</button>
                                                            <form method="POST" action="{{ route('classes.learning-material.destroy', $material) }}" class="m-0">
                                                                @csrf @method('DELETE')
                                                                <button type="submit"
                                                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                                                        style="background-color: #dc2626;">
                                                                    <span class="material-symbols-outlined text-[16px]">delete</span>Ya, Hapus
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">menu_book</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada materi pembelajaran.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (!empty($materials) && $materials->hasPages())
        <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">{{ $materials->links() }}</div>
        @endif
    </div>
</div>
@endsection
