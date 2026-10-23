@extends('layouts.dashboard')

@section('title', 'Edit Sosialisasi - MINDSIA')
@section('header_title', 'Edit Sosialisasi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('socializations.show', $socialization) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Detail Sosialisasi
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Pemasaran</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Sosialisasi</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui detail nama lokasi dan informasi kompensasi fee partner.
        </p>
    </div>

    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('socializations.update', $socialization) }}" method="POST"
          class="rounded-2xl space-y-6"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @csrf
        @method('PATCH')

        <div class="px-6 py-4 rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Informasi Kegiatan</h2>
        </div>

        <div class="p-6 space-y-6">
            <div>
                <label for="location_name" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                    Nama Lokasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="location_name" id="location_name"
                    value="{{ old('location_name', $socialization->location_name) }}"
                    class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                @error('location_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t" style="border-color: var(--dash-card-border);">
                <h3 class="text-sm font-semibold mb-4" style="color: var(--dash-text-heading);">Fee Partner <span class="font-normal" style="color: var(--dash-text-muted);">(opsional)</span></h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="partner_fee_amount" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Jumlah Fee (Rp)</label>
                        <input type="number" name="partner_fee_amount" id="partner_fee_amount"
                            value="{{ old('partner_fee_amount', $socialization->partner_fee_amount) }}"
                            min="0" step="1000" placeholder="0"
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label for="partner_fee_due_date" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Tenggat Fee</label>
                        <input type="date" name="partner_fee_due_date" id="partner_fee_due_date"
                            value="{{ old('partner_fee_due_date', $socialization->partner_fee_due_date?->format('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 rounded-b-2xl flex items-center justify-end gap-3"
             style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <a href="{{ route('socializations.show', $socialization) }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">close</span>
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
