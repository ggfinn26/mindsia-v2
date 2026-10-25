@extends('layouts.dashboard')

@section('title', 'Inventaris Cabang - MINDSIA')
@section('header_title', 'Inventaris Cabang')

@section('content')
@php
    $items = \App\Models\InventoryItem::with('branch')->latest()->paginate(20);

    $statusBadge = [
        'active'   => 'bg-emerald-100 text-emerald-800',
        'disposed' => 'bg-red-100 text-red-800',
        'inactive' => 'bg-slate-100 text-slate-600',
    ];

    $conditionLabels = [
        'GOOD'         => 'Baik',
        'NEEDS_REPAIR' => 'Perlu Perbaikan',
        'DAMAGED'      => 'Rusak',
        'UNUSABLE'     => 'Tidak Dapat Dipakai',
        'baik'         => 'Baik',
        'perbaikan'    => 'Perlu Perbaikan',
        'rusak'        => 'Rusak',
    ];

    $conditionBadges = [
        'GOOD'         => 'bg-emerald-100 text-emerald-800',
        'NEEDS_REPAIR' => 'bg-amber-100 text-amber-800',
        'DAMAGED'      => 'bg-orange-100 text-orange-800',
        'UNUSABLE'     => 'bg-red-100 text-red-800',
        'baik'         => 'bg-emerald-100 text-emerald-800',
        'perbaikan'    => 'bg-amber-100 text-amber-800',
        'rusak'        => 'bg-red-100 text-red-800',
    ];

    $typeLabels = [
        'FIXED_ASSET' => 'Aset Tetap',
        'SUPPLIES'    => 'Perlengkapan',
        'OTHER'       => 'Lainnya',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Fasilitas</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Inventaris Cabang</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Daftar aset fisik, perlengkapan, dan barang inventaris di seluruh cabang.
            </p>
        </div>
        @can('facility.inventory.create')
            <div>
                <a href="{{ route('facility.inventory.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Inventaris
                </a>
            </div>
        @endcan
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Kode / Nama Barang</th>
                        <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Kategori</th>
                        <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Tipe</th>
                        <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Cabang</th>
                        <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Kuantitas</th>
                        <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Kondisi</th>
                        <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse ($items as $item)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $item->item_name }}</p>
                                <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-faint);">{{ $item->item_code }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-sm" style="color: var(--dash-text-body);">
                                {{ $item->category ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-sm" style="color: var(--dash-text-body);">
                                {{ $typeLabels[$item->inventory_type] ?? ($item->inventory_type ?: '-') }}
                            </td>
                            <td class="px-5 py-3.5 text-sm" style="color: var(--dash-text-body);">
                                {{ $item->branch?->branch_name ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-sm font-medium" style="color: var(--dash-text-heading);">
                                {{ $item->quantity }} {{ $item->unit }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($item->condition_status)
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $conditionBadges[$item->condition_status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $conditionLabels[$item->condition_status] ?? \App\Support\EnumLabels::get('inventory_condition', $item->condition_status) }}
                                    </span>
                                @else
                                    <span style="color: var(--dash-text-faint);">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusBadge[$item->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ \App\Support\EnumLabels::get('inventory_item_status', $item->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <a href="{{ route('facility.inventory.show', $item) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">inventory_2</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data inventaris.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Barang inventaris yang ditambahkan akan muncul di daftar ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($items->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection