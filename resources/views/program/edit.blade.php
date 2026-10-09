@extends('layouts.dashboard')

@section('title', 'Edit ' . $program->program_name . ' - MINDSIA')
@section('header_title', 'Edit Program')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('programs.show', $program) }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Edit Program</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">{{ $program->program_name }}</p>
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

    <form action="{{ route('programs.update', $program) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        {{-- Informasi Dasar --}}
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
                <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Dasar</h3>
            </div>
            <div class="p-5 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Kode Program <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" name="program_code" required
                               value="{{ old('program_code', $program->program_code) }}"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        @error('program_code')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Nama Program <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" name="program_name" required
                               value="{{ old('program_name', $program->program_name) }}"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        @error('program_name')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
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
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Harga Program <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="hidden" name="program_price" x-model="rawValue">
                    <input type="text" x-ref="input" @input="updateValue($event)" required placeholder="Rp. 0"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    @error('program_price')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>

                <div x-data="{ showAdminFee: {{ $program->admin_fee > 0 ? 'true' : 'false' }} }">
                    <label class="flex items-center gap-3 cursor-pointer mb-3">
                        <input type="checkbox" x-model="showAdminFee" class="h-4 w-4 rounded">
                        <span class="text-sm font-semibold" style="color: var(--dash-text-body);">Kenakan Biaya Admin</span>
                    </label>
                    <div x-show="showAdminFee" x-transition class="rounded-xl p-4 space-y-4"
                         style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-card-border);">
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
                                    <option value="per_registration" {{ old('admin_fee_mode', $program->admin_fee_mode) === 'per_registration' ? 'selected' : '' }}>Per Registrasi</option>
                                    <option value="per_transaction" {{ old('admin_fee_mode', $program->admin_fee_mode) === 'per_transaction' ? 'selected' : '' }}>Per Transaksi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">Waktu Penagihan</label>
                                <select name="admin_fee_timing"
                                        class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                    <option value="on_first_payment" {{ old('admin_fee_timing', $program->admin_fee_timing) === 'on_first_payment' ? 'selected' : '' }}>Saat Pembayaran Pertama</option>
                                    <option value="on_registration" {{ old('admin_fee_timing', $program->admin_fee_timing) === 'on_registration' ? 'selected' : '' }}>Saat Registrasi</option>
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
            </div>
            <div class="p-5 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Foto Program <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                    </label>
                    @if($program->image_path)
                        <div class="mb-2">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($program->image_path) }}"
                                 alt="Foto Program" class="h-28 object-contain rounded-xl"
                                 style="border: 1px solid var(--dash-card-border);">
                        </div>
                    @endif
                    <label class="flex items-center gap-3 cursor-pointer rounded-lg px-3 py-2 transition-colors"
                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                           onmouseover="this.style.borderColor='var(--dash-primary)'"
                           onmouseout="this.style.borderColor='var(--dash-card-border)'">
                        <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-text-muted);">image</span>
                        <span class="text-sm truncate flex-1" id="edit-img-file-name" style="color: var(--dash-text-muted);">Pilih foto baru</span>
                        <input type="file" name="image" accept="image/*" class="hidden"
                               onchange="const n=document.getElementById('edit-img-file-name'); n.textContent=this.files[0]?.name||'Pilih foto baru'; n.style.color=this.files[0]?'var(--dash-text-body)':'var(--dash-text-muted)';">
                    </label>
                    <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Kosongkan jika tidak ingin mengubah foto.</p>
                    @error('image')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Deskripsi <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                    </label>
                    <textarea name="program_description" rows="3"
                              class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                              style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">{{ old('program_description', $program->program_description) }}</textarea>
                    @error('program_description')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Template Kwitansi (.docx)
                    </label>
                    @if($program->receipt_template_path)
                        <div class="flex items-center gap-2 mb-2 px-3 py-2 rounded-lg text-sm"
                             style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
                            <span class="material-symbols-outlined text-[16px]">description</span>
                            Template sudah ada. Upload baru untuk mengganti.
                        </div>
                    @endif
                    <label class="flex items-center gap-3 cursor-pointer rounded-lg px-3 py-2 transition-colors"
                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                           onmouseover="this.style.borderColor='var(--dash-primary)'"
                           onmouseout="this.style.borderColor='var(--dash-card-border)'">
                        <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-text-muted);">attach_file</span>
                        <span class="text-sm truncate flex-1" id="edit-rcpt-file-name" style="color: var(--dash-text-muted);">Pilih file .docx</span>
                        <input type="file" name="receipt_template" accept=".docx" class="hidden"
                               onchange="const n=document.getElementById('edit-rcpt-file-name'); n.textContent=this.files[0]?.name||'Pilih file .docx'; n.style.color=this.files[0]?'var(--dash-text-body)':'var(--dash-text-muted)';">
                    </label>
                    <div class="flex items-center gap-3 mt-1">
                        <p class="text-xs" style="color: var(--dash-text-faint);">Kosongkan jika tidak ingin mengubah template.</p>
                        <a href="{{ route('programs.receipt-template.download') }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold shrink-0 hover:underline"
                           style="color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[14px]">download</span>Contoh Template
                        </a>
                    </div>
                    @error('receipt_template')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <input type="hidden" name="is_active" value="0">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $program->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded">
                        <span class="text-sm font-medium" style="color: var(--dash-text-body);">Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 justify-end">
            <a href="{{ route('programs.show', $program) }}"
               class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</a>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">Simpan</button>
        </div>
    </form>
</div>
@endsection
