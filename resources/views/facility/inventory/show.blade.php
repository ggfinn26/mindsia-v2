@extends('layouts.dashboard')

@section('title', 'Detail Inventaris')
@section('header_title', 'Inventaris Cabang')

@section('content')
@php
    $statusBadge = [
        'active'   => 'bg-emerald-100 text-emerald-800',
        'disposed' => 'bg-red-100 text-red-800',
        'inactive' => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]',
    ];
    $conditionBadge = [
        'baik'      => 'bg-emerald-100 text-emerald-800',
        'rusak'     => 'bg-red-100 text-red-800',
        'perbaikan' => 'bg-amber-100 text-amber-800',
    ];
@endphp

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('facility.inventory.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Inventaris Cabang
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $inventoryItem->item_name }}</h1>
            <div class="mt-1 flex items-center gap-3">
                <p class="font-mono text-sm text-[color:var(--dash-text-faint)]">{{ $inventoryItem->item_code }}</p>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$inventoryItem->status] ?? 'bg-[var(--dash-nav-hover-bg)]' }}">{{ ucfirst($inventoryItem->status) }}</span>
                @if ($inventoryItem->condition_status)
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $conditionBadge[$inventoryItem->condition_status] ?? 'bg-[var(--dash-nav-hover-bg)]' }}">{{ ucfirst($inventoryItem->condition_status) }}</span>
                @endif
            </div>
        </div>
        @can('facility.inventory.edit')
            <a href="{{ route('facility.inventory.edit', $inventoryItem) }}" class="inline-flex min-h-10 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">
                <span class="material-symbols-outlined text-[18px]">edit</span> Edit
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Info --}}
    <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 text-sm">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Cabang</p><p class="mt-1 font-medium">{{ $inventoryItem->branch?->branch_name ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Kategori</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $inventoryItem->category ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Tipe</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $inventoryItem->inventory_type ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Jumlah</p><p class="mt-1 font-semibold text-[color:var(--dash-text-heading)]">{{ $inventoryItem->quantity }} {{ $inventoryItem->unit }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Lokasi</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $inventoryItem->location ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Rencana Anggaran</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $inventoryItem->budgetEstimateItem?->item_name ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Tanggal Pembelian</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $inventoryItem->purchase_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Harga Pembelian</p><p class="mt-1 font-semibold text-[color:var(--dash-text-heading)]">{{ $inventoryItem->purchase_price ? 'Rp ' . number_format($inventoryItem->purchase_price, 0, ',', '.') : '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Tanggal Input</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $inventoryItem->input_date?->format('d M Y') ?? '—' }}</p></div>
        </div>
        @if ($inventoryItem->notes)
            <div class="mt-4 border-t border-[color:var(--dash-card-border)] pt-4">
                <p class="text-xs text-[color:var(--dash-text-muted)]">Catatan</p>
                <p class="mt-1 text-[color:var(--dash-text-body)] whitespace-pre-line">{{ $inventoryItem->notes }}</p>
            </div>
        @endif
        @if ($inventoryItem->disposed_at)
            <div class="mt-4 border-t border-red-100 pt-4">
                <p class="text-xs text-red-600 font-semibold">Disposed: {{ $inventoryItem->disposed_at->format('d M Y') }}</p>
                @if ($inventoryItem->disposal_reason)
                    <p class="mt-1 text-sm text-red-700">{{ $inventoryItem->disposal_reason }}</p>
                @endif
            </div>
        @endif
    </div>

    {{-- Adjust Quantity --}}
    @can('facility.inventory.update')
        @if ($inventoryItem->isActive())
        <details class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
            <summary class="cursor-pointer px-5 py-3 text-sm font-bold text-[color:var(--dash-text-heading)] hover:bg-[var(--dash-nav-hover-bg)] select-none">
                <span class="material-symbols-outlined text-[16px] align-middle mr-1">tune</span> Sesuaikan Kuantitas
            </summary>
            <div class="border-t border-[color:var(--dash-card-border)] p-5 space-y-3">
                <form method="POST" action="{{ route('facility.inventory.adjust', $inventoryItem) }}" class="flex items-end gap-3">
                    @csrf
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Perubahan (±)</label>
                        <input type="number" name="quantity" required class="block w-28 border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]" placeholder="-1, +5">
                    </div>
                    <div class="flex-1">
                        <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Alasan</label>
                        <input type="text" name="reason" required class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    </div>
                    <button type="submit" class="inline-flex min-h-9 items-center bg-[var(--dash-primary,#215aac)] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Simpan</button>
                </form>
            </div>
        </details>
        @endif
    @endcan

    {{-- Dispose --}}
    @can('facility.inventory.dispose')
        @if ($inventoryItem->isActive())
        <details class="border border-red-200 bg-[var(--dash-card-bg)]">
            <summary class="cursor-pointer px-5 py-3 text-sm font-bold text-red-700 hover:bg-red-50 select-none">
                <span class="material-symbols-outlined text-[16px] align-middle mr-1">delete</span> Dispose Inventaris
            </summary>
            <div class="border-t border-red-200 p-5 space-y-3">
                <form method="POST" action="{{ route('facility.inventory.dispose', $inventoryItem) }}">
                    @csrf
                    <p class="text-sm text-red-700 mb-3">Inventaris akan ditandai sebagai disposed dan tidak dapat dikembalikan.</p>
                    <div class="flex items-end gap-3">
                        <div class="flex-1">
                            <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Alasan Dispose</label>
                            <input type="text" name="reason" required class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <button type="submit" class="inline-flex min-h-9 items-center bg-red-600 px-3 text-xs font-semibold text-white hover:bg-red-700">Dispose</button>
                    </div>
                </form>
            </div>
        </details>
        @endif
    @endcan

    {{-- History --}}
    @if ($inventoryItem->histories->isNotEmpty())
    <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[color:var(--dash-card-border)] px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Riwayat Perubahan</h3>
        </div>
        <ul class="divide-y divide-[color:var(--dash-card-border)]">
            @foreach ($inventoryItem->histories as $h)
                <li class="px-5 py-3 text-sm flex items-start gap-3">
                    <span class="mt-0.5 text-xs font-mono text-[color:var(--dash-text-faint)] whitespace-nowrap">{{ $h->changed_at?->format('d M H:i') }}</span>
                    <div>
                        <span class="font-semibold text-[color:var(--dash-text-heading)]">{{ $h->change_type ?? 'Perubahan' }}</span>
                        @if ($h->quantity_before !== null || $h->quantity_after !== null)
                            <span class="text-[color:var(--dash-text-muted)]"> qty: {{ $h->quantity_before ?? '—' }} → {{ $h->quantity_after ?? '—' }}</span>
                        @endif
                        @if ($h->condition_before || $h->condition_after)
                            <span class="text-[color:var(--dash-text-muted)]"> kondisi: {{ $h->condition_before ?? '—' }} → {{ $h->condition_after ?? '—' }}</span>
                        @endif
                        @if ($h->location_before || $h->location_after)
                            <span class="text-[color:var(--dash-text-muted)]"> lokasi: {{ $h->location_before ?? '—' }} → {{ $h->location_after ?? '—' }}</span>
                        @endif
                        @if ($h->reason)
                            <span class="text-[color:var(--dash-text-faint)]"> — {{ $h->reason }}</span>
                        @endif
                        <span class="text-[color:var(--dash-text-faint)] text-xs"> oleh {{ $h->changedBy?->full_name }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endsection