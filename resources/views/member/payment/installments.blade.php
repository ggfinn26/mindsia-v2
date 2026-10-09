@extends('layouts.dashboard')

@section('title', 'Status Cicilan - MINDSIA')
@section('header_title', 'Status Cicilan')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold font-jakarta text-[color:var(--dash-text-heading)]">Status Cicilan</h2>
        <p class="text-sm text-[color:var(--dash-text-muted)] mt-1">Pantau tagihan dan jadwal pembayaran cicilan program Anda.</p>
    </div>

    @if($registrations->isEmpty())
        <div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-10 flex flex-col items-center justify-center text-center">
            <div class="h-20 w-20 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[#5586DB] text-4xl">receipt_long</span>
            </div>
            <h3 class="text-lg font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-2">Tidak Ada Tagihan Aktif</h3>
            <p class="text-[color:var(--dash-text-muted)] max-w-md mx-auto mb-6">Saat ini Anda tidak memiliki tagihan atau cicilan program yang sedang berjalan.</p>
            <a href="{{ route('member.dashboard') }}" class="px-5 py-2.5 bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] text-[color:var(--dash-text-body)] font-medium rounded-lg hover:bg-[var(--dash-nav-hover-bg)] transition-colors shadow-sm">
                Kembali ke Dashboard
            </a>
        </div>
    @else
        @foreach($registrations as $registration)
            <div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] overflow-hidden">
                <div class="px-6 py-4 border-b border-[color:var(--dash-card-border)] bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-[color:var(--dash-text-heading)]">
                            {{ $registration->program?->program_name ?? $registration->receipt_program_name }}
                        </h3>
                        <p class="text-xs text-[color:var(--dash-text-muted)] mt-0.5">{{ $registration->receipt_payment_plan_label ?? '—' }}</p>
                    </div>
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $registration->status_color }}">
                        {{ $registration->status_label }}
                    </span>
                </div>

                @if($registration->payments->isEmpty())
                    <p class="px-6 py-6 text-sm text-center text-[color:var(--dash-text-faint)]">Belum ada data cicilan.</p>
                @else
                    <div class="divide-y divide-[color:var(--dash-card-border)]">
                        @foreach($registration->payments->sortBy('installment_number') as $payment)
                            <div class="px-6 py-4 flex items-center gap-4">
                                <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                    {{ $payment->payment_status === 'paid' ? 'bg-green-100' : 'bg-[var(--dash-nav-hover-bg)]' }}">
                                    <span class="material-symbols-outlined text-[16px] {{ $payment->payment_status === 'paid' ? 'text-green-600' : 'text-[color:var(--dash-text-faint)]' }}">
                                        {{ $payment->payment_status === 'paid' ? 'check_circle' : 'radio_button_unchecked' }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[color:var(--dash-text-heading)]">
                                        {{ $payment->installment_number === 1 ? 'DP / Cicilan 1' : 'Cicilan ' . $payment->installment_number }}
                                        <span class="ml-2 font-bold text-[#5586DB]">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                    </p>
                                    <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5">
                                        Jatuh tempo: {{ $payment->due_date?->format('d M Y') ?? '—' }}
                                        @if($payment->paid_at)
                                            · Dibayar: {{ $payment->paid_at->format('d M Y') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="shrink-0">
                                    @if($payment->payment_status === 'paid')
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-2.5 py-1 rounded-full">
                                            <span class="material-symbols-outlined text-[13px]">check</span> Terkonfirmasi
                                        </span>
                                    @elseif($payment->due_date && $payment->due_date->isPast())
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-100 px-2.5 py-1 rounded-full">
                                            <span class="material-symbols-outlined text-[13px]">warning</span> Jatuh Tempo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-full">
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
                    <div class="px-6 py-3 border-t border-[color:var(--dash-card-border)] bg-gray-50/50 space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-[color:var(--dash-text-muted)]">Total Tagihan</span>
                            <span class="font-bold text-[color:var(--dash-text-heading)]">Rp {{ number_format($registration->final_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[color:var(--dash-text-muted)]">Sudah Dibayar</span>
                            <span class="text-green-700 font-semibold">Rp {{ number_format($paid, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-[color:var(--dash-card-border)] pt-1">
                            <span class="font-semibold text-[color:var(--dash-text-body)]">Sisa Tagihan</span>
                            <span class="font-bold {{ $sisa > 0 ? 'text-red-600' : 'text-green-700' }}">Rp {{ number_format($sisa, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection
