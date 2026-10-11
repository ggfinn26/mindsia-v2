@extends('layouts.dashboard')

@section('title', 'Edit ' . $program->program_name)

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Program</h1>
        <a href="{{ route('programs.show', $program) }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('programs.update', $program) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow sm:rounded-lg">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Program</label>
                <input type="text" name="program_code" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       value="{{ old('program_code', $program->program_code) }}" />
                @error('program_code')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Program</label>
                <input type="text" name="program_name" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       value="{{ old('program_name', $program->program_name) }}" />
                @error('program_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Program</label>
                <input type="hidden" name="program_price" x-model="rawValue" />
                <input type="text" x-ref="input" @input="updateValue($event)" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Rp. 0" />
                @error('program_price')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ showAdminFee: {{ $program->admin_fee > 0 ? 'true' : 'false' }} }">
                <div class="flex items-center gap-2 mb-2">
                    <input type="checkbox" id="toggle_admin_fee" x-model="showAdminFee" class="rounded" />
                    <label for="toggle_admin_fee" class="text-sm font-medium text-gray-700 cursor-pointer">Kenakan Biaya Admin</label>
                </div>
                <div x-show="showAdminFee" x-transition class="space-y-4 pl-4 border-l-2 border-blue-200">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Biaya Admin</label>
                        <input type="hidden" name="admin_fee" x-model="rawValue" />
                        <input type="text" x-ref="input" @input="updateValue($event)"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="Rp. 0" />
                        @error('admin_fee')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mode Biaya Admin</label>
                        <select name="admin_fee_mode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="per_registration" {{ old('admin_fee_mode', $program->admin_fee_mode) === 'per_registration' ? 'selected' : '' }}>Per Registrasi</option>
                            <option value="per_transaction" {{ old('admin_fee_mode', $program->admin_fee_mode) === 'per_transaction' ? 'selected' : '' }}>Per Transaksi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Penagihan</label>
                        <select name="admin_fee_timing" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="on_first_payment" {{ old('admin_fee_timing', $program->admin_fee_timing) === 'on_first_payment' ? 'selected' : '' }}>Saat Pembayaran Pertama</option>
                            <option value="on_registration" {{ old('admin_fee_timing', $program->admin_fee_timing) === 'on_registration' ? 'selected' : '' }}>Saat Registrasi</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Program (Opsional)</label>
                @if($program->image_path)
                    <div class="mb-2">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($program->image_path) }}" alt="Foto Program" class="h-32 object-contain border border-gray-200 rounded">
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah foto.</p>
                @error('image')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                <textarea name="program_description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('program_description', $program->program_description) }}</textarea>
                @error('program_description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Template Kwitansi (.docx)</label>
                @if($program->receipt_template_path)
                    <p class="text-sm text-green-700 bg-green-50 border border-green-200 rounded px-3 py-2 mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">description</span>
                        Template sudah ada. Upload file baru untuk mengganti.
                    </p>
                @endif
                <input type="file" name="receipt_template" accept=".docx"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                <div class="flex items-center gap-3 mt-1">
                    <p class="text-xs text-gray-500">Format: .docx. Kosongkan jika tidak ingin mengubah template.</p>
                    <a href="{{ route('programs.receipt-template.download') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#215aac] hover:underline shrink-0">
                        <span class="material-symbols-outlined text-[14px]">download</span>
                        Unduh Contoh Template
                    </a>
                </div>
                @error('receipt_template')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input type="hidden" name="is_active" value="0">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $program->is_active) ? 'checked' : '' }} class="rounded" />
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3 justify-end">
            <a href="{{ route('programs.show', $program) }}" class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
