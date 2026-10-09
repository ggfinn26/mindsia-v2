@extends('layouts.dashboard')

@section('title', 'Upload Template Surat')
@section('header_title', 'Upload Template Surat')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 sm:space-y-8 pb-10">
    <div>
        <a href="{{ route('letter-templates.index') }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-3 focus:outline-none focus:ring-2 focus:ring-[color:var(--dash-primary,#215aac)] rounded-sm focus:ring-offset-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke daftar
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Upload Template Baru</h1>
        <p class="mt-2 text-[14px] leading-relaxed text-slate-600">
            Unggah file dalam format <strong>.docx</strong>. Placeholder (kolom isian) menggunakan <em>alt text</em> pada gambar atau tabel di dalam dokumen tersebut.
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

    <form method="POST" action="{{ route('letter-templates.store') }}" enctype="multipart/form-data" class="bg-white border border-slate-200 shadow-sm rounded-xl overflow-hidden" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
        @csrf
        <div class="p-5 sm:p-7 space-y-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <!-- Kode Template -->
                <div>
                    <label for="template_code" class="mb-1.5 block text-[13px] font-bold text-slate-700">Kode template <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="text" id="template_code" name="template_code" value="{{ old('template_code') }}" required maxlength="100" placeholder="SK_KARYAWAN" 
                           class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] text-slate-900 uppercase focus:bg-white focus:outline-none focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)] transition-colors"
                           aria-required="true">
                </div>
                
                <!-- Nama Template -->
                <div>
                    <label for="template_name" class="mb-1.5 block text-[13px] font-bold text-slate-700">Nama template <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="text" id="template_name" name="template_name" value="{{ old('template_name') }}" required maxlength="255" placeholder="Contoh: SK Karyawan Tetap"
                           class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] text-slate-900 focus:bg-white focus:outline-none focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)] transition-colors"
                           aria-required="true">
                </div>
                
                <!-- Kategori -->
                <div>
                    <label for="letter_category" class="mb-1.5 block text-[13px] font-bold text-slate-700">Kategori <span class="text-red-500">*</span></label>
                    <select id="letter_category" name="letter_category" required
                            class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] text-slate-900 focus:bg-white focus:outline-none focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)] transition-colors">
                        <option value="">— Pilih kategori —</option>
                        <option value="generated" @selected(old('letter_category') === 'generated')>Generated (Surat Karyawan)</option>
                        <option value="marketing" @selected(old('letter_category') === 'marketing')>Marketing (Kontrak Marketing)</option>
                        <option value="announcement" @selected(old('letter_category') === 'announcement')>Announcement (Surat Pemberitahuan)</option>
                        <option value="member" @selected(old('letter_category') === 'member')>Member (Kwitansi / Form Member)</option>
                    </select>
                </div>
                
                <!-- Format Nomor -->
                <div>
                    <label for="letter_number_format" class="mb-1.5 block text-[13px] font-bold text-slate-700">Format nomor surat</label>
                    <input type="text" id="letter_number_format" name="letter_number_format" value="{{ old('letter_number_format') }}" placeholder="{NO}/SK/{YYYY}" 
                           class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] text-slate-900 focus:bg-white focus:outline-none focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)] transition-colors">
                    <p class="mt-1.5 text-[12px] text-slate-500">
                        Token: <code class="bg-slate-100 px-1 rounded font-mono">{NO}</code> <code class="bg-slate-100 px-1 rounded font-mono">{SEQ:5d}</code> <code class="bg-slate-100 px-1 rounded font-mono">{TYPE}</code> <code class="bg-slate-100 px-1 rounded font-mono">{BRANCH}</code>
                        <code class="bg-slate-100 px-1 rounded font-mono">{YEAR}</code> <code class="bg-slate-100 px-1 rounded font-mono">{YY}</code>
                        <code class="bg-slate-100 px-1 rounded font-mono">{MONTH}</code> <code class="bg-slate-100 px-1 rounded font-mono">{BULAN_ROMAWI}</code> <code class="bg-slate-100 px-1 rounded font-mono">{BULAN_NAMA}</code> <code class="bg-slate-100 px-1 rounded font-mono">{MMYY}</code> <code class="bg-slate-100 px-1 rounded font-mono">{MMYYYY}</code><br>
                        Contoh: <code class="bg-slate-100 px-1 rounded font-mono">{NO}/A/{TYPE}/MINDSIA/{BULAN_ROMAWI}/{YEAR}</code> → <em>001/A/SP1/MINDSIA/IX/2025</em>
                    </p>
                </div>
            </div>

            <!-- File Upload -->
            <div class="pt-2">
                <label for="file" class="mb-1.5 block text-[13px] font-bold text-slate-700">File template (.docx) <span class="text-red-500" aria-hidden="true">*</span></label>
                <div class="relative w-full border border-slate-300 border-dashed rounded-xl bg-[#F8FAFC] hover:bg-slate-50 transition-colors flex items-center p-1.5 focus-within:border-[color:var(--dash-primary,#215aac)] focus-within:ring-1 focus-within:ring-[color:var(--dash-primary,#215aac)]">
                    <input type="file" id="file" name="file" accept=".docx" required
                           class="block w-full text-[14px] text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-[13px] file:font-semibold file:bg-white file:text-[color:var(--dash-primary-text,#215aac)] file:border file:border-slate-200 file:shadow-sm hover:file:bg-slate-50 cursor-pointer focus:outline-none min-h-[44px]"
                           aria-required="true">
                </div>
            </div>

            <!-- Is Active -->
            <div class="pt-2">
                <label class="relative flex items-start gap-3 p-3 sm:p-4 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors group focus-within:ring-2 focus-within:ring-[color:var(--dash-primary,#215aac)] focus-within:border-transparent">
                    <div class="flex items-center h-5 mt-0.5">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', true)) 
                               class="w-5 h-5 border-slate-300 text-[color:var(--dash-primary-text,#215aac)] rounded focus:ring-0 focus:outline-none cursor-pointer">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[14px] font-bold text-slate-800">Aktifkan Template</span>
                        <span class="text-[13px] text-slate-500 mt-0.5">Template ini akan langsung muncul dan bisa digunakan oleh user.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="border-t border-slate-100 bg-slate-50 p-5 sm:px-7 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <a href="{{ route('letter-templates.index') }}" 
               class="w-full sm:w-auto inline-flex justify-center min-h-[44px] items-center border border-slate-300 bg-white px-5 rounded-lg text-[14px] font-bold text-slate-700 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-colors">
                Batal
            </a>
            <button type="submit" 
                    :disabled="isSubmitting"
                    class="w-full sm:w-auto inline-flex justify-center min-h-[44px] items-center gap-2 bg-[var(--dash-primary,#215aac)] px-6 rounded-lg text-[14px] font-bold text-white hover:bg-[#194a91] focus:outline-none focus:ring-2 focus:ring-[color:var(--dash-primary,#215aac)] focus:ring-offset-2 transition-colors disabled:opacity-70 disabled:cursor-not-allowed">
                <span x-show="!isSubmitting" class="material-symbols-outlined text-[18px]">cloud_upload</span>
                <span x-show="!isSubmitting">Upload Template</span>
                <span x-show="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]" x-cloak>progress_activity</span>
                <span x-show="isSubmitting" x-cloak>Mengupload...</span>
            </button>
        </div>
    </form>
</div>
@endsection
