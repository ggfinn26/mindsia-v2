@extends('layouts.dashboard')

@section('title', 'Tambah Inventaris - MINDSIA')
@section('header_title', 'Tambah Inventaris')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('facility.inventory.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Inventaris Cabang
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Fasilitas</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Tambah Inventaris</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Catat item inventaris baru, aset fisik, atau perlengkapan operasional cabang.
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
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('facility.inventory.store') }}" method="POST">
            @csrf

            {{-- Section 1: Informasi Item --}}
            <div class="px-6 py-4 flex items-center gap-2 rounded-t-2xl"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">inventory_2</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Barang</h2>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Kode Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="item_code" value="{{ old('item_code') }}" required maxlength="50"
                               placeholder="Contoh: INV-JKT-001"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all font-mono"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('item_code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Nama Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="item_name" value="{{ old('item_name') }}" required maxlength="200"
                               placeholder="Contoh: Meja Belajar Kayu"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('item_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Kategori
                        </label>
                        <input type="text" name="category" value="{{ old('category') }}" maxlength="100"
                               placeholder="Contoh: Elektronik, Mebel, Alat Tulis"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('category')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Tipe Inventaris <span class="text-red-500">*</span>
                        </label>
                        <select name="inventory_type" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Tipe Inventaris</option>
                            <option value="FIXED_ASSET" {{ old('inventory_type') === 'FIXED_ASSET' ? 'selected' : '' }}>Aset Tetap</option>
                            <option value="SUPPLIES" {{ old('inventory_type') === 'SUPPLIES' ? 'selected' : '' }}>Perlengkapan</option>
                            <option value="OTHER" {{ old('inventory_type') === 'OTHER' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('inventory_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Cabang <span class="text-red-500">*</span>
                        </label>
                        <select name="branch_id" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Jumlah <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="quantity" value="{{ old('quantity', 1) }}" required min="1"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('quantity')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Satuan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="unit" value="{{ old('unit', 'pcs') }}" required maxlength="50"
                               placeholder="Contoh: unit, pcs, set"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('unit')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Kondisi Awal <span class="text-red-500">*</span>
                        </label>
                        <select name="condition_status" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="GOOD" {{ old('condition_status', 'GOOD') === 'GOOD' ? 'selected' : '' }}>Baik</option>
                            <option value="NEEDS_REPAIR" {{ old('condition_status') === 'NEEDS_REPAIR' ? 'selected' : '' }}>Perlu Perbaikan</option>
                            <option value="DAMAGED" {{ old('condition_status') === 'DAMAGED' ? 'selected' : '' }}>Rusak</option>
                            <option value="UNUSABLE" {{ old('condition_status') === 'UNUSABLE' ? 'selected' : '' }}>Tidak Dapat Digunakan</option>
                        </select>
                        @error('condition_status')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Lokasi Penyimpanan
                        </label>
                        <input type="text" name="location" value="{{ old('location') }}" maxlength="150"
                               placeholder="Contoh: Ruang Kelas A, Lantai 2"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('location')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2: Pembelian --}}
            <div class="px-6 py-4 flex items-center gap-2"
                 style="border-top: 1px solid var(--dash-card-border); border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">payments</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Pembelian</h2>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Tanggal Pembelian
                        </label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date') }}"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('purchase_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Harga Beli (Rp)
                        </label>
                        <input type="number" name="purchase_price" value="{{ old('purchase_price') }}" min="0" step="0.01"
                               placeholder="0"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('purchase_price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Tanggal Input
                        </label>
                        <input type="date" name="input_date" value="{{ old('input_date', now()->format('Y-m-d')) }}"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('input_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 3: Catatan --}}
            <div class="px-6 py-4 flex items-center gap-2"
                 style="border-top: 1px solid var(--dash-card-border); border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">notes</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Catatan Tambahan</h2>
            </div>

            <div class="p-6">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Catatan
                    </label>
                    <textarea name="notes" rows="3"
                              placeholder="Keterangan spesifikasi, nomor seri, atau catatan operasional lainnya..."
                              class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                              onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                              onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 rounded-b-2xl flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('facility.inventory.index') }}"
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
                    Simpan Inventaris
                </button>
            </div>
        </form>
    </div>
</div>
@endsection