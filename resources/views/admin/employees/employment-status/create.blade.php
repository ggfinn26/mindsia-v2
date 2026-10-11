@extends('layouts.dashboard')

@section('title', 'Buat Kontrak - ' . $employee->full_name)
@section('header_title', 'Buat Kontrak Kerja')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('employment-statuses.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Kelola Kontrak
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Kepegawaian</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Buat Kontrak Kerja</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Pegawai: <span class="font-medium" style="color: var(--dash-text-heading);">{{ $employee->full_name }}</span> ({{ $employee->employee_code }})
        </p>
    </div>

    {{-- Flash & Error Messages --}}
    @if (session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

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

    <div x-data="{ activeTab: 'lengkap' }"
         class="rounded-2xl"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        {{-- Tab Navigation --}}
        <div class="rounded-t-2xl" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button type="button" @click="activeTab = 'lengkap'"
                        :style="activeTab === 'lengkap' ? 'border-bottom: 2px solid var(--dash-primary); color: var(--dash-primary); background-color: var(--dash-card-bg);' : 'border-bottom: 2px solid transparent; color: var(--dash-text-muted);'"
                        class="w-1/2 py-4 px-3 text-center font-medium text-sm transition-colors flex flex-col items-center justify-center gap-1 rounded-tl-2xl">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">description</span>
                        <span class="text-base font-bold">Form Lengkap</span>
                    </div>
                    <span class="text-xs font-normal">Isi seluruh detail secara manual</span>
                </button>
                <button type="button" @click="activeTab = 'cepat'"
                        :style="activeTab === 'cepat' ? 'border-bottom: 2px solid var(--dash-primary); color: var(--dash-primary); background-color: var(--dash-card-bg);' : 'border-bottom: 2px solid transparent; color: var(--dash-text-muted);'"
                        class="w-1/2 py-4 px-3 text-center font-medium text-sm transition-colors flex flex-col items-center justify-center gap-1 rounded-tr-2xl">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">bolt</span>
                        <span class="text-base font-bold">Mode Cepat</span>
                    </div>
                    <span class="text-xs font-normal">Upload draft file kontrak</span>
                </button>
            </nav>
        </div>

        {{-- Tab Panels --}}
        <div class="p-6 sm:p-8">

            {{-- Tab 1: Form Lengkap --}}
            <form x-show="activeTab === 'lengkap'"
                  action="{{ route('employment-statuses.store', $employee->id) }}"
                  method="POST"
                  class="space-y-6">
                @csrf
                <input type="hidden" name="tab" value="lengkap">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Jenis Ketenagakerjaan --}}
                    <div>
                        <label for="type_employment" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Jenis Ketenagakerjaan <span class="text-red-500">*</span>
                        </label>
                        <select id="type_employment" name="type_employment"
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Jenis</option>
                            @foreach (['Tetap', 'Kontrak', 'Magang'] as $tipe)
                                <option value="{{ $tipe }}" @selected(old('type_employment') === $tipe)>{{ $tipe }}</option>
                            @endforeach
                        </select>
                        @error('type_employment')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Mulai Kontrak --}}
                    <div>
                        <label for="contract_start_date" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Tanggal Mulai Kontrak <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="contract_start_date" name="contract_start_date" value="{{ old('contract_start_date') }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('contract_start_date')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Akhir Kontrak --}}
                    <div>
                        <label for="contract_end_date" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Tanggal Akhir Kontrak <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                        </label>
                        <input type="date" id="contract_end_date" name="contract_end_date" value="{{ old('contract_end_date') }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('contract_end_date')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Posisi / Jabatan --}}
                    <div>
                        <label for="position_id" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Posisi / Jabatan <span class="text-red-500">*</span>
                        </label>
                        <select id="position_id" name="position_id"
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Posisi</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}" @selected(old('position_id') == $position->id)>
                                    {{ $position->position_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-6 mt-8" style="border-top: 1px solid var(--dash-card-border);">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        Simpan Kontrak Lengkap
                    </button>
                    <a href="{{ route('employment-statuses.index') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                       style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </a>
                </div>
            </form>

            {{-- Tab 2: Mode Cepat --}}
            <form x-show="activeTab === 'cepat'" x-cloak
                  action="{{ route('employment-statuses.store', $employee->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf
                <input type="hidden" name="tab" value="cepat">

                <div class="flex items-start gap-3 p-4 rounded-xl text-sm"
                     style="background-color: var(--dash-primary-bg); border: 1px solid color-mix(in srgb, var(--dash-primary) 30%, transparent); color: var(--dash-primary-text);">
                    <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5">info</span>
                    <div>
                        <strong class="font-semibold">Mode Cepat:</strong> Upload file kontrak terlebih dahulu. Tanggal mulai kontrak dapat dilengkapi di kemudian hari melalui menu Edit.
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Jenis Ketenagakerjaan --}}
                    <div>
                        <label for="cepat_type_employment" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Jenis Ketenagakerjaan <span class="text-red-500">*</span>
                        </label>
                        <select id="cepat_type_employment" name="type_employment"
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Jenis</option>
                            @foreach (['Tetap', 'Kontrak', 'Magang'] as $tipe)
                                <option value="{{ $tipe }}" @selected(old('type_employment') === $tipe)>{{ $tipe }}</option>
                            @endforeach
                        </select>
                        @error('type_employment')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Akhir Kontrak --}}
                    <div>
                        <label for="cepat_contract_end_date" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Tanggal Akhir Kontrak <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                        </label>
                        <input type="date" id="cepat_contract_end_date" name="contract_end_date" value="{{ old('contract_end_date') }}"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('contract_end_date')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Posisi / Jabatan --}}
                    <div class="md:col-span-2">
                        <label for="cepat_position_id" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Posisi / Jabatan <span class="text-red-500">*</span>
                        </label>
                        <select id="cepat_position_id" name="position_id"
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Posisi</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}" @selected(old('position_id') == $position->id)>
                                    {{ $position->position_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- File Upload --}}
                <div x-data="{ fileName: null }">
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        File Kontrak <span class="text-red-500">*</span> <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(PDF maksimal 10MB)</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-xl transition-colors relative"
                         style="border-color: var(--dash-input-border); background-color: var(--dash-input-bg);"
                         onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                         onmouseout="this.style.backgroundColor='var(--dash-input-bg)'">
                        <div class="space-y-2 text-center">
                            <span class="material-symbols-outlined text-[40px] mx-auto block" style="color: var(--dash-text-faint);">upload_file</span>
                            <div class="flex text-sm justify-center" style="color: var(--dash-text-body);">
                                <label for="contract_file_path" class="relative cursor-pointer font-medium focus-within:outline-none" style="color: var(--dash-primary-text);">
                                    <span>Pilih file kontrak</span>
                                    <input id="contract_file_path" name="contract_file_path" type="file" class="sr-only" accept=".pdf" @change="fileName = $event.target.files[0]?.name">
                                </label>
                                <p class="pl-1" style="color: var(--dash-text-muted);">atau drag and drop</p>
                            </div>
                            <p class="text-xs" style="color: var(--dash-text-faint);">Format dokumen PDF hingga 10MB</p>
                        </div>
                    </div>
                    <div x-show="fileName" x-cloak class="mt-3 flex items-center gap-2 text-sm p-3 rounded-xl"
                         style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        <span>File terpilih: <strong x-text="fileName"></strong></span>
                    </div>
                    @error('contract_file_path')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-6 mt-8" style="border-top: 1px solid var(--dash-card-border);">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">upload</span>
                        Upload & Buat Kontrak
                    </button>
                    <a href="{{ route('employment-statuses.index') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                       style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
