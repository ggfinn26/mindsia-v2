@extends('layouts.dashboard')

@section('title', 'Edit Sosialisasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('socializations.show', $socialization) }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Edit Sosialisasi</h1>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Ubah informasi lokasi atau catatan sosialisasi.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #fecaca; background-color: #fef2f2; color: #991b1b;">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('socializations.update', $socialization) }}" method="POST" class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-2xl p-6 space-y-6">
        @csrf
        @method('PATCH')

        <div>
            <label for="location_name" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Nama Lokasi</label>
            <input type="text" name="location_name" id="location_name"
                value="{{ old('location_name', $socialization->location_name) }}"
                class="block w-full border-[var(--dash-input-border)] rounded-lg shadow-sm text-sm focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('location_name') border-red-300 @enderror">
            @error('location_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="border-t border-[var(--dash-card-border)] pt-5">
            <h3 class="text-sm font-semibold text-[var(--dash-text-body)] mb-3">Fee Partner</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="partner_fee_amount" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Jumlah Fee (Rp)</label>
                    <input type="number" name="partner_fee_amount" id="partner_fee_amount"
                        value="{{ old('partner_fee_amount', $socialization->partner_fee_amount) }}"
                        min="0" step="1000" placeholder="0"
                        class="block w-full bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)] focus:outline-none">
                </div>
                <div>
                    <label for="partner_fee_due_date" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Tenggat Fee</label>
                    <input type="date" name="partner_fee_due_date" id="partner_fee_due_date"
                        value="{{ old('partner_fee_due_date', $socialization->partner_fee_due_date?->format('Y-m-d')) }}"
                        class="block w-full bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)] focus:outline-none">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('socializations.show', $socialization) }}" class="px-4 py-2 text-sm font-medium text-[var(--dash-text-body)] bg-[var(--dash-card-bg)] border border-[var(--dash-input-border)] rounded-lg hover:bg-[var(--dash-page-bg)] inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">close</span>
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[var(--dash-primary)] rounded-lg hover:opacity-90 inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
