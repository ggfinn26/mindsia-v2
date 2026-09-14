@extends('layouts.dashboard')

@section('title', 'Financial Statement Marketing')
@section('header_title', 'Financial Statement')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Marketing</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Financial Statement Marketing</h2>
        <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-600">Data pembayaran member per program. Read-only — menampilkan status cicilan, DP, dan kewajiban pembayaran.</p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex gap-3 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <form method="GET" class="flex flex-wrap gap-3 border border-slate-200 bg-white p-4">
        @can('marketing.member_payment_statement.view_branch')
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-700">Cabang</label>
                <select name="branch_id" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    <option value="">Semua cabang</option>
                    @foreach (\App\Models\Branch::where('is_active', true)->orderBy('branch_name')->get() as $branch)
                        <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
        @endcan
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Urutkan</label>
            <select name="sort" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="member_name" @selected($sort === 'member_name')>Nama member</option>
                <option value="institution" @selected($sort === 'institution')>Institusi</option>
                <option value="payment_status" @selected($sort === 'payment_status')>Status pembayaran</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Arah</label>
            <select name="direction" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="asc" @selected($direction === 'asc')>Naik</option>
                <option value="desc" @selected($direction === 'desc')>Turun</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="inline-flex min-h-9 items-center bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Terapkan</button>
        </div>
    </form>

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4 flex items-center justify-between">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Data pembayaran member</h3>
            <span class="text-xs font-medium text-slate-500">{{ $registrations->total() }} pendaftaran</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nama Member</th>
                        <th class="px-5 py-3">Institusi</th>
                        <th class="px-5 py-3">Program</th>
                        <th class="px-5 py-3">WhatsApp</th>
                        <th class="px-5 py-3 text-right">DP</th>
                        <th class="px-5 py-3 text-center">Cicilan</th>
                        <th class="px-5 py-3 text-right">Total Dibayar</th>
                        <th class="px-5 py-3 text-right">Sisa</th>
                        <th class="px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($registrations as $registration)
                        @php
                            $member = $registration->memberData;
                            $totalPaid = $registration->payments->where('payment_status', 'paid')->sum('amount');
                            $remaining = max(0, ($registration->final_price ?? 0) - $totalPaid);
                            $totalInstallments = $registration->payments->count();
                            $paidInstallments = $registration->payments->where('payment_status', 'paid')->count();

                            $statusClasses = match ($registration->payment_status) {
                                'paid', 'lunas' => 'bg-emerald-100 text-emerald-800',
                                'partial', 'installment' => 'bg-amber-100 text-amber-800',
                                default => 'bg-slate-100 text-slate-700',
                            };
                            $statusLabel = match ($registration->payment_status) {
                                'paid', 'lunas' => 'Lunas',
                                'partial', 'installment' => 'Cicil',
                                default => ucfirst($registration->payment_status),
                            };

                            $dp = $registration->payments->where('installment_number', 1)->first();
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $member?->full_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $member?->institution?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $registration->program?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $member?->whatsapp_number ?? '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-slate-700">
                                @if ($dp && $dp->payment_status === 'paid')
                                    Rp {{ number_format($dp->amount, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center tabular-nums text-slate-700">
                                {{ $paidInstallments }}/{{ $totalInstallments }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums font-semibold text-slate-900">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-semibold {{ $remaining > 0 ? 'text-red-700' : 'text-slate-500' }}">Rp {{ number_format($remaining, 0, ',', '.') }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">receipt_long</span>
                                <p class="mt-2 font-semibold text-slate-700">Tidak ada data pembayaran.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($registrations->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
