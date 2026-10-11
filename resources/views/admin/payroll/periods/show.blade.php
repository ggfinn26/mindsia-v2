@extends('layouts.dashboard')

@section('title', 'Kelola Periode Payroll - Detail - MINDSIA')
@section('header_title', 'Kelola Periode Payroll')

@section('content')
@php
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
        showConfirmModal: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmActionUrl: '',
        confirmMethod: 'POST',
        confirmActionText: 'Lanjutkan',
        confirmIsDanger: false,
        openConfirm(title, message, url, method = 'POST', actionText = 'Lanjutkan', isDanger = false) {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmActionUrl = url;
            this.confirmMethod = method;
            this.confirmActionText = actionText;
            this.confirmIsDanger = isDanger;
            this.showConfirmModal = true;
        }
     }">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
        <div>
            <a href="{{ route('payroll.periods.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span> Kembali ke Kelola Periode Payroll
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Gaji & Bonus</p>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                    style="color: var(--dash-text-heading);">
                    Periode {{ $months[$period->period_month] ?? $period->period_month }} {{ $period->period_year }}
                </h1>
                @if ($period->isFinalized())
                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold"
                          style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                        Finalized
                    </span>
                @elseif ($period->isReview())
                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold"
                          style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
                        <span class="material-symbols-outlined text-[14px]">hourglass_top</span>
                        Review
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold"
                          style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                        <span class="material-symbols-outlined text-[14px]">edit_note</span>
                        Draft
                    </span>
                @endif
            </div>
            <div class="mt-2 flex flex-wrap items-center gap-4 text-xs" style="color: var(--dash-text-muted);">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">event</span>
                    <span>Tanggal Gajian: <strong>{{ $period->pay_date?->format('d M Y') ?? '-' }}</strong></span>
                </div>
                @if ($period->notes)
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">notes</span>
                        <span>Catatan: {{ $period->notes }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Actions Toolbar --}}
        <div class="flex flex-wrap items-center gap-2">
            @if (! $period->isFinalized())
                @can('payroll.period.update')
                    <a href="{{ route('payroll.periods.edit', $period) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                        Edit Periode
                    </a>
                @endcan
            @endif

            @if ($period->isDraft())
                @can('payroll.period.generate')
                    @if ($payrolls->isEmpty())
                        <button type="button"
                                @click="openConfirm('Generate Payroll', 'Generate data penggajian untuk semua pegawai aktif pada periode ini?', '{{ route('payroll.periods.generate', $period) }}', 'POST', 'Generate Sekarang', false)"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">bolt</span>
                            Generate Payroll
                        </button>
                    @endif
                @endcan
                @can('payroll.period.update')
                    <button type="button"
                            @click="openConfirm('Maju ke Status Review', 'Apakah Anda yakin ingin memindahkan status periode ini ke Review? Pastikan data gaji telah diperiksa.', '{{ route('payroll.periods.advance-status', $period) }}', 'PATCH', 'Maju ke Review', false)"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
                            style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text); border: 1px solid color-mix(in srgb, var(--dash-status-warning-text) 30%, transparent);">
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        Maju ke Review
                    </button>
                @endcan
                @can('payroll.period.delete')
                    <button type="button"
                            @click="openConfirm('Hapus Periode', 'Apakah Anda yakin ingin menghapus periode payroll draft ini? Tindakan ini tidak dapat dibatalkan.', '{{ route('payroll.periods.destroy', $period) }}', 'DELETE', 'Hapus Periode', true)"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-danger);">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        Hapus
                    </button>
                @endcan
            @elseif ($period->isReview())
                @can('payroll.period.update')
                    <button type="button"
                            @click="openConfirm('Finalisasi Periode', 'Finalisasi periode payroll ini? Setelah difinalisasi, komponen tidak dapat diubah lagi dan slip gaji dapat diterbitkan.', '{{ route('payroll.periods.advance-status', $period) }}', 'PATCH', 'Finalisasi Periode', false)"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                            style="background-color: #059669;">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        Finalisasi
                    </button>
                @endcan
            @elseif ($period->isFinalized())
                @can('payroll.period.revert')
                    <button type="button"
                            @click="openConfirm('Revert ke Draft', 'Revert periode payroll ini kembali ke status Draft? Pastikan belum ada pembayaran yang tercatat.', '{{ route('payroll.periods.revert', $period) }}', 'PATCH', 'Revert ke Draft', true)"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">undo</span>
                        Revert ke Draft
                    </button>
                @endcan
            @endif
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Summary Cards --}}
    @if ($payrolls->count())
        @php
            $totalNet = $payrolls->sum('net_amount');
            $totalPaid = $payrolls->sum(fn($p) => $p->payments->where('payment_status', 'paid')->sum('amount'));
            $countUnpaid = $payrolls->where('payment_status', 'unpaid')->count();
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl border p-5 shadow-sm"
                 style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider"
                          style="color: var(--dash-text-muted);">Total Pegawai</span>
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">group</span>
                </div>
                <p class="font-jakarta text-2xl font-bold mt-2" style="color: var(--dash-text-heading);">
                    {{ $payrolls->count() }}
                </p>
                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Pegawai diproses</p>
            </div>

            <div class="rounded-2xl border p-5 shadow-sm"
                 style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider"
                          style="color: var(--dash-text-muted);">Total Bersih</span>
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">account_balance_wallet</span>
                </div>
                <p class="font-jakarta text-2xl font-bold mt-2" style="color: var(--dash-text-heading);">
                    Rp {{ number_format($totalNet, 0, ',', '.') }}
                </p>
                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Total take home pay</p>
            </div>

            <div class="rounded-2xl border p-5 shadow-sm"
                 style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider"
                          style="color: var(--dash-text-muted);">Total Dibayar</span>
                    <span class="material-symbols-outlined text-[20px]" style="color: #059669;">payments</span>
                </div>
                <p class="font-jakarta text-2xl font-bold mt-2" style="color: #059669;">
                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                </p>
                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Realisasi pencairan</p>
            </div>

            <div class="rounded-2xl border p-5 shadow-sm"
                 style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider"
                          style="color: var(--dash-text-muted);">Belum Dibayar</span>
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-danger);">pending</span>
                </div>
                <p class="font-jakarta text-2xl font-bold mt-2" style="color: var(--dash-danger);">
                    {{ $countUnpaid }} Pegawai
                </p>
                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Menunggu pembayaran</p>
            </div>
        </div>
    @endif

    {{-- Payroll Table Card --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">payments</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Daftar Payroll Pegawai</h2>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                {{ $payrolls->count() }} Data
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Cabang</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Pendapatan</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Potongan</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Gaji Bersih</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Status Pembayaran</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse ($payrolls as $payroll)
                        <tr class="transition-colors"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-6 py-4">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">
                                    {{ $payroll->employee_name_snapshot }}
                                </p>
                                <p class="text-xs font-mono mt-0.5" style="color: var(--dash-text-muted);">
                                    {{ $payroll->employee_code_snapshot }}
                                </p>
                            </td>
                            <td class="px-6 py-4" style="color: var(--dash-text-body);">
                                {{ $payroll->branch_name_snapshot ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium" style="color: var(--dash-text-heading);">
                                Rp {{ number_format($payroll->total_earnings, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium" style="color: var(--dash-danger);">
                                Rp {{ number_format($payroll->total_deductions, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold" style="color: var(--dash-text-heading);">
                                Rp {{ number_format($payroll->net_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if (in_array($payroll->payment_status, ['paid', 'paid_full']))
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                        Dibayar
                                    </span>
                                @elseif ($payroll->payment_status === 'partial')
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
                                        <span class="material-symbols-outlined text-[14px]">pie_chart</span>
                                        Sebagian
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); color: var(--dash-danger);">
                                        <span class="material-symbols-outlined text-[14px]">cancel</span>
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('payroll.payrolls.show', [$period, $payroll]) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-symbols-outlined text-4xl mb-2"
                                          style="color: var(--dash-text-faint);">payments</span>
                                    <p class="font-semibold" style="color: var(--dash-text-heading);">Payroll belum digenerate.</p>
                                    @if ($period->isDraft())
                                        <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                                            Klik tombol "Generate Payroll" di bagian atas untuk memproses gaji seluruh pegawai aktif.
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Custom Alpine Confirmation Modal --}}
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
                             :style="confirmIsDanger ? 'background-color: color-mix(in srgb, var(--dash-danger) 15%, transparent); color: var(--dash-danger);' : 'background-color: var(--dash-primary-bg); color: var(--dash-primary-text);'">
                            <span class="material-symbols-outlined text-[24px]" x-text="confirmIsDanger ? 'warning' : 'help'"></span>
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
                    <button type="button" @click="$refs.confirmForm.submit()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            :style="confirmIsDanger ? 'background-color: var(--dash-danger);' : 'background-color: var(--dash-primary);'"
                            x-text="confirmActionText">
                    </button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
