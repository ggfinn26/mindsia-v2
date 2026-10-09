@extends('layouts.dashboard')

@section('title', 'Generate Payroll - MINDSIA')
@section('header_title', 'Generate Payroll')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Payroll</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Generate Payroll</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            Pilih periode untuk memproses perhitungan gaji karyawan.
        </p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                Periode siap di-generate
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[22%]">
                    <col class="w-[18%]">
                    <col class="w-[14%]">
                    <col class="w-[10%]">
                    <col class="w-[36%]">
                </colgroup>
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Periode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Tanggal Gajian</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $period)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4 font-semibold" style="color: var(--dash-text-heading);">
                                {{ $months[$period->period_month] }} {{ $period->period_year }}
                            </td>
                            <td class="px-5 py-4 text-sm" style="color: var(--dash-text-body);">
                                {{ $period->pay_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $badgeStyle = match($period->status) {
                                        'finalized' => 'background-color:#f0fdf4;color:#059669;',
                                        'review'    => 'background-color:#fef3c7;color:#d97706;',
                                        default     => 'background-color:var(--dash-nav-hover-bg);color:var(--dash-text-muted);',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                      style="{{ $badgeStyle }}">
                                    {{ ucfirst($period->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm" style="color: var(--dash-text-body);">
                                {{ $period->employee_payrolls_count ?? 0 }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    <a href="{{ route('payroll.periods.show', $period) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                        Lihat Detail
                                    </a>
                                    @if(($period->employee_payrolls_count ?? 0) === 0)
                                        <form method="POST" action="{{ route('payroll.periods.generate', $period) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Generate payroll untuk semua pegawai aktif periode {{ $months[$period->period_month] }} {{ $period->period_year }}?')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                                                    style="background-color: var(--dash-primary);">
                                                <span class="material-symbols-outlined text-[14px]">bolt</span>
                                                Generate
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                              style="background-color: #f0fdf4; color: #059669;">
                                            <span class="material-symbols-outlined text-[12px]">check_circle</span>
                                            Sudah digenerate
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">pending</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
                                    Tidak ada periode yang siap di-generate.
                                </p>
                                <p class="text-sm mt-1" style="color: var(--dash-text-faint);">
                                    Finalize periode terlebih dahulu di halaman
                                    <a href="{{ route('payroll.periods.index') }}"
                                       class="underline" style="color: var(--dash-primary);">Kelola Periode</a>.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
