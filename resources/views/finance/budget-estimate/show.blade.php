@extends('layouts.dashboard')

@section('title', 'Detail Anggaran')
@section('header_title', 'Detail Anggaran')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $statusBadge = [
        'draft'          => 'bg-slate-100 text-slate-700',
        'ops_review'     => 'bg-blue-100 text-blue-800',
        'finance_review' => 'bg-amber-100 text-amber-800',
        'accepted'       => 'bg-emerald-100 text-emerald-800',
        'sent'           => 'bg-purple-100 text-purple-800',
    ];
@endphp

<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('budget-estimates.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Anggaran Cabang
            </a>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $budgetEstimate->title }}</h2>
            <div class="mt-1 flex items-center gap-3">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$budgetEstimate->status] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ str_replace('_', ' ', ucfirst($budgetEstimate->status)) }}
                </span>
                <span class="text-sm text-slate-500">{{ $months[$budgetEstimate->period_month] }} {{ $budgetEstimate->period_year }}</span>
            </div>
        </div>
        @if ($budgetEstimate->isDraft())
            <a href="{{ route('budget-estimates.edit', $budgetEstimate) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
        @endif
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Summary --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Total Anggaran</p>
            <p class="mt-1 font-jakarta text-xl font-bold text-slate-900">Rp {{ number_format($budgetEstimate->total_amount, 0, ',', '.') }}</p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Diajukan oleh</p>
            <p class="mt-1 text-sm font-medium text-slate-900">{{ $budgetEstimate->submittedBy?->full_name ?? '—' }}</p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Items</p>
            <p class="mt-1 font-jakarta text-2xl font-bold text-slate-900">{{ $budgetEstimate->items->count() }}</p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Cabang</p>
            <p class="mt-1 text-sm font-medium text-slate-900">{{ $budgetEstimate->branch?->branch_name ?? '—' }}</p>
        </div>
    </div>

    {{-- Items --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Rincian Item Anggaran</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                <tr>
                    <th class="px-5 py-3">Item</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3 text-right">Qty</th>
                    <th class="px-5 py-3 text-right">Satuan</th>
                    <th class="px-5 py-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($budgetEstimate->items as $item)
                    <tr>
                        <td class="px-5 py-3 font-medium text-slate-900">{{ $item->item_name }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $item->category ?? '—' }}</td>
                        <td class="px-5 py-3 text-right text-slate-700">{{ $item->quantity }}</td>
                        <td class="px-5 py-3 text-right tabular-nums text-slate-700">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right tabular-nums font-semibold text-slate-900">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-6 text-center text-sm text-slate-400">Belum ada item.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap gap-3">
        @if ($budgetEstimate->isDraft())
            @can('finance.budget_estimate.create')
                <form method="POST" action="{{ route('budget-estimates.submit-ops', $budgetEstimate) }}">
                    @csrf
                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">Ajukan ke OPS</button>
                </form>
            @endcan
        @elseif ($budgetEstimate->status === 'ops_review')
            @can('finance.budget_estimate.ops_review')
                <form method="POST" action="{{ route('budget-estimates.submit-finance', $budgetEstimate) }}">
                    @csrf
                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">Teruskan ke Finance</button>
                </form>
            @endcan
        @elseif ($budgetEstimate->status === 'finance_review')
            @can('finance.budget_estimate.finance_review')
                <form method="POST" action="{{ route('budget-estimates.review-finance', $budgetEstimate) }}" class="flex items-center gap-3">
                    @csrf
                    <input type="hidden" name="action" value="accept">
                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700">Terima</button>
                </form>
                <form method="POST" action="{{ route('budget-estimates.review-finance', $budgetEstimate) }}" class="flex items-center gap-3">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <input type="text" name="rejection_notes" placeholder="Alasan penolakan" required class="border-slate-300 text-sm focus:border-red-400 focus:ring-red-400">
                    <button type="submit" class="inline-flex min-h-10 items-center border border-red-200 px-4 text-sm font-semibold text-red-700 hover:bg-red-50">Kembalikan</button>
                </form>
            @endcan
        @elseif ($budgetEstimate->status === 'accepted')
            @can('finance.budget_estimate.send')
                <form method="POST" action="{{ route('budget-estimates.send', $budgetEstimate) }}" onsubmit="return confirm('Kirim anggaran ini? Draft inventory akan dibuat otomatis.')">
                    @csrf
                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-purple-600 px-4 text-sm font-semibold text-white hover:bg-purple-700">Kirim & Buat Draft Inventory</button>
                </form>
            @endcan
        @endif
    </div>

    @if ($budgetEstimate->rejection_notes)
        <div class="border border-red-100 bg-red-50 px-5 py-4 text-sm">
            <p class="text-xs font-semibold text-red-700">Catatan penolakan</p>
            <p class="mt-1 text-red-800">{{ $budgetEstimate->rejection_notes }}</p>
        </div>
    @endif
</div>
@endsection
