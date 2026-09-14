@extends('layouts.dashboard')

@section('title', 'Periode Payroll')
@section('header_title', 'Periode Payroll')

@section('content')
@php
    $months = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $statusBadge = [
        'draft'     => 'bg-slate-100 text-slate-700',
        'review'    => 'bg-amber-100 text-amber-800',
        'finalized' => 'bg-emerald-100 text-emerald-800',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Payroll</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Periode Payroll</h2>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Kelola periode penggajian. Generate payroll dilakukan per periode.</p>
        </div>
        @can('payroll.period.create')
            <form method="POST" action="{{ route('payroll.periods.auto-create') }}">
                @csrf
                <button type="submit" onclick="return confirm('Buat otomatis semua periode tahun ini dan tahun depan?')"
                        class="inline-flex min-h-10 items-center gap-2 border border-[#215aac] px-4 text-sm font-semibold text-[#215aac] hover:bg-blue-50">
                    <span class="material-symbols-outlined text-[18px]">auto_fix_high</span>
                    Buat Otomatis
                </button>
            </form>
        @endcan
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
    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Create Form --}}
    @can('payroll.period.create')
    <form method="POST" action="{{ route('payroll.periods.store') }}" class="border border-slate-200 bg-white p-5">
        @csrf
        <div class="mb-4 flex items-start gap-3">
            <span class="material-symbols-outlined mt-0.5 text-[#215aac]">add_circle</span>
            <h3 class="font-jakarta text-base font-bold text-slate-900">Buat periode baru</h3>
        </div>
        <div class="grid gap-4 sm:grid-cols-4">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Bulan</label>
                <select name="period_month" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    @foreach (array_slice($months, 1, null, true) as $num => $label)
                        <option value="{{ $num }}" @selected(old('period_month') == $num)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Tahun</label>
                <input type="number" name="period_year" value="{{ old('period_year', now()->year) }}"
                       min="2020" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal Gajian</label>
                <input type="date" name="pay_date" value="{{ old('pay_date') }}"
                       class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div class="flex items-end">
                <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Buat Periode
                </button>
            </div>
        </div>
    </form>
    @endcan

    {{-- Periods Table --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Semua periode</h3>
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
                                {{ $period->employeePayrolls->count() }}
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
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">calendar_month</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada periode payroll.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
