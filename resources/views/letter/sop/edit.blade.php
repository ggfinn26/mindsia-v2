@extends('layouts.dashboard')

@section('title', 'Edit Dokumen SOP')
@section('header_title', 'Edit Dokumen SOP')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6 pb-10">
    {{-- Header Section Pola B3 --}}
    <div>
        <a href="{{ route('sop-documents.show', $sopDocument) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke {{ $sopDocument->title }}
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Dokumen SOP</h1>
        <p class="mt-2 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
            Perbarui informasi atau ganti file dokumen SOP yang telah diunggah.
        </p>
    </div>

    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);"
             role="alert" aria-live="polite">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px] mt-0.5">error</span>
                <div>
                    <p class="font-bold">Terdapat kesalahan pengisian form</p>
                    <ul class="mt-1 list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('sop-documents.update', $sopDocument) }}" enctype="multipart/form-data"
          class="rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
          x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
        @csrf @method('PUT')
        <div class="p-5 sm:p-7 space-y-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="title" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Judul dokumen <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $sopDocument->title) }}" required maxlength="255"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="document_code" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Kode dokumen
                    </label>
                    <input type="text" id="document_code" name="document_code" value="{{ old('document_code', $sopDocument->document_code) }}" maxlength="100"
                           class="w-full px-4 py-2.5 text-sm rounded-lg uppercase focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="version" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Versi
                    </label>
                    <input type="text" id="version" name="version" value="{{ old('version', $sopDocument->version) }}" maxlength="20"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="category" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Kategori
                    </label>
                    <input type="text" id="category" name="category" value="{{ old('category', $sopDocument->category) }}" maxlength="100"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="effective_date" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Tanggal efektif
                    </label>
                    <input type="date" id="effective_date" name="effective_date" value="{{ old('effective_date', $sopDocument->effective_date?->toDateString()) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
            </div>

            <!-- Role Checkboxes -->
            <div class="pt-2">
                <label class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                    Akses Dokumen Berdasarkan Role <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(kosongkan jika dapat diakses semua role)</span>
                </label>
                <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-3 p-4 rounded-xl"
                     style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);">
                    @foreach ($roles as $role)
                        @php $checked = in_array($role, old('visible_to', $sopDocument->visible_to ?? [])); @endphp
                        <label class="flex items-center gap-2 text-sm cursor-pointer" style="color: var(--dash-text-body);">
                            <input type="checkbox" name="visible_to[]" value="{{ $role }}"
                                   {{ $checked ? 'checked' : '' }}
                                   class="w-4 h-4 rounded cursor-pointer"
                                   style="accent-color: var(--dash-primary);">
                            <span>{{ $role }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- File Upload -->
            <div class="pt-2">
                <label for="file" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                    Ganti file SOP <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                </label>
                <div class="p-3 rounded-xl border border-dashed"
                     style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border);">
                    <input type="file" id="file" name="file"
                           class="block w-full text-sm cursor-pointer focus:outline-none"
                           style="color: var(--dash-text-body);">
                </div>
                @if ($sopDocument->telegram_file_id)
                    <p class="mt-1.5 text-xs font-medium text-emerald-700 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        File SOP saat ini sudah tersimpan. Biarkan kosong jika tidak ingin mengganti.
                    </p>
                @endif
            </div>
        </div>

        <!-- Form Actions -->
        <div class="p-5 sm:px-7 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 rounded-b-2xl"
             style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
            <a href="{{ route('sop-documents.show', $sopDocument) }}"
               class="w-full sm:w-auto inline-flex justify-center items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[18px]">close</span> Batal
            </a>
            <button type="submit"
                    :disabled="isSubmitting"
                    class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 disabled:opacity-70 disabled:cursor-not-allowed"
                    style="background-color: var(--dash-primary);">
                <span x-show="!isSubmitting" class="material-symbols-outlined text-[18px]">save</span>
                <span x-show="!isSubmitting">Simpan Perubahan</span>
                <span x-show="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]" x-cloak>progress_activity</span>
                <span x-show="isSubmitting" x-cloak>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
@endsection
