@extends('layouts.dashboard')

@section('title', 'Generate Payroll')
@section('header_title', 'Generate Payroll')

@section('content')
@php
    $months = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="max-w-7xl mx-auto space-y-6"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmActionText: 'Generate Payroll' }">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Gaji & Bonus</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Generate Payroll</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Pilih periode untuk memproses perhitungan gaji karyawan.
            </p>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: var(--dash-status-success-bg); border-color: rgba(5, 150, 105, 0.25); color: var(--dash-status-success-text);">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: var(--dash-status-danger-bg); border-color: rgba(220, 38, 38, 0.25); color: var(--dash-status-danger-text);">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Period Table Card --}}
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Periode siap di-generate</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Periode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Gajian</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($periods as $period)
                        <tr class="transition-colors"
                            style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-4 font-semibold" style="color: var(--dash-text-heading);">
                                {{ $months[$period->period_month] }} {{ $period->period_year }}
                            </td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">
                                {{ $period->pay_date?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($period->status === 'finalized')
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                                        Finalized
                                    </span>
                                @elseif ($period->status === 'review')
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
                                        Review
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                                        {{ ucfirst($period->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">
                                {{ $period->employee_payrolls_count ?? 0 }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('payroll.periods.show', $period) }}"
                                       class="inline-flex min-h-9 items-center gap-1 rounded-lg px-3 text-xs font-semibold transition-colors"
                                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        Lihat Detail
                                    </a>
                                    @if (($period->employee_payrolls_count ?? 0) === 0)
                                        <button type="button"
                                                @click="confirmTitle = 'Generate Payroll'; confirmMessage = 'Generate payroll untuk semua pegawai aktif periode {{ $months[$period->period_month] }} {{ $period->period_year }}?'; confirmActionUrl = '{{ route('payroll.periods.generate', $period) }}'; confirmActionText = 'Generate'; showConfirmModal = true;"
                                                class="inline-flex min-h-9 items-center gap-1 rounded-lg px-3 text-xs font-semibold text-white transition-opacity hover:opacity-90"
                                                style="background-color: var(--dash-primary);">
                                            <span class="material-symbols-outlined text-[16px]">bolt</span>
                                            Generate
                                        </button>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                                              style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                            Sudah digenerate
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl" style="color: var(--dash-text-faint);">pending</span>
                                <p class="mt-2 font-semibold" style="color: var(--dash-text-heading);">Tidak ada periode yang siap di-generate.</p>
                                <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Finalize periode terlebih dahulu di halaman <a href="{{ route('payroll.periods.index') }}" class="underline font-medium" style="color: var(--dash-primary);">Kelola Periode</a>.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Konfirmasi Generate --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="showConfirmModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                            <span class="material-symbols-outlined text-[24px]">bolt</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showConfirmModal = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="button" @click="$refs.confirmGenerateForm.submit()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);"
                            x-text="confirmActionText">
                    </button>
                </div>
                <form x-ref="confirmGenerateForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
