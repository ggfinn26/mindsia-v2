@extends('layouts.dashboard')

@section('title', 'Data Pendapatan Bulanan')
@section('header_title', 'Data Pendapatan')

@section('content')
@php $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp

<div class="w-full space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="font-jakarta text-xl font-bold tracking-tight text-slate-900">Data Pendapatan Bulanan</h2>
            <p class="mt-1 text-sm text-slate-500">Laporan pembayaran dan pendapatan bersih per marketing.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 rounded-xl">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-2 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 rounded-xl">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <form method="GET" class="flex flex-wrap gap-4 items-end bg-white p-4 sm:p-5 rounded-xl border border-slate-100 shadow-sm">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Bulan</label>
            <select name="month" class="block w-full min-w-[140px] bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ now()->year + 1 }}" 
                class="block w-28 bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
        </div>
        @can('marketing.monthly_revenue_data.view_branch')
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Cabang</label>
                <select name="branch_id" class="block w-full min-w-[180px] bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                    <option value="">Semua cabang</option>
                    @foreach (\App\Models\Branch::active()->orderBy('branch_name')->get() as $branch)
                        <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Marketing</label>
                <select name="employee_id" class="block w-full min-w-[200px] bg-slate-50 border border-slate-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                    <option value="">Semua marketing</option>
                    @foreach (\App\Models\Employee::whereHas('employmentStatuses.position', fn ($q) => $q->where('position_name', 'like', '%Marketing%'))->orderBy('full_name')->get() as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
        @endcan
        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#215aac] rounded-lg hover:bg-[#194a91] focus:outline-none transition-colors">
                Terapkan Filter
            </button>
            @if(request()->hasAny(['branch_id', 'employee_id']) || request('month') != now()->month || request('year') != now()->year)
                <a href="{{ route('marketing.monthly-revenue-data') }}" class="px-3 py-2 text-sm text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Reset</a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="border-b border-slate-100 px-5 py-4 flex items-center justify-between bg-slate-50/50 rounded-t-xl">
            <h3 class="font-jakarta text-sm font-bold text-slate-800">Pendapatan {{ $months[$month] }} {{ $year }}</h3>
            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-full">{{ $registrations->total() }} pendaftaran</span>
        </div>
        <div class="w-full">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">Tanggal</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">Member</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">Institusi</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">Program</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Pembayaran 1</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Pembayaran 2</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Biaya Admin</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Pendapatan Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php $totalP1 = 0; $totalP2 = 0; $totalAdmin = 0; $totalIncome = 0; @endphp
                    @forelse ($registrations as $registration)
                        @php
                            $member = $registration->memberData;
                            $payments = $registration->payments->where('payment_status', 'paid');
                            $p1 = $payments->where('installment_number', 1)->sum('amount');
                            $p2 = $payments->where('installment_number', 2)->sum('amount');
                            $adminFee = (int) ($registration->program?->admin_fee ?? 0);
                            $income = $p1 + $p2 - $adminFee;

                            $totalP1 += $p1;
                            $totalP2 += $p2;
                            $totalAdmin += $adminFee;
                            $totalIncome += $income;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-4 text-slate-500 whitespace-nowrap">{{ $registration->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-5 py-4 font-medium text-slate-900">{{ $member?->full_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $registration->receipt_institution_name ?? $member?->institution?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $registration->receipt_program_name ?? $registration->program?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-slate-600">{{ $p1 ? 'Rp ' . number_format($p1, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-slate-600">{{ $p2 ? 'Rp ' . number_format($p2, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-red-500">{{ $adminFee ? '-Rp ' . number_format($adminFee, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-emerald-600">{{ $income > 0 ? 'Rp ' . number_format($income, 0, ',', '.') : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-4xl text-slate-300 mb-2 block">receipt_long</span>
                                <p class="font-semibold text-slate-700">Tidak ada data pendapatan.</p>
                                <p class="text-sm text-slate-500 mt-1">Pilih bulan dan tahun lain untuk melihat data.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($registrations->count())
                    <tfoot class="bg-slate-50/80 border-t border-slate-100">
                        <tr>
                            <td colspan="4" class="px-5 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Halaman Ini:</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-slate-800">{{ $totalP1 ? 'Rp ' . number_format($totalP1, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-slate-800">{{ $totalP2 ? 'Rp ' . number_format($totalP2, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-red-600">{{ $totalAdmin ? '-Rp ' . number_format($totalAdmin, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-emerald-700">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if ($registrations->hasPages())
            <div class="border-t border-slate-100 px-5 py-4 rounded-b-xl bg-white">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
