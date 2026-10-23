@extends('layouts.dashboard')

@section('title', 'Upload Surat Keluar')
@section('header_title', 'Upload Surat Keluar')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6 pb-10">
    {{-- Header Section Pola B3 --}}
    <div>
        <a href="{{ route('out-letters-upload.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Arsip Surat Keluar
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Upload Surat Keluar</h1>
        <p class="mt-2 text-sm" style="color: var(--dash-text-muted);">Unggah file surat keluar yang sudah diterbitkan fisik sebagai arsip digital.</p>
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
                        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('out-letters-upload.store') }}" enctype="multipart/form-data"
          class="rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
          x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
        @csrf
        <div class="p-5 sm:p-7 space-y-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="branch_id" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Cabang <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <select id="branch_id" name="branch_id" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih cabang</option>
                        @foreach (App\Models\Branch::where('is_active', true)->orderBy('branch_name')->get() as $branch)
                            <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="letter_number" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">Nomor surat</label>
                    <input type="text" id="letter_number" name="letter_number" value="{{ old('letter_number') }}" maxlength="100"
                           placeholder="001/SK/MINDSIA/X/2026"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label for="letter_date" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">Tanggal surat</label>
                    <input type="date" id="letter_date" name="letter_date" value="{{ old('letter_date') }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div class="sm:col-span-2">
                    <label for="subject" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Perihal <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required maxlength="255"
                           placeholder="Contoh: Surat Tugas Pembinaan Cabang"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label for="recipient" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">Penerima</label>
                    <input type="text" id="recipient" name="recipient" value="{{ old('recipient') }}" maxlength="255"
                           placeholder="Nama orang / instansi penerima"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label for="letter_type" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">Jenis surat</label>
                    <input type="text" id="letter_type" name="letter_type" value="{{ old('letter_type') }}" maxlength="100" placeholder="SK, SP, Undangan, Tugas..."
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
            </div>
            <div class="pt-2">
                <label for="file" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                    File surat <span style="color: var(--dash-danger);">*</span>
                </label>
                <div class="p-3 rounded-xl border border-dashed"
                     style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border);">
                    <input type="file" id="file" name="file" required class="block w-full text-sm cursor-pointer focus:outline-none"
                           style="color: var(--dash-text-body);" aria-required="true">
                </div>
            </div>
            <div>
                <label for="notes" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">Catatan Internal</label>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="p-5 sm:px-7 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 rounded-b-2xl"
             style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
            <a href="{{ route('out-letters-upload.index') }}"
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
                <span x-show="!isSubmitting" class="material-symbols-outlined text-[18px]">upload</span>
                <span x-show="!isSubmitting">Upload Surat</span>
                <span x-show="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]" x-cloak>progress_activity</span>
                <span x-show="isSubmitting" x-cloak>Mengunggah...</span>
            </button>
        </div>
    </form>
</div>
@endsection
