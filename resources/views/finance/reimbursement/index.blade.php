@extends('layouts.dashboard')

@section('title', 'Reimbursement Saya')
@section('header_title', 'Reimbursement')

@section('content')
@php
    $statusBadge = [
        'DRAFT'    => 'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-500/10',
        'PENDING'  => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20',
        'APPROVED' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20',
        'REJECTED' => 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10',
        'PAID'     => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500">Finance</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Reimbursement Saya</h2>
        </div>
        @can('finance.reimbursement.create')
            <a href="{{ route('reimbursements.create') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-[#215aac] px-5 text-sm font-semibold text-white shadow-sm hover:bg-[#194a91] transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span> Buat Reimbursement
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Tujuan</th>
                        <th class="px-6 py-4">Periode Pengeluaran</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($reimbursements as $r)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $r->business_purpose }}</td>
                            <td class="px-6 py-4 font-medium text-slate-600">
                                {{ $r->expense_period_start?->format('d M Y') }} – {{ $r->expense_period_end?->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right tabular-nums font-bold text-emerald-600">Rp {{ number_format($r->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider {{ $statusBadge[$r->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $r->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('reimbursements.show', $r) }}" class="inline-flex min-h-9 items-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-colors">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 mb-3">
                                    <span class="material-symbols-outlined text-slate-400 text-2xl">receipt</span>
                                </div>
                                <h3 class="text-sm font-medium text-slate-900">Belum ada reimbursement</h3>
                                <p class="mt-1 text-sm text-slate-500">Klik "Buat Reimbursement" untuk mulai mengajukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($reimbursements, 'hasPages') && $reimbursements->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-4">{{ $reimbursements->links() }}</div>
        @endif
    </div>
</div>
@endsection
