@extends('layouts.dashboard')

@section('title', 'Detail Payroll Pegawai - MINDSIA')
@section('header_title', 'Kelola Periode Payroll')

@section('content')
@php
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('payroll.periods.show', $period) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Kelola Periode Payroll
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Gaji & Bonus</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">
            {{ $payroll->employee_name_snapshot }}
        </h1>
        <p class="text-sm mt-1 font-mono" style="color: var(--dash-text-muted);">
            {{ $payroll->employee_code_snapshot }} - {{ $payroll->position_name_snapshot }}
            @if($payroll->branch_name_snapshot)
                - {{ $payroll->branch_name_snapshot }}
            @endif
        </p>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl border p-5 shadow-sm"
             style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider"
                      style="color: var(--dash-text-muted);">Total Pendapatan</span>
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">add_circle</span>
            </div>
            <p class="font-jakarta text-2xl font-bold mt-2" style="color: var(--dash-text-heading);">
                Rp {{ number_format($payroll->total_earnings, 0, ',', '.') }}
            </p>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Gaji pokok & tunjangan</p>
        </div>

        <div class="rounded-2xl border p-5 shadow-sm"
             style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider"
                      style="color: var(--dash-text-muted);">Total Potongan</span>
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-danger);">remove_circle</span>
            </div>
            <p class="font-jakarta text-2xl font-bold mt-2" style="color: var(--dash-danger);">
                Rp {{ number_format($payroll->total_deductions, 0, ',', '.') }}
            </p>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Potongan & penalti</p>
        </div>

        <div class="rounded-2xl border p-5 shadow-sm"
             style="background-color: var(--dash-card-bg); border-color: color-mix(in srgb, #059669 30%, transparent);">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider"
                      style="color: #059669;">Gaji Bersih (Take Home)</span>
                <span class="material-symbols-outlined text-[20px]" style="color: #059669;">account_balance_wallet</span>
            </div>
            <p class="font-jakarta text-2xl font-bold mt-2" style="color: #059669;">
                Rp {{ number_format($payroll->net_amount, 0, ',', '.') }}
            </p>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Total diterima pegawai</p>
        </div>
    </div>

    {{-- Rekap Kehadiran Card --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center gap-2"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">date_range</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Rekap Kehadiran</h2>
        </div>
        <div class="grid grid-cols-4 sm:grid-cols-8 divide-x divide-y sm:divide-y-0 text-center text-sm"
             style="border-color: var(--dash-card-border);">
            @foreach ([
                ['Hadir', $payroll->days_present],
                ['Absen', $payroll->days_absent],
                ['Sakit', $payroll->days_sick],
                ['Izin', $payroll->days_permission],
                ['Cuti', $payroll->days_leave],
                ['Libur', $payroll->days_holiday],
                ['Terlambat', $payroll->days_late],
                ['Sesi', $payroll->total_sessions],
            ] as [$label, $val])
                <div class="py-4 px-2">
                    <p class="font-bold text-base" style="color: var(--dash-text-heading);">{{ $val ?? 0 }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Rincian Komponen Gaji Card --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">list_alt</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Rincian Komponen Gaji</h2>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                {{ $payroll->items->count() }} Komponen
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Komponen</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Tipe</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Nilai Satuan</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Kuantitas (Qty)</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse ($payroll->items as $item)
                        <tr class="transition-colors"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-6 py-4 font-semibold" style="color: var(--dash-text-heading);">
                                {{ $item->component_name_snapshot }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->component_type_snapshot === 'earning')
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                                        <span class="material-symbols-outlined text-[13px]">add</span>
                                        Pendapatan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); color: var(--dash-danger);">
                                        <span class="material-symbols-outlined text-[13px]">remove</span>
                                        Potongan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-medium" style="color: var(--dash-text-body);">
                                Rp {{ number_format($item->unit_value, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium" style="color: var(--dash-text-body);">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold"
                                style="color: {{ $item->component_type_snapshot === 'deduction' ? 'var(--dash-danger)' : 'var(--dash-text-heading)' }};">
                                {{ $item->component_type_snapshot === 'deduction' ? '-' : '' }}Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-xs" style="color: var(--dash-text-muted);">
                                Tidak ada komponen gaji pada payroll ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Slip & Pembayaran --}}
    @if ($period->isFinalized())
        <div class="grid gap-6 sm:grid-cols-2">
            {{-- Slip Gaji --}}
            <div class="rounded-2xl border p-6 shadow-sm"
                 style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">receipt_long</span>
                    <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Slip Gaji Pegawai</h3>
                </div>
                @if ($payroll->slip)
                    <p class="text-xs mb-4" style="color: var(--dash-text-muted);">
                        Slip gaji telah digenerate. Klik tombol di bawah untuk mengunduh slip dalam format PDF.
                    </p>
                    <a href="{{ route('payroll.payrolls.slip.download', [$period, $payroll]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold transition-colors"
                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">download</span>
                        Download Slip Gaji
                    </a>
                @else
                    <p class="text-xs mb-4" style="color: var(--dash-text-muted);">
                        Slip gaji belum diterbitkan untuk pegawai ini.
                    </p>
                    @can('payroll.period.pay')
                        <form method="POST" action="{{ route('payroll.payrolls.slip.generate', [$period, $payroll]) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[16px]">receipt</span>
                                Generate Slip Gaji
                            </button>
                        </form>
                    @endcan
                @endif
            </div>

            {{-- Proses Pembayaran --}}
            @can('payroll.period.pay')
            <div class="rounded-2xl border p-6 shadow-sm"
                 style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-[20px]" style="color: #059669;">payment</span>
                    <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Proses Pembayaran Gaji</h3>
                </div>
                <form method="POST" action="{{ route('payroll.payrolls.payment.store', [$period, $payroll]) }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                                   style="color: var(--dash-text-body);">
                                Metode Pembayaran <span style="color: var(--dash-danger);">*</span>
                            </label>
                            <select name="payment_method" required
                                    class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                <option value="" disabled>Pilih Metode</option>
                                <option value="bank_transfer" selected>Transfer Bank</option>
                                <option value="cash">Tunai</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                                   style="color: var(--dash-text-body);">
                                Jumlah (Rp) <span style="color: var(--dash-danger);">*</span>
                            </label>
                            <input type="number" name="amount" min="0.01" step="0.01"
                                   value="{{ old('amount', $payroll->net_amount) }}"
                                   placeholder="0" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                               style="color: var(--dash-text-body);">
                            Nomor Referensi / Catatan
                        </label>
                        <input type="text" name="payment_reference" placeholder="No. transaksi transfer atau keterangan"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg text-xs font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                            style="background-color: #059669;">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        Catat Pembayaran
                    </button>
                </form>
            </div>
            @endcan
        </div>
    @endif

    {{-- Manual Adjustment Form --}}
    @if ($period->isReview() || $period->isFinalized())
        @can('payroll.item.adjust')
        <div class="rounded-2xl border p-6 shadow-sm"
             style="background-color: var(--dash-card-bg); border-color: color-mix(in srgb, var(--dash-status-warning-text) 30%, transparent);">
            <div class="flex items-center gap-2 mb-3">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-status-warning-text);">tune</span>
                <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">
                    Adjustment Manual (Koreksi Komponen)
                </h3>
            </div>
            <p class="text-xs mb-4" style="color: var(--dash-text-muted);">
                Lakukan penyesuaian nilai komponen gaji atau koreksi langsung total gaji bersih pegawai beserta alasan wajib.
            </p>

            <form method="POST" action="{{ route('payroll.payrolls.adjust', [$period, $payroll]) }}" class="space-y-4">
                @csrf
                @if ($errors->any())
                    <div class="p-3 rounded-xl text-xs font-medium"
                         style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                               style="color: var(--dash-text-body);">
                            Tipe Adjustment <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="adjustment_type" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="" disabled>Pilih Tipe</option>
                            <option value="earning">Pendapatan (Earning)</option>
                            <option value="deduction">Potongan (Deduction)</option>
                            <option value="correction">Koreksi Langsung Net Amount</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                               style="color: var(--dash-text-body);">
                            Nilai Baru (Rp) <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <input type="number" name="new_amount" min="0" step="0.01" required
                               placeholder="0"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                               style="color: var(--dash-text-body);">
                            Item Komponen (Opsional)
                        </label>
                        <select name="payroll_item_id"
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Komponen (atau Koreksi Net Amount)</option>
                            @foreach ($payroll->items as $item)
                                <option value="{{ $item->id }}">{{ $item->component_name_snapshot }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                           style="color: var(--dash-text-body);">
                        Alasan Penyesuaian (Min. 5 Karakter) <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="text" name="adjustment_reason" required minlength="5"
                           placeholder="Jelaskan alasan penyesuaian atau nomor surat persetujuan..."
                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-xs font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-status-warning-text);">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        Simpan Adjustment
                    </button>
                </div>
            </form>
        </div>
        @endcan
    @endif

    {{-- Riwayat Pembayaran Table Card --}}
    @if ($payroll->payments->isNotEmpty())
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
            <div class="px-6 py-4 flex items-center justify-between"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]" style="color: #059669;">history</span>
                    <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Riwayat Pembayaran</h2>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                      style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                    {{ $payroll->payments->count() }} Transaksi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Tanggal</th>
                            <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Metode</th>
                            <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                                style="color: var(--dash-text-muted);">Jumlah</th>
                            <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Referensi</th>
                            <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                        @foreach ($payroll->payments as $payment)
                            <tr class="transition-colors"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-6 py-4" style="color: var(--dash-text-body);">
                                    {{ $payment->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4" style="color: var(--dash-text-body);">
                                    {{ $payment->payment_method === 'bank_transfer' ? 'Transfer Bank' : 'Tunai' }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold" style="color: var(--dash-text-heading);">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs" style="color: var(--dash-text-muted);">
                                    {{ $payment->payment_reference ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($payment->payment_status === 'paid')
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                              style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                                            <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                            Paid
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                              style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); color: var(--dash-danger);">
                                            <span class="material-symbols-outlined text-[13px]">cancel</span>
                                            {{ ucfirst($payment->payment_status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
