@extends('layouts.dashboard')

@section('title', 'Buat Kontrak Sewa')
@section('header_title', 'Kontrak Sewa Cabang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('facility.rent-contracts.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kontrak Sewa Cabang
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Buat Kontrak Sewa</h1>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('facility.rent-contracts.store') }}" class="space-y-5">
        @csrf
        <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Informasi Kontrak</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Nama Pemilik</label>
                    <input type="text" name="owner_name" required maxlength="255" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Telepon Pemilik</label>
                    <div class="flex rounded-md shadow-sm">
                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-[color:var(--dash-input-border)] bg-[var(--dash-nav-hover-bg)] px-3 text-sm font-bold text-[color:var(--dash-text-muted)]">+62</span>
                        <input type="text" name="owner_phone" placeholder="8xxxxxxxxxx"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                            class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]" minlength="8" maxlength="15">
                    </div>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Cabang</label>
                <select name="branch_id" required class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    <option value="">— Pilih cabang —</option>
                    @foreach (\App\Models\Branch::where('is_active', true)->get() as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Tanggal Mulai</label>
                    <input type="date" name="start_date" required class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Tanggal Selesai</label>
                    <input type="date" name="end_date" required class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
            </div>
        </div>

        <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Pembayaran</h3>

            <div class="grid grid-cols-3 gap-4">
                <div x-data="{
                        rawValue: '{{ old('rent_amount', '') }}',
                        format(val) {
                            let num = val.toString().replace(/[^0-9]/g, '');
                            return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                        }
                    }">
                    <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Total Sewa</label>
                    <div class="flex rounded-md shadow-sm">
                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-[color:var(--dash-input-border)] bg-[var(--dash-nav-hover-bg)] px-3 text-sm font-bold text-[color:var(--dash-text-muted)]">Rp</span>
                        <input type="text" :value="format(rawValue)" @input="rawValue = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(rawValue)" required class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                        <input type="hidden" name="rent_amount" x-model="rawValue">
                    </div>
                </div>
                <div x-data="{
                        rawValue: '{{ old('down_payment', '0') }}',
                        format(val) {
                            let num = val.toString().replace(/[^0-9]/g, '');
                            return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                        }
                    }">
                    <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Down Payment</label>
                    <div class="flex rounded-md shadow-sm">
                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-[color:var(--dash-input-border)] bg-[var(--dash-nav-hover-bg)] px-3 text-sm font-bold text-[color:var(--dash-text-muted)]">Rp</span>
                        <input type="text" :value="format(rawValue)" @input="rawValue = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(rawValue)" required class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                        <input type="hidden" name="down_payment" x-model="rawValue">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Jumlah Termin</label>
                    <input type="number" name="termin_count" required min="1" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]" value="12">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Periode Sewa</label>
                <select name="rent_period" required class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    <option value="">Pilih Periode</option>
                    <option value="monthly" {{ old('rent_period') === 'monthly' ? 'selected' : '' }}>Bulanan (Monthly)</option>
                    <option value="yearly" {{ old('rent_period') === 'yearly' ? 'selected' : '' }}>Tahunan (Yearly)</option>
                    <option value="one_time" {{ old('rent_period') === 'one_time' ? 'selected' : '' }}>Sekali Bayar (One Time)</option>
                </select>
            </div>
        </div>

        {{-- Live preview jadwal cicilan --}}
        <div id="termin-preview" class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 space-y-3 hidden">
            <div class="flex items-center justify-between">
                <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Preview Jadwal Cicilan</h3>
                <div class="text-xs text-[color:var(--dash-text-muted)]">
                    Sisa setelah DP: <span id="preview-sisa" class="font-semibold text-[color:var(--dash-text-heading)]"></span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                    <thead class="bg-[var(--dash-nav-hover-bg)] text-left text-xs font-semibold text-[color:var(--dash-text-muted)]">
                        <tr>
                            <th class="px-4 py-2">Termin</th>
                            <th class="px-4 py-2">Jatuh Tempo</th>
                            <th class="px-4 py-2">Jumlah Cicilan</th>
                        </tr>
                    </thead>
                    <tbody id="preview-rows" class="divide-y divide-[color:var(--dash-card-border)] text-sm text-[color:var(--dash-text-body)]"></tbody>
                </table>
            </div>
        </div>

        <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Catatan</h3>
            <textarea name="notes" rows="3" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]" placeholder="Catatan tambahan (opsional)"></textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('facility.rent-contracts.index') }}" class="inline-flex min-h-10 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Batal</a>
            <button type="submit" class="inline-flex min-h-10 items-center bg-[var(--dash-primary,#215aac)] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
        </div>
    </form>
</div>
@endsection