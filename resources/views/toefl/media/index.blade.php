@extends('layouts.dashboard')

@section('title', 'Media TOEFL')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto"
    x-data="{
        showUploadModal: false,
        showConfirmModal: false,
        confirmActionUrl: '',
        uploading: false,
        uploadError: '',
        uploadSuccess: '',
        submitUpload(form) {
            this.uploading = true;
            this.uploadError = '';
            this.uploadSuccess = '';
            const data = new FormData(form);
            fetch('{{ route('toefl.media.store') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: data,
            })
            .then(r => r.ok ? r.json() : r.json().then(e => Promise.reject(e)))
            .then(() => {
                this.uploading = false;
                this.showUploadModal = false;
                window.location.reload();
            })
            .catch(err => {
                this.uploading = false;
                this.uploadError = err?.message ?? 'Upload gagal. Coba lagi.';
            });
        }
    }">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Media TOEFL</h1>
            <p class="text-sm text-[color:var(--dash-text-muted)] mt-0.5">Audio dan gambar untuk soal listening.</p>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            @can('toefl.media.create')
                <button type="button" @click="showUploadModal = true"
                    class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-lg bg-[var(--dash-primary)] hover:opacity-90 text-white text-sm font-medium transition-colors">
                    <span class="material-symbols-outlined text-sm mr-1">upload</span>
                    Upload Media
                </button>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
    @endif

    <!-- Filter -->
    <div class="mb-5">
        <form method="GET" action="{{ route('toefl.media.index') }}" class="flex gap-2 items-center">
            <select name="type" onchange="this.form.submit()"
                class="form-select text-sm border-[color:var(--dash-input-border)] rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 min-h-[44px] min-w-[44px]">
                <option value="">Semua Tipe</option>
                <option value="audio" {{ request('type') === 'audio' ? 'selected' : '' }}>Audio</option>
                <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Gambar</option>
            </select>
            @if (request('type'))
                <a href="{{ route('toefl.media.index') }}" class="inline-flex items-center justify-center px-3 py-2 min-h-[44px] min-w-[44px] rounded-md text-[color:var(--dash-text-muted)] hover:bg-[var(--dash-nav-hover-bg)] hover:text-[color:var(--dash-text-body)] transition-colors text-sm gap-1.5"><span class="material-symbols-outlined text-[16px]">restart_alt</span>Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)]">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-[color:var(--dash-text-muted)] bg-[var(--dash-nav-hover-bg)] border-t border-b border-[color:var(--dash-card-border)]">
                    <tr>
                        <th class="px-6 py-3 text-left">Nama File</th>
                        <th class="px-6 py-3 text-left w-24">Tipe</th>
                        <th class="px-6 py-3 text-left w-64">Preview</th>
                        <th class="px-6 py-3 text-left w-36">Diupload Oleh</th>
                        <th class="px-6 py-3 text-right w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-[color:var(--dash-card-border)]">
                    @forelse ($media as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-medium text-[color:var(--dash-text-heading)] truncate max-w-xs">{{ $item->original_name }}</p>
                                <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5">ID: {{ $item->id }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($item->media_type === 'audio')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Audio</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Gambar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->media_type === 'audio')
                                    <audio controls class="w-full max-w-xs h-8" preload="none">
                                        <source src="{{ Storage::url($item->file_path) }}" type="audio/mpeg">
                                    </audio>
                                @else
                                    <img src="{{ Storage::url($item->file_path) }}"
                                        class="h-16 w-auto rounded object-contain" alt="{{ $item->original_name }}">
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-[color:var(--dash-text-muted)]">
                                {{ optional($item->uploadedBy)->full_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @can('toefl.media.delete')
                                    <button type="button"
                                        @click="showConfirmModal = true; confirmActionUrl = '{{ route('toefl.media.destroy', $item->id) }}'"
                                        class="inline-flex items-center justify-center px-3 py-2 min-h-[44px] min-w-[44px] rounded-lg transition-colors text-sm text-white bg-[#dc2626] hover:opacity-90 font-semibold gap-1.5"><span class="material-symbols-outlined text-[16px]">delete</span>Hapus</button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-[color:var(--dash-text-faint)]">
                                Belum ada media. Upload audio atau gambar untuk soal listening.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[color:var(--dash-card-border)]">
            {{ $media->withQueryString()->links() }}
        </div>
    </div>

    <!-- Upload Modal -->
    <div x-show="showUploadModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center px-4"
        style="display:none" @keydown.escape.window="showUploadModal = false">
        <div class="bg-[var(--dash-card-bg)] rounded-lg shadow-xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b">
                <h3 class="font-bold text-[color:var(--dash-text-heading)]">Upload Media</h3>
                <button type="button" @click="showUploadModal = false" class="inline-flex items-center justify-center w-11 h-11 min-h-[44px] min-w-[44px] rounded-md text-[color:var(--dash-text-faint)] hover:bg-[var(--dash-nav-hover-bg)] hover:text-[color:var(--dash-text-body)] transition-colors text-xl leading-none" aria-label="Tutup">&times;</button>
            </div>
            <form @submit.prevent="submitUpload($el)" class="p-5 space-y-4">
                @csrf

                <div x-show="uploadError" class="bg-red-50 border border-red-300 text-red-700 px-3 py-2 rounded text-sm" x-text="uploadError"></div>

                <div>
                    <label class="block text-xs font-medium text-[color:var(--dash-text-body)] mb-1">Tipe Media <span class="text-red-500">*</span></label>
                    <select name="media_type" required class="form-select w-full text-sm min-h-[44px]">
                        <option value="audio">Audio (MP3)</option>
                        <option value="image">Gambar</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-[color:var(--dash-text-body)] mb-1">File <span class="text-red-500">*</span></label>
                    <input name="file" type="file" required accept="audio/*,image/*"
                        class="block w-full text-sm text-[color:var(--dash-text-muted)] file:mr-4 file:py-2 file:px-4 file:min-h-[44px] file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer focus:outline-none" />
                    <p class="text-xs text-[color:var(--dash-text-faint)] mt-1">Audio: MP3, OGG. Gambar: JPG, PNG, WEBP.</p>
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4 mt-2 border-t border-[color:var(--dash-card-border)]">
                    <button type="button" @click="showUploadModal = false"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] hover:bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)] text-sm font-medium transition-colors gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                    <button type="submit" :disabled="uploading"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-lg bg-[var(--dash-primary)] hover:opacity-90 text-white text-sm font-medium disabled:opacity-60 transition-colors"
                        x-text="uploading ? 'Mengupload...' : 'Upload'"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="relative z-10 inline-block align-bottom bg-[var(--dash-card-bg)] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-[var(--dash-card-bg)] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-[color:var(--dash-text-heading)]">Hapus Media</h3>
                            <p class="text-sm text-[color:var(--dash-text-muted)] mt-2">Yakin ingin menghapus media ini? Media yang masih digunakan di soal tidak bisa dihapus.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-[var(--dash-nav-hover-bg)] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="$refs.deleteForm.submit()"
                        class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 min-h-[44px] min-w-[44px] bg-[#dc2626] text-base font-medium text-white hover:opacity-90 focus:outline-none sm:ml-3 sm:text-sm transition-colors gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        Hapus
                    </button>
                    <button type="button" @click="showConfirmModal = false"
                        class="mt-3 sm:mt-0 sm:ml-3 w-full sm:w-auto inline-flex items-center justify-center rounded-md border border-[color:var(--dash-input-border)] shadow-sm px-4 py-2 min-h-[44px] min-w-[44px] bg-[var(--dash-card-bg)] text-base font-medium text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] focus:outline-none sm:text-sm transition-colors gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        Batal
                    </button>
                </div>
                <form x-ref="deleteForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
