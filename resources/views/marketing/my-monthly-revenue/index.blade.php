@extends('layouts.dashboard')

@section('title', 'Data Pendapatan Saya')
@section('header_title', 'Pendapatan Saya')

@section('content')
@php $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp

<div class="w-full space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="font-jakarta text-xl font-bold tracking-tight text-[var(--dash-text-heading)]">Data Pendapatan Saya</h2>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Laporan pembayaran dari member referral Anda.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2 border border-emerald-200 [.mode-dark_&]:border-emerald-500/20 bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 px-4 py-3 text-sm text-emerald-800 [.mode-dark_&]:text-emerald-400 rounded-xl">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-2 border border-red-200 [.mode-dark_&]:border-red-500/20 bg-red-50 [.mode-dark_&]:bg-red-500/10 px-4 py-3 text-sm text-red-800 [.mode-dark_&]:text-red-400 rounded-xl">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <form method="GET" class="flex flex-wrap gap-4 items-end bg-[var(--dash-card-bg)] p-4 sm:p-5 rounded-xl border border-[var(--dash-card-border)] shadow-sm">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide whitespace-nowrap">Bulan</label>
            <select name="month" class="block w-full min-w-[140px] bg-[var(--dash-page-bg)] border border-[var(--dash-input-border)] rounded-lg text-sm px-3 py-2 outline-none focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)] transition-colors">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide whitespace-nowrap">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ now()->year + 1 }}"
                class="block w-28 bg-[var(--dash-page-bg)] border border-[var(--dash-input-border)] rounded-lg text-sm px-3 py-2 outline-none focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)] transition-colors">
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[var(--dash-primary)] rounded-lg hover:opacity-90 focus:outline-none transition-colors">
                Terapkan Filter
            </button>
            @if(request('month') != now()->month || request('year') != now()->year)
                <a href="{{ route('marketing.my-monthly-revenue') }}" class="px-3 py-2 text-sm text-[var(--dash-text-muted)] bg-[var(--dash-input-bg)] rounded-lg hover:bg-[var(--dash-input-border)] transition-colors">Reset</a>
            @endif
        </div>
    </form>

    <div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[var(--dash-card-border)]">
        <div class="border-b border-[var(--dash-card-border)] px-5 py-4 flex items-center justify-between bg-[var(--dash-page-bg)] rounded-t-xl">
            <h3 class="font-jakarta text-sm font-bold text-[var(--dash-text-heading)]">Pendapatan {{ $months[$month] }} {{ $year }}</h3>
            <span class="px-2.5 py-1 bg-[var(--dash-input-bg)] text-[var(--dash-text-muted)] text-xs font-semibold rounded-full">{{ $registrations->total() }} pendaftaran</span>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[800px]">
                <thead class="bg-[var(--dash-card-bg)] text-[var(--dash-text-muted)] border-b border-[var(--dash-card-border)]">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide whitespace-nowrap">Tanggal</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide whitespace-nowrap">Member</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide whitespace-nowrap">Institusi</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide whitespace-nowrap">Program</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Pembayaran 1</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Pembayaran 2</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Biaya Admin</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right">Pendapatan Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)]">
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
                        <tr class="hover:bg-[var(--dash-page-bg)] transition-colors">
                            <td class="px-5 py-4 text-[var(--dash-text-muted)] whitespace-nowrap">{{ $registration->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-5 py-4 font-medium text-[var(--dash-text-heading)]">{{ $member?->full_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-[var(--dash-text-muted)]">{{ $registration->receipt_institution_name ?? $member?->institution?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-[var(--dash-text-muted)]">{{ $registration->receipt_program_name ?? $registration->program?->program_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-[var(--dash-text-muted)]">{{ $p1 ? 'Rp ' . number_format($p1, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-[var(--dash-text-muted)]">{{ $p2 ? 'Rp ' . number_format($p2, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-red-500 [.mode-dark_&]:text-red-400">{{ $adminFee ? '-Rp ' . number_format($adminFee, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-emerald-600 [.mode-dark_&]:text-emerald-400">{{ $income > 0 ? 'Rp ' . number_format($income, 0, ',', '.') : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-4xl text-[var(--dash-text-faint)] mb-2 block">receipt_long</span>
                                <p class="font-semibold text-[var(--dash-text-body)]">Tidak ada data pendapatan bulan ini.</p>
                                <p class="text-sm text-[var(--dash-text-muted)] mt-1">Pilih bulan dan tahun lain untuk melihat data.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($registrations->count())
                    <tfoot class="bg-[var(--dash-page-bg)] border-t border-[var(--dash-card-border)]">
                        <tr>
                            <td colspan="4" class="px-5 py-4 text-right text-xs font-semibold text-[var(--dash-text-muted)] uppercase tracking-wide whitespace-nowrap">Total Halaman Ini:</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-[var(--dash-text-heading)]">{{ $totalP1 ? 'Rp ' . number_format($totalP1, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-[var(--dash-text-heading)]">{{ $totalP2 ? 'Rp ' . number_format($totalP2, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-red-600 [.mode-dark_&]:text-red-400">{{ $totalAdmin ? '-Rp ' . number_format($totalAdmin, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-emerald-700 [.mode-dark_&]:text-emerald-400">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if ($registrations->hasPages())
            <div class="border-t border-[var(--dash-card-border)] px-5 py-4 rounded-b-xl bg-[var(--dash-card-bg)]">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
