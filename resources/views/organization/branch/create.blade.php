@extends('layouts.dashboard')

@section('title', 'Tambah Cabang - MINDSIA')
@section('header_title', 'Tambah Cabang')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('branches.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Kelola Cabang
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Organisasi</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Tambah Cabang</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Tambahkan cabang baru ke dalam jaringan organisasi.
        </p>
    </div>

    {{-- Validation Errors --}}
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

    {{-- Form Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center gap-2"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">add_business</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Cabang Baru</h2>
        </div>

        <form action="{{ route('branches.store') }}" method="POST">
            @csrf

            <div class="px-6 py-5 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Kode Cabang --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Kode Cabang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code_branches" value="{{ old('code_branches') }}" required
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    {{-- Nama Cabang --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Nama Cabang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="branch_name" value="{{ old('branch_name') }}" required
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    {{-- Area --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Area <span class="text-red-500">*</span>
                        </label>
                        <select name="areas_id" required
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Area</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ old('areas_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Alamat --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Alamat
                        </label>
                        <textarea name="address" rows="3"
                                  class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                  style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                  onfocus="this.style.borderColor='var(--dash-primary)'"
                                  onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('address') }}</textarea>
                    </div>

                    {{-- URL Google Maps --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            URL Google Maps
                        </label>
                        <input type="url" name="gmaps_url" value="{{ old('gmaps_url') }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    {{-- No. WhatsApp --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            No. WhatsApp
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3.5 text-sm font-semibold rounded-l-lg"
                                  style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-input-border); border-right: 0; color: var(--dash-text-muted);">
                                +62
                            </span>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="8xxxxxxxxxx"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')" minlength="8" maxlength="15"
                                   class="w-full px-4 py-2.5 text-sm rounded-r-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>
                    </div>

                    {{-- Instagram --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Instagram
                        </label>
                        <input type="text" name="instagram" value="{{ old('instagram') }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    {{-- Latitude --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Latitude
                        </label>
                        <input type="number" step="any" name="latitude" value="{{ old('latitude') }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    {{-- Longitude --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Longitude
                        </label>
                        <input type="number" step="any" name="longitude" value="{{ old('longitude') }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    {{-- Radius --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Radius (Meter)
                        </label>
                        <input type="number" name="radius_meters" value="{{ old('radius_meters', 100) }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    {{-- Status Aktif Switch --}}
                    <div class="flex items-center pt-6">
                        <input type="hidden" name="is_active" value="0">
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-10 h-6 rounded-full transition-colors peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"
                                 style="background-color: var(--dash-input-border);"></div>
                            <span class="ml-3 text-sm font-medium" style="color: var(--dash-text-body);">Status Aktif</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('branches.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Cabang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

