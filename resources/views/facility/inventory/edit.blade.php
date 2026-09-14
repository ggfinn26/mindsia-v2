@extends('layouts.dashboard')

@section('title', 'Edit Inventaris')
@section('header_title', 'Inventaris Cabang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('facility.inventory.show', $inventoryItem) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $inventoryItem->item_name }}
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Edit Inventaris</h2>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('facility.inventory.update', $inventoryItem) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="border border-slate-200 bg-white p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Informasi Item</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Kode Item</label>
                    <input type="text" name="item_code" required maxlength="50" value="{{ old('item_code', $inventoryItem->item_code) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Nama Item</label>
                    <input type="text" name="item_name" required maxlength="255" value="{{ old('item_name', $inventoryItem->item_name) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Kategori</label>
                    <input type="text" name="category" maxlength="100" value="{{ old('category', $inventoryItem->category) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Tipe Inventaris</label>
                    <input type="text" name="inventory_type" maxlength="100" value="{{ old('inventory_type', $inventoryItem->inventory_type) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Cabang</label>
                    <select name="branch_id" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        @foreach (\App\Models\Branch::where('is_active', true)->get() as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $inventoryItem->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Satuan</label>
                    <input type="text" name="unit" maxlength="30" value="{{ old('unit', $inventoryItem->unit) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Kondisi</label>
                    <select name="condition_status" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        <option value="baik" {{ old('condition_status', $inventoryItem->condition_status) === 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="perbaikan" {{ old('condition_status', $inventoryItem->condition_status) === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="rusak" {{ old('condition_status', $inventoryItem->condition_status) === 'rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Lokasi Penyimpanan</label>
                <input type="text" name="location" maxlength="255" value="{{ old('location', $inventoryItem->location) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
        </div>

        <div class="border border-slate-200 bg-white p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Pembelian</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', $inventoryItem->purchase_date?->format('Y-m-d')) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Harga Pembelian (Rp)</label>
                    <input type="number" name="purchase_price" min="0" step="0.01" value="{{ old('purchase_price', $inventoryItem->purchase_price) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Tanggal Input</label>
                    <input type="date" name="input_date" value="{{ old('input_date', $inventoryItem->input_date?->format('Y-m-d')) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </div>
        </div>

        <div class="border border-slate-200 bg-white p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Catatan</h3>
            <textarea name="notes" rows="3" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">{{ old('notes', $inventoryItem->notes) }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('facility.inventory.show', $inventoryItem) }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex min-h-10 items-center bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
        </div>
    </form>
</div>
@endsection