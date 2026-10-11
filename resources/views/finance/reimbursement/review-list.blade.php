@extends('layouts.dashboard')

@section('title', 'Review Reimbursement')
@section('header_title', 'Review Reimbursement')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Finance</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Review Reimbursement</h2>
        <p class="mt-1 text-sm text-slate-600">Reimbursement yang menunggu persetujuan finance.</p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Pegawai</th>
                        <th class="px-5 py-3">Tujuan</th>
                        <th class="px-5 py-3">Periode</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reimbursements as $r)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $r->employee?->full_name }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ $r->business_purpose }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $r->expense_period_start?->format('d M') }} – {{ $r->expense_period_end?->format('d M Y') }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-semibold text-slate-900">Rp {{ number_format($r->total_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('reimbursements.show', $r) }}" class="inline-flex min-h-9 items-center bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">check_circle</span>
                                <p class="mt-2 font-semibold text-slate-700">Tidak ada reimbursement yang perlu direviu.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($reimbursements, 'hasPages') && $reimbursements->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $reimbursements->links() }}</div>
        @endif
    </div>
</div>
@endsection
