@extends('layouts.dashboard')

@section('title', 'Rekap Payroll')
@section('header_title', 'Rekap Payroll')

@section('content')
@php
    $months = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $statusBadge = [
        'draft'     => 'bg-slate-100 text-slate-700',
        'review'    => 'bg-amber-100 text-amber-800',
        'finalized' => 'bg-emerald-100 text-emerald-800',
    ];
    $statusLabel = [
        'draft'     => 'Draft',
        'review'    => 'Review',
        'finalized' => 'Final',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Payroll</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Rekap Payroll</h2>
        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Ringkasan finansial payroll per periode. Klik baris untuk melihat detail pegawai.</p>
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

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Total Periode</p>
            <p class="mt-1 font-jakarta text-2xl font-bold text-slate-900">{{ $periods->count() }}</p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Total Gaji Bersih</p>
            <p class="mt-1 font-jakarta text-xl font-bold text-slate-900">Rp {{ number_format($grandNet, 0, ',', '.') }}</p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Total Dibayarkan</p>
            <p class="mt-1 font-jakarta text-xl font-bold text-emerald-700">Rp {{ number_format($grandPaid, 0, ',', '.') }}</p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Sisa Belum Dibayar</p>
            <p class="mt-1 font-jakarta text-xl font-bold text-red-700">Rp {{ number_format(max(0, $grandNet - $grandPaid), 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Rekap per periode</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Periode</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Pegawai</th>
                        <th class="px-5 py-3 text-right">Total Pendapatan</th>
                        <th class="px-5 py-3 text-right">Total Potongan</th>
                        <th class="px-5 py-3 text-right">Total Bersih</th>
                        <th class="px-5 py-3 text-right">Dibayarkan</th>
                        <th class="px-5 py-3 text-right">Sisa</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($periods as $period)
                        @php
                            $earnings   = $period->employeePayrolls->sum('total_earnings');
                            $deductions = $period->employeePayrolls->sum('total_deductions');
                            $net        = $period->employeePayrolls->sum('net_amount');
                            $paid       = $period->employeePayrolls->sum(fn($ep) => $ep->payments->where('payment_status', 'paid')->sum('amount'));
                            $remaining  = max(0, $net - $paid);
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-semibold text-slate-900">
                                {{ $months[$period->period_month] }} {{ $period->period_year }}
                                @if ($period->pay_date)
                                    <p class="mt-0.5 text-xs font-normal text-slate-400">Gajian {{ $period->pay_date->format('d M Y') }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$period->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $statusLabel[$period->status] ?? $period->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums text-slate-700">{{ $period->employeePayrolls->count() }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-slate-700">Rp {{ number_format($earnings, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-red-700">Rp {{ number_format($deductions, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-semibold text-slate-900">Rp {{ number_format($net, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-emerald-700">Rp {{ number_format($paid, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right tabular-nums {{ $remaining > 0 ? 'font-semibold text-red-700' : 'text-slate-400' }}">
                                {{ $remaining > 0 ? 'Rp ' . number_format($remaining, 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('payroll.periods.show', $period) }}"
                                   class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300" aria-hidden="true">summarize</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada data payroll.</p>
                                <p class="mt-1 text-sm text-slate-500">Buat periode payroll dan generate data terlebih dahulu.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
