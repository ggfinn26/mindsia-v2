@extends('layouts.dashboard')

@section('title', 'Tambah Program')
@section('header_title', 'Tambah Program')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page header --}}
    <div>
        <a href="{{ route('programs.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Program
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Program</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Tambah Program Baru</h1>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
    <div class="flex gap-3 px-4 py-3 rounded-xl border text-sm"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[20px] shrink-0">error</span>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('programs.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- Section: Informasi Dasar --}}
        <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Informasi Dasar</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Identitas dan detail utama program</p>
            </div>
            <div class="px-6 py-5 space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Kode Program <span class="text-red-500">*</span></label>
                        <input type="text" name="program_code" required
                               value="{{ old('program_code') }}" placeholder="Contoh: TOEFL-INTENSIVE"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                        @error('program_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Nama Program <span class="text-red-500">*</span></label>
                        <input type="text" name="program_name" required
                               value="{{ old('program_name') }}" placeholder="Contoh: TOEFL Intensive"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                        @error('program_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div x-data="{
                    rawValue: '{{ old('program_price', '') }}',
                    get displayValue() {
                        if (this.rawValue === '' || this.rawValue === null) return '';
                        return 'Rp. ' + new Intl.NumberFormat('id-ID').format(this.rawValue);
                    },
                    updateValue(event) {
                        let digits = event.target.value.replace(/\D/g, '');
                        this.rawValue = digits ? parseInt(digits, 10) : '';
                        event.target.value = this.displayValue;
                    }
                }" x-init="$refs.input.value = displayValue">
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Harga Program <span class="text-red-500">*</span></label>
                    <input type="hidden" name="program_price" x-model="rawValue" />
                    <input type="text" x-ref="input" @input="updateValue($event)" required placeholder="Rp. 0"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('program_price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div x-data="{ showAdminFee: {{ old('admin_fee') ? 'true' : 'false' }} }">
                    <label class="flex items-center gap-3 cursor-pointer mb-3">
                        <input type="checkbox" id="toggle_admin_fee" x-model="showAdminFee"
                               class="rounded"
                               style="border-color: var(--dash-input-border);" />
                        <div>
                            <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">Kenakan Biaya Admin</span>
                            <p class="text-xs" style="color: var(--dash-text-muted);">Biaya tambahan di luar harga program utama.</p>
                        </div>
                    </label>

                    <div x-show="showAdminFee" x-transition
                         class="p-5 rounded-xl space-y-4"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <div x-data="{
                            rawValue: '{{ old('admin_fee', 0) }}',
                            get displayValue() {
                                if (this.rawValue === '' || this.rawValue === null) return '';
                                return 'Rp. ' + new Intl.NumberFormat('id-ID').format(this.rawValue);
                            },
                            updateValue(event) {
                                let digits = event.target.value.replace(/\D/g, '');
                                this.rawValue = digits ? parseInt(digits, 10) : '';
                                event.target.value = this.displayValue;
                            }
                        }" x-init="$refs.input.value = displayValue">
                            <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Nominal Biaya Admin</label>
                            <input type="hidden" name="admin_fee" x-model="rawValue" />
                            <input type="text" x-ref="input" @input="updateValue($event)" placeholder="Rp. 0"
                                   class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                            @error('admin_fee')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Mode Biaya Admin</label>
                                <select name="admin_fee_mode"
                                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <option value="per_registration" {{ old('admin_fee_mode', 'per_registration') === 'per_registration' ? 'selected' : '' }}>Per Registrasi</option>
                                    <option value="per_transaction" {{ old('admin_fee_mode') === 'per_transaction' ? 'selected' : '' }}>Per Transaksi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Waktu Penagihan</label>
                                <select name="admin_fee_timing"
                                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <option value="on_first_payment" {{ old('admin_fee_timing', 'on_first_payment') === 'on_first_payment' ? 'selected' : '' }}>Saat Pembayaran Pertama</option>
                                    <option value="on_registration" {{ old('admin_fee_timing') === 'on_registration' ? 'selected' : '' }}>Saat Registrasi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Section: Berkas & Media --}}
        <div class="rounded-2xl overflow-hidden mt-6" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Berkas & Media</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Template dokumen dan foto pendukung</p>
            </div>
            <div class="px-6 py-5 space-y-5">

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Template Kwitansi (.docx) <span class="text-red-500">*</span></label>
                    <input type="file" name="receipt_template" accept=".docx"
                           class="w-full px-4 py-2 text-sm rounded-lg file:mr-4 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);" />
                    <div class="flex items-center gap-3 mt-1.5">
                        <p class="text-xs" style="color: var(--dash-text-muted);">Digunakan untuk generate kwitansi PDF otomatis.</p>
                        <a href="{{ route('programs.receipt-template.download') }}"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold hover:underline shrink-0"
                           style="color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[14px]">download</span>Unduh Contoh Template
                        </a>
                        <span style="color: var(--dash-card-border);">|</span>
                        <a href="{{ route('programs.receipt-template.test') }}"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold hover:underline shrink-0"
                           style="color: var(--dash-text-muted);">
                            <span class="material-symbols-outlined text-[14px]">science</span>Tes Template
                        </a>
                    </div>
                    @error('receipt_template')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Foto Program <span class="text-xs font-normal" style="color: var(--dash-text-faint);">(Opsional)</span></label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-4 py-2 text-sm rounded-lg file:mr-4 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);" />
                    @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Deskripsi <span class="text-xs font-normal" style="color: var(--dash-text-faint);">(Opsional)</span></label>
                    <x-rich-editor name="program_description" :rows="4" />
                    @error('program_description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked
                               class="rounded"
                               style="border-color: var(--dash-input-border);" />
                        <div>
                            <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">Aktifkan Program</span>
                            <p class="text-xs" style="color: var(--dash-text-muted);">Program dapat dipilih saat pendaftaran member.</p>
                        </div>
                    </label>
                </div>

            </div>
        </div>

        @include('program.partials.landing-fields')

        <div class="rounded-2xl overflow-hidden mt-6" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            {{-- Actions --}}
            <div class="flex gap-3 justify-end px-6 py-4 rounded-b-2xl" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('programs.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">close</span>Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>Simpan Program
                </button>
            </div>
        </div>

    </form>
</div>
@endsection
