@extends('layouts.dashboard')

@section('title', 'Edit Template Surat')
@section('header_title', 'Edit Template Surat')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6 pb-10">
    {{-- Header Section Pola B3 --}}
    <div>
        <a href="{{ route('letter-templates.show', $letterTemplate) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke {{ $letterTemplate->template_name }}
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Template Surat</h1>
        <p class="mt-2 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
            Perbarui informasi atau unggah file template <strong>.docx</strong> baru jika ada perubahan format.
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

    <form method="POST" action="{{ route('letter-templates.update', $letterTemplate) }}" enctype="multipart/form-data"
          class="rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
          x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
        @csrf @method('PUT')
        <div class="p-5 sm:p-7 space-y-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <!-- Kode Template -->
                <div>
                    <label for="template_code" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Kode template <span style="color: var(--dash-danger);" aria-hidden="true">*</span>
                    </label>
                    <input type="text" id="template_code" name="template_code" value="{{ old('template_code', $letterTemplate->template_code) }}" required maxlength="100" placeholder="SK_KARYAWAN"
                           class="w-full px-4 py-2.5 text-sm rounded-lg uppercase focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                           aria-required="true">
                </div>

                <!-- Nama Template -->
                <div>
                    <label for="template_name" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Nama template <span style="color: var(--dash-danger);" aria-hidden="true">*</span>
                    </label>
                    <input type="text" id="template_name" name="template_name" value="{{ old('template_name', $letterTemplate->template_name) }}" required maxlength="255" placeholder="Contoh: SK Karyawan Tetap"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                           aria-required="true">
                </div>

                <!-- Kategori -->
                <div>
                    <label for="letter_category" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Kategori <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <select id="letter_category" name="letter_category" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="generated" @selected(old('letter_category', $letterTemplate->letter_category) === 'generated')>Generated (Surat Karyawan)</option>
                        <option value="marketing" @selected(old('letter_category', $letterTemplate->letter_category) === 'marketing')>Marketing (Kontrak Marketing)</option>
                        <option value="announcement" @selected(old('letter_category', $letterTemplate->letter_category) === 'announcement')>Announcement (Surat Pemberitahuan)</option>
                        <option value="member" @selected(old('letter_category', $letterTemplate->letter_category) === 'member')>Member (Kwitansi / Form Member)</option>
                    </select>
                </div>

                <!-- Format Nomor -->
                <div>
                    <label for="letter_number_format" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Format nomor surat
                    </label>
                    <input type="text" id="letter_number_format" name="letter_number_format" value="{{ old('letter_number_format', $letterTemplate->letter_number_format) }}" placeholder="{NO}/SK/{YYYY}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    <p class="mt-1.5 text-xs leading-relaxed" style="color: var(--dash-text-muted);">
                        Gunakan <code class="px-1 py-0.5 rounded font-mono" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">{NO}</code> atau <code class="px-1 py-0.5 rounded font-mono" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">{YYYY}</code>.
                    </p>
                </div>
            </div>

            <!-- File Upload -->
            <div class="pt-2">
                <label for="file" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                    Ganti file template (.docx)
                </label>
                <div class="p-3 rounded-xl border border-dashed"
                     style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border);">
                    <input type="file" id="file" name="file" accept=".docx"
                           class="block w-full text-sm cursor-pointer focus:outline-none"
                           style="color: var(--dash-text-body);">
                </div>
                @if ($letterTemplate->telegram_file_id)
                    <p class="mt-1.5 text-xs font-medium text-emerald-700 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        File template saat ini sudah tersimpan. Biarkan kosong jika tidak ingin mengganti.
                    </p>
                @endif
            </div>

            <!-- Is Active -->
            <div class="pt-2">
                <label class="relative flex items-start gap-3 p-4 rounded-xl cursor-pointer transition-colors"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);">
                    <div class="flex items-center h-5 mt-0.5">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $letterTemplate->is_active))
                               class="w-4 h-4 rounded cursor-pointer"
                               style="accent-color: var(--dash-primary);">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold" style="color: var(--dash-text-heading);">Aktifkan Template</span>
                        <span class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Template ini akan langsung muncul dan bisa digunakan oleh user.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="p-5 sm:px-7 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 rounded-b-2xl"
             style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
            <a href="{{ route('letter-templates.show', $letterTemplate) }}"
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
