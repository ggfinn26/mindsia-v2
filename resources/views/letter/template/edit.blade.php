@extends('layouts.dashboard')

@section('title', 'Edit Template Surat')
@section('header_title', 'Edit Template Surat')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 sm:space-y-8 pb-10">
    <div>
        <a href="{{ route('letter-templates.show', $letterTemplate) }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-heading)] transition-colors mb-3 focus:outline-none focus:ring-2 focus:ring-[color:var(--dash-primary,#215aac)] rounded-sm focus:ring-offset-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke {{ $letterTemplate->template_name }}
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Template</h1>
        <p class="mt-2 text-[14px] leading-relaxed text-[color:var(--dash-text-body)]">
            Perbarui informasi atau unggah file template <strong>.docx</strong> baru jika ada perubahan format.
        </p>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 p-4 rounded-lg" role="alert" aria-live="polite">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px] text-red-600 mt-0.5">error</span>
                <div>
                    <h3 class="text-[13px] font-bold text-red-800">Terdapat kesalahan pengisian form</h3>
                    <ul class="mt-1 list-disc pl-4 text-[13px] text-red-700 space-y-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('letter-templates.update', $letterTemplate) }}" enctype="multipart/form-data" class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] shadow-sm rounded-xl overflow-hidden" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
        @csrf @method('PUT')
        <div class="p-5 sm:p-7 space-y-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <!-- Kode Template -->
                <div>
                    <label for="template_code" class="mb-1.5 block text-[13px] font-bold text-[color:var(--dash-text-body)]">Kode template <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="text" id="template_code" name="template_code" value="{{ old('template_code', $letterTemplate->template_code) }}" required maxlength="100" placeholder="SK_KARYAWAN" 
                           class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-[color:var(--dash-input-border)] bg-[#F8FAFC] text-[14px] text-[color:var(--dash-text-heading)] uppercase focus:bg-[var(--dash-card-bg)] focus:outline-none focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)] transition-colors"
                           aria-required="true">
                </div>
                
                <!-- Nama Template -->
                <div>
                    <label for="template_name" class="mb-1.5 block text-[13px] font-bold text-[color:var(--dash-text-body)]">Nama template <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="text" id="template_name" name="template_name" value="{{ old('template_name', $letterTemplate->template_name) }}" required maxlength="255" placeholder="Contoh: SK Karyawan Tetap"
                           class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-[color:var(--dash-input-border)] bg-[#F8FAFC] text-[14px] text-[color:var(--dash-text-heading)] focus:bg-[var(--dash-card-bg)] focus:outline-none focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)] transition-colors"
                           aria-required="true">
                </div>
                
                <!-- Kategori -->
                <div>
                    <label for="letter_category" class="mb-1.5 block text-[13px] font-bold text-[color:var(--dash-text-body)]">Kategori <span class="text-red-500" aria-hidden="true">*</span></label>
                    <select id="letter_category" name="letter_category" required class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-[color:var(--dash-input-border)] bg-[#F8FAFC] text-[14px] text-[color:var(--dash-text-heading)] focus:bg-[var(--dash-card-bg)] focus:outline-none focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)] transition-colors appearance-none pr-8 ">
                        <option value="generated" @selected(old('letter_category', $letterTemplate->letter_category) === 'generated')>Generated (Surat Karyawan)</option>
                        <option value="marketing" @selected(old('letter_category', $letterTemplate->letter_category) === 'marketing')>Marketing (Kontrak Marketing)</option>
                        <option value="announcement" @selected(old('letter_category', $letterTemplate->letter_category) === 'announcement')>Announcement (Surat Pemberitahuan)</option>
                        <option value="member" @selected(old('letter_category', $letterTemplate->letter_category) === 'member')>Member (Kwitansi / Form Member)</option>
                    </select>
                </div>
                
                <!-- Format Nomor -->
                <div>
                    <label for="letter_number_format" class="mb-1.5 block text-[13px] font-bold text-[color:var(--dash-text-body)]">Format nomor surat</label>
                    <input type="text" id="letter_number_format" name="letter_number_format" value="{{ old('letter_number_format', $letterTemplate->letter_number_format) }}" placeholder="{NO}/SK/{YYYY}" 
                           class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-[color:var(--dash-input-border)] bg-[#F8FAFC] text-[14px] text-[color:var(--dash-text-heading)] focus:bg-[var(--dash-card-bg)] focus:outline-none focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)] transition-colors">
                    <p class="mt-1.5 text-[12px] text-[color:var(--dash-text-muted)]">Gunakan <code class="bg-[var(--dash-nav-hover-bg)] px-1 py-0.5 rounded text-[color:var(--dash-text-body)] font-mono">{NO}</code> atau <code class="bg-[var(--dash-nav-hover-bg)] px-1 py-0.5 rounded text-[color:var(--dash-text-body)] font-mono">{YYYY}</code>.</p>
                </div>
            </div>

            <!-- File Upload -->
            <div class="pt-2">
                <label for="file" class="mb-1.5 block text-[13px] font-bold text-[color:var(--dash-text-body)]">
                    Ganti file template (.docx)
                </label>
                <div class="relative w-full border border-[color:var(--dash-input-border)] border-dashed rounded-xl bg-[#F8FAFC] hover:bg-[var(--dash-nav-hover-bg)] transition-colors flex items-center p-1.5 focus-within:border-[color:var(--dash-primary,#215aac)] focus-within:ring-1 focus-within:ring-[color:var(--dash-primary,#215aac)]">
                    <input type="file" id="file" name="file" accept=".docx"
                           class="block w-full text-[14px] text-[color:var(--dash-text-body)] file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-[13px] file:font-semibold file:bg-[var(--dash-card-bg)] file:text-[color:var(--dash-primary-text,#215aac)] file:border file:border-[color:var(--dash-card-border)] file:shadow-sm hover:file:bg-[var(--dash-nav-hover-bg)] cursor-pointer focus:outline-none min-h-[44px]">
                </div>
                @if ($letterTemplate->telegram_file_id)
                    <p class="mt-1.5 text-[12px] text-emerald-700 font-medium flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        File template saat ini sudah tersimpan. Biarkan kosong jika tidak ingin mengganti.
                    </p>
                @endif
            </div>

            <!-- Is Active -->
            <div class="pt-2">
                <label class="relative flex items-start gap-3 p-3 sm:p-4 border border-[color:var(--dash-card-border)] rounded-xl cursor-pointer hover:bg-[var(--dash-nav-hover-bg)] transition-colors group focus-within:ring-2 focus-within:ring-[color:var(--dash-primary,#215aac)] focus-within:border-transparent">
                    <div class="flex items-center h-5 mt-0.5">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $letterTemplate->is_active)) 
                               class="w-5 h-5 border-[color:var(--dash-input-border)] text-[color:var(--dash-primary-text,#215aac)] rounded focus:ring-0 focus:outline-none cursor-pointer">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[14px] font-bold text-[color:var(--dash-text-heading)]">Aktifkan Template</span>
                        <span class="text-[13px] text-[color:var(--dash-text-muted)] mt-0.5">Template ini akan langsung muncul dan bisa digunakan oleh user.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="border-t border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] p-5 sm:px-7 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <a href="{{ route('letter-templates.show', $letterTemplate) }}" 
               class="w-full sm:w-auto inline-flex justify-center min-h-[44px] items-center border border-[color:var(--dash-input-border)] bg-[var(--dash-card-bg)] px-5 rounded-lg text-[14px] font-bold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] hover:text-[color:var(--dash-text-heading)] focus:outline-none focus:ring-2 focus:ring-slate-300 transition-colors">
                Batal
            </a>
            <button type="submit" 
                    :disabled="isSubmitting"
                    class="w-full sm:w-auto inline-flex justify-center min-h-[44px] items-center gap-2 bg-[var(--dash-primary,#215aac)] px-6 rounded-lg text-[14px] font-bold text-white hover:bg-[#194a91] focus:outline-none focus:ring-2 focus:ring-[color:var(--dash-primary,#215aac)] focus:ring-offset-2 transition-colors disabled:opacity-70 disabled:cursor-not-allowed">
                <span x-show="!isSubmitting" class="material-symbols-outlined text-[18px]">save</span>
                <span x-show="!isSubmitting">Simpan Perubahan</span>
                <span x-show="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]" x-cloak>progress_activity</span>
                <span x-show="isSubmitting" x-cloak>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
@endsection
