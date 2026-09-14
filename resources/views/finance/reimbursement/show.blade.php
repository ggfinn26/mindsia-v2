@extends('layouts.dashboard')

@section('title', 'Detail Reimbursement')
@section('header_title', 'Detail Reimbursement')

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

<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('reimbursements.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Reimbursement
            </a>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $reimbursement->business_purpose }}</h2>
            <div class="mt-1 flex items-center gap-3">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$reimbursement->status] ?? 'bg-slate-100' }}">{{ $reimbursement->status }}</span>
                <span class="text-sm text-slate-500">{{ $reimbursement->expense_period_start?->format('d M') }} – {{ $reimbursement->expense_period_end?->format('d M Y') }}</span>
            </div>
        </div>
        <div class="flex gap-2">
            @if ($reimbursement->isDraft())
                <a href="{{ route('reimbursements.edit', $reimbursement) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
                <form method="POST" action="{{ route('reimbursements.submit', $reimbursement) }}">
                    @csrf
                    <button type="submit" class="inline-flex min-h-9 items-center bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Ajukan</button>
                </form>
            @elseif ($reimbursement->status === 'REJECTED')
                <form method="POST" action="{{ route('reimbursements.revert', $reimbursement) }}">
                    @csrf
                    <button type="submit" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Kembalikan ke Draft</button>
                </form>
            @elseif ($reimbursement->status === 'APPROVED')
                @can('finance.reimbursement.pay')
                    <form method="POST" action="{{ route('reimbursements.mark-paid', $reimbursement) }}" onsubmit="return confirm('Tandai reimbursement ini sudah dibayar?')">
                        @csrf
                        <button type="submit" class="inline-flex min-h-9 items-center bg-emerald-600 px-3 text-xs font-semibold text-white hover:bg-emerald-700">Tandai Dibayar</button>
                    </form>
                @endcan
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span><p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Info --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 text-sm border border-slate-200 bg-white p-5">
        <div><p class="text-xs text-slate-500">Pegawai</p><p class="mt-1 font-medium text-slate-900">{{ $reimbursement->employee?->full_name }}</p></div>
        <div><p class="text-xs text-slate-500">Total</p><p class="mt-1 font-jakarta text-lg font-bold text-slate-900">Rp {{ number_format($reimbursement->total_amount, 0, ',', '.') }}</p></div>
        @if ($reimbursement->review_notes)
            <div><p class="text-xs text-slate-500">Catatan review</p><p class="mt-1 text-slate-700">{{ $reimbursement->review_notes }}</p></div>
        @endif
        @if ($reimbursement->rejection_notes)
            <div class="col-span-2 sm:col-span-3"><p class="text-xs font-semibold text-red-700">Alasan ditolak</p><p class="mt-1 text-red-800">{{ $reimbursement->rejection_notes }}</p></div>
        @endif
    </div>

    {{-- Items --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Rincian Pengeluaran</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                <tr>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Deskripsi</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($reimbursement->items as $item)
                    <tr>
                        <td class="px-5 py-3 text-slate-600">{{ $item->expense_date?->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-slate-900">{{ $item->description }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $item->category ?? '—' }}</td>
                        <td class="px-5 py-3 text-right tabular-nums font-semibold text-slate-900">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-6 text-center text-sm text-slate-400">Belum ada item pengeluaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Review Form --}}
    @if ($reimbursement->status === 'PENDING')
        @can('finance.reimbursement.review')
            <div class="border border-slate-200 bg-white p-5 space-y-3">
                <h3 class="font-jakarta text-sm font-bold text-slate-900">Review Reimbursement</h3>
                <form method="POST" action="{{ route('reimbursements.review', $reimbursement) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Catatan review</label>
                        <textarea name="review_notes" rows="2" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]"></textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-red-700">Alasan penolakan <span class="text-slate-400">(jika ditolak)</span></label>
                        <textarea name="rejection_notes" rows="2" class="block w-full border-red-200 text-sm focus:border-red-400 focus:ring-red-400"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" name="action" value="approve" class="inline-flex min-h-9 items-center gap-2 bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700">Setujui</button>
                        <button type="submit" name="action" value="reject" class="inline-flex min-h-9 items-center border border-red-200 px-4 text-sm font-semibold text-red-700 hover:bg-red-50">Tolak</button>
                    </div>
                </form>
            </div>
        @endcan
    @endif
</div>
@endsection
