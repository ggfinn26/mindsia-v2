@extends('layouts.dashboard')

@section('title', 'Status Cicilan - MINDSIA')
@section('header_title', 'Status Cicilan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Portal Siswa</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Status Cicilan</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Pantau tagihan dan jadwal pembayaran cicilan program Anda.
        </p>
    </div>

    @if($registrations->isEmpty())
        {{-- Empty State Card --}}
        <div class="rounded-2xl p-12 flex flex-col items-center justify-center text-center shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="h-16 w-16 rounded-full flex items-center justify-center mb-4"
                 style="background-color: var(--dash-status-info-bg);">
                <span class="material-symbols-outlined text-3xl" style="color: var(--dash-primary);">receipt_long</span>
            </div>
            <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Tidak Ada Tagihan Aktif</h3>
            <p class="text-sm max-w-md mx-auto mb-6" style="color: var(--dash-text-muted);">Saat ini Anda tidak memiliki tagihan atau cicilan program yang sedang berjalan.</p>
            <a href="{{ route('member.dashboard') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Dashboard
            </a>
        </div>
    @else
        @foreach($registrations as $registration)
            @php
                $statusBg = match ($registration->payment_status) {
                    'paid_full' => 'var(--dash-status-success-bg)',
                    'installments' => 'var(--dash-status-info-bg)',
                    default => 'var(--dash-status-danger-bg)',
                };
                $statusText = match ($registration->payment_status) {
                    'paid_full' => 'var(--dash-status-success-text)',
                    'installments' => 'var(--dash-status-info-text)',
                    default => 'var(--dash-status-danger-text)',
                };
            @endphp
            <div class="rounded-2xl shadow-sm overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                {{-- Header --}}
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <div>
                        <h3 class="font-bold font-jakarta text-base" style="color: var(--dash-text-heading);">
                            {{ $registration->program?->program_name ?? $registration->receipt_program_name }}
                        </h3>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                            Skema: {{ $registration->receipt_payment_plan_label ?? '-' }}
                        </p>
                    </div>
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold shadow-xs self-start sm:self-auto"
                          style="background-color: {{ $statusBg }}; color: {{ $statusText }}; border: 1px solid var(--dash-card-border);">
                        {{ $registration->status_label }}
                    </span>
                </div>

                @if($registration->payments->isEmpty())
                    <p class="px-6 py-8 text-sm text-center" style="color: var(--dash-text-faint);">Belum ada data cicilan.</p>
                @else
                    <div class="divide-y" style="border-color: var(--dash-card-border);">
                        @foreach($registration->payments->sortBy('installment_number') as $payment)
                            <div class="px-6 py-4 flex items-center gap-4 transition-colors"
                                 onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                 onmouseout="this.style.backgroundColor='transparent'">
                                <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                     style="{{ $payment->payment_status === 'paid' ? 'background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);' : 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-faint); border: 1px solid var(--dash-card-border);' }}">
                                    <span class="material-symbols-outlined text-[18px]">
                                        {{ $payment->payment_status === 'paid' ? 'check_circle' : 'radio_button_unchecked' }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold font-jakarta" style="color: var(--dash-text-heading);">
                                        {{ $payment->installment_number === 1 ? 'DP / Cicilan 1' : 'Cicilan ' . $payment->installment_number }}
                                        <span class="ml-2 font-bold" style="color: var(--dash-primary);">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                    </p>
                                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                        Jatuh tempo: {{ $payment->due_date?->format('d M Y') ?? '-' }}
                                        @if($payment->paid_at)
                                            · Dibayar: {{ $payment->paid_at->format('d M Y') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="shrink-0">
                                    @if($payment->payment_status === 'paid')
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full shadow-xs"
                                              style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid var(--dash-card-border);">
                                            <span class="material-symbols-outlined text-[13px]">check</span> Terkonfirmasi
                                        </span>
                                    @elseif($payment->due_date && $payment->due_date->isPast())
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full shadow-xs"
                                              style="background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text); border: 1px solid var(--dash-card-border);">
                                            <span class="material-symbols-outlined text-[13px]">warning</span> Jatuh Tempo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full shadow-xs"
                                              style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text); border: 1px solid var(--dash-card-border);">
                                            <span class="material-symbols-outlined text-[13px]">schedule</span> Menunggu
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @php
                        $paid = $registration->payments->where('payment_status', 'paid')->sum('amount');
                        $sisa = $registration->final_price - $paid;
                    @endphp
                    <div class="px-6 py-4 space-y-1.5 text-sm"
                         style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                        <div class="flex justify-between">
                            <span style="color: var(--dash-text-muted);">Total Tagihan</span>
                            <span class="font-bold font-jakarta" style="color: var(--dash-text-heading);">Rp {{ number_format($registration->final_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: var(--dash-text-muted);">Sudah Dibayar</span>
                            <span class="font-semibold font-jakarta" style="color: var(--dash-status-success-text);">Rp {{ number_format($paid, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-1.5" style="border-top: 1px solid var(--dash-card-border);">
                            <span class="font-semibold" style="color: var(--dash-text-heading);">Sisa Tagihan</span>
                            <span class="font-bold font-jakarta" style="color: {{ $sisa > 0 ? 'var(--dash-status-danger-text)' : 'var(--dash-status-success-text)' }};">
                                Rp {{ number_format($sisa, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection
