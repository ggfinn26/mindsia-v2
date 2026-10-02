@extends('layouts.dashboard')

@section('title', 'Tambah Program')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Manajemen Program</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Tambah Program Baru</h2>
        </div>
        <a href="{{ route('programs.index') }}" class="inline-flex min-h-10 items-center gap-2 border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="flex gap-3 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('programs.store') }}" method="POST" enctype="multipart/form-data" novalidate class="border border-slate-200 bg-white">
        @csrf

        {{-- Section: Informasi Dasar --}}
        <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Informasi Dasar</h3>
            <p class="text-xs text-slate-500 mt-0.5">Identitas dan detail utama program</p>
        </div>

        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Kode Program <span class="text-red-500">*</span></label>
                    <input type="text" name="program_code" required
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                           value="{{ old('program_code') }}" placeholder="Contoh: TOEFL-INTENSIVE" />
                    @error('program_code') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Program <span class="text-red-500">*</span></label>
                    <input type="text" name="program_name" required
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                           value="{{ old('program_name') }}" placeholder="Contoh: TOEFL Intensive" />
                    @error('program_name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
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
                <label class="block text-xs font-semibold text-slate-500 mb-1">Harga Program <span class="text-red-500">*</span></label>
                <input type="hidden" name="program_price" x-model="rawValue" />
                <input type="text" x-ref="input" @input="updateValue($event)" required
                       class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                       placeholder="Rp. 0" />
                @error('program_price') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div x-data="{ showAdminFee: {{ old('admin_fee') ? 'true' : 'false' }} }">
                <label class="flex items-center gap-3 cursor-pointer mb-3">
                    <input type="checkbox" id="toggle_admin_fee" x-model="showAdminFee" class="rounded border-slate-300 text-[#215aac] focus:ring-[#215aac]" />
                    <div>
                        <span class="text-sm font-semibold text-slate-800">Kenakan Biaya Admin</span>
                        <p class="text-xs text-slate-500">Biaya tambahan di luar harga program utama.</p>
                    </div>
                </label>

                <div x-show="showAdminFee" x-transition class="border border-blue-200 bg-blue-50/50 p-5 space-y-4">
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
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Nominal Biaya Admin</label>
                        <input type="hidden" name="admin_fee" x-model="rawValue" />
                        <input type="text" x-ref="input" @input="updateValue($event)"
                               class="w-full border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                               placeholder="Rp. 0" />
                        @error('admin_fee') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Mode Biaya Admin</label>
                            <select name="admin_fee_mode" class="w-full border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                                <option value="per_registration" {{ old('admin_fee_mode', 'per_registration') === 'per_registration' ? 'selected' : '' }}>Per Registrasi</option>
                                <option value="per_transaction" {{ old('admin_fee_mode') === 'per_transaction' ? 'selected' : '' }}>Per Transaksi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Waktu Penagihan</label>
                            <select name="admin_fee_timing" class="w-full border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                                <option value="on_first_payment" {{ old('admin_fee_timing', 'on_first_payment') === 'on_first_payment' ? 'selected' : '' }}>Saat Pembayaran Pertama</option>
                                <option value="on_registration" {{ old('admin_fee_timing') === 'on_registration' ? 'selected' : '' }}>Saat Registrasi</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Berkas & Media --}}
        <div class="border-b border-t border-slate-200 bg-slate-50/50 px-6 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Berkas & Media</h3>
            <p class="text-xs text-slate-500 mt-0.5">Template dokumen dan foto pendukung</p>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Template Kwitansi (.docx) <span class="text-red-500">*</span></label>
                <input type="file" name="receipt_template" accept=".docx"
                       class="w-full border border-slate-300 px-4 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100" />
                <div class="flex items-center gap-3 mt-1.5">
                    <p class="text-xs text-slate-500">Digunakan untuk generate kwitansi PDF otomatis.</p>
                    <a href="{{ route('programs.receipt-template.download') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#215aac] hover:underline shrink-0">
                        <span class="material-symbols-outlined text-[14px]">download</span>
                        Unduh Contoh Template
                    </a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('programs.receipt-template.test') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:underline shrink-0">
                        <span class="material-symbols-outlined text-[14px]">science</span>
                        Tes Template
                    </a>
                </div>
                @error('receipt_template') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Foto Program <span class="text-slate-400">(Opsional)</span></label>
                <input type="file" name="image" accept="image/*"
                       class="w-full border border-slate-300 px-4 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100" />
                @error('image') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Deskripsi <span class="text-slate-400">(Opsional)</span></label>
                <textarea name="program_description" rows="4"
                          class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">{{ old('program_description') }}</textarea>
                @error('program_description') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-[#215aac] focus:ring-[#215aac]" />
                    <div>
                        <span class="text-sm font-semibold text-slate-800">Aktifkan Program</span>
                        <p class="text-xs text-slate-500">Program dapat dipilih saat pendaftaran member.</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 flex gap-3 justify-end">
            <a href="{{ route('programs.index') }}" class="px-6 py-2.5 text-sm font-semibold text-slate-600 border border-slate-300 hover:bg-slate-50">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-[#215aac] text-white text-sm font-semibold hover:bg-[#194a91]">
                Simpan Program
            </button>
        </div>
    </form>
</div>
@endsection
