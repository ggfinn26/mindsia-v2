@extends('layouts.dashboard')

@section('title', 'Edit ' . $program->program_name)
@section('header_title', 'Edit Program')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page header --}}
    <div>
        <a href="{{ route('programs.show', $program) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Detail Program
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Program</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Program</h1>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
    <div class="flex gap-3 px-4 py-3 rounded-xl border text-sm"
         style="background-color: var(--dash-status-danger-bg); border-color: var(--dash-card-border); color: var(--dash-status-danger-text);">
        <span class="material-symbols-outlined text-[20px] shrink-0">error</span>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('programs.update', $program) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')

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
                               value="{{ old('program_code', $program->program_code) }}" placeholder="Contoh: TOEFL-INTENSIVE"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                        @error('program_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Nama Program <span class="text-red-500">*</span></label>
                        <input type="text" name="program_name" required
                               value="{{ old('program_name', $program->program_name) }}" placeholder="Contoh: TOEFL Intensive"
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                        @error('program_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div x-data="{
                    rawValue: '{{ old('program_price', (int)$program->program_price) }}',
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

                <div x-data="{ showAdminFee: {{ old('admin_fee', $program->admin_fee) > 0 ? 'true' : 'false' }} }">
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
                            rawValue: '{{ old('admin_fee', (int)$program->admin_fee) }}',
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
                                    <option value="per_registration" {{ old('admin_fee_mode', $program->admin_fee_mode) === 'per_registration' ? 'selected' : '' }}>Per Registrasi</option>
                                    <option value="per_transaction" {{ old('admin_fee_mode', $program->admin_fee_mode) === 'per_transaction' ? 'selected' : '' }}>Per Transaksi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Waktu Penagihan</label>
                                <select name="admin_fee_timing"
                                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <option value="on_first_payment" {{ old('admin_fee_timing', $program->admin_fee_timing) === 'on_first_payment' ? 'selected' : '' }}>Saat Pembayaran Pertama</option>
                                    <option value="on_registration" {{ old('admin_fee_timing', $program->admin_fee_timing) === 'on_registration' ? 'selected' : '' }}>Saat Registrasi</option>
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
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Template Kwitansi (.docx)</label>
                    @if($program->receipt_template_path)
                        <div class="text-xs font-semibold px-3 py-2 rounded-lg mb-2 flex items-center gap-2"
                             style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid var(--dash-card-border);">
                            <span class="material-symbols-outlined text-[16px]">description</span>
                            Template aktif tersimpan. Unggah berkas baru untuk mengganti template.
                        </div>
                    @endif
                    <input type="file" name="receipt_template" accept=".docx"
                           class="w-full px-4 py-2 text-sm rounded-lg file:mr-4 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);" />
                    <div class="flex items-center gap-3 mt-1.5">
                        <p class="text-xs" style="color: var(--dash-text-muted);">Format: .docx. Kosongkan jika tidak ingin mengubah template.</p>
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
                    @if($program->image_path)
                        <div class="mb-2">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($program->image_path) }}" alt="Foto Program"
                                 class="h-28 object-contain rounded-xl"
                                 style="border: 1px solid var(--dash-card-border);">
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-4 py-2 text-sm rounded-lg file:mr-4 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);" />
                    <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Kosongkan jika tidak ingin mengubah foto.</p>
                    @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Deskripsi <span class="text-xs font-normal" style="color: var(--dash-text-faint);">(Opsional)</span></label>
                    <x-rich-editor name="program_description" :value="$program->program_description" :rows="4" />
                    @error('program_description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="pt-1">
                    <input type="hidden" name="is_active" value="0">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $program->is_active) ? 'checked' : '' }}
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
                <a href="{{ route('programs.show', $program) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">close</span>Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity cursor-pointer shadow-sm"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>Simpan Perubahan
                </button>
            </div>
        </div>

    </form>
</div>
@endsection
