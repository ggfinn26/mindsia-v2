@extends('layouts.dashboard')

@section('title', 'Kelola Posisi - MINDSIA')
@section('header_title', 'Kelola Posisi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '' }">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kelola Posisi</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Daftar posisi / jabatan dan hierarki organisasi.
            </p>
        </div>
        <a href="{{ route('positions.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Posisi
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium mb-6"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium mb-6"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Table card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Nama Posisi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Role Terkait</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Hierarki</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions as $position)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap"
                                style="color: var(--dash-text-heading);">
                                {{ $position->position_name ?? $position->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($position->role)
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                                        {{ $position->role->name }}
                                    </span>
                                @else
                                    <span class="text-xs italic" style="color: var(--dash-text-faint);">Tidak ada role</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap"
                                style="color: var(--dash-text-muted);">
                                {{ $position->hierarchy_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('positions.edit', $position->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                    </a>
                                    <button type="button"
                                            @click="showConfirmModal = true; confirmTitle = 'Hapus Posisi'; confirmMessage = 'Yakin ingin menghapus posisi ini?'; confirmActionUrl = '{{ route('positions.destroy', $position->id) }}';"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                            style="background-color: #dc2626;">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">work</span>
                                <p class="text-sm" style="color: var(--dash-text-muted);">
                                    Belum ada data posisi.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($positions->hasPages())
            <div class="px-6 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $positions->links() }}
            </div>
        @endif
    </div>

    {{-- Confirm delete modal --}}
    <div x-show="showConfirmModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="absolute inset-0 bg-black/50"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showConfirmModal = false"></div>

        <div class="relative rounded-2xl p-6 max-w-md w-full shadow-xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full"
                     style="background-color: #fee2e2;">
                    <span class="material-symbols-outlined text-[20px]" style="color: #dc2626;">warning</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-semibold mb-1" style="color: var(--dash-text-heading);"
                        x-text="confirmTitle"></h3>
                    <p class="text-sm" style="color: var(--dash-text-muted);"
                       x-text="confirmMessage"></p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button"
                        @click="showConfirmModal = false"
                        class="px-4 py-2 rounded-xl text-sm font-medium"
                        style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);">
                    Batal
                </button>
                <form :action="confirmActionUrl" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: #dc2626;">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
