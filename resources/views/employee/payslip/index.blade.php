@extends('layouts.dashboard')

@section('title', 'Slip Gaji Saya - MINDSIA')
@section('header_title', 'Slip Gaji Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Personal Dashboard</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Slip Gaji Saya</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Riwayat slip gaji dari seluruh periode payroll yang telah difinalisasi.
        </p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- Tabel --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Periode</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Gaji Bersih</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide hidden sm:table-cell"
                        style="color: var(--dash-text-muted);">Status Slip</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrolls as $payroll)
                @php
                    $periodLabel = \Carbon\Carbon::create(
                        $payroll->period->period_year,
                        $payroll->period->period_month,
                        1
                    )->translatedFormat('F Y');
                    $slipExists = $payroll->slip && $payroll->slip->telegram_file_id;
                @endphp
                <tr style="border-bottom: 1px solid var(--dash-card-border);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <td class="px-5 py-3.5">
                        <div class="font-semibold" style="color: var(--dash-text-heading);">{{ $periodLabel }}</div>
                        @if($payroll->period->pay_date)
                        <div class="text-xs mt-0.5" style="color: var(--dash-text-faint);">
                            Tgl Bayar: {{ $payroll->period->pay_date->translatedFormat('d M Y') }}
                        </div>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right tabular-nums font-semibold"
                        style="color: var(--dash-text-heading);">
                        Rp {{ number_format((float) $payroll->net_amount, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3.5 hidden sm:table-cell">
                        @if($slipExists)
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Tersedia
                        </span>
                        @else
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-faint); border: 1px solid var(--dash-card-border);">
                            Belum tersedia
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        @if($slipExists)
                        <a href="{{ route('employee.payslips.download', $payroll) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            <span class="material-symbols-outlined text-[13px]">download</span>Unduh
                        </a>
                        @else
                        <span style="color: var(--dash-text-faint);">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-12 text-center">
                        <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
                              style="color: var(--dash-text-faint);">receipt_long</span>
                        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada slip gaji.</p>
                        <p class="text-xs mt-1" style="color: var(--dash-text-faint);">
                            Slip gaji muncul setelah periode payroll difinalisasi.
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
