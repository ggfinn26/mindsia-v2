@extends('layouts.dashboard')

@section('title', 'Data Pendapatan Saya - MINDSIA')
@section('header_title', 'Pendapatan Saya')

@section('content')
@php $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Personal Dashboard</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Data Pendapatan Saya</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Laporan pembayaran dari member referral Anda.
        </p>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl shadow-sm"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Bulan</label>
            <select name="month" data-no-enhance
                    class="block w-full min-w-[140px] text-sm px-3.5 py-2.5 rounded-lg outline-none transition-all cursor-pointer"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ now()->year + 1 }}"
                   class="block w-28 text-sm px-3.5 py-2.5 rounded-lg outline-none transition-all"
                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                   onfocus="this.style.borderColor='var(--dash-primary)'"
                   onblur="this.style.borderColor='var(--dash-input-border)'">
        </div>
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg text-white transition-all shadow-sm"
                    style="background-color: var(--dash-primary);"
                    onmouseover="this.style.opacity='0.9'"
                    onmouseout="this.style.opacity='1'">
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                Terapkan Filter
            </button>
            @if(request('month') != now()->month || request('year') != now()->year)
                <a href="{{ route('marketing.my-monthly-revenue') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 text-sm font-medium rounded-lg transition-all"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-text-heading)';"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-muted)';">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Data Table --}}
    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-4 flex items-center justify-between"
             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">
                Pendapatan {{ $months[$month] }} {{ $year }}
            </h3>
            <span class="px-2.5 py-1 text-xs font-semibold rounded-md"
                  style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                {{ $registrations->total() }} pendaftaran
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Tanggal</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Member</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Institusi</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Program</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-right" style="color: var(--dash-text-faint);">Pembayaran 1</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-right" style="color: var(--dash-text-faint);">Pembayaran 2</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-right" style="color: var(--dash-text-faint);">Biaya Admin</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-right" style="color: var(--dash-text-faint);">Pendapatan Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
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
                        <tr class="transition-colors"
                            style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-4 whitespace-nowrap" style="color: var(--dash-text-muted);">
                                {{ $registration->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-5 py-4 font-medium" style="color: var(--dash-text-heading);">
                                {{ $member?->full_name ?? '-' }}
                            </td>
                            <td class="px-5 py-4" style="color: var(--dash-text-muted);">
                                {{ $registration->receipt_institution_name ?? $member?->institution?->name ?? '-' }}
                            </td>
                            <td class="px-5 py-4" style="color: var(--dash-text-muted);">
                                {{ $registration->receipt_program_name ?? $registration->program?->program_name ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums font-medium" style="color: var(--dash-text-body);">
                                {{ $p1 ? 'Rp ' . number_format($p1, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums font-medium" style="color: var(--dash-text-body);">
                                {{ $p2 ? 'Rp ' . number_format($p2, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums font-medium" style="color: #dc2626;">
                                {{ $adminFee ? '-Rp ' . number_format($adminFee, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold" style="color: #059669;">
                                {{ $income > 0 ? 'Rp ' . number_format($income, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-4xl block mb-2" style="color: var(--dash-text-faint);">receipt_long</span>
                                <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">Tidak ada data pendapatan bulan ini.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Pilih bulan dan tahun lain untuk melihat data.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($registrations->count())
                    <tfoot style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                        <tr>
                            <td colspan="4" class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">
                                Total Halaman Ini:
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold" style="color: var(--dash-text-heading);">
                                {{ $totalP1 ? 'Rp ' . number_format($totalP1, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold" style="color: var(--dash-text-heading);">
                                {{ $totalP2 ? 'Rp ' . number_format($totalP2, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold" style="color: #dc2626;">
                                {{ $totalAdmin ? '-Rp ' . number_format($totalAdmin, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold" style="color: #059669;">
                                Rp {{ number_format($totalIncome, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if ($registrations->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
