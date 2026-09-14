@extends('layouts.dashboard')

@section('title', 'Slip Gaji Saya')
@section('header_title', 'Slip Gaji Saya')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Keuangan</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Slip Gaji Saya</h2>
        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Riwayat slip gaji dari seluruh periode payroll yang telah difinalisasi.</p>
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

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Riwayat slip gaji</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Periode</th>
                        <th class="px-5 py-3 text-right">Gaji Bersih</th>
                        <th class="px-5 py-3">Status Slip</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payrolls as $payroll)
                        @php
                            $periodLabel = \Carbon\Carbon::create(
                                $payroll->period->period_year,
                                $payroll->period->period_month,
                                1
                            )->translatedFormat('F Y');

                            $slipExists = $payroll->slip && $payroll->slip->telegram_file_id;
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $periodLabel }}</p>
                                @if ($payroll->period->pay_date)
                                    <p class="mt-0.5 text-xs text-slate-500">Tanggal bayar: {{ $payroll->period->pay_date->translatedFormat('d M Y') }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums font-semibold text-slate-900">
                                Rp {{ number_format((float) $payroll->net_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($slipExists)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span> Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                        <span class="material-symbols-outlined text-[14px]">schedule</span> Belum tersedia
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if ($slipExists)
                                    <a href="{{ route('employee.payslips.download', $payroll) }}"
                                       class="inline-flex min-h-9 items-center gap-1.5 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        <span class="material-symbols-outlined text-[16px]">download</span> Unduh
                                    </a>
                                @else
                                    <span class="text-xs font-medium text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">description</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada slip gaji.</p>
                                <p class="mt-1 text-sm text-slate-500">Slip gaji akan tersedia setelah periode payroll difinalisasi dan slip digenerate.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
