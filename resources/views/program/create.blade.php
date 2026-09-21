@extends('layouts.dashboard')

@section('title', 'Tambah Program')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Program</h1>
        <a href="{{ route('programs.index') }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">
            Kembali
        </a>
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

    <form action="{{ route('programs.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow sm:rounded-lg">
        @csrf
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Program</label>
                <input type="text" name="program_code" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       value="{{ old('program_code') }}" placeholder="Contoh: TOEFL-INTENSIVE" />
                @error('program_code')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Program</label>
                <input type="text" name="program_name" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       value="{{ old('program_name') }}" placeholder="Contoh: TOEFL Intensive" />
                @error('program_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Program</label>
                <input type="number" name="program_price" required min="0" step="0.01"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       value="{{ old('program_price') }}" placeholder="0" />
                @error('program_price')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ showAdminFee: {{ old('admin_fee') ? 'true' : 'false' }} }">
                <div class="flex items-center gap-2 mb-2">
                    <input type="checkbox" id="toggle_admin_fee" x-model="showAdminFee" class="rounded" />
                    <label for="toggle_admin_fee" class="text-sm font-medium text-gray-700 cursor-pointer">Kenakan Biaya Admin</label>
                </div>
                <div x-show="showAdminFee" x-transition class="space-y-4 pl-4 border-l-2 border-blue-200">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Biaya Admin</label>
                        <input type="number" name="admin_fee" min="0" step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               value="{{ old('admin_fee', 0) }}" placeholder="0" />
                        @error('admin_fee')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mode Biaya Admin</label>
                        <select name="admin_fee_mode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="per_registration" {{ old('admin_fee_mode', 'per_registration') === 'per_registration' ? 'selected' : '' }}>Per Registrasi</option>
                            <option value="per_transaction" {{ old('admin_fee_mode') === 'per_transaction' ? 'selected' : '' }}>Per Transaksi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Penagihan</label>
                        <select name="admin_fee_timing" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="on_first_payment" {{ old('admin_fee_timing', 'on_first_payment') === 'on_first_payment' ? 'selected' : '' }}>Saat Pembayaran Pertama</option>
                            <option value="on_registration" {{ old('admin_fee_timing') === 'on_registration' ? 'selected' : '' }}>Saat Registrasi</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Template Kwitansi (.docx)</label>
                <input type="file" name="receipt_template" accept=".docx"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                <p class="text-xs text-gray-500 mt-1">Format: .docx. Digunakan untuk generate kwitansi PDF otomatis.</p>
                @error('receipt_template')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Program (Opsional)</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                @error('image')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                <textarea name="program_description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('program_description') }}</textarea>
                @error('program_description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded" />
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3 justify-end">
            <a href="{{ route('programs.index') }}" class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
