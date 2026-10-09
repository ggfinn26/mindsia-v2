@extends('layouts.dashboard')

@section('title', 'Detail Payroll Pegawai - MINDSIA')
@section('header_title', 'Detail Payroll')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <a href="{{ route('payroll.periods.show', $period) }}"
           class="inline-flex items-center gap-1 text-xs font-medium mb-2"
           style="color: var(--dash-text-muted);"
           onmouseover="this.style.color='var(--dash-text-body)'"
           onmouseout="this.style.color='var(--dash-text-muted)'">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            {{ $months[$period->period_month] }} {{ $period->period_year }}
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">
            {{ $payroll->employee_name_snapshot }}
        </h1>
        <p class="mt-0.5 font-mono text-sm" style="color: var(--dash-text-muted);">
            {{ $payroll->employee_code_snapshot }} · {{ $payroll->position_name_snapshot }}
        </p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Summary cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-2xl px-5 py-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Pendapatan</p>
            <p class="mt-2 font-jakarta text-xl font-bold" style="color: var(--dash-text-heading);">
                Rp {{ number_format($payroll->total_earnings, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-2xl px-5 py-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Potongan</p>
            <p class="mt-2 font-jakarta text-xl font-bold" style="color: #dc2626;">
                Rp {{ number_format($payroll->total_deductions, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-2xl px-5 py-4"
             style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: #059669;">Gaji Bersih</p>
            <p class="mt-2 font-jakarta text-xl font-bold" style="color: #065f46;">
                Rp {{ number_format($payroll->net_amount, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Rekap Kehadiran --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-3" style="border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Rekap Kehadiran</h3>
        </div>
        <div class="grid grid-cols-4 sm:grid-cols-8 text-center text-sm">
            @foreach([
                ['Hadir',     $payroll->days_present],
                ['Absen',     $payroll->days_absent],
                ['Sakit',     $payroll->days_sick],
                ['Izin',      $payroll->days_permission],
                ['Cuti',      $payroll->days_leave],
                ['Libur',     $payroll->days_holiday],
                ['Terlambat', $payroll->days_late],
                ['Sesi',      $payroll->total_sessions],
            ] as [$label, $val])
                <div class="py-4" style="border-right: 1px solid var(--dash-card-border);">
                    <p class="font-bold" style="color: var(--dash-text-heading);">{{ $val ?? 0 }}</p>
                    <p class="mt-0.5 text-xs" style="color: var(--dash-text-muted);">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Rincian Komponen --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-3" style="border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Rincian Komponen</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[32%]">
                    <col class="w-[16%]">
                    <col class="w-[20%]">
                    <col class="w-[12%]">
                    <col class="w-[20%]">
                </colgroup>
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Komponen</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Tipe</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Nilai Satuan</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Qty</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payroll->items as $item)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);
                                   {{ $item->component_type_snapshot === 'deduction' ? 'background-color: #fff5f5;' : '' }}"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='{{ $item->component_type_snapshot === 'deduction' ? '#fff5f5' : '' }}'">
                            <td class="px-5 py-3 font-medium truncate" style="color: var(--dash-text-heading);">
                                {{ $item->component_name_snapshot }}
                            </td>
                            <td class="px-5 py-3">
                                @if($item->component_type_snapshot === 'earning')
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                          style="background-color: #f0fdf4; color: #059669;">Pendapatan</span>
                                @else
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                          style="background-color: #fee2e2; color: #dc2626;">Potongan</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right" style="color: var(--dash-text-body);">
                                Rp {{ number_format($item->unit_value, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-right" style="color: var(--dash-text-body);">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-5 py-3 text-right font-semibold" style="color: var(--dash-text-heading);">
                                Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-sm" style="color: var(--dash-text-faint);">
                                Tidak ada komponen.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Slip & Pembayaran --}}
    @if($period->isFinalized())
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl p-5"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-sm font-bold mb-3" style="color: var(--dash-text-heading);">Slip Gaji</h3>
            @if($payroll->slip)
                <a href="{{ route('payroll.payrolls.slip.download', [$period, $payroll]) }}"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-colors border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    Download Slip
                </a>
            @else
                @can('payroll.period.pay')
                    <form method="POST" action="{{ route('payroll.payrolls.slip.generate', [$period, $payroll]) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">receipt</span>
                            Generate Slip
                        </button>
                    </form>
                @endcan
            @endif
        </div>

        @can('payroll.period.pay')
        <div class="rounded-2xl p-5"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-sm font-bold mb-3" style="color: var(--dash-text-heading);">Proses Pembayaran</h3>
            <form method="POST" action="{{ route('payroll.payrolls.payment.store', [$period, $payroll]) }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">Metode</label>
                        <select name="payment_method" required
                                class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                            <option value="bank_transfer">Transfer Bank</option>
                            <option value="cash">Tunai</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">Jumlah</label>
                        <input type="number" name="amount" min="0.01" step="0.01" placeholder="0"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">Referensi</label>
                    <input type="text" name="payment_reference" placeholder="No. transaksi / keterangan"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                </div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: #059669;">
                    <span class="material-symbols-outlined text-[14px]">payments</span>
                    Catat Pembayaran
                </button>
            </form>
        </div>
        @endcan
    </div>
    @endif

    {{-- Adjustment --}}
    @if($period->isReview() || $period->isFinalized())
    @can('payroll.item.adjust')
    <div class="rounded-2xl p-5"
         style="background-color: #fffbeb; border: 1px solid #fcd34d;">
        <h3 class="font-jakarta text-sm font-bold mb-3" style="color: #92400e;">Adjustment Manual</h3>
        <form method="POST" action="{{ route('payroll.payrolls.adjust', [$period, $payroll]) }}" class="space-y-3">
            @csrf
            @if($errors->any())
                <div class="text-xs" style="color: #dc2626;">
                    <ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="grid gap-3 sm:grid-cols-3">
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: #92400e;">Tipe</label>
                    <select name="adjustment_type" required
                            class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                            style="background-color: var(--dash-card-bg); border: 1px solid #fcd34d; color: var(--dash-text-body);">
                        <option value="earning">Pendapatan</option>
                        <option value="deduction">Potongan</option>
                        <option value="correction">Koreksi langsung</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: #92400e;">Nilai Baru</label>
                    <input type="number" name="new_amount" min="0" step="0.01" required
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid #fcd34d; color: var(--dash-text-body);">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: #92400e;">Item (opsional)</label>
                    <select name="payroll_item_id"
                            class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                            style="background-color: var(--dash-card-bg); border: 1px solid #fcd34d; color: var(--dash-text-body);">
                        <option value="">Koreksi net amount</option>
                        @foreach($payroll->items as $item)
                            <option value="{{ $item->id }}">{{ $item->component_name_snapshot }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1" style="color: #92400e;">Alasan (min. 5 karakter)</label>
                <input type="text" name="adjustment_reason" required minlength="5"
                       placeholder="Jelaskan alasan adjustment"
                       class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid #fcd34d; color: var(--dash-text-body);">
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: #d97706;">
                <span class="material-symbols-outlined text-[14px]">save</span>
                Simpan Adjustment
            </button>
        </form>
    </div>
    @endcan
    @endif

    {{-- Riwayat Pembayaran --}}
    @if($payroll->payments->isNotEmpty())
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-3" style="border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Riwayat Pembayaran</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[22%]">
                    <col class="w-[14%]">
                    <col class="w-[20%]">
                    <col class="w-[30%]">
                    <col class="w-[14%]">
                </colgroup>
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Metode</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Jumlah</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Referensi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payroll->payments as $payment)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-3 text-sm" style="color: var(--dash-text-body);">
                                {{ $payment->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-3 text-sm" style="color: var(--dash-text-body);">
                                {{ $payment->payment_method === 'bank_transfer' ? 'Transfer' : 'Tunai' }}
                            </td>
                            <td class="px-5 py-3 text-right font-semibold" style="color: var(--dash-text-heading);">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 truncate" style="color: var(--dash-text-muted);">
                                {{ $payment->payment_reference ?? '—' }}
                            </td>
                            <td class="px-5 py-3">
                                @if($payment->payment_status === 'paid')
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                          style="background-color: #f0fdf4; color: #059669;">Paid</span>
                                @else
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                          style="background-color: #fee2e2; color: #dc2626;">
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
