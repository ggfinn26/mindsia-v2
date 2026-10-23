@extends('layouts.dashboard')

@section('title', 'Edit Surat Keluar')
@section('header_title', 'Edit Surat Keluar')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6 pb-10">
    {{-- Header Section Pola B3 --}}
    <div>
        <a href="{{ route('out-letters-generate.show', $outLetterViaGenerate) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Detail Surat
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Surat (Draft)</h1>
        <p class="mt-2 text-sm" style="color: var(--dash-text-muted);">Perbarui metadata surat keluar sebelum diterbitkan.</p>
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

    <form method="POST" action="{{ route('out-letters-generate.update', $outLetterViaGenerate) }}"
          class="rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @csrf @method('PUT')
        <div class="p-5 sm:p-7 space-y-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">Penerima</label>
                    <input type="text" name="recipient" value="{{ old('recipient', $outLetterViaGenerate->recipient) }}" maxlength="255"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">Perihal</label>
                    <input type="text" name="subject" value="{{ old('subject', $outLetterViaGenerate->subject) }}" maxlength="255"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">Catatan Internal</label>
                <textarea name="notes" rows="3"
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('notes', $outLetterViaGenerate->notes) }}</textarea>
            </div>
        </div>

        <div class="p-5 sm:px-7 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 rounded-b-2xl"
             style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
            <a href="{{ route('out-letters-generate.show', $outLetterViaGenerate) }}"
               class="w-full sm:w-auto inline-flex justify-center items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[18px]">close</span> Batal
            </a>
            <button type="submit"
                    class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">save</span> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
