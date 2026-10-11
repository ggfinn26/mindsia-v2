@extends('layouts.dashboard')

@section('title', 'Proses Pembayaran')
@section('header_title', 'Proses Pembayaran')

@section('content')
<div class="max-w-7xl mx-auto space-y-6"
     x-data="{
        showPaymentModal: false,
        paymentActionUrl: '',
        paymentEmployee: '',
        paymentPeriod: '',
        paymentRemaining: 0,
        rawValue: '',
        format(val) {
            let num = (val || '').toString().replace(/[^0-9]/g, '');
            return num ? new Intl.NumberFormat('id-ID').format(num) : '';
        },
        openPaymentModal(url, emp, period, remaining) {
            this.paymentActionUrl = url;
            this.paymentEmployee = emp;
            this.paymentPeriod = period;
            this.paymentRemaining = remaining;
            this.rawValue = remaining.toString();
            this.showPaymentModal = true;
        }
     }">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Gaji & Bonus</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Proses Pembayaran</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Catat pembayaran untuk payroll yang telah difinalisasi. Pembayaran parsial akan tetap muncul sebagai sisa yang perlu diselesaikan.
            </p>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: var(--dash-status-success-bg); border-color: rgba(5, 150, 105, 0.25); color: var(--dash-status-success-text);"
             role="status">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: var(--dash-status-danger-bg); border-color: rgba(220, 38, 38, 0.25); color: var(--dash-status-danger-text);"
             role="alert">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filter Card --}}
    <form method="GET" action="{{ route('payroll.payments.index') }}"
          class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_13rem_auto] md:items-end">
            <div>
                <label for="search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                    Cari payroll
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute inset-y-0 left-3 flex items-center text-[18px]"
                          style="color: var(--dash-text-faint);" aria-hidden="true">search</span>
                    <input id="search" name="search" value="{{ request('search') }}" type="search"
                           placeholder="Nama atau kode pegawai"
                           class="block w-full rounded-lg text-sm py-2 pl-10 pr-3 transition-colors"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); outline: none;"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'">
                </div>
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                    Status pembayaran
                </label>
                <select id="status" name="status"
                        class="block w-full rounded-lg text-sm py-2 px-3 transition-colors"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); outline: none;"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua status</option>
                    @foreach (['unpaid' => 'Belum dibayar', 'partial' => 'Dibayar sebagian', 'paid' => 'Lunas', 'failed' => 'Gagal'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg px-4 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    Terapkan
                </button>
                @if (request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('payroll.payments.index') }}"
                       class="inline-flex min-h-10 items-center justify-center rounded-lg px-4 text-sm font-semibold transition-colors"
                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Payments Table Card --}}
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Payroll siap dibayarkan</h2>
            <p class="mt-0.5 text-xs" style="color: var(--dash-text-muted);">Hanya payroll dari periode berstatus final yang dapat diproses.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Pegawai</th>
                        <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Periode</th>
                        <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Nilai payroll</th>
                        <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Sisa dibayar</th>
                        <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                        <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payrolls as $payroll)
                        @php
                            $totalPaid = (float) $payroll->payments->where('payment_status', 'paid')->sum('amount');
                            $remainingAmount = max(0, (float) $payroll->net_amount - $totalPaid);
                            $periodLabel = \Carbon\Carbon::create($payroll->period->period_year, $payroll->period->period_month, 1)->translatedFormat('F Y');
                        @endphp
                        <tr class="align-top transition-colors"
                            style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-4">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $payroll->employee_name_snapshot }}</p>
                                <p class="mt-0.5 text-xs" style="color: var(--dash-text-muted);">
                                    {{ $payroll->employee_code_snapshot }}@if ($payroll->position_name_snapshot) - {{ $payroll->position_name_snapshot }}@endif
                                </p>
                            </td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">
                                <p class="font-medium">{{ $periodLabel }}</p>
                                <p class="mt-0.5 text-xs" style="color: var(--dash-text-muted);">
                                    Jatuh tempo {{ $payroll->period->pay_date?->translatedFormat('d M Y') ?? 'belum ditentukan' }}
                                </p>
                            </td>
                            <td class="px-5 py-4 text-right font-medium tabular-nums" style="color: var(--dash-text-body);">
                                Rp {{ number_format((float) $payroll->net_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold tabular-nums" style="color: var(--dash-text-heading);">
                                Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($payroll->payment_status === 'paid')
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                                        Lunas
                                    </span>
                                @elseif ($payroll->payment_status === 'partial')
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
                                        Sebagian
                                    </span>
                                @elseif ($payroll->payment_status === 'failed')
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text);">
                                        Gagal
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                                        Belum dibayar
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if ($remainingAmount > 0)
                                    <button type="button"
                                            @click="openPaymentModal('{{ route('payroll.payrolls.payment.store', [$payroll->period, $payroll]) }}', '{{ addslashes($payroll->employee_name_snapshot) }}', '{{ addslashes($periodLabel) }}', {{ $remainingAmount }})"
                                            class="inline-flex min-h-9 items-center gap-1.5 rounded-lg px-3 text-xs font-semibold text-white transition-opacity hover:opacity-90"
                                            style="background-color: var(--dash-primary);">
                                        <span class="material-symbols-outlined text-[16px]">payments</span>
                                        Catat pembayaran
                                    </button>
                                @else
                                    <span class="text-xs font-medium" style="color: var(--dash-text-faint);">Tidak ada sisa</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl" style="color: var(--dash-text-faint);" aria-hidden="true">payments</span>
                                <p class="mt-2 font-semibold" style="color: var(--dash-text-heading);">Tidak ada payroll yang sesuai.</p>
                                <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Finalisasi periode payroll terlebih dahulu agar pembayaran dapat diproses di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payrolls->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Catat Pembayaran --}}
    <div x-show="showPaymentModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showPaymentModal" @click="showPaymentModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="showPaymentModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <form method="POST" :action="paymentActionUrl">
                    @csrf
                    <div class="p-6 rounded-t-2xl space-y-4">
                        <div class="flex items-center justify-between pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
                            <div>
                                <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Catat Pembayaran</h3>
                                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);" x-text="paymentEmployee + ' - ' + paymentPeriod"></p>
                            </div>
                            <button type="button" @click="showPaymentModal = false" class="p-1 rounded-lg transition-colors"
                                    style="color: var(--dash-text-faint);"
                                    onmouseover="this.style.color='var(--dash-text-heading)'"
                                    onmouseout="this.style.color='var(--dash-text-faint)'">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <label class="text-xs font-semibold" style="color: var(--dash-text-heading);">
                                    Jumlah Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <span class="text-xs" style="color: var(--dash-text-muted);">
                                    Sisa: Rp <span x-text="format(paymentRemaining)"></span>
                                </span>
                            </div>
                            <div class="flex rounded-lg overflow-hidden transition-colors" style="border: 1px solid var(--dash-input-border);">
                                <span class="inline-flex items-center px-3 text-xs font-bold"
                                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border-right: 1px solid var(--dash-input-border);">Rp</span>
                                <input type="text" :value="format(rawValue)"
                                       @input="rawValue = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(rawValue)"
                                       required
                                       class="block w-full px-3 py-2 text-sm"
                                       style="background-color: var(--dash-input-bg); color: var(--dash-text-body); outline: none;"
                                       onfocus="this.parentElement.style.borderColor='var(--dash-primary)'"
                                       onblur="this.parentElement.style.borderColor='var(--dash-input-border)'">
                                <input type="hidden" name="amount" :value="rawValue">
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold" style="color: var(--dash-text-heading);">
                                Metode Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_method" required class="block w-full rounded-lg text-sm px-3 py-2 transition-colors"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); outline: none;"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                <option value="bank_transfer">Transfer bank</option>
                                <option value="cash">Tunai</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold" style="color: var(--dash-text-heading);">Referensi Pembayaran</label>
                            <input name="payment_reference" type="text" placeholder="Nomor referensi transfer"
                                   class="block w-full rounded-lg text-sm px-3 py-2 transition-colors"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); outline: none;"
                                   onfocus="this.style.borderColor='var(--dash-primary)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold" style="color: var(--dash-text-heading);">Catatan</label>
                            <textarea name="notes" rows="2" class="block w-full rounded-lg text-sm px-3 py-2 transition-colors"
                                      style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); outline: none;"
                                      onfocus="this.style.borderColor='var(--dash-primary)'"
                                      onblur="this.style.borderColor='var(--dash-input-border)'"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                         style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                        <button type="button" @click="showPaymentModal = false"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
