@extends('layouts.dashboard')

@section('title', 'Rekap Payroll - MINDSIA')
@section('header_title', 'Rekap Payroll')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $totalPegawai   = 0;
    $grandEarnings  = 0;
    $grandDeductions = 0;
    $grandNet       = 0;
    $grandPaid      = 0;
    foreach ($periods as $p) {
        $totalPegawai   += $p->employeePayrolls->count();
        $grandEarnings  += $p->employeePayrolls->sum('total_earnings');
        $grandDeductions += $p->employeePayrolls->sum('total_deductions');
        $grandNet       += $p->employeePayrolls->sum('net_amount');
        $grandPaid      += $p->employeePayrolls->sum(fn($ep) => $ep->payments->where('payment_status','paid')->sum('amount'));
    }
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Payroll</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Rekap Payroll</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            Ringkasan finansial payroll per periode. Klik baris untuk melihat detail pegawai.
        </p>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl px-5 py-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Periode</p>
            <p class="mt-2 font-jakarta text-2xl font-bold" style="color: var(--dash-text-heading);">{{ $periods->count() }}</p>
        </div>
        <div class="rounded-2xl px-5 py-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Gaji Bersih</p>
            <p class="mt-2 font-jakarta text-xl font-bold" style="color: var(--dash-text-heading);">
                Rp {{ number_format($grandNet, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-2xl px-5 py-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Dibayarkan</p>
            <p class="mt-2 font-jakarta text-xl font-bold" style="color: #059669;">
                Rp {{ number_format($grandPaid, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-2xl px-5 py-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Sisa Belum Dibayar</p>
            <p class="mt-2 font-jakarta text-xl font-bold" style="color: #dc2626;">
                Rp {{ number_format(max(0, $grandNet - $grandPaid), 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Rekap per periode</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[16%]">
                    <col class="w-[9%]">
                    <col class="w-[7%]">
                    <col class="w-[14%]">
                    <col class="w-[13%]">
                    <col class="w-[13%]">
                    <col class="w-[13%]">
                    <col class="w-[10%]">
                    <col class="w-[5%]">
                </colgroup>
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Periode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Pgw</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Pendapatan</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Potongan</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Bersih</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Dibayar</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Sisa</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $period)
                        @php
                            $earnings   = $period->employeePayrolls->sum('total_earnings');
                            $deductions = $period->employeePayrolls->sum('total_deductions');
                            $net        = $period->employeePayrolls->sum('net_amount');
                            $paid       = $period->employeePayrolls->sum(fn($ep) => $ep->payments->where('payment_status','paid')->sum('amount'));
                            $remaining  = max(0, $net - $paid);
                            $statusStyle = match($period->status) {
                                'finalized' => 'background-color:#f0fdf4;color:#059669;',
                                'review'    => 'background-color:#fef3c7;color:#d97706;',
                                default     => 'background-color:var(--dash-nav-hover-bg);color:var(--dash-text-muted);',
                            };
                            $statusLabel = match($period->status) {
                                'finalized' => 'Final',
                                'review'    => 'Review',
                                default     => 'Draft',
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">
                                    {{ $months[$period->period_month] }} {{ $period->period_year }}
                                </p>
                                @if($period->pay_date)
                                    <p class="mt-0.5 text-xs" style="color: var(--dash-text-muted);">
                                        Gajian {{ $period->pay_date->format('d M Y') }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                      style="{{ $statusStyle }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums" style="color: var(--dash-text-body);">
                                {{ $period->employeePayrolls->count() }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums text-xs" style="color: var(--dash-text-body);">
                                Rp {{ number_format($earnings, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums text-xs" style="color: #dc2626;">
                                Rp {{ number_format($deductions, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums text-xs font-semibold" style="color: var(--dash-text-heading);">
                                Rp {{ number_format($net, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums text-xs" style="color: #059669;">
                                Rp {{ number_format($paid, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums text-xs {{ $remaining > 0 ? 'font-semibold' : '' }}"
                                style="color: {{ $remaining > 0 ? '#dc2626' : 'var(--dash-text-faint)' }};">
                                {{ $remaining > 0 ? 'Rp '.number_format($remaining, 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-3 py-4 text-right">
                                <a href="{{ route('payroll.periods.show', $period) }}"
                                   title="Detail"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">summarize</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
                                    Belum ada data payroll.
                                </p>
                                <p class="text-sm mt-1" style="color: var(--dash-text-faint);">
                                    Buat periode payroll dan generate data terlebih dahulu.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
