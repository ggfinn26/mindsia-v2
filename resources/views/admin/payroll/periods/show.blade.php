@extends('layouts.dashboard')

@section('title', 'Periode Payroll — Detail')
@section('header_title', 'Detail Periode Payroll')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('payroll.periods.index') }}"
               class="inline-flex items-center gap-1 text-xs font-medium mb-2"
               style="color: var(--dash-text-muted);"
               onmouseover="this.style.color='var(--dash-text-body)'"
               onmouseout="this.style.color='var(--dash-text-muted)'">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Semua periode
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">
                {{ $months[$period->period_month] }} {{ $period->period_year }}
            </h1>
            <div class="mt-1 flex items-center gap-3">
                @php
                    $statusStyle = match($period->status) {
                        'finalized' => 'background-color:#f0fdf4;color:#059669;',
                        'review'    => 'background-color:#fef3c7;color:#d97706;',
                        default     => 'background-color:var(--dash-nav-hover-bg);color:var(--dash-text-muted);',
                    };
                @endphp
                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                      style="{{ $statusStyle }}">
                    {{ ucfirst($period->status) }}
                </span>
                @if($period->pay_date)
                    <span class="text-sm" style="color: var(--dash-text-muted);">
                        Gajian: {{ $period->pay_date->format('d M Y') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @if($period->isDraft())
                @can('payroll.period.generate')
                    @if($payrolls->isEmpty())
                        <form method="POST" action="{{ route('payroll.periods.generate', $period) }}" class="inline">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Generate payroll untuk semua pegawai aktif periode ini?')"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[18px]">bolt</span>
                                Generate Payroll
                            </button>
                        </form>
                    @endif
                @endcan
                @can('payroll.period.update')
                    <form method="POST" action="{{ route('payroll.periods.advance-status', $period) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit"
                                onclick="return confirm('Pindahkan ke status Review?')"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
                                style="background-color: #fef3c7; color: #d97706; border: 1px solid #fcd34d;"
                                onmouseover="this.style.backgroundColor='#fde68a'"
                                onmouseout="this.style.backgroundColor='#fef3c7'">
                            Maju ke Review
                        </button>
                    </form>
                @endcan
            @elseif($period->isReview())
                @can('payroll.period.update')
                    <form method="POST" action="{{ route('payroll.periods.advance-status', $period) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit"
                                onclick="return confirm('Finalisasi periode payroll ini?')"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                style="background-color: #059669;">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            Finalisasi
                        </button>
                    </form>
                @endcan
            @elseif($period->isFinalized())
                @can('payroll.period.update')
                    <form method="POST" action="{{ route('payroll.periods.revert', $period) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit"
                                onclick="return confirm('Revert ke Draft? Pastikan belum ada pembayaran.')"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
                                style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Revert ke Draft
                        </button>
                    </form>
                @endcan
            @endif
        </div>
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
    @if($payrolls->count())
    @php
        $totalNet    = $payrolls->sum('net_amount');
        $totalPaid   = $payrolls->sum(fn($p) => $p->payments->where('payment_status','paid')->sum('amount'));
        $countUnpaid = $payrolls->where('payment_status','unpaid')->count();
    @endphp
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl px-5 py-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Pegawai</p>
            <p class="mt-2 font-jakarta text-2xl font-bold" style="color: var(--dash-text-heading);">{{ $payrolls->count() }}</p>
        </div>
        <div class="rounded-2xl px-5 py-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Bersih</p>
            <p class="mt-2 font-jakarta text-2xl font-bold" style="color: var(--dash-text-heading);">
                Rp {{ number_format($totalNet, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-2xl px-5 py-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Dibayar</p>
            <p class="mt-2 font-jakarta text-2xl font-bold" style="color: #059669;">
                Rp {{ number_format($totalPaid, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-2xl px-5 py-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Belum Dibayar</p>
            <p class="mt-2 font-jakarta text-2xl font-bold" style="color: #dc2626;">{{ $countUnpaid }}</p>
        </div>
    </div>
    @endif

    {{-- Payroll table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                Daftar payroll pegawai
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[22%]">
                    <col class="w-[14%]">
                    <col class="w-[14%]">
                    <col class="w-[12%]">
                    <col class="w-[14%]">
                    <col class="w-[12%]">
                    <col class="w-[12%]">
                </colgroup>
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Cabang</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Pendapatan</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Potongan</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Bersih</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Pembayaran</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4">
                                <p class="font-semibold truncate" style="color: var(--dash-text-heading);">
                                    {{ $payroll->employee_name_snapshot }}
                                </p>
                                <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-muted);">
                                    {{ $payroll->employee_code_snapshot }}
                                </p>
                            </td>
                            <td class="px-5 py-4 text-sm truncate" style="color: var(--dash-text-body);">
                                {{ $payroll->branch_name_snapshot ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-right" style="color: var(--dash-text-body);">
                                Rp {{ number_format($payroll->total_earnings, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-sm text-right font-medium" style="color: #dc2626;">
                                Rp {{ number_format($payroll->total_deductions, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-sm text-right font-semibold" style="color: var(--dash-text-heading);">
                                Rp {{ number_format($payroll->net_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $payStyle = match($payroll->payment_status) {
                                        'paid','paid_full' => 'background-color:#f0fdf4;color:#059669;',
                                        'partial'          => 'background-color:#fef3c7;color:#d97706;',
                                        default            => 'background-color:#fee2e2;color:#dc2626;',
                                    };
                                    $payLabel = match($payroll->payment_status) {
                                        'paid','paid_full' => 'Paid',
                                        'partial'          => 'Partial',
                                        default            => 'Unpaid',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                      style="{{ $payStyle }}">
                                    {{ $payLabel }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-right">
                                <a href="{{ route('payroll.payrolls.show', [$period, $payroll]) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                   title="Detail"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">payments</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
                                    Payroll belum digenerate.
                                </p>
                                @if($period->isDraft())
                                    <p class="text-sm mt-1" style="color: var(--dash-text-faint);">
                                        Klik "Generate Payroll" untuk membuat data gaji semua pegawai aktif.
                                    </p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
