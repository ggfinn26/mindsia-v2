@extends('layouts.dashboard')

@section('title', 'Reimbursement Saya')
@section('header_title', 'Reimbursement')

@section('content')
@php
    $statusBadge = [
        'DRAFT'    => 'bg-slate-100 text-slate-700',
        'PENDING'  => 'bg-amber-100 text-amber-800',
        'APPROVED' => 'bg-emerald-100 text-emerald-800',
        'REJECTED' => 'bg-red-100 text-red-800',
        'PAID'     => 'bg-blue-100 text-blue-800',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Finance</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Reimbursement Saya</h2>
        </div>
        @can('finance.reimbursement.create')
            <a href="{{ route('reimbursements.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">add</span> Buat Reimbursement
            </a>
        @endcan
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
                        <th class="px-5 py-3">Tujuan</th>
                        <th class="px-5 py-3">Periode Pengeluaran</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reimbursements as $r)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $r->business_purpose }}</td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $r->expense_period_start?->format('d M Y') }} – {{ $r->expense_period_end?->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums font-semibold text-slate-900">Rp {{ number_format($r->total_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$r->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $r->status }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('reimbursements.show', $r) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">receipt</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada reimbursement.</p>
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
