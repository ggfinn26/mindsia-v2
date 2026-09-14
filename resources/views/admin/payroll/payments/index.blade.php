@extends('layouts.dashboard')

@section('title', 'Proses Pembayaran Payroll')
@section('header_title', 'Proses Pembayaran')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Payroll final</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Antrean pembayaran gaji</h2>
        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Catat pembayaran untuk payroll yang telah difinalisasi. Pembayaran parsial akan tetap muncul sebagai sisa yang perlu diselesaikan.</p>
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

    <form method="GET" action="{{ route('payroll.payments.index') }}" class="border border-slate-200 bg-white p-4">
        <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_13rem_auto] md:items-end">
            <div>
                <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700">Cari payroll</label>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400" aria-hidden="true">search</span>
                    <input id="search" name="search" value="{{ request('search') }}" type="search" placeholder="Nama atau kode pegawai" class="block w-full border-slate-300 py-2 pl-10 pr-3 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">Status pembayaran</label>
                <select id="status" name="status" class="block w-full border-slate-300 py-2 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    <option value="">Semua status</option>
                    @foreach (['unpaid' => 'Belum dibayar', 'partial' => 'Dibayar sebagian', 'paid' => 'Lunas', 'failed' => 'Gagal'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex min-h-10 items-center justify-center bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91] focus:outline-none focus:ring-2 focus:ring-[#215aac] focus:ring-offset-2">Terapkan</button>
                @if (request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('payroll.payments.index') }}" class="inline-flex min-h-10 items-center justify-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#215aac] focus:ring-offset-2">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Payroll siap dibayarkan</h3>
            <p class="mt-1 text-sm text-slate-500">Hanya payroll dari periode berstatus final yang dapat diproses.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3">Pegawai</th>
                        <th scope="col" class="px-5 py-3">Periode</th>
                        <th scope="col" class="px-5 py-3 text-right">Nilai payroll</th>
                        <th scope="col" class="px-5 py-3 text-right">Sisa dibayar</th>
                        <th scope="col" class="px-5 py-3">Status</th>
                        <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payrolls as $payroll)
                        @php
                            $totalPaid = (float) $payroll->payments->where('payment_status', 'paid')->sum('amount');
                            $remainingAmount = max(0, (float) $payroll->net_amount - $totalPaid);
                            $periodLabel = \Carbon\Carbon::create($payroll->period->period_year, $payroll->period->period_month, 1)->translatedFormat('F Y');
                            $statusClasses = match ($payroll->payment_status) {
                                'paid' => 'bg-emerald-100 text-emerald-800',
                                'partial' => 'bg-amber-100 text-amber-800',
                                'failed' => 'bg-red-100 text-red-800',
                                default => 'bg-slate-100 text-slate-700',
                            };
                            $statusLabel = match ($payroll->payment_status) {
                                'paid' => 'Lunas',
                                'partial' => 'Sebagian',
                                'failed' => 'Gagal',
                                default => 'Belum dibayar',
                            };
                        @endphp
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $payroll->employee_name_snapshot }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $payroll->employee_code_snapshot }}@if ($payroll->position_name_snapshot) · {{ $payroll->position_name_snapshot }}@endif</p>
                            </td>
                            <td class="px-5 py-4 text-slate-700">
                                <p>{{ $periodLabel }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">Jatuh tempo {{ $payroll->period->pay_date?->translatedFormat('d M Y') ?? 'belum ditentukan' }}</p>
                            </td>
                            <td class="px-5 py-4 text-right font-medium tabular-nums text-slate-800">Rp {{ number_format((float) $payroll->net_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right font-semibold tabular-nums text-slate-900">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $statusLabel }}</span></td>
                            <td class="px-5 py-4 text-right">
                                @if ($remainingAmount > 0)
                                    <details class="relative inline-block text-left">
                                        <summary class="inline-flex min-h-9 cursor-pointer list-none items-center justify-center bg-[#725c00] px-3 text-xs font-semibold text-white hover:bg-[#5b4900] focus:outline-none focus:ring-2 focus:ring-[#725c00] focus:ring-offset-2">Catat pembayaran</summary>
                                        <div class="absolute right-0 z-10 mt-2 w-80 border border-slate-300 bg-white p-4 shadow-lg">
                                            <form method="POST" action="{{ route('payroll.payrolls.payment.store', [$payroll->period, $payroll]) }}" class="space-y-3">
                                                @csrf
                                                <div>
                                                    <label for="amount-{{ $payroll->id }}" class="mb-1 block text-xs font-semibold text-slate-700">Jumlah pembayaran</label>
                                                    <input id="amount-{{ $payroll->id }}" name="amount" type="number" min="0.01" max="{{ $remainingAmount }}" step="0.01" value="{{ $remainingAmount }}" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                                                </div>
                                                <div>
                                                    <label for="method-{{ $payroll->id }}" class="mb-1 block text-xs font-semibold text-slate-700">Metode</label>
                                                    <select id="method-{{ $payroll->id }}" name="payment_method" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                                                        <option value="bank_transfer">Transfer bank</option>
                                                        <option value="cash">Tunai</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label for="reference-{{ $payroll->id }}" class="mb-1 block text-xs font-semibold text-slate-700">Referensi pembayaran</label>
                                                    <input id="reference-{{ $payroll->id }}" name="payment_reference" type="text" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]" placeholder="Nomor referensi transfer">
                                                </div>
                                                <div>
                                                    <label for="notes-{{ $payroll->id }}" class="mb-1 block text-xs font-semibold text-slate-700">Catatan</label>
                                                    <textarea id="notes-{{ $payroll->id }}" name="notes" rows="2" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]"></textarea>
                                                </div>
                                                <button type="submit" class="inline-flex min-h-9 w-full items-center justify-center bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91] focus:outline-none focus:ring-2 focus:ring-[#215aac] focus:ring-offset-2">Simpan pembayaran</button>
                                            </form>
                                        </div>
                                    </details>
                                @else
                                    <span class="text-xs font-medium text-slate-500">Tidak ada sisa</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300" aria-hidden="true">payments</span>
                                <p class="mt-2 font-semibold text-slate-700">Tidak ada payroll yang sesuai.</p>
                                <p class="mt-1 text-sm text-slate-500">Finalisasi periode payroll terlebih dahulu agar pembayaran dapat diproses di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payrolls->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
