@extends('layouts.dashboard')

@section('title', 'Monthly Revenue Data')
@section('header_title', 'Monthly Revenue')

@section('content')
@php $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Marketing</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Monthly Revenue Data</h2>
        <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-600">Data pendapatan per pendaftaran member. Read-only — menampilkan pembayaran cicilan, biaya admin, dan pendapatan bersih per marketing.</p>
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

    <form method="GET" class="flex flex-wrap gap-3 border border-slate-200 bg-white p-4">
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Bulan</label>
            <select name="month" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ now()->year + 1 }}" class="block w-24 border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
        </div>
        @can('marketing.monthly_revenue_data.view_branch')
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-700">Cabang</label>
                <select name="branch_id" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    <option value="">Semua cabang</option>
                    @foreach (\App\Models\Branch::where('is_active', true)->orderBy('branch_name')->get() as $branch)
                        <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-700">Marketing</label>
                <select name="employee_id" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    <option value="">Semua marketing</option>
                    @foreach (\App\Models\Employee::whereHas('position', fn ($q) => $q->where('name', 'like', '%Marketing%'))->orderBy('full_name')->get() as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
        @endcan
        <div class="flex items-end">
            <button type="submit" class="inline-flex min-h-9 items-center bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Terapkan</button>
        </div>
    </form>

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4 flex items-center justify-between">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Pendapatan {{ $months[$month] }} {{ $year }}</h3>
            <span class="text-xs font-medium text-slate-500">{{ $registrations->total() }} pendaftaran</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Tanggal Daftar</th>
                        <th class="px-5 py-3">Nama Member</th>
                        <th class="px-5 py-3">Institusi</th>
                        <th class="px-5 py-3">Program</th>
                        <th class="px-5 py-3 text-right">Pembayaran 1</th>
                        <th class="px-5 py-3 text-right">Pembayaran 2</th>
                        <th class="px-5 py-3 text-right">Biaya Admin</th>
                        <th class="px-5 py-3 text-right">Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $totalP1 = 0; $totalP2 = 0; $totalAdmin = 0; $totalIncome = 0; @endphp
                    @forelse ($registrations as $registration)
                        @php
                            $member = $registration->memberData;
                            $payments = $registration->payments->where('payment_status', 'paid');
                            $p1 = $payments->where('installment_number', 1)->sum('amount');
                            $p2 = $payments->where('installment_number', 2)->sum('amount');
                            $adminFee = 0; // admin fee not tracked per payment currently
                            $income = $p1 + $p2 - $adminFee;

                            $totalP1 += $p1;
                            $totalP2 += $p2;
                            $totalAdmin += $adminFee;
                            $totalIncome += $income;
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 text-slate-600">{{ $registration->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $member?->full_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $member?->institution?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $registration->program?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-slate-700">{{ $p1 ? 'Rp ' . number_format($p1, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-slate-700">{{ $p2 ? 'Rp ' . number_format($p2, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-slate-500">{{ $adminFee ? 'Rp ' . number_format($adminFee, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-semibold text-slate-900">Rp {{ number_format($income, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">trending_up</span>
                                <p class="mt-2 font-semibold text-slate-700">Tidak ada data pendapatan.</p>
                                <p class="mt-1 text-sm text-slate-500">Pilih bulan dan tahun untuk melihat data pendapatan marketing.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($registrations->count())
                    <tfoot class="bg-slate-50">
                        <tr>
                            <td colspan="4" class="px-5 py-3 text-right text-xs font-semibold text-slate-500">Total:</td>
                            <td class="px-5 py-3 text-right tabular-nums font-bold text-slate-900">{{ $totalP1 ? 'Rp ' . number_format($totalP1, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-bold text-slate-900">{{ $totalP2 ? 'Rp ' . number_format($totalP2, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-bold text-slate-900">{{ $totalAdmin ? 'Rp ' . number_format($totalAdmin, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-bold text-slate-900">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if ($registrations->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
