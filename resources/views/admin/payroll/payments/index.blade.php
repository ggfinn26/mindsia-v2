@extends('layouts.dashboard')

@section('title', 'Proses Pembayaran Payroll')
@section('header_title', 'Proses Pembayaran')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ 
    showPaymentModal: false, 
    paymentUrl: '', 
    employeeName: '', 
    remainingRaw: '0', 
    format(val) { 
        let num = val.toString().replace(/[^0-9]/g, ''); 
        return num ? new Intl.NumberFormat('id-ID').format(num) : ''; 
    } 
}">
    <div>
        <p class="text-sm font-medium text-[var(--dash-primary)]">Payroll final</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Antrean pembayaran gaji</h1>
        <p class="mt-1 max-w-2xl text-sm leading-6 text-[var(--dash-text-body)]">Catat pembayaran untuk payroll yang telah difinalisasi. Pembayaran parsial akan tetap muncul sebagai sisa yang perlu diselesaikan.</p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex gap-3 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <form method="GET" action="{{ route('payroll.payments.index') }}" class="border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-4">
        <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_13rem_auto] md:items-end">
            <div>
                <label for="search" class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Cari payroll</label>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] leading-none" style="color: var(--dash-text-faint);" aria-hidden="true">search</span>
                    <input id="search" name="search" value="{{ request('search') }}" type="text" placeholder="Nama atau kode pegawai"
                           class="block w-full rounded-lg py-2 pr-3 text-sm focus:outline-none"
                           style="padding-left: 2.5rem; background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                </div>
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Status pembayaran</label>
                <select id="status" name="status" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full rounded-lg border-[var(--dash-input-border)] py-2 text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                    <option value="">Semua status</option>
                    @foreach (['unpaid' => 'Belum dibayar', 'partial' => 'Dibayar sebagian', 'paid' => 'Lunas', 'failed' => 'Gagal'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex min-h-10 items-center rounded-lg justify-center bg-[var(--dash-primary)] px-4 text-sm font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:ring-offset-2">Terapkan</button>
                @if (request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('payroll.payments.index') }}" class="inline-flex min-h-10 items-center rounded-lg justify-center border border-[var(--dash-input-border)] px-4 text-sm font-semibold text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] focus:outline-none focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:ring-offset-2">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="overflow-hidden border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[var(--dash-card-border)] px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)]">Payroll siap dibayarkan</h3>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Hanya payroll dari periode berstatus final yang dapat diproses.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-semibold text-[var(--dash-text-muted)]">
                    <tr>
                        <th scope="col" class="px-5 py-3">Pegawai</th>
                        <th scope="col" class="px-5 py-3">Periode</th>
                        <th scope="col" class="px-5 py-3 text-right">Nilai payroll</th>
                        <th scope="col" class="px-5 py-3 text-right">Sisa dibayar</th>
                        <th scope="col" class="px-5 py-3">Status</th>
                        <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)]">
                    @forelse ($payrolls as $payroll)
                        @php
                            $totalPaid = (float) $payroll->payments->where('payment_status', 'paid')->sum('amount');
                            $remainingAmount = max(0, (float) $payroll->net_amount - $totalPaid);
                            $periodLabel = \Carbon\Carbon::create($payroll->period->period_year, $payroll->period->period_month, 1)->translatedFormat('F Y');
                            $statusClasses = match ($payroll->payment_status) {
                                'paid' => 'bg-emerald-100 text-emerald-800',
                                'partial' => 'bg-amber-100 text-amber-800',
                                'failed' => 'bg-red-100 text-red-800',
                                default => 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]',
                            };
                            $statusLabel = match ($payroll->payment_status) {
                                'paid' => 'Lunas',
                                'partial' => 'Sebagian',
                                'failed' => 'Gagal',
                                default => 'Belum dibayar',
                            };
                        @endphp
                        <tr class="align-top hover:bg-[var(--dash-page-bg)]">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[var(--dash-text-heading)]">{{ $payroll->employee_name_snapshot }}</p>
                                <p class="mt-0.5 text-xs text-[var(--dash-text-muted)]">{{ $payroll->employee_code_snapshot }}@if ($payroll->position_name_snapshot) · {{ $payroll->position_name_snapshot }}@endif</p>
                            </td>
                            <td class="px-5 py-4 text-[var(--dash-text-body)]">
                                <p>{{ $periodLabel }}</p>
                                <p class="mt-0.5 text-xs text-[var(--dash-text-muted)]">Jatuh tempo {{ $payroll->period->pay_date?->translatedFormat('d M Y') ?? 'belum ditentukan' }}</p>
                            </td>
                            <td class="px-5 py-4 text-right font-medium tabular-nums text-[var(--dash-text-heading)]">Rp {{ number_format((float) $payroll->net_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right font-semibold tabular-nums text-[var(--dash-text-heading)]">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $statusLabel }}</span></td>
                            <td class="px-5 py-4 text-right">
                                @if ($remainingAmount > 0)
                                    <button type="button" @click="
                                            paymentUrl = '{{ route('payroll.payrolls.payment.store', [$payroll->period, $payroll]) }}';
                                            employeeName = '{{ addslashes($payroll->employee_name_snapshot) }}';
                                            remainingRaw = '{{ $remainingAmount }}';
                                            showPaymentModal = true;
                                        " 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-primary);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">payments</span>Catat
                                    </button>
                                @else
                                    <span class="text-xs font-medium text-[var(--dash-text-muted)]">Tidak ada sisa</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-[var(--dash-text-faint)]" aria-hidden="true">payments</span>
                                <p class="mt-2 font-semibold text-[var(--dash-text-body)]">Tidak ada payroll yang sesuai.</p>
                                <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Finalisasi periode payroll terlebih dahulu agar pembayaran dapat diproses di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payrolls->hasPages())
            <div class="border-t border-[var(--dash-card-border)] px-5 py-4">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>

    {{-- Payment Modal --}}
    <div x-show="showPaymentModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div @click="showPaymentModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>
            <div class="relative z-10 inline-block align-bottom bg-[var(--dash-card-bg)] rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <form method="POST" :action="paymentUrl">
                    @csrf
                    <div class="bg-[var(--dash-card-bg)] px-4 pt-5 pb-4 sm:p-6 sm:pb-4 rounded-t-lg">
                        <div class="flex items-center justify-between mb-4 border-b border-[var(--dash-card-border)] pb-3">
                            <h3 class="text-lg font-bold text-[var(--dash-text-heading)]">Catat Pembayaran</h3>
                            <button type="button" @click="showPaymentModal = false" class="text-[var(--dash-text-muted)] hover:text-[var(--dash-text-heading)]">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-[var(--dash-text-body)]">Pegawai: <strong x-text="employeeName" class="text-[var(--dash-text-heading)]"></strong></p>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-[var(--dash-text-heading)]">Jumlah pembayaran</label>
                                <div class="flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center rounded-l-md border border-r-0 border-[var(--dash-input-border)] bg-[var(--dash-page-bg)] px-3 text-sm font-bold text-[var(--dash-text-muted)]">Rp</span>
                                    <input type="text" :value="format(remainingRaw)" @input="remainingRaw = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(remainingRaw)" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full min-w-0 flex-1 rounded-none rounded-r-md border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                                    <input type="hidden" name="amount" x-model="remainingRaw">
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-[var(--dash-text-heading)]">Metode</label>
                                <select name="payment_method" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] rounded-md text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                                    <option value="bank_transfer">Transfer bank</option>
                                    <option value="cash">Tunai</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-[var(--dash-text-heading)]">Referensi pembayaran</label>
                                <input name="payment_reference" type="text" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full rounded-md border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30" placeholder="Nomor referensi transfer">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-[var(--dash-text-heading)]">Catatan</label>
                                <textarea name="notes" rows="2" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full rounded-md border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[var(--dash-page-bg)] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-[var(--dash-primary)] text-base font-medium text-white hover:opacity-90 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Simpan Pembayaran</button>
                        <button type="button" @click="showPaymentModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-[var(--dash-card-border)] shadow-sm px-4 py-2 bg-[var(--dash-card-bg)] text-base font-medium text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
