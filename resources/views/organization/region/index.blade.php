@extends('layouts.dashboard')

@section('title', 'Kelola Region')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ showModal: false, isEdit: false, formData: { id: '', province_id: '', name: '' }, showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[var(--dash-text-heading)]">Kelola Region</h1>
        <button @click="showModal = true; isEdit = false; formData = { id: '', province_id: '', name: '' }"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Region
        </button>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 border border-emerald-200 [.mode-dark_&]:border-emerald-500/20 text-emerald-700 [.mode-dark_&]:text-emerald-400 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="mb-4 bg-red-50 [.mode-dark_&]:bg-red-500/10 border border-red-200 [.mode-dark_&]:border-red-500/20 text-red-700 [.mode-dark_&]:text-red-400 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-[var(--dash-card-bg)] shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-[var(--dash-card-border)]">
            <thead class="bg-[var(--dash-page-bg)]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-[var(--dash-text-muted)] uppercase tracking-wider">Nama Region</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-[var(--dash-text-muted)] uppercase tracking-wider">Provinsi</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-[var(--dash-text-muted)] uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-[var(--dash-card-bg)] divide-y divide-[var(--dash-card-border)]">
                @forelse($regions as $region)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--dash-text-heading)]">{{ $region->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--dash-text-muted)]">{{ $region->province->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="inline-flex items-center gap-2">
                                <button @click="showModal = true; isEdit = true; formData = { id: '{{ $region->id }}', province_id: '{{ $region->province_id }}', name: '{{ addslashes($region->name) }}' }"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                </button>
                                <button type="button"
                                        @click="showConfirmModal = true; confirmTitle = 'Hapus Region'; confirmMessage = 'Yakin ingin menghapus region ini?'; confirmActionUrl = '{{ route('regions.destroy', $region->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                        style="background-color: #dc2626;">
                                    <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-[var(--dash-text-muted)] text-center">Belum ada data region.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-slate-800/50 opacity-75"></div>
            </div>
            <div x-show="showConfirmModal" class="relative z-10 inline-block align-bottom bg-[var(--dash-card-bg)] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-[var(--dash-card-bg)] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-50 [.mode-dark_&]:bg-red-500/10 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600 [.mode-dark_&]:text-red-400">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-[var(--dash-text-heading)]" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-[var(--dash-text-muted)]" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-[var(--dash-page-bg)] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="$refs.confirmForm.submit()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 [.mode-dark_&]:bg-red-500 text-base font-medium text-white hover:bg-red-700 [.mode-dark_&]:hover:bg-red-400 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" x-text="confirmActionText"></button>
                    <button type="button" @click="showConfirmModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-[var(--dash-input-border)] shadow-sm px-4 py-2 bg-[var(--dash-card-bg)] text-base font-medium text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" @click="showModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-slate-800/50 opacity-75"></div>
            </div>
            <div x-show="showModal" class="relative z-10 inline-block align-bottom bg-[var(--dash-card-bg)] rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="isEdit ? '/regions/' + formData.id : '{{ route('regions.store') }}'" method="POST">
                    @csrf
                    <template x-if="isEdit">
                        @method('PUT')
                    </template>
                    <div class="bg-[var(--dash-card-bg)] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-[var(--dash-text-heading)] mb-4" x-text="isEdit ? 'Edit Region' : 'Tambah Region'"></h3>
                        
                        <div class="mb-4">
                            <label for="province_id" class="block text-sm font-medium text-[var(--dash-text-body)]">Provinsi</label>
                            <select name="province_id" id="province_id" x-model="formData.province_id" required class="mt-1 block w-full border border-[var(--dash-input-border)] rounded-md shadow-sm py-2 px-3 focus:outline-none bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] sm:text-sm">
                                <option value="">Pilih Provinsi...</option>
                                @foreach(\App\Models\Province::all() as $prov)
                                    <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-[var(--dash-text-body)]">Nama Region</label>
                            <input type="text" name="name" id="name" x-model="formData.name" required class="mt-1 block w-full border border-[var(--dash-input-border)] rounded-md shadow-sm py-2 px-3 focus:outline-none bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] focus:ring-[var(--dash-primary)] focus:border-[var(--dash-primary)] sm:text-sm">
                        </div>
                    </div>
                    <div class="bg-[var(--dash-page-bg)] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[var(--dash-primary)] text-base font-medium text-white hover:opacity-90 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm gap-1.5"><span class="material-symbols-outlined text-[16px]">save</span>Simpan</button>
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-[var(--dash-input-border)] shadow-sm px-4 py-2 bg-[var(--dash-card-bg)] text-base font-medium text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
