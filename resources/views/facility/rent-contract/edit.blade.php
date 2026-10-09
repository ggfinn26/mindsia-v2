@extends('layouts.dashboard')

@section('title', 'Edit Kontrak Sewa')
@section('header_title', 'Kontrak Sewa Cabang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('facility.rent-contracts.show', $branchRentContract) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $branchRentContract->owner_name }}
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Kontrak Sewa</h1>
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
                    <input type="text" name="owner_name" required maxlength="255" value="{{ old('owner_name', $branchRentContract->owner_name) }}" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Telepon Pemilik</label>
                    <div class="flex rounded-md shadow-sm">
                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-slate-300 bg-slate-50 px-3 text-sm font-bold text-slate-500">+62</span>
                        <input type="text" name="owner_phone" placeholder="8xxxxxxxxxx"
                            value="{{ old('owner_phone', $branchRentContract- minlength="8" maxlength="15">owner_phone) }}"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                            class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    </div>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Cabang</label>
                <select name="branch_id" required class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    @foreach (\App\Models\Branch::where('is_active', true)->get() as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $branchRentContract->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Tanggal Mulai</label>
                    <input type="date" name="start_date" required value="{{ old('start_date', $branchRentContract->start_date?->format('Y-m-d')) }}" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Tanggal Selesai</label>
                    <input type="date" name="end_date" required value="{{ old('end_date', $branchRentContract->end_date?->format('Y-m-d')) }}" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
            </div>
        </div>

        <div class="border border-slate-200 bg-white p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Pembayaran</h3>

            <div class="grid grid-cols-3 gap-4">
                <div x-data="{
                        rawValue: '{{ old('rent_amount', $branchRentContract->rent_amount) }}',
                        format(val) {
                            let num = val.toString().replace(/[^0-9]/g, '');
                            return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                        }
                    }">
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Total Sewa</label>
                    <div class="flex rounded-md shadow-sm">
                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-slate-300 bg-slate-50 px-3 text-sm font-bold text-slate-500">Rp</span>
                        <input type="text" :value="format(rawValue)" @input="rawValue = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(rawValue)" required class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                        <input type="hidden" name="rent_amount" x-model="rawValue">
                    </div>
                </div>
                <div x-data="{
                        rawValue: '{{ old('down_payment', $branchRentContract->down_payment) }}',
                        format(val) {
                            let num = val.toString().replace(/[^0-9]/g, '');
                            return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                        }
                    }">
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Down Payment</label>
                    <div class="flex rounded-md shadow-sm">
                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-slate-300 bg-slate-50 px-3 text-sm font-bold text-slate-500">Rp</span>
                        <input type="text" :value="format(rawValue)" @input="rawValue = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(rawValue)" required class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                        <input type="hidden" name="down_payment" x-model="rawValue">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Jumlah Termin</label>
                    <input type="number" name="termin_count" required min="1" value="{{ old('termin_count', $branchRentContract->termin_count) }}" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Periode Sewa</label>
                <select name="rent_period" required class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    <option value="">Pilih Periode</option>
                    <option value="monthly" {{ old('rent_period', $branchRentContract->rent_period) === 'monthly' ? 'selected' : '' }}>Bulanan (Monthly)</option>
                    <option value="yearly" {{ old('rent_period', $branchRentContract->rent_period) === 'yearly' ? 'selected' : '' }}>Tahunan (Yearly)</option>
                    <option value="one_time" {{ old('rent_period', $branchRentContract->rent_period) === 'one_time' ? 'selected' : '' }}>Sekali Bayar (One Time)</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Status</label>
                <select name="status" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    <option value="active" {{ old('status', $branchRentContract->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ old('status', $branchRentContract->status) === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="terminated" {{ old('status', $branchRentContract->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>
        </div>

        <div class="border border-slate-200 bg-white p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Catatan</h3>
            <textarea name="notes" rows="3" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">{{ old('notes', $branchRentContract->notes) }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('facility.rent-contracts.show', $branchRentContract) }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex min-h-10 items-center bg-[var(--dash-primary,#215aac)] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
        </div>
    </form>
</div>
@endsection