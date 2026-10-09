@extends('layouts.dashboard')

@section('title', 'Tambah Program - MINDSIA')
@section('header_title', 'Tambah Program')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('programs.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Tambah Program Baru</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Kurikulum → Program</p>
        </div>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('programs.store') }}" method="POST" enctype="multipart/form-data" novalidate class="space-y-5">
        @csrf

        {{-- Informasi Dasar --}}
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
                <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Dasar</h3>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Identitas dan detail utama program</p>
            </div>
            <div class="p-5 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Kode Program <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" name="program_code" required value="{{ old('program_code') }}"
                               placeholder="TOEFL-INTENSIVE"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        @error('program_code')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Nama Program <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" name="program_name" required value="{{ old('program_name') }}"
                               placeholder="TOEFL Intensive"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        @error('program_name')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
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
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Harga Program <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="hidden" name="program_price" x-model="rawValue">
                    <input type="text" x-ref="input" @input="updateValue($event)" required placeholder="Rp. 0"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    @error('program_price')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>

                <div x-data="{ showAdminFee: {{ old('admin_fee') ? 'true' : 'false' }} }">
                    <label class="flex items-center gap-3 cursor-pointer mb-3">
                        <input type="checkbox" x-model="showAdminFee" class="h-4 w-4 rounded">
                        <div>
                            <span class="text-sm font-semibold" style="color: var(--dash-text-body);">Kenakan Biaya Admin</span>
                            <p class="text-xs" style="color: var(--dash-text-muted);">Biaya tambahan di luar harga program utama.</p>
                        </div>
                    </label>
                    <div x-show="showAdminFee" x-transition class="rounded-xl p-4 space-y-4"
                         style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-card-border);">
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
                            <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">Nominal Biaya Admin</label>
                            <input type="hidden" name="admin_fee" x-model="rawValue">
                            <input type="text" x-ref="input" @input="updateValue($event)" placeholder="Rp. 0"
                                   class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                                   style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                            @error('admin_fee')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">Mode Biaya Admin</label>
                                <select name="admin_fee_mode"
                                        class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                    <option value="per_registration" {{ old('admin_fee_mode', 'per_registration') === 'per_registration' ? 'selected' : '' }}>Per Registrasi</option>
                                    <option value="per_transaction" {{ old('admin_fee_mode') === 'per_transaction' ? 'selected' : '' }}>Per Transaksi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">Waktu Penagihan</label>
                                <select name="admin_fee_timing"
                                        class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                    <option value="on_first_payment" {{ old('admin_fee_timing', 'on_first_payment') === 'on_first_payment' ? 'selected' : '' }}>Saat Pembayaran Pertama</option>
                                    <option value="on_registration" {{ old('admin_fee_timing') === 'on_registration' ? 'selected' : '' }}>Saat Registrasi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Berkas & Media --}}
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
                <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Berkas & Media</h3>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Template dokumen dan foto pendukung</p>
            </div>
            <div class="p-5 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Template Kwitansi (.docx) <span style="color:#dc2626;">*</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer rounded-lg px-3 py-2 transition-colors"
                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                           onmouseover="this.style.borderColor='var(--dash-primary)'"
                           onmouseout="this.style.borderColor='var(--dash-card-border)'">
                        <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-text-muted);">attach_file</span>
                        <span class="text-sm truncate flex-1" id="rcpt-file-name" style="color: var(--dash-text-muted);">Pilih file .docx</span>
                        <input type="file" name="receipt_template" accept=".docx" class="hidden"
                               onchange="const n=document.getElementById('rcpt-file-name'); n.textContent=this.files[0]?.name||'Pilih file .docx'; n.style.color=this.files[0]?'var(--dash-text-body)':'var(--dash-text-muted)';">
                    </label>
                    <div class="flex items-center gap-3 mt-1.5">
                        <p class="text-xs" style="color: var(--dash-text-faint);">Digunakan untuk generate kwitansi PDF otomatis.</p>
                        <a href="{{ route('programs.receipt-template.download') }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold shrink-0 hover:underline"
                           style="color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[14px]">download</span>Contoh Template
                        </a>
                    </div>
                    @error('receipt_template')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Foto Program <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer rounded-lg px-3 py-2 transition-colors"
                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                           onmouseover="this.style.borderColor='var(--dash-primary)'"
                           onmouseout="this.style.borderColor='var(--dash-card-border)'">
                        <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-text-muted);">image</span>
                        <span class="text-sm truncate flex-1" id="img-file-name" style="color: var(--dash-text-muted);">Pilih foto</span>
                        <input type="file" name="image" accept="image/*" class="hidden"
                               onchange="const n=document.getElementById('img-file-name'); n.textContent=this.files[0]?.name||'Pilih foto'; n.style.color=this.files[0]?'var(--dash-text-body)':'var(--dash-text-muted)';">
                    </label>
                    @error('image')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Deskripsi <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                    </label>
                    <textarea name="program_description" rows="3"
                              class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                              style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">{{ old('program_description') }}</textarea>
                    @error('program_description')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded">
                        <div>
                            <span class="text-sm font-semibold" style="color: var(--dash-text-body);">Aktifkan Program</span>
                            <p class="text-xs" style="color: var(--dash-text-muted);">Program dapat dipilih saat pendaftaran member.</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 justify-end">
            <a href="{{ route('programs.index') }}"
               class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors inline-flex items-center gap-1.5"
               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[16px]">close</span>Batal</a>
            <button type="submit"
                    class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity inline-flex items-center gap-1.5"
                    style="background-color: var(--dash-primary);"><span class="material-symbols-outlined text-[16px]">save</span>Simpan Program</button>
        </div>
    </form>
</div>
@endsection
