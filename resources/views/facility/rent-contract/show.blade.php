@extends('layouts.dashboard')

@section('title', 'Detail Kontrak Sewa')
@section('header_title', 'Detail Kontrak Sewa')

@section('content')
@php
    $terminStatusBadges = [
        'paid'    => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'Paid (Lunas)'],
        'unpaid'  => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'dot' => 'bg-slate-400', 'label' => 'Unpaid (Belum Lunas)'],
        'overdue' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500', 'label' => 'Overdue (Jatuh Tempo)'],
    ];

    $contractStatusBadges = [
        'active'     => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'Active'],
        'expired'    => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'dot' => 'bg-slate-400', 'label' => 'Expired'],
        'terminated' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500', 'label' => 'Terminated'],
    ];

    $contractStatus = $contractStatusBadges[$branchRentContract->status] ?? [
        'bg' => 'bg-slate-100',
        'text' => 'text-slate-700',
        'dot' => 'bg-slate-400',
        'label' => ucfirst($branchRentContract->status ?? 'Unknown'),
    ];

    $paidTerminTotal = $branchRentContract->termins->where('status', 'paid')->sum('amount');
    $unpaidTerminTotal = $branchRentContract->termins->whereIn('status', ['unpaid', 'overdue'])->sum('amount');
    $totalSudahBayar = $branchRentContract->down_payment + $paidTerminTotal;
    $rentPeriodLabel = [
        'monthly'  => 'Bulanan (Monthly)',
        'yearly'   => 'Tahunan (Yearly)',
        'one_time' => 'Sekali Bayar (One Time)',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6"
     x-data="{
        showModal: false,
        selectedTerminNumber: 1,
        selectedDueDate: '',
        selectedAmount: '0',
        markPaidUrl: '',
        openMarkPaidModal(terminNumber, dueDate, amount, url) {
            this.selectedTerminNumber = terminNumber;
            this.selectedDueDate = dueDate;
            this.selectedAmount = amount;
            this.markPaidUrl = url;
            this.showModal = true;
        }
     }">

    {{-- Page Header (Pola Detail B3) --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('facility.rent-contracts.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Kontrak Sewa Cabang
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Fasilitas</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">
                Kontrak - {{ $branchRentContract->owner_name }}
            </h1>
            <div class="mt-2 flex flex-wrap items-center gap-3">
                <span class="text-sm font-medium" style="color: var(--dash-text-muted);">
                    {{ $branchRentContract->branch?->branch_name ?? '-' }}
                </span>
                <span class="inline-block w-1 h-1 rounded-full" style="background-color: var(--dash-card-border);"></span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $contractStatus['bg'] }} {{ $contractStatus['text'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $contractStatus['dot'] }}"></span>
                    {{ $contractStatus['label'] }}
                </span>
            </div>
        </div>

        @can('facility.rent_contract.edit')
            <a href="{{ route('facility.rent-contracts.edit', $branchRentContract) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors shrink-0"
               style="color: var(--dash-text-body); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                Edit Kontrak
            </a>
        @endcan
    </div>

    {{-- Flash Message --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Financial Summary Cards (Stat Cards Grid) --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5 sm:gap-4">
        {{-- Total Sewa --}}
        <div class="p-4 rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary);">account_balance_wallet</span>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Nilai Sewa</p>
            </div>
            <p class="font-jakarta text-base sm:text-lg font-bold" style="color: var(--dash-text-heading);">
                Rp {{ number_format($branchRentContract->rent_amount, 0, ',', '.') }}
            </p>
        </div>

        {{-- Down Payment --}}
        <div class="p-4 rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-muted);">savings</span>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Down Payment</p>
            </div>
            <p class="font-jakarta text-base sm:text-lg font-bold" style="color: var(--dash-text-heading);">
                Rp {{ number_format($branchRentContract->down_payment, 0, ',', '.') }}
            </p>
        </div>

        {{-- Sudah Dibayar --}}
        <div class="p-4 rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Sudah Dibayar</p>
            </div>
            <p class="font-jakarta text-base sm:text-lg font-bold text-emerald-700">
                Rp {{ number_format($totalSudahBayar, 0, ',', '.') }}
            </p>
        </div>

        {{-- Sisa Pembayaran --}}
        <div class="p-4 rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-[16px] {{ $unpaidTerminTotal > 0 ? 'text-amber-600' : 'text-slate-400' }}">pending_actions</span>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Sisa Pembayaran</p>
            </div>
            <p class="font-jakarta text-base sm:text-lg font-bold {{ $unpaidTerminTotal > 0 ? 'text-amber-700' : '' }}"
               style="{{ $unpaidTerminTotal === 0.0 ? 'color: var(--dash-text-muted);' : '' }}">
                Rp {{ number_format($unpaidTerminTotal, 0, ',', '.') }}
            </p>
        </div>

        {{-- Per Termin --}}
        <div class="p-4 rounded-2xl col-span-2 lg:col-span-1"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary-text);">calendar_month</span>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Per Termin</p>
            </div>
            <p class="font-jakarta text-base sm:text-lg font-bold" style="color: var(--dash-primary-text);">
                Rp {{ number_format($branchRentContract->terminAmountPerCycle(), 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Contract Detail Info Card --}}
    <div class="rounded-2xl"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Informasi Rinci Kontrak</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Spesifikasi legalitas, kontak pemilik, dan jangka waktu sewa.</p>
        </div>
        <div class="px-6 py-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-6 text-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Nama Pemilik</p>
                    <p class="mt-1 font-semibold" style="color: var(--dash-text-heading);">{{ $branchRentContract->owner_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Nomor Telepon</p>
                    <p class="mt-1 font-medium" style="color: var(--dash-text-body);">
                        {{ $branchRentContract->owner_phone ? '+62 ' . $branchRentContract->owner_phone : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Cabang Properti</p>
                    <p class="mt-1 font-medium" style="color: var(--dash-text-body);">
                        {{ $branchRentContract->branch?->branch_name ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Periode Sewa</p>
                    <p class="mt-1 font-medium" style="color: var(--dash-text-body);">
                        {{ $branchRentContract->start_date?->format('d M Y') ?? '-' }}
                        <span class="mx-1" style="color: var(--dash-text-faint);">-</span>
                        {{ $branchRentContract->end_date?->format('d M Y') ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Skema Periode</p>
                    <p class="mt-1 font-medium" style="color: var(--dash-text-body);">
                        {{ $rentPeriodLabel[$branchRentContract->rent_period] ?? $branchRentContract->rent_period ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Jumlah Termin Cicilan</p>
                    <p class="mt-1 font-semibold" style="color: var(--dash-text-heading);">
                        {{ $branchRentContract->termin_count }} Kali
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Dibuat Oleh</p>
                    <p class="mt-1 font-medium" style="color: var(--dash-text-body);">
                        {{ $branchRentContract->createdBy?->full_name ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Tanggal Dibuat</p>
                    <p class="mt-1 font-medium" style="color: var(--dash-text-body);">
                        {{ $branchRentContract->created_at?->format('d M Y, H:i') ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status Kontrak</p>
                    <p class="mt-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $contractStatus['bg'] }} {{ $contractStatus['text'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $contractStatus['dot'] }}"></span>
                            {{ $contractStatus['label'] }}
                        </span>
                    </p>
                </div>
            </div>

            @if ($branchRentContract->notes)
                <div class="mt-5 pt-4" style="border-top: 1px solid var(--dash-card-border);">
                    <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-muted);">Catatan Kontrak</p>
                    <p class="text-sm whitespace-pre-line leading-relaxed" style="color: var(--dash-text-body);">{{ $branchRentContract->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Termin Schedule Table --}}
    @if ($branchRentContract->termins->isNotEmpty())
        @php
            $paidCount = $branchRentContract->termins->where('status', 'paid')->count();
        @endphp
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <div>
                    <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Jadwal Termin Pembayaran</h2>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Daftar seluruh termin cicilan sewa dan status pelunasan.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                        <span class="material-symbols-outlined text-[14px]" style="color: var(--dash-primary);">payments</span>
                        {{ $paidCount }} / {{ $branchRentContract->termin_count }} Lunas
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Termin</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Jatuh Tempo</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Nominal Cicilan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Tanggal Bayar</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branchRentContract->termins as $termin)
                            @php
                                $terminInfo = $terminStatusBadges[$termin->status] ?? [
                                    'bg' => 'bg-slate-100',
                                    'text' => 'text-slate-700',
                                    'dot' => 'bg-slate-400',
                                    'label' => ucfirst($termin->status),
                                ];
                                $formattedAmount = number_format($termin->amount, 0, ',', '.');
                                $dueDateText = $termin->due_date?->format('d M Y') ?? '-';
                                $markPaidRoute = route('facility.rent-contracts.termins.mark-paid', [$branchRentContract, $termin]);
                            @endphp
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3.5 font-semibold" style="color: var(--dash-text-heading);">
                                    Termin ke-{{ $termin->termin_number }}
                                </td>
                                <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-body);">
                                    {{ $dueDateText }}
                                </td>
                                <td class="px-5 py-3.5 font-semibold" style="color: var(--dash-text-heading);">
                                    Rp {{ $formattedAmount }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $terminInfo['bg'] }} {{ $terminInfo['text'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $terminInfo['dot'] }}"></span>
                                        {{ $terminInfo['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                                    @if ($termin->paid_at)
                                        <div class="font-medium">{{ $termin->paid_at->format('d M Y') }}</div>
                                        <p class="text-xs" style="color: var(--dash-text-muted);">
                                            oleh {{ $termin->paidBy?->full_name ?? '-' }}
                                        </p>
                                    @else
                                        <span style="color: var(--dash-text-faint);">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    @if ($termin->status !== 'paid')
                                        @can('facility.rent_contract.mark_paid')
                                            <button type="button"
                                                    @click="openMarkPaidModal('{{ $termin->termin_number }}', '{{ $dueDateText }}', '{{ $formattedAmount }}', '{{ $markPaidRoute }}')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                                    style="background-color: #059669;">
                                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                                Tandai Lunas
                                            </button>
                                        @endcan
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600">
                                            <span class="material-symbols-outlined text-[14px]">done_all</span>
                                            Terverifikasi
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Custom Alpine.js Modal Konfirmasi Pelunasan (ZERO NATIVE DIALOGS) --}}
    <template x-teleport="body">
        <div x-show="showModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    {{-- Modal Card Container: DILARANG overflow-hidden --}}
                    <div x-show="showModal" @click.away="showModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        {{-- Header modal (rounded-t-2xl) --}}
                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-emerald-600 text-[22px]">payments</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                                        Konfirmasi Pelunasan Termin
                                    </h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Pastikan bukti pembayaran termin sewa cabang telah diterima dan diverifikasi dengan benar.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Body Modal --}}
                        <div class="px-6 py-4 space-y-3">
                            <div class="p-3.5 rounded-xl text-sm"
                                 style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-card-border);">
                                <div class="flex items-center justify-between py-1">
                                    <span style="color: var(--dash-text-muted);">Termin:</span>
                                    <span class="font-semibold" style="color: var(--dash-text-heading);">
                                        Termin ke-<span x-text="selectedTerminNumber"></span>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between py-1">
                                    <span style="color: var(--dash-text-muted);">Jatuh Tempo:</span>
                                    <span class="font-medium" style="color: var(--dash-text-heading);" x-text="selectedDueDate"></span>
                                </div>
                                <div class="flex items-center justify-between py-1 pt-2"
                                     style="border-top: 1px solid var(--dash-card-border);">
                                    <span class="font-semibold" style="color: var(--dash-text-body);">Nominal Bayar:</span>
                                    <span class="font-bold text-base text-emerald-700">
                                        Rp <span x-text="selectedAmount"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Footer modal (rounded-b-2xl) --}}
                        <div class="rounded-b-2xl px-6 py-4 flex items-center justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                                Batal
                            </button>
                            <form method="POST" :action="markPaidUrl" class="m-0">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: #059669;">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                    Ya, Tandai Lunas
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection