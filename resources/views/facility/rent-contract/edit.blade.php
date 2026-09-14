@extends('layouts.dashboard')

@section('title', 'Edit Kontrak Sewa')
@section('header_title', 'Kontrak Sewa Cabang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('facility.rent-contracts.show', $branchRentContract) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $branchRentContract->owner_name }}
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Edit Kontrak Sewa</h2>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('facility.rent-contracts.update', $branchRentContract) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="border border-slate-200 bg-white p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Informasi Kontrak</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Nama Pemilik</label>
                    <input type="text" name="owner_name" required maxlength="255" value="{{ old('owner_name', $branchRentContract->owner_name) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Telepon Pemilik</label>
                    <input type="text" name="owner_phone" maxlength="30" value="{{ old('owner_phone', $branchRentContract->owner_phone) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Cabang</label>
                    <select name="branch_id" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        @foreach (\App\Models\Branch::where('is_active', true)->get() as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $branchRentContract->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Area</label>
                    <select name="area_id" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        <option value="">— Pilih area —</option>
                        @foreach (\App\Models\Area::with('region')->get() as $area)
                            <option value="{{ $area->id }}" {{ old('area_id', $branchRentContract->area_id) == $area->id ? 'selected' : '' }}>{{ $area->region?->region_name ?? '' }} — {{ $area->area_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Tanggal Mulai</label>
                    <input type="date" name="start_date" required value="{{ old('start_date', $branchRentContract->start_date?->format('Y-m-d')) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Tanggal Selesai</label>
                    <input type="date" name="end_date" required value="{{ old('end_date', $branchRentContract->end_date?->format('Y-m-d')) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </div>
        </div>

        <div class="border border-slate-200 bg-white p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Pembayaran</h3>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Total Sewa (Rp)</label>
                    <input type="number" name="rent_amount" required min="0" step="0.01" value="{{ old('rent_amount', $branchRentContract->rent_amount) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Down Payment (Rp)</label>
                    <input type="number" name="down_payment" required min="0" step="0.01" value="{{ old('down_payment', $branchRentContract->down_payment) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Jumlah Termin</label>
                    <input type="number" name="termin_count" required min="1" value="{{ old('termin_count', $branchRentContract->termin_count) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Periode Sewa</label>
                <input type="text" name="rent_period" maxlength="100" value="{{ old('rent_period', $branchRentContract->rent_period) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Status</label>
                <select name="status" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    <option value="active" {{ old('status', $branchRentContract->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ old('status', $branchRentContract->status) === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="terminated" {{ old('status', $branchRentContract->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>
        </div>

        <div class="border border-slate-200 bg-white p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Catatan</h3>
            <textarea name="notes" rows="3" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">{{ old('notes', $branchRentContract->notes) }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('facility.rent-contracts.show', $branchRentContract) }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex min-h-10 items-center bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
        </div>
    </form>
</div>
@endsection