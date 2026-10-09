@extends('layouts.dashboard')

@section('title', 'Data Pendapatan Bulanan')
@section('header_title', 'Data Pendapatan')

@section('content')
@php $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Sales & Service</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Data Pendapatan Bulanan</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Laporan pembayaran dan pendapatan bersih per marketing.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Bulan</label>
            <select name="month"
                    class="block min-w-[140px] rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ now()->year + 1 }}"
                   class="block w-28 rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
        </div>
        @can('marketing.monthly_revenue_data.view_branch')
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Cabang</label>
                <select name="branch_id"
                        class="block min-w-[180px] rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua cabang</option>
                    @foreach (\App\Models\Branch::active()->orderBy('branch_name')->get() as $branch)
                        <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Marketing</label>
                <select name="employee_id"
                        class="block min-w-[200px] rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua marketing</option>
                    @foreach (\App\Models\Employee::whereHas('employmentStatuses.position', fn ($q) => $q->where('position_name', 'like', '%Marketing%'))->orderBy('full_name')->get() as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
        @endcan
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                Terapkan Filter
            </button>
            @if(request()->hasAny(['branch_id', 'employee_id']) || request('month') != now()->month || request('year') != now()->year)
                <a href="{{ route('marketing.monthly-revenue-data') }}"
                   class="px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Reset</a>
            @endif
        </div>
    </form>

    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Pendapatan {{ $months[$month] }} {{ $year }}</h3>
            <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                  style="background-color: var(--dash-card-border); color: var(--dash-text-muted);">{{ $registrations->total() }} pendaftaran</span>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Tanggal</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Member</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Institusi</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Program</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--dash-text-muted);">Pembayaran 1</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--dash-text-muted);">Pembayaran 2</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--dash-text-muted);">Biaya Admin</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-right" style="color: var(--dash-text-muted);">Pendapatan Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalP1 = 0; $totalP2 = 0; $totalAdmin = 0; $totalIncome = 0; @endphp
                    @forelse ($registrations as $registration)
                        @php
                            $member   = $registration->memberData;
                            $payments = $registration->payments->where('payment_status', 'paid');
                            $p1       = $payments->where('installment_number', 1)->sum('amount');
                            $p2       = $payments->where('installment_number', 2)->sum('amount');
                            $adminFee = (int) ($registration->program?->admin_fee ?? 0);
                            $income   = $p1 + $p2 - $adminFee;
                            $totalP1 += $p1; $totalP2 += $p2; $totalAdmin += $adminFee; $totalIncome += $income;
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-4 whitespace-nowrap" style="color: var(--dash-text-muted);">{{ $registration->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-5 py-4 font-medium" style="color: var(--dash-text-heading);">{{ $member?->full_name ?? '—' }}</td>
                            <td class="px-5 py-4" style="color: var(--dash-text-muted);">{{ $registration->receipt_institution_name ?? $member?->institution?->name ?? '—' }}</td>
                            <td class="px-5 py-4" style="color: var(--dash-text-muted);">{{ $registration->receipt_program_name ?? $registration->program?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums" style="color: var(--dash-text-body);">{{ $p1 ? 'Rp ' . number_format($p1, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums" style="color: var(--dash-text-body);">{{ $p2 ? 'Rp ' . number_format($p2, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-red-500">{{ $adminFee ? '-Rp ' . number_format($adminFee, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-emerald-600">{{ $income > 0 ? 'Rp ' . number_format($income, 0, ',', '.') : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-4xl mb-2 block" style="color: var(--dash-text-faint);">receipt_long</span>
                                <p class="font-semibold" style="color: var(--dash-text-heading);">Tidak ada data pendapatan.</p>
                                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Pilih bulan dan tahun lain untuk melihat data.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($registrations->count())
                    <tfoot style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <tr>
                            <td colspan="4" class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Total Halaman Ini:</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold" style="color: var(--dash-text-heading);">{{ $totalP1 ? 'Rp ' . number_format($totalP1, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold" style="color: var(--dash-text-heading);">{{ $totalP2 ? 'Rp ' . number_format($totalP2, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-red-600">{{ $totalAdmin ? '-Rp ' . number_format($totalAdmin, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-bold text-emerald-700">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if ($registrations->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
