@extends('layouts.dashboard')

@section('title', 'Riwayat Registrasi - MINDSIA')
@section('header_title', 'Riwayat Registrasi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    @if(session('success'))
        <div class="rounded-xl px-4 py-3 text-sm font-medium"
             style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid var(--dash-card-border);">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl px-4 py-3 text-sm font-medium"
             style="background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text); border: 1px solid var(--dash-card-border);">
            {{ session('error') }}
        </div>
    @endif

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Portal Siswa</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Riwayat Registrasi</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Daftar program yang pernah Anda daftarkan.
            </p>
        </div>
        <a href="{{ route('member.program.browse') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90 shadow-sm self-start sm:self-auto"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[16px]">add</span>
            Daftar Program Baru
        </a>
    </div>

    @if($registrations->isEmpty())
        {{-- Empty State Card --}}
        <div class="rounded-2xl p-12 flex flex-col items-center justify-center text-center shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="h-16 w-16 rounded-full flex items-center justify-center mb-4"
                 style="background-color: var(--dash-status-info-bg);">
                <span class="material-symbols-outlined text-3xl" style="color: var(--dash-primary);">history_edu</span>
            </div>
            <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Belum Ada Riwayat Registrasi</h3>
            <p class="text-sm max-w-md mx-auto mb-6" style="color: var(--dash-text-muted);">Anda belum pernah mendaftar ke program apa pun.</p>
            <a href="{{ route('member.program.browse') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">explore</span>
                Lihat Katalog Program
            </a>
        </div>
    @else
        <div class="rounded-2xl shadow-sm overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Program</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Skema Bayar</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Tagihan</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Daftar</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kwitansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                        @foreach($registrations as $registration)
                            @php
                                $paidPayments = $registration->payments->where('payment_status', 'paid')->sortBy('installment_number');
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
                            <tr class="transition-colors"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-6 py-4 font-semibold font-jakarta" style="color: var(--dash-text-heading);">
                                    {{ $registration->program?->program_name ?? $registration->receipt_program_name }}
                                </td>
                                <td class="px-6 py-4" style="color: var(--dash-text-body);">
                                    {{ $registration->receipt_payment_plan_label ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-semibold font-jakarta" style="color: var(--dash-text-heading);">
                                    Rp {{ number_format($registration->final_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold shadow-xs"
                                          style="background-color: {{ $statusBg }}; color: {{ $statusText }}; border: 1px solid var(--dash-card-border);">
                                        {{ $registration->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4" style="color: var(--dash-text-muted);">
                                    {{ $registration->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($paidPayments->isEmpty())
                                        <span class="text-xs" style="color: var(--dash-text-faint);">-</span>
                                    @else
                                        <div class="flex flex-col gap-1.5">
                                            @foreach($paidPayments as $payment)
                                                @if($payment->telegram_receipt_id)
                                                    <a href="{{ route('member.receipt.download', $payment) }}"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90 w-fit"
                                                       style="background-color: var(--dash-primary);">
                                                        <span class="material-symbols-outlined text-[14px]">download</span>
                                                        Cicilan {{ $payment->installment_number }}
                                                    </a>
                                                @else
                                                    <form method="POST" action="{{ route('member.receipt.generate', $payment) }}" class="m-0">
                                                        @csrf
                                                        <button type="submit"
                                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors w-fit"
                                                                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                            <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                                                            Buat Cicilan {{ $payment->installment_number }}
                                                        </button>
                                                    </form>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
