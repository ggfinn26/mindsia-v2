@extends('layouts.dashboard')

@section('title', 'Buat Sosialisasi - MINDSIA')
@section('header_title', 'Buat Sosialisasi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6"
    x-data="{
        noInstitusi: {{ old('institution_id') ? 'false' : (old('location_name') ? 'true' : 'false') }}
    }">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('socializations.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Sosialisasi
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Pemasaran</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Buat Sosialisasi Baru</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Isi detail lokasi dan jadwal pelaksanaan. Penugasan tim dilakukan setelah sosialisasi dibuat.
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

    <form action="{{ route('socializations.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Area info --}}
        <div class="rounded-xl px-4 py-3 text-sm flex items-center gap-2"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-text-muted);">location_on</span>
            <span class="font-semibold" style="color: var(--dash-text-heading);">Area:</span>
            <span>{{ $area?->name ?? 'Semua Area' }}</span>
            @if ($area?->region)
                <span style="color: var(--dash-text-faint);">·</span>
                <span style="color: var(--dash-text-muted);">{{ $area->region->name }}</span>
            @endif
        </div>

        {{-- SECTION: Lokasi --}}
        <div class="rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 rounded-t-2xl"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Lokasi Sosialisasi</h2>
            </div>
            <div class="p-6 space-y-4">
                {{-- Institution dropdown --}}
                <div x-show="!noInstitusi">
                    <label for="institution_id" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Institusi / Sekolah <span class="text-red-500">*</span>
                    </label>
                    <select name="institution_id" id="institution_id"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Institusi ...</option>
                        @foreach ($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                                {{ $inst->institution_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('institution_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Toggle: bukan institusi --}}
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" x-model="noInstitusi"
                        class="w-4 h-4 rounded"
                        style="accent-color: var(--dash-primary);">
                    <span class="text-sm font-medium" style="color: var(--dash-text-body);">Institusi tidak ditemukan / bukan sekolah</span>
                </label>

                {{-- location_name muncul saat toggle --}}
                <div x-show="noInstitusi" x-cloak>
                    <label for="location_name" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Nama Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="location_name" id="location_name"
                        value="{{ old('location_name') }}"
                        placeholder="Contoh: Gedung Serbaguna RT 05 Kelurahan Antang"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('location_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- SECTION: Jadwal --}}
        <div class="rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 rounded-t-2xl"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Jadwal Pelaksanaan</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="schedule_date" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="schedule_date" id="schedule_date"
                        value="{{ old('schedule_date') }}" required
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('schedule_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Jam Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="start_time" id="start_time"
                            value="{{ old('start_time') }}" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('start_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Jam Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="end_time" id="end_time"
                            value="{{ old('end_time') }}" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('end_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION: Fee Partner --}}
        <div class="rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 rounded-t-2xl"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                    Fee Partner <span class="font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="partner_fee_amount" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Jumlah Fee (Rp)</label>
                        <input type="number" name="partner_fee_amount" id="partner_fee_amount"
                            value="{{ old('partner_fee_amount') }}" min="0" step="1000" placeholder="0"
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label for="partner_fee_due_date" class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Tenggat Fee</label>
                        <input type="date" name="partner_fee_due_date" id="partner_fee_due_date"
                            value="{{ old('partner_fee_due_date') }}"
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions: dua tombol sesuai flow.md --}}
        <div class="flex items-center justify-end gap-3 pt-2 pb-6">
            <a href="{{ route('socializations.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">close</span>
                Batal
            </a>
            <button type="submit" name="schedule_now" value="0"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                style="color: var(--dash-text-heading); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Simpan sebagai Draft
            </button>
            <button type="submit" name="schedule_now" value="1"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">event_available</span>
                Jadwalkan
            </button>
        </div>
    </form>
</div>
@endsection
