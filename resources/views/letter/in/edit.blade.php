@extends('layouts.dashboard')

@section('title', 'Edit Surat Masuk')
@section('header_title', 'Edit Surat Masuk')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6 pb-10">
    {{-- Header Section Pola B3 --}}
    <div>
        <a href="{{ route('in-letters.show', $inLetter) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Detail Surat
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Surat Masuk</h1>
        <p class="mt-2 text-sm" style="color: var(--dash-text-muted);">Perbarui data surat masuk atau ganti berkas dokumen yang tersimpan.</p>
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

    <form method="POST" action="{{ route('in-letters.update', $inLetter) }}" enctype="multipart/form-data"
          class="rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
          x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
        @csrf @method('PUT')
        <div class="p-5 sm:p-7 space-y-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="letter_number" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">Nomor surat</label>
                    <input type="text" id="letter_number" name="letter_number" value="{{ old('letter_number', $inLetter->letter_number) }}" maxlength="100"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label for="receive_date" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Tanggal diterima <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="date" id="receive_date" name="receive_date" value="{{ old('receive_date', $inLetter->receive_date?->toDateString()) }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label for="sender_name" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Pengirim <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="text" id="sender_name" name="sender_name" value="{{ old('sender_name', $inLetter->sender_name) }}" required maxlength="255"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label for="subject" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Perihal <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject', $inLetter->subject) }}" required maxlength="255"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
            </div>
            <div class="pt-2">
                <label for="file" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                    Ganti file surat <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                </label>
                <div class="p-3 rounded-xl border border-dashed"
                     style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border);">
                    <input type="file" id="file" name="file" class="block w-full text-sm cursor-pointer focus:outline-none"
                           style="color: var(--dash-text-body);">
                </div>
                @if ($inLetter->telegram_file_id)
                    <p class="mt-1.5 text-xs font-medium text-emerald-700 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        File arsip surat saat ini sudah tersimpan. Biarkan kosong jika tidak ingin mengganti.
                    </p>
                @endif
            </div>
        </div>

        <div class="p-5 sm:px-7 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 rounded-b-2xl"
             style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
            <a href="{{ route('in-letters.show', $inLetter) }}"
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
