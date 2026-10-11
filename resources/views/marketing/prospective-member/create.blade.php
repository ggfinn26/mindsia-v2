@extends('layouts.dashboard')

@section('title', 'Tambah Calon Member')
@section('header_title', 'Tambah Calon Member')

@section('content')
@php
    $backUrl = request('socialization_id')
        ? route('socializations.show', request('socialization_id'))
        : route('prospective-members.index');
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ $backUrl }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Marketing</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Tambah Calon Member</h1>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('prospective-members.store') }}" method="POST">
        @csrf
        @if(request('socialization_id'))
            <input type="hidden" name="socialization_id" value="{{ request('socialization_id') }}">
        @endif

        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Data Calon Member</h2>
            </div>
            <div class="px-6 py-5 space-y-5">

                {{-- Nama + WA --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Nomor WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <div class="flex rounded-lg overflow-hidden"
                             style="border: 1px solid var(--dash-input-border);">
                            <span class="inline-flex items-center px-3 text-sm font-bold shrink-0"
                                  style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border-right: 1px solid var(--dash-input-border);">+62</span>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required
                                   placeholder="8xxxxxxxxxx" minlength="8" maxlength="15"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                                   class="flex-1 min-w-0 px-3 py-2.5 text-sm focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); color: var(--dash-text-heading);"
                                   onfocus="this.closest('div').style.borderColor='var(--dash-primary)'; this.closest('div').style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.closest('div').style.borderColor='var(--dash-input-border)'; this.closest('div').style.boxShadow='none'">
                        </div>
                        @error('whatsapp_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Gender + Status + Email --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Jenis Kelamin</label>
                        <select name="gender"
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih ...</option>
                            <option value="L" @selected(old('gender') === 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender') === 'P')>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Status Awal</label>
                        <select name="status"
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="ALMOST" @selected(old('status', 'ALMOST') === 'ALMOST')>Hampir</option>
                            <option value="YES"    @selected(old('status') === 'YES')>Berminat</option>
                            <option value="NO"     @selected(old('status') === 'NO')>Tidak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Email (opsional)</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Sekolah --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Sekolah (opsional)</label>
                    <select name="institution_id"
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih ...</option>
                        @foreach(\App\Models\Institution::orderBy('institution_name')->get() as $inst)
                            <option value="{{ $inst->id }}" @selected(old('institution_id') == $inst->id)>
                                {{ $inst->institution_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Instagram --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Instagram (opsional)</label>
                    <input type="text" name="instagram" value="{{ old('instagram') }}"
                           placeholder="@username"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Catatan Awal</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all resize-none"
                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                              onfocus="this.style.borderColor='var(--dash-primary)'"
                              onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('notes') }}</textarea>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 flex items-center gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Simpan
                </button>
                <a href="{{ $backUrl }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
            </div>
        </div>
    </form>

</div>
@endsection
