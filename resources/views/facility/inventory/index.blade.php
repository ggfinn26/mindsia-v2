@extends('layouts.dashboard')

@section('title', 'Inventaris Cabang')
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

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Fasilitas</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Inventaris Cabang</h1>
        </div>
        @can('facility.inventory.create')
            <a href="{{ route('facility.inventory.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">add</span> Tambah Inventaris
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-hidden border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-nav-hover-bg)] text-left text-xs font-semibold text-[color:var(--dash-text-muted)]">
                    <tr>
                        <th class="px-5 py-3">Kode / Nama</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Tipe</th>
                        <th class="px-5 py-3">Cabang</th>
                        <th class="px-5 py-3">Qty</th>
                        <th class="px-5 py-3">Kondisi</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[color:var(--dash-card-border)]">
                    @php $items = \App\Models\InventoryItem::with('branch')->latest()->paginate(20); @endphp
                    @forelse ($items as $item)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[color:var(--dash-text-heading)]">{{ $item->item_name }}</p>
                                <p class="mt-0.5 font-mono text-xs text-[color:var(--dash-text-faint)]">{{ $item->item_code }}</p>
                            </td>
                            <td class="px-5 py-4 text-[color:var(--dash-text-body)]">{{ $item->category ?? '—' }}</td>
                            <td class="px-5 py-4 text-[color:var(--dash-text-body)]">{{ $item->inventory_type ?? '—' }}</td>
                            <td class="px-5 py-4 text-[color:var(--dash-text-body)]">{{ $item->branch?->branch_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-[color:var(--dash-text-body)]">{{ $item->quantity }} {{ $item->unit }}</td>
                            <td class="px-5 py-4">
                                @if ($item->condition_status)
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $conditionBadge[$item->condition_status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' }}">{{ ucfirst($item->condition_status) }}</span>
                                @else
                                    <span class="text-[color:var(--dash-text-faint)]">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$item->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' }}">{{ ucfirst($item->status) }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('facility.inventory.show', $item) }}" class="inline-flex min-h-9 items-center border border-[color:var(--dash-input-border)] px-3 text-xs font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">inventory_2</span>
                                <p class="mt-2 font-semibold text-[color:var(--dash-text-body)]">Belum ada data inventaris.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($items->hasPages())
            <div class="border-t border-[color:var(--dash-card-border)] px-5 py-4">{{ $items->links() }}</div>
        @endif
    </div>
</div>
@endsection