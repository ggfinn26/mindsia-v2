@extends('layouts.dashboard')

@section('title', 'Generate Payroll')
@section('header_title', 'Generate Payroll')

@section('content')
@php
    $months = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $statusBadge = [
        'review'    => 'bg-amber-100 text-amber-800',
        'finalized' => 'bg-emerald-100 text-emerald-800',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Payroll</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Generate Payroll</h2>
        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Pilih periode untuk memproses perhitungan gaji karyawan.</p>
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
            <h3 class="font-jakarta text-base font-bold text-slate-900">Periode siap di-generate</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Periode</th>
                        <th class="px-5 py-3">Tanggal Gajian</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Pegawai</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($periods as $period)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-semibold text-slate-900">
                                {{ $months[$period->period_month] }} {{ $period->period_year }}
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $period->pay_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$period->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($period->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $period->employee_payrolls_count ?? 0 }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('payroll.periods.show', $period) }}"
                                       class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Lihat Detail
                                    </a>
                                    @if (($period->employee_payrolls_count ?? 0) === 0)
                                        <form method="POST" action="{{ route('payroll.periods.generate', $period) }}">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Generate payroll untuk semua pegawai aktif periode {{ $months[$period->period_month] }} {{ $period->period_year }}?')"
                                                    class="inline-flex min-h-9 items-center gap-1 bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">
                                                <span class="material-symbols-outlined text-[16px]">bolt</span>
                                                Generate
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                            Sudah digenerate
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">pending</span>
                                <p class="mt-2 font-semibold text-slate-700">Tidak ada periode yang siap di-generate.</p>
                                <p class="mt-1 text-sm text-slate-500">Finalize periode terlebih dahulu di halaman <a href="{{ route('payroll.periods.index') }}" class="text-[#215aac] underline">Kelola Periode</a>.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
