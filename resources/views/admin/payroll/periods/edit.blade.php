@extends('layouts.dashboard')

@section('title', 'Edit Periode Payroll - MINDSIA')
@section('header_title', 'Kelola Periode Payroll')

@section('content')
@php
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('payroll.periods.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Kelola Periode Payroll
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Gaji & Bonus</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">
            Edit Periode: {{ $months[$period->period_month] ?? $period->period_month }} {{ $period->period_year }}
        </h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui tanggal pembayaran gaji dan catatan periode payroll.
        </p>
    </div>

    {{-- Flash messages --}}
    @if ($errors->any())
        <div class="p-4 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <div class="flex items-center gap-2 mb-2 font-semibold">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>Terdapat kesalahan pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="rounded-2xl border shadow-sm"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">edit_calendar</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Periode</h2>
            </div>
            <div>
                @if ($period->status === 'finalized')
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                          style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                        Finalized
                    </span>
                @elseif ($period->status === 'review')
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                          style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
                        <span class="material-symbols-outlined text-[14px]">hourglass_top</span>
                        Review
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                          style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                        <span class="material-symbols-outlined text-[14px]">edit_note</span>
                        Draft
                    </span>
                @endif
            </div>
        </div>

        <form action="{{ route('payroll.periods.update', $period) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">
                {{-- Periode Info (Read-Only) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <div>
                        <span class="block text-xs font-semibold uppercase tracking-wider mb-1"
                              style="color: var(--dash-text-muted);">Bulan & Tahun</span>
                        <span class="text-sm font-bold" style="color: var(--dash-text-heading);">
                            {{ $months[$period->period_month] ?? $period->period_month }} {{ $period->period_year }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold uppercase tracking-wider mb-1"
                              style="color: var(--dash-text-muted);">Jumlah Pegawai</span>
                        <span class="text-sm font-bold" style="color: var(--dash-text-heading);">
                            {{ $period->employeePayrolls->count() }} Pegawai
                        </span>
                    </div>
                </div>

                {{-- Tanggal Gajian --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Tanggal Gajian
                    </label>
                    <input type="date" name="pay_date" value="{{ old('pay_date', $period->pay_date?->format('Y-m-d')) }}"
                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                        Tanggal perkiraan pencairan gaji kepada pegawai.
                    </p>
                    @error('pay_date')
                        <p class="mt-1 text-xs" style="color: var(--dash-danger);">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Catatan
                    </label>
                    <textarea name="notes" rows="3"
                              placeholder="Catatan tambahan mengenai periode payroll ini..."
                              class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                              onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                              onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('notes', $period->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs" style="color: var(--dash-danger);">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 rounded-b-2xl flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('payroll.periods.show', $period) }}"
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
