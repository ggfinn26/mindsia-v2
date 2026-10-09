@extends('layouts.dashboard')

@section('title', 'Slip Gaji Saya - MINDSIA')
@section('header_title', 'Slip Gaji Saya')

@section('content')
<h1 class="sr-only">Slip Gaji Saya</h1>
<div class="max-w-4xl mx-auto space-y-5 pt-8 sm:pt-16">

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        <div class="px-6 sm:px-8 py-4 border-b flex items-center gap-2.5"
             style="border-color: var(--dash-card-border);">
            <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
            <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Riwayat Slip Gaji</h3>
            <span class="text-xs ml-1" style="color: var(--dash-text-muted);">Periode payroll yang telah difinalisasi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-6 sm:px-8 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Periode</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Gaji Bersih</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payrolls as $payroll)
                        @php
                            $periodLabel = \Carbon\Carbon::create(
                                $payroll->period->period_year,
                                $payroll->period->period_month,
                                1
                            )->translatedFormat('F Y');
                            $slipExists = $payroll->slip && $payroll->slip->telegram_file_id;
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);">
                            <td class="px-6 sm:px-8 py-4">
                                <div class="font-semibold" style="color: var(--dash-text-heading);">{{ $periodLabel }}</div>
                                @if ($payroll->period->pay_date)
                                    <div class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                        Tgl Bayar: {{ $payroll->period->pay_date->translatedFormat('d M Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right font-semibold tabular-nums" style="color: var(--dash-text-heading);">
                                Rp {{ number_format((float) $payroll->net_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($slipExists)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold"
                                          style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
                                        <span class="material-symbols-outlined text-[12px]">check_circle</span>
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold"
                                          style="background-color: var(--dash-primary-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">
                                        Belum tersedia
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if ($slipExists)
                                    <a href="{{ route('employee.payslips.download', $payroll) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                        <span class="material-symbols-outlined text-[15px]">download</span>
                                        Unduh
                                    </a>
                                @else
                                    <span class="text-xs" style="color: var(--dash-text-faint);">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-24 text-center">
                                <span class="material-symbols-outlined text-4xl" style="color: var(--dash-text-faint);">receipt_long</span>
                                <p class="mt-2 font-semibold text-sm" style="color: var(--dash-text-heading);">Belum ada slip gaji</p>
                                <p class="mt-1 text-xs" style="color: var(--dash-text-muted);">Slip gaji muncul setelah periode payroll difinalisasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
