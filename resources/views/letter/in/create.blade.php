@extends('layouts.dashboard')

@section('title', 'Arsipkan Surat Masuk')
@section('header_title', 'Arsipkan Surat Masuk')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6 pb-10">
    {{-- Header Section Pola B3 --}}
    <div>
        <a href="{{ route('in-letters.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Surat Masuk
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Arsipkan Surat Masuk</h1>
        <p class="mt-2 text-sm" style="color: var(--dash-text-muted);">Catat dan simpan dokumen surat yang diterima dari pihak luar ke dalam arsip digital.</p>
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

    <form method="POST" action="{{ route('in-letters.store') }}" enctype="multipart/form-data"
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
                           placeholder="Nomor surat dari pengirim"
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
                <div>
                    <label for="receive_date" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Tanggal diterima <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="date" id="receive_date" name="receive_date" value="{{ old('receive_date', today()->toDateString()) }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label for="sender_name" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                        Pengirim <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="text" id="sender_name" name="sender_name" value="{{ old('sender_name') }}" required maxlength="255"
                           placeholder="Nama orang / instansi pengirim"
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
                           placeholder="Contoh: Undangan Sosialisasi Program Kemitraan"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
            </div>
            <div class="pt-2">
                <label for="file" class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">
                    File berkas surat
                </label>
                <div class="p-3 rounded-xl border border-dashed"
                     style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border);">
                    <input type="file" id="file" name="file" class="block w-full text-sm cursor-pointer focus:outline-none"
                           style="color: var(--dash-text-body);">
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
            <a href="{{ route('in-letters.index') }}"
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
                <span x-show="!isSubmitting">Simpan Surat Masuk</span>
                <span x-show="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]" x-cloak>progress_activity</span>
                <span x-show="isSubmitting" x-cloak>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
@endsection
