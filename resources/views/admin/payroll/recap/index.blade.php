@extends('layouts.dashboard')

@section('title', 'Rekap Payroll')
@section('header_title', 'Rekap Payroll')

@section('content')
@php
    $months = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $statusLabel = [
        'draft'     => 'Draft',
        'review'    => 'Review',
        'finalized' => 'Final',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Gaji & Bonus</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Rekap Payroll</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Ringkasan finansial payroll per periode. Klik baris untuk melihat detail pegawai.
            </p>
        </div>
    </div>

    @php
        $totalPegawai = 0;
        $grandEarnings = 0;
        $grandDeductions = 0;
        $grandNet = 0;
        $grandPaid = 0;

        foreach ($periods as $p) {
            $totalPegawai += $p->employeePayrolls->count();
            $grandEarnings += $p->employeePayrolls->sum('total_earnings');
            $grandDeductions += $p->employeePayrolls->sum('total_deductions');
            $grandNet += $p->employeePayrolls->sum('net_amount');
            $grandPaid += $p->employeePayrolls->sum(fn($ep) => $ep->payments->where('payment_status', 'paid')->sum('amount'));
        }
    @endphp

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Periode</p>
            <p class="mt-1 font-jakarta text-2xl font-bold" style="color: var(--dash-text-heading);">{{ $periods->count() }}</p>
        </div>
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Gaji Bersih</p>
            <p class="mt-1 font-jakarta text-xl font-bold" style="color: var(--dash-text-heading);">Rp {{ number_format($grandNet, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Dibayarkan</p>
            <p class="mt-1 font-jakarta text-xl font-bold" style="color: var(--dash-status-success-text);">Rp {{ number_format($grandPaid, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Sisa Belum Dibayar</p>
            <p class="mt-1 font-jakarta text-xl font-bold" style="color: var(--dash-status-danger-text);">Rp {{ number_format(max(0, $grandNet - $grandPaid), 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Recap Table Card --}}
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Rekap per periode</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Periode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Pendapatan</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Potongan</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Bersih</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Dibayarkan</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Sisa</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($periods as $period)
                        @php
                            $earnings   = $period->employeePayrolls->sum('total_earnings');
                            $deductions = $period->employeePayrolls->sum('total_deductions');
                            $net        = $period->employeePayrolls->sum('net_amount');
                            $paid       = $period->employeePayrolls->sum(fn($ep) => $ep->payments->where('payment_status', 'paid')->sum('amount'));
                            $remaining  = max(0, $net - $paid);
                        @endphp
                        <tr class="transition-colors"
                            style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-4 font-semibold" style="color: var(--dash-text-heading);">
                                {{ $months[$period->period_month] }} {{ $period->period_year }}
                                @if ($period->pay_date)
                                    <p class="mt-0.5 text-xs font-normal" style="color: var(--dash-text-faint);">Gajian {{ $period->pay_date->format('d M Y') }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($period->status === 'finalized')
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                                        Final
                                    </span>
                                @elseif ($period->status === 'review')
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
                                        Review
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums" style="color: var(--dash-text-body);">{{ $period->employeePayrolls->count() }}</td>
                            <td class="px-5 py-4 text-right tabular-nums" style="color: var(--dash-text-body);">Rp {{ number_format($earnings, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right tabular-nums" style="color: var(--dash-status-danger-text);">Rp {{ number_format($deductions, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-semibold" style="color: var(--dash-text-heading);">Rp {{ number_format($net, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right tabular-nums" style="color: var(--dash-status-success-text);">Rp {{ number_format($paid, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-semibold"
                                style="color: {{ $remaining > 0 ? 'var(--dash-status-danger-text)' : 'var(--dash-text-faint)' }};">
                                {{ $remaining > 0 ? 'Rp ' . number_format($remaining, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('payroll.periods.show', $period) }}"
                                   class="inline-flex min-h-9 items-center gap-1 rounded-lg px-3 text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl" style="color: var(--dash-text-faint);" aria-hidden="true">summarize</span>
                                <p class="mt-2 font-semibold" style="color: var(--dash-text-heading);">Belum ada data payroll.</p>
                                <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Buat periode payroll dan generate data terlebih dahulu.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
