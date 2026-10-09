@extends('layouts.dashboard')

@section('title', 'Tambah Inventaris')
@section('header_title', 'Inventaris Cabang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('facility.inventory.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Inventaris Cabang
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Tambah Inventaris</h1>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('facility.inventory.store') }}" class="space-y-5">
        @csrf
        <div class="border border-slate-200 bg-white p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Informasi Item</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Kode Item</label>
                    <input type="text" name="item_code" required maxlength="50" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]" placeholder="INV-001">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Nama Item</label>
                    <input type="text" name="item_name" required maxlength="200" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Kategori</label>
                    <input type="text" name="category" maxlength="100" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]" placeholder="Elektronik, Mebel, dll">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Tipe Inventaris</label>
                    <select name="inventory_type" required class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                        <option value="">— Pilih tipe —</option>
                        <option value="FIXED_ASSET">Aset Tetap</option>
                        <option value="SUPPLIES">Perlengkapan</option>
                        <option value="OTHER">Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Cabang</label>
                    <select name="branch_id" required class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                        <option value="">— Pilih cabang —</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Jumlah</label>
                    <input type="number" name="quantity" required min="1" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]" value="1">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Satuan</label>
                    <input type="text" name="unit" required maxlength="50" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]" placeholder="unit, pcs, set">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Kondisi</label>
                    <select name="condition_status" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                        <option value="GOOD">Baik</option>
                        <option value="NEEDS_REPAIR">Perlu Perbaikan</option>
                        <option value="DAMAGED">Rusak</option>
                        <option value="UNUSABLE">Tidak Dapat Digunakan</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Lokasi Penyimpanan</label>
                    <input type="text" name="location" maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
            </div>
        </div>

        <div class="border border-slate-200 bg-white p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Pembelian</h3>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Harga Pembelian (Rp)</label>
                    <input type="number" name="purchase_price" min="0" step="0.01" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Tanggal Input</label>
                    <input type="date" name="input_date" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]" value="{{ now()->format('Y-m-d') }}">
                </div>
            </div>

        </div>

        <div class="border border-slate-200 bg-white p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Catatan</h3>
            <textarea name="notes" rows="3" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]" placeholder="Catatan tambahan (opsional)"></textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('facility.inventory.index') }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex min-h-10 items-center bg-[var(--dash-primary,#215aac)] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
        </div>
    </form>
</div>
@endsection