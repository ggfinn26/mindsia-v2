@extends('layouts.dashboard')

@section('title', 'Detail Payroll Pegawai')
@section('header_title', 'Detail Payroll')

@section('content')
@php
    $months = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    <div>
        <a href="{{ route('payroll.periods.show', $period) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            {{ $months[$period->period_month] }} {{ $period->period_year }}
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $payroll->employee_name_snapshot }}</h2>
        <p class="mt-0.5 font-mono text-sm text-slate-400">{{ $payroll->employee_code_snapshot }} · {{ $payroll->position_name_snapshot }}</p>
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

    {{-- Summary Cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Total Pendapatan</p>
            <p class="mt-1 font-jakarta text-xl font-bold text-slate-900">Rp {{ number_format($payroll->total_earnings, 0, ',', '.') }}</p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Total Potongan</p>
            <p class="mt-1 font-jakarta text-xl font-bold text-red-700">Rp {{ number_format($payroll->total_deductions, 0, ',', '.') }}</p>
        </div>
        <div class="border border-emerald-200 bg-emerald-50 px-5 py-4">
            <p class="text-xs font-semibold text-emerald-700">Gaji Bersih</p>
            <p class="mt-1 font-jakarta text-xl font-bold text-emerald-900">Rp {{ number_format($payroll->net_amount, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Kehadiran --}}
    <div class="border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Rekap Kehadiran</h3>
        </div>
        <div class="grid grid-cols-4 divide-x divide-slate-100 px-0 py-0 text-center text-sm sm:grid-cols-8">
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
                <div class="py-4">
                    <p class="font-bold text-slate-900">{{ $val ?? 0 }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Payroll Items --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Rincian Komponen</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Komponen</th>
                    <th class="px-5 py-3 text-left">Tipe</th>
                    <th class="px-5 py-3 text-right">Nilai Satuan</th>
                    <th class="px-5 py-3 text-right">Qty</th>
                    <th class="px-5 py-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($payroll->items as $item)
                    <tr class="{{ $item->component_type_snapshot === 'deduction' ? 'bg-red-50/40' : '' }}">
                        <td class="px-5 py-3 font-medium text-slate-900">{{ $item->component_name_snapshot }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $item->component_type_snapshot === 'earning' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $item->component_type_snapshot === 'earning' ? 'Pendapatan' : 'Potongan' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right text-slate-700">Rp {{ number_format($item->unit_value, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right text-slate-700">{{ $item->quantity }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-900">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-400">Tidak ada komponen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Slip & Pembayaran --}}
    @if ($period->isFinalized())
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="border border-slate-200 bg-white p-5">
            <h3 class="font-jakarta text-sm font-bold text-slate-900 mb-3">Slip Gaji</h3>
            @if ($payroll->slip)
                <a href="{{ route('payroll.payrolls.slip.download', [$period, $payroll]) }}"
                   class="inline-flex min-h-9 items-center gap-2 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    Download Slip
                </a>
            @else
                @can('payroll.period.pay')
                    <form method="POST" action="{{ route('payroll.payrolls.slip.generate', [$period, $payroll]) }}">
                        @csrf
                        <button type="submit" class="inline-flex min-h-9 items-center gap-2 bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">
                            <span class="material-symbols-outlined text-[16px]">receipt</span>
                            Generate Slip
                        </button>
                    </form>
                @endcan
            @endif
        </div>

        @can('payroll.period.pay')
        <div class="border border-slate-200 bg-white p-5">
            <h3 class="font-jakarta text-sm font-bold text-slate-900 mb-3">Proses Pembayaran</h3>
            <form method="POST" action="{{ route('payroll.payrolls.payment.store', [$period, $payroll]) }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Metode</label>
                        <select name="payment_method" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                            <option value="bank_transfer">Transfer Bank</option>
                            <option value="cash">Tunai</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Jumlah</label>
                        <input type="number" name="amount" min="0.01" step="0.01"
                               placeholder="0" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Referensi</label>
                    <input type="text" name="payment_reference" placeholder="No. transaksi / keterangan"
                           class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <button type="submit" class="inline-flex min-h-9 w-full items-center justify-center gap-2 bg-emerald-600 px-3 text-xs font-semibold text-white hover:bg-emerald-700">
                    Catat Pembayaran
                </button>
            </form>
        </div>
        @endcan
    </div>
    @endif

    {{-- Adjustment (finalized only) --}}
    @if ($period->isFinalized())
    @can('payroll.adjustment.create')
    <div class="border border-amber-200 bg-amber-50 p-5">
        <h3 class="font-jakarta text-sm font-bold text-amber-900 mb-3">Adjustment Manual</h3>
        <form method="POST" action="{{ route('payroll.payrolls.adjust', [$period, $payroll]) }}" class="space-y-3">
            @csrf
            @if ($errors->any())
                <div class="text-xs text-red-700"><ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <div class="grid gap-3 sm:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-amber-800">Tipe</label>
                    <select name="adjustment_type" required class="block w-full border-amber-300 bg-white text-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="earning">Pendapatan</option>
                        <option value="deduction">Potongan</option>
                        <option value="correction">Koreksi langsung</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-amber-800">Nilai Baru</label>
                    <input type="number" name="new_amount" min="0" step="0.01" required
                           class="block w-full border-amber-300 bg-white text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-amber-800">Item (opsional)</label>
                    <select name="payroll_item_id" class="block w-full border-amber-300 bg-white text-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">— Koreksi net amount —</option>
                        @foreach ($payroll->items as $item)
                            <option value="{{ $item->id }}">{{ $item->component_name_snapshot }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-amber-800">Alasan (min. 5 karakter)</label>
                <input type="text" name="adjustment_reason" required minlength="5"
                       class="block w-full border-amber-300 bg-white text-sm focus:border-amber-500 focus:ring-amber-500"
                       placeholder="Jelaskan alasan adjustment">
            </div>
            <button type="submit" class="inline-flex min-h-9 items-center gap-2 bg-amber-600 px-4 text-xs font-semibold text-white hover:bg-amber-700">
                Simpan Adjustment
            </button>
        </form>
    </div>
    @endcan
    @endif

    {{-- Riwayat Pembayaran --}}
    @if ($payroll->payments->isNotEmpty())
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Riwayat Pembayaran</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Tanggal</th>
                    <th class="px-5 py-3 text-left">Metode</th>
                    <th class="px-5 py-3 text-right">Jumlah</th>
                    <th class="px-5 py-3 text-left">Referensi</th>
                    <th class="px-5 py-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($payroll->payments as $payment)
                    <tr>
                        <td class="px-5 py-3 text-slate-600">{{ $payment->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $payment->payment_method === 'bank_transfer' ? 'Transfer' : 'Tunai' }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $payment->payment_reference ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $payment->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($payment->payment_status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
