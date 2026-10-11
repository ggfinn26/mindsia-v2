@extends('layouts.dashboard')

@section('title', 'Periode Payroll — Detail')
@section('header_title', 'Detail Periode Payroll')

@section('content')
@php
    $months = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $statusBadge = [
        'draft'     => 'bg-slate-100 text-slate-700',
        'review'    => 'bg-amber-100 text-amber-800',
        'finalized' => 'bg-emerald-100 text-emerald-800',
    ];
    $paymentBadge = [
        'unpaid'       => 'bg-red-100 text-red-800',
        'partial'      => 'bg-amber-100 text-amber-800',
        'paid'         => 'bg-emerald-100 text-emerald-800',
        'paid_full'    => 'bg-emerald-100 text-emerald-800',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('payroll.periods.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Semua periode
            </a>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">
                {{ $months[$period->period_month] }} {{ $period->period_year }}
            </h2>
            <div class="mt-1 flex items-center gap-3">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$period->status] ?? '' }}">
                    {{ ucfirst($period->status) }}
                </span>
                @if ($period->pay_date)
                    <span class="text-sm text-slate-500">Gajian: {{ $period->pay_date->format('d M Y') }}</span>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @if ($period->isDraft())
                @can('payroll.period.generate')
                    @if ($payrolls->isEmpty())
                        <form method="POST" action="{{ route('payroll.periods.generate', $period) }}">
                            @csrf
                            <button type="submit" onclick="return confirm('Generate payroll untuk semua pegawai aktif periode ini?')"
                                    class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                                <span class="material-symbols-outlined text-[18px]">bolt</span>
                                Generate Payroll
                            </button>
                        </form>
                    @endif
                @endcan
                @can('payroll.period.update')
                    <form method="POST" action="{{ route('payroll.periods.advance-status', $period) }}">
                        @csrf @method('PATCH')
                        <button type="submit" onclick="return confirm('Pindahkan ke status Review?')"
                                class="inline-flex min-h-10 items-center gap-2 border border-amber-300 bg-amber-50 px-4 text-sm font-semibold text-amber-800 hover:bg-amber-100">
                            Maju ke Review
                        </button>
                    </form>
                @endcan
            @elseif ($period->isReview())
                @can('payroll.period.update')
                    <form method="POST" action="{{ route('payroll.periods.advance-status', $period) }}">
                        @csrf @method('PATCH')
                        <button type="submit" onclick="return confirm('Finalisasi periode payroll ini?')"
                                class="inline-flex min-h-10 items-center gap-2 bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            Finalisasi
                        </button>
                    </form>
                @endcan
            @elseif ($period->isFinalized())
                @can('payroll.period.update')
                    <form method="POST" action="{{ route('payroll.periods.revert', $period) }}">
                        @csrf @method('PATCH')
                        <button type="submit" onclick="return confirm('Revert ke Draft? Pastikan belum ada pembayaran.')"
                                class="inline-flex min-h-10 items-center gap-2 border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Revert ke Draft
                        </button>
                    </form>
                @endcan
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex gap-3 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Summary --}}
    @if ($payrolls->count())
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        @php
            $totalNet = $payrolls->sum('net_amount');
            $totalPaid = $payrolls->sum(fn($p) => $p->payments->where('payment_status','paid')->sum('amount'));
            $countUnpaid = $payrolls->where('payment_status', 'unpaid')->count();
        @endphp
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Total Pegawai</p>
            <p class="mt-1 font-jakarta text-2xl font-bold text-slate-900">{{ $payrolls->count() }}</p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Total Bersih</p>
            <p class="mt-1 font-jakarta text-2xl font-bold text-slate-900">Rp {{ number_format($totalNet, 0, ',', '.') }}</p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Total Dibayar</p>
            <p class="mt-1 font-jakarta text-2xl font-bold text-emerald-700">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Belum Dibayar</p>
            <p class="mt-1 font-jakarta text-2xl font-bold text-red-700">{{ $countUnpaid }}</p>
        </div>
    </div>
    @endif

    {{-- Payroll Table --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Daftar payroll pegawai</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Pegawai</th>
                        <th class="px-5 py-3">Cabang</th>
                        <th class="px-5 py-3 text-right">Pendapatan</th>
                        <th class="px-5 py-3 text-right">Potongan</th>
                        <th class="px-5 py-3 text-right">Bersih</th>
                        <th class="px-5 py-3">Pembayaran</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payrolls as $payroll)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $payroll->employee_name_snapshot }}</p>
                                <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $payroll->employee_code_snapshot }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $payroll->branch_name_snapshot ?? '—' }}</td>
                            <td class="px-5 py-4 text-right text-slate-700">Rp {{ number_format($payroll->total_earnings, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right text-red-700">Rp {{ number_format($payroll->total_deductions, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-900">Rp {{ number_format($payroll->net_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $paymentBadge[$payroll->payment_status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $payroll->payment_status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('payroll.payrolls.show', [$period, $payroll]) }}"
                                   class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">payments</span>
                                <p class="mt-2 font-semibold text-slate-700">Payroll belum digenerate.</p>
                                @if ($period->isDraft())
                                    <p class="mt-1 text-sm text-slate-500">Klik "Generate Payroll" untuk membuat data gaji semua pegawai aktif.</p>
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
