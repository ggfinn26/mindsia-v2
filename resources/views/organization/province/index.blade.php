@extends('layouts.dashboard')

@section('title', 'Kelola Provinsi')
@section('header_title', 'Provinsi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
        showModal: false,
        isEdit: false,
        formData: { id: '', name: '' },
        showConfirmModal: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmActionUrl: '',
        confirmMethod: 'DELETE',
        confirmActionText: 'Hapus'
     }">

    {{-- Page Header (Pola A) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Organisasi</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kelola Provinsi</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola daftar wilayah provinsi dalam struktur organisasi.
            </p>
        </div>
        <button type="button"
                @click="showModal = true; isEdit = false; formData = { id: '', name: '' }"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                style="background-color: var(--dash-primary);"
                onmouseover="this.style.opacity='0.9'"
                onmouseout="this.style.opacity='1'">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Provinsi
        </button>
    </div>

    {{-- Flash Messages --}}
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

    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Table Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-faint);">Nama Provinsi</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-faint);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($provinces as $province)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                            {{ $province->name }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="inline-flex items-center gap-2 justify-end">
                                <button type="button"
                                        @click="showModal = true; isEdit = true; formData = { id: '{{ $province->id }}', name: '{{ addslashes($province->name) }}' }"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                    Edit
                                </button>
                                <button type="button"
                                        @click="showConfirmModal = true; confirmTitle = 'Hapus Provinsi'; confirmMessage = 'Yakin ingin menghapus provinsi ini? Tindakan ini tidak dapat dibatalkan.'; confirmActionUrl = '{{ route('provinces.destroy', $province->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                        style="background-color: #dc2626;">
                                    <span class="material-symbols-outlined text-[14px]">delete</span>
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">map</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data provinsi.</p>
                            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Klik tombol &quot;Tambah Provinsi&quot; untuk menambahkan data baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($provinces, 'hasPages') && $provinces->hasPages())
        <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
            {{ $provinces->links() }}
        </div>
        @endif
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-confirm-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div x-show="showConfirmModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/60 transition-opacity"
                 @click="showConfirmModal = false"
                 aria-hidden="true"></div>

            <div x-show="showConfirmModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative inline-block rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-md w-full z-10"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full"
                             style="background-color: #fee2e2;">
                            <span class="material-symbols-outlined text-[22px]" style="color: #dc2626;">warning</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-jakarta text-base font-bold"
                                style="color: var(--dash-text-heading);"
                                id="modal-confirm-title"
                                x-text="confirmTitle"></h3>
                            <p class="text-sm mt-1.5"
                               style="color: var(--dash-text-muted);"
                               x-text="confirmMessage"></p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 flex items-center justify-end gap-3"
                     style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <button type="button"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                            @click="showConfirmModal = false">
                        Batal
                    </button>
                    <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="_method" :value="confirmMethod">
                        <button type="button"
                                @click="$refs.confirmForm.submit()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: #dc2626;"
                                x-text="confirmActionText">
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Form Tambah / Edit --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-form-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/60 transition-opacity"
                 @click="showModal = false"
                 aria-hidden="true"></div>

            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative inline-block rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-lg w-full z-10"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <form :action="isEdit ? '/provinces/' + formData.id : '{{ route('provinces.store') }}'" method="POST">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="px-6 py-4"
                         style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <h3 class="font-jakarta text-base font-bold"
                            style="color: var(--dash-text-heading);"
                            id="modal-form-title"
                            x-text="isEdit ? 'Edit Provinsi' : 'Tambah Provinsi'"></h3>
                    </div>

                    <div class="px-6 py-5 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                                Nama Provinsi <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   x-model="formData.name"
                                   required
                                   placeholder="Masukkan nama provinsi"
                                   class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>
                    </div>

                    <div class="px-6 py-4 flex items-center justify-end gap-3"
                         style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <button type="button"
                                @click="showModal = false"
                                class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);"
                                onmouseover="this.style.opacity='0.9'"
                                onmouseout="this.style.opacity='1'">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
