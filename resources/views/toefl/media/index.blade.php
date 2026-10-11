@extends('layouts.dashboard')

@section('title', 'Media TOEFL')
@section('header_title', 'Media TOEFL')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
        showUploadModal: false,
        showConfirmModal: false,
        confirmActionUrl: '',
        uploading: false,
        uploadError: '',
        submitUpload(form) {
            this.uploading = true;
            this.uploadError = '';
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

    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">TOEFL</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Media TOEFL</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Audio dan gambar untuk soal listening.</p>
        </div>
        @can('toefl.media.create')
            <button type="button" @click="showUploadModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">upload</span>Upload Media
            </button>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('toefl.media.index') }}"
          class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Tipe</label>
            <select name="type"
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'"
                    onchange="this.form.submit()">
                <option value="">Semua Tipe</option>
                <option value="audio" {{ request('type') === 'audio' ? 'selected' : '' }}>Audio</option>
                <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Gambar</option>
            </select>
        </div>
        @if (request('type'))
            <a href="{{ route('toefl.media.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">restart_alt</span>Reset
            </a>
        @endif
    </form>

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Nama File</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide w-24"
                        style="color: var(--dash-text-muted);">Tipe</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide w-64"
                        style="color: var(--dash-text-muted);">Preview</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide w-36"
                        style="color: var(--dash-text-muted);">Diupload Oleh</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide w-20"
                        style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($media as $item)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5">
                            <p class="font-medium truncate max-w-xs" style="color: var(--dash-text-heading);">{{ $item->original_name }}</p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">ID: {{ $item->id }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($item->media_type === 'audio')
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">Audio</span>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Gambar</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($item->media_type === 'audio')
                                <audio controls class="w-full max-w-xs h-8" preload="none">
                                    <source src="{{ Storage::url($item->file_path) }}" type="audio/mpeg">
                                </audio>
                            @else
                                <img src="{{ Storage::url($item->file_path) }}"
                                     class="h-16 w-auto rounded object-contain" alt="{{ $item->original_name }}">
                            @endif
                        </td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-muted);">
                            {{ optional($item->uploadedBy)->full_name ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @can('toefl.media.delete')
                                <button type="button"
                                        @click="showConfirmModal = true; confirmActionUrl = '{{ route('toefl.media.destroy', $item->id) }}'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                        style="background-color: #dc2626;">
                                    <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">audio_file</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada media.</p>
                            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Upload audio atau gambar untuk soal listening.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($media->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $media->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- Upload Modal --}}
    <template x-teleport="body">
        <div x-show="showUploadModal" style="display:none;" class="relative z-50"
             @keydown.escape.window="showUploadModal = false">
            <div x-show="showUploadModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                 @click="showUploadModal = false"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-0">
                    <div x-show="showUploadModal" @click.away="showUploadModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                        <div class="rounded-t-2xl px-6 py-5 flex items-center justify-between"
                             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <h3 class="font-jakarta font-bold text-base" style="color: var(--dash-text-heading);">Upload Media</h3>
                            <button type="button" @click="showUploadModal = false"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted);"
                                    onmouseover="this.style.backgroundColor='var(--dash-card-border)'"
                                    onmouseout="this.style.backgroundColor='transparent'"
                                    aria-label="Tutup">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        </div>
                        <form @submit.prevent="submitUpload($el)" class="px-6 py-5 space-y-4">
                            @csrf

                            <div x-show="uploadError"
                                 class="px-4 py-3 rounded-xl text-sm"
                                 style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;"
                                 x-text="uploadError"></div>

                            <div>
                                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                                    Tipe Media <span class="text-red-500">*</span>
                                </label>
                                <select name="media_type" required
                                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <option value="audio">Audio (MP3)</option>
                                    <option value="image">Gambar</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                                    File <span class="text-red-500">*</span>
                                </label>
                                <input name="file" type="file" required accept="audio/*,image/*"
                                       class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold cursor-pointer focus:outline-none"
                                       style="color: var(--dash-text-muted);" />
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Audio: MP3, OGG. Gambar: JPG, PNG, WEBP.</p>
                            </div>

                            <div class="rounded-b-2xl flex justify-end gap-3 pt-4"
                                 style="border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showUploadModal = false"
                                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</button>
                                <button type="submit" :disabled="uploading"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity disabled:opacity-60"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]" x-show="!uploading">upload</span>
                                    <span x-text="uploading ? 'Mengupload...' : 'Upload'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Delete Confirm Modal --}}
    <template x-teleport="body">
        <div x-show="showConfirmModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showConfirmModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                 @click="showConfirmModal = false"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-0">
                    <div x-show="showConfirmModal" @click.away="showConfirmModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Media?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Media yang masih digunakan di soal tidak bisa dihapus. Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showConfirmModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</button>
                            <form x-ref="deleteForm" :action="confirmActionUrl" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="$refs.deleteForm.submit()"
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
@endsection
