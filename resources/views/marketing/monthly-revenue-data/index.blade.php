@extends('layouts.dashboard')

@section('title', 'Data Pendapatan Bulanan')
@section('header_title', 'Data Pendapatan')

@section('content')
@php
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    $summaryP1 = 0;
    $summaryP2 = 0;
    $summaryAdmin = 0;
    $summaryIncome = 0;

    foreach ($registrations as $reg) {
        $regPayments = $reg->payments->where('payment_status', 'paid');
        $p1Amount = $regPayments->where('installment_number', 1)->sum('amount');
        $p2Amount = $regPayments->where('installment_number', 2)->sum('amount');
        $adminAmount = (int) ($reg->program?->admin_fee ?? 0);
        $incomeAmount = $p1Amount + $p2Amount - $adminAmount;

        $summaryP1 += $p1Amount;
        $summaryP2 += $p2Amount;
        $summaryAdmin += $adminAmount;
        $summaryIncome += $incomeAmount;
    }
@endphp

<div class="space-y-6">
    {{-- Pola A Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="text-xs font-semibold tracking-wider uppercase mb-1" style="color: var(--dash-primary-text)">Pemasaran</div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading)">Data Pendapatan Bulanan</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted)">Rekapitulasi pembayaran member, pemotongan biaya administrasi, dan pendapatan bersih marketing.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold"
                style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body)">
                <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary)">calendar_month</span>
                {{ $months[$month] }} {{ $year }}
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2 border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-600 dark:text-emerald-400 rounded-xl">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-2 border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-600 dark:text-rose-400 rounded-xl">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Executive Summary Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Pembayaran 1 --}}
        <div class="rounded-2xl border p-4 sm:p-5 shadow-sm transition-all"
            style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Total Pembayaran 1</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-500/10 text-blue-600 dark:text-blue-400">
                    <span class="material-symbols-outlined text-[18px]">payments</span>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-bold tabular-nums" style="color: var(--dash-text-heading)">
                Rp {{ number_format($summaryP1, 0, ',', '.') }}
            </div>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted)">Pencairan termin pertama</p>
        </div>

        {{-- Card 2: Pembayaran 2 --}}
        <div class="rounded-2xl border p-4 sm:p-5 shadow-sm transition-all"
            style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Total Pembayaran 2</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-violet-500/10 text-violet-600 dark:text-violet-400">
                    <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-bold tabular-nums" style="color: var(--dash-text-heading)">
                Rp {{ number_format($summaryP2, 0, ',', '.') }}
            </div>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted)">Pencairan termin kedua</p>
        </div>

        {{-- Card 3: Biaya Administrasi --}}
        <div class="rounded-2xl border p-4 sm:p-5 shadow-sm transition-all"
            style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Biaya Administrasi</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-500/10 text-rose-600 dark:text-rose-400">
                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-bold tabular-nums text-rose-500">
                -Rp {{ number_format($summaryAdmin, 0, ',', '.') }}
            </div>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted)">Potongan operasional program</p>
        </div>

        {{-- Card 4: Pendapatan Bersih --}}
        <div class="rounded-2xl border p-4 sm:p-5 shadow-sm transition-all"
            style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Pendapatan Bersih</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <span class="material-symbols-outlined text-[18px]">trending_up</span>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-bold tabular-nums text-emerald-600 dark:text-emerald-400">
                Rp {{ number_format($summaryIncome, 0, ',', '.') }}
            </div>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted)">Total bersih halaman ini</p>
        </div>
    </div>

    {{-- Filter Card --}}
    <form method="GET" class="flex flex-wrap gap-4 items-end rounded-2xl border p-4 sm:p-5 shadow-sm"
        style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Bulan</label>
            <select name="month" 
                onfocus="this.style.borderColor='var(--dash-primary)'" 
                onblur="this.style.borderColor='var(--dash-input-border)'"
                class="block w-full min-w-[140px] rounded-lg border px-3 py-2 text-xs sm:text-sm outline-none transition-colors"
                style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-heading)">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ now()->year + 1 }}" 
                onfocus="this.style.borderColor='var(--dash-primary)'" 
                onblur="this.style.borderColor='var(--dash-input-border)'"
                class="block w-28 rounded-lg border px-3 py-2 text-xs sm:text-sm outline-none transition-colors"
                style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-heading)">
        </div>
        @can('marketing.monthly_revenue_data.view_branch')
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Cabang</label>
                <select name="branch_id" 
                    onfocus="this.style.borderColor='var(--dash-primary)'" 
                    onblur="this.style.borderColor='var(--dash-input-border)'"
                    class="block w-full min-w-[180px] rounded-lg border px-3 py-2 text-xs sm:text-sm outline-none transition-colors"
                    style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-heading)">
                    <option value="">Semua cabang</option>
                    @foreach (\App\Models\Branch::active()->orderBy('branch_name')->get() as $branch)
                        <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Marketing</label>
                <select name="employee_id" 
                    onfocus="this.style.borderColor='var(--dash-primary)'" 
                    onblur="this.style.borderColor='var(--dash-input-border)'"
                    class="block w-full min-w-[200px] rounded-lg border px-3 py-2 text-xs sm:text-sm outline-none transition-colors"
                    style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-heading)">
                    <option value="">Semua marketing</option>
                    @foreach (\App\Models\Employee::whereHas('employmentStatuses.position', fn ($q) => $q->where('position_name', 'like', '%Marketing%'))->orderBy('full_name')->get() as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
        @endcan
        <div class="flex items-center gap-2">
            <button type="submit" 
                class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-xs sm:text-sm font-semibold shadow-sm transition-opacity hover:opacity-90"
                style="background-color: var(--dash-primary); color: #ffffff">
                <span class="material-symbols-outlined text-[16px] mr-1">filter_alt</span>
                Terapkan Filter
            </button>
            @if(request()->hasAny(['branch_id', 'employee_id']) || request('month') != now()->month || request('year') != now()->year)
                <a href="{{ route('marketing.monthly-revenue-data') }}" 
                    class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-xs sm:text-sm font-medium transition-colors"
                    style="border-color: var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg)">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Table Card with Zero Horizontal Scroll --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
        style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
        <div class="flex items-center justify-between border-b px-5 py-4"
            style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg)">
            <h2 class="text-sm font-bold" style="color: var(--dash-text-heading)">
                Daftar Transaksi Pendapatan {{ $months[$month] }} {{ $year }}
            </h2>
            <span class="rounded-full border px-2.5 py-1 text-xs font-semibold"
                style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted)">
                {{ $registrations->total() }} pendaftaran
            </span>
        </div>
        <div class="w-full">
            <table class="w-full text-sm text-left">
                <thead class="border-b text-[11px] font-semibold uppercase tracking-wider"
                    style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted)">
                    <tr>
                        <th class="px-5 py-3.5">Member & Tanggal</th>
                        <th class="px-5 py-3.5">Institusi & Program</th>
                        <th class="px-5 py-3.5 text-right">Pembayaran 1</th>
                        <th class="px-5 py-3.5 text-right">Pembayaran 2</th>
                        <th class="px-5 py-3.5 text-right">Biaya Admin</th>
                        <th class="px-5 py-3.5 text-right">Pendapatan Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border)">
                    @forelse ($registrations as $registration)
                        @php
                            $member = $registration->memberData;
                            $payments = $registration->payments->where('payment_status', 'paid');
                            $p1 = $payments->where('installment_number', 1)->sum('amount');
                            $p2 = $payments->where('installment_number', 2)->sum('amount');
                            $adminFee = (int) ($registration->program?->admin_fee ?? 0);
                            $income = $p1 + $p2 - $adminFee;
                        @endphp
                        <tr onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'" onmouseout="this.style.backgroundColor='transparent'" class="transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="font-semibold" style="color: var(--dash-text-heading)">{{ $member?->full_name ?? '-' }}</div>
                                <div class="text-xs mt-0.5" style="color: var(--dash-text-muted)">{{ $registration->created_at->translatedFormat('d M Y') }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div style="color: var(--dash-text-body)">{{ $registration->receipt_institution_name ?? $member?->institution?->name ?? '-' }}</div>
                                <div class="text-xs mt-0.5" style="color: var(--dash-text-muted)">{{ $registration->receipt_program_name ?? $registration->program?->name ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-medium" style="color: var(--dash-text-body)">
                                {{ $p1 ? 'Rp ' . number_format($p1, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-medium" style="color: var(--dash-text-body)">
                                {{ $p2 ? 'Rp ' . number_format($p2, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-medium text-rose-500">
                                {{ $adminFee ? '-Rp ' . number_format($adminFee, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $income > 0 ? 'Rp ' . number_format($income, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-4xl opacity-40 mb-2 block" style="color: var(--dash-text-faint)">receipt_long</span>
                                <p class="font-semibold" style="color: var(--dash-text-heading)">Tidak ada data pendapatan.</p>
                                <p class="text-xs sm:text-sm mt-1" style="color: var(--dash-text-muted)">Pilih bulan dan tahun lain untuk melihat data.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($registrations->count())
                    <tfoot class="border-t" style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg)">
                        <tr>
                            <td colspan="2" class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">
                                Total Halaman Ini:
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-bold" style="color: var(--dash-text-heading)">
                                {{ $summaryP1 ? 'Rp ' . number_format($summaryP1, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-bold" style="color: var(--dash-text-heading)">
                                {{ $summaryP2 ? 'Rp ' . number_format($summaryP2, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-bold text-rose-500">
                                {{ $summaryAdmin ? '-Rp ' . number_format($summaryAdmin, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-bold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($summaryIncome, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if ($registrations->hasPages())
            <div class="border-t px-5 py-4" style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg)">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
