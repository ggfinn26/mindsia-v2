@extends('layouts.dashboard')

@section('title', 'Detail Inventaris - MINDSIA')
@section('header_title', 'Detail Inventaris')

@section('content')
@php
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

    $historyLabels = [
        'created'           => 'Dibuat',
        'updated'           => 'Diperbarui',
        'quantity_adjusted' => 'Penyesuaian Kuantitas',
        'condition_changed' => 'Perubahan Kondisi',
        'location_changed'  => 'Pindah Lokasi',
        'disposed'          => 'Disposed',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6"
     x-data="{ showAdjustModal: false, showDisposeModal: false }">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
            <a href="{{ route('facility.inventory.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span> Kembali ke Inventaris Cabang
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Fasilitas</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">{{ $inventoryItem->item_name }}</h1>

            <div class="mt-2 flex flex-wrap items-center gap-2.5">
                <span class="font-mono text-xs px-2.5 py-1 rounded-md"
                      style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    {{ $inventoryItem->item_code }}
                </span>
                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusBadge[$inventoryItem->status] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ \App\Support\EnumLabels::get('inventory_item_status', $inventoryItem->status) }}
                </span>
                @if ($inventoryItem->condition_status)
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $conditionBadges[$inventoryItem->condition_status] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ $conditionLabels[$inventoryItem->condition_status] ?? \App\Support\EnumLabels::get('inventory_condition', $inventoryItem->condition_status) }}
                    </span>
                @endif
                <span class="text-xs" style="color: var(--dash-text-muted);">
                    {{ $inventoryItem->branch?->branch_name ?: 'Semua Cabang' }}
                </span>
            </div>
        </div>

        {{-- Action Buttons Header --}}
        <div class="flex flex-wrap items-center gap-2">
            @canany(['facility.inventory.update', 'facility.inventory.edit'])
                @if ($inventoryItem->isActive())
                    <button type="button" @click="showAdjustModal = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors shadow-sm"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">tune</span>
                        Sesuaikan Kuantitas
                    </button>
                    <a href="{{ route('facility.inventory.edit', $inventoryItem) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-white shadow-sm transition-opacity hover:opacity-90"
                       style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                        Edit
                    </a>
                @endif
            @endcanany

            @can('facility.inventory.dispose')
                @if ($inventoryItem->isActive())
                    <button type="button" @click="showDisposeModal = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90 shadow-sm"
                            style="background-color: var(--dash-danger, #dc2626);">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        Dispose
                    </button>
                @endif
            @endcan
        </div>
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

    {{-- Banner jika sudah Disposed --}}
    @if ($inventoryItem->disposed_at)
        <div class="rounded-2xl p-5 border"
             style="background-color: #fef2f2; border-color: #fecaca;">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                     style="background-color: #fee2e2; color: #dc2626;">
                    <span class="material-symbols-outlined text-[22px]">delete_forever</span>
                </div>
                <div class="space-y-1">
                    <h3 class="font-jakarta text-sm font-bold" style="color: #991b1b;">
                        Inventaris Telah Di-dispose
                    </h3>
                    <p class="text-xs" style="color: #b91c1c;">
                        Tanggal Disposal: {{ $inventoryItem->disposed_at->format('d M Y, H:i') }}
                    </p>
                    @if ($inventoryItem->disposal_reason)
                        <p class="text-sm font-medium pt-1" style="color: #7f1d1d;">
                            Alasan: {{ $inventoryItem->disposal_reason }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Card Informasi Rinci --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">info</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Rinci Inventaris</h2>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md"
                  style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                {{ $typeLabels[$inventoryItem->inventory_type] ?? ($inventoryItem->inventory_type ?: 'Umum') }}
            </span>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--dash-text-muted);">Cabang</p>
                    <p class="mt-1 text-sm font-semibold" style="color: var(--dash-text-heading);">
                        {{ $inventoryItem->branch?->branch_name ?: '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--dash-text-muted);">Kategori</p>
                    <p class="mt-1 text-sm font-semibold" style="color: var(--dash-text-heading);">
                        {{ $inventoryItem->category ?: '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--dash-text-muted);">Kuantitas</p>
                    <p class="mt-1 text-base font-bold" style="color: var(--dash-primary-text);">
                        {{ $inventoryItem->quantity }} <span class="text-xs font-medium" style="color: var(--dash-text-body);">{{ $inventoryItem->unit }}</span>
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--dash-text-muted);">Lokasi Penyimpanan</p>
                    <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-heading);">
                        {{ $inventoryItem->location ?: '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--dash-text-muted);">Kondisi Fisik</p>
                    <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-heading);">
                        {{ $conditionLabels[$inventoryItem->condition_status] ?? ($inventoryItem->condition_status ?: '-') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--dash-text-muted);">Rencana Anggaran Terkait</p>
                    <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-heading);">
                        {{ $inventoryItem->budgetEstimateItem?->item_name ?: '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Pembelian</p>
                    <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">
                        {{ $inventoryItem->purchase_date?->format('d M Y') ?: '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--dash-text-muted);">Harga Pembelian</p>
                    <p class="mt-1 text-sm font-semibold" style="color: var(--dash-text-heading);">
                        {{ $inventoryItem->purchase_price !== null ? 'Rp ' . number_format($inventoryItem->purchase_price, 0, ',', '.') : '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Input</p>
                    <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">
                        {{ $inventoryItem->input_date?->format('d M Y') ?: '-' }}
                    </p>
                </div>
            </div>

            @if ($inventoryItem->notes)
                <div class="mt-6 pt-5" style="border-top: 1px solid var(--dash-card-border);">
                    <p class="text-xs font-medium uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">Catatan Khusus</p>
                    <div class="p-4 rounded-xl text-sm whitespace-pre-line leading-relaxed"
                         style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);">
                        {{ $inventoryItem->notes }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Card Riwayat Perubahan --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">history</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Riwayat Perubahan</h2>
            </div>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                  style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                {{ $inventoryItem->histories->count() }} Catatan
            </span>
        </div>

        <div class="p-6">
            @if ($inventoryItem->histories->isNotEmpty())
                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5"
                     style="before:background-color: var(--dash-card-border);">
                    @foreach ($inventoryItem->histories as $history)
                        <div class="relative flex items-start gap-3">
                            <div class="absolute -left-6 top-1 w-4 h-4 rounded-full border-2"
                                 style="background-color: var(--dash-card-bg); border-color: var(--dash-primary);"></div>
                            <div class="flex-1 p-4 rounded-xl"
                                 style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md"
                                              style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                                            {{ $historyLabels[$history->change_type] ?? \App\Support\EnumLabels::humanize($history->change_type ?: 'Perubahan') }}
                                        </span>
                                        <span class="text-xs" style="color: var(--dash-text-muted);">
                                            oleh <strong style="color: var(--dash-text-heading);">{{ $history->changedBy?->full_name ?: 'Sistem' }}</strong>
                                        </span>
                                    </div>
                                    <span class="font-mono text-xs" style="color: var(--dash-text-faint);">
                                        {{ $history->changed_at?->format('d M Y, H:i') ?: '-' }}
                                    </span>
                                </div>

                                <div class="text-sm space-y-1" style="color: var(--dash-text-body);">
                                    @if ($history->quantity_before !== null || $history->quantity_after !== null)
                                        <p>
                                            Kuantitas: <span style="color: var(--dash-text-muted);">{{ $history->quantity_before ?? '-' }}</span>
                                            <span class="material-symbols-outlined text-[14px] align-middle mx-1">arrow_forward</span>
                                            <strong style="color: var(--dash-text-heading);">{{ $history->quantity_after ?? '-' }} {{ $inventoryItem->unit }}</strong>
                                        </p>
                                    @endif

                                    @if ($history->condition_before || $history->condition_after)
                                        <p>
                                            Kondisi: <span style="color: var(--dash-text-muted);">{{ $conditionLabels[$history->condition_before] ?? ($history->condition_before ?: '-') }}</span>
                                            <span class="material-symbols-outlined text-[14px] align-middle mx-1">arrow_forward</span>
                                            <strong style="color: var(--dash-text-heading);">{{ $conditionLabels[$history->condition_after] ?? ($history->condition_after ?: '-') }}</strong>
                                        </p>
                                    @endif

                                    @if ($history->location_before || $history->location_after)
                                        <p>
                                            Lokasi: <span style="color: var(--dash-text-muted);">{{ $history->location_before ?: '-' }}</span>
                                            <span class="material-symbols-outlined text-[14px] align-middle mx-1">arrow_forward</span>
                                            <strong style="color: var(--dash-text-heading);">{{ $history->location_after ?: '-' }}</strong>
                                        </p>
                                    @endif

                                    @if ($history->reason)
                                        <p class="pt-1.5 text-xs italic" style="color: var(--dash-text-muted);">
                                            Alasan: "{{ $history->reason }}"
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center">
                    <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                          style="color: var(--dash-text-faint);">history</span>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada riwayat perubahan.</p>
                    <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Setiap penyesuaian kuantitas, pembaruan status, atau mutasi akan terekam di sini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL 1: Adjust Quantity --}}
    <template x-teleport="body">
        <div x-show="showAdjustModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="showAdjustModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showAdjustModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showAdjustModal" @click.away="showAdjustModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        {{-- Header modal (rounded-t-2xl) --}}
                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                     style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                                    <span class="material-symbols-outlined text-[22px]">tune</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                                        Sesuaikan Kuantitas
                                    </h3>
                                    <p class="mt-0.5 text-xs" style="color: var(--dash-text-muted);">
                                        Perbarui total stok inventaris barang {{ $inventoryItem->item_name }}.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Form Body --}}
                        <form method="POST" action="{{ route('facility.inventory.adjust', $inventoryItem) }}">
                            @csrf
                            <div class="p-6 space-y-4">
                                <div class="p-3.5 rounded-xl flex items-center justify-between"
                                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                    <span class="text-xs font-semibold" style="color: var(--dash-text-muted);">Kuantitas Saat Ini</span>
                                    <span class="text-sm font-bold" style="color: var(--dash-text-heading);">
                                        {{ $inventoryItem->quantity }} {{ $inventoryItem->unit }}
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                                        Kuantitas Baru <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="quantity" required min="0" value="{{ $inventoryItem->quantity }}"
                                           placeholder="Masukkan jumlah baru"
                                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                                        Alasan Penyesuaian <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="reason" rows="2.5" required
                                              placeholder="Contoh: Pengadaan unit tambahan, koreksi stok opname, dll."
                                              class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                              onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                              onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"></textarea>
                                </div>
                            </div>

                            {{-- Footer modal (rounded-b-2xl) --}}
                            <div class="rounded-b-2xl px-6 py-4 flex items-center justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showAdjustModal = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">save</span>
                                    Simpan Kuantitas
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL 2: Dispose Inventory --}}
    <template x-teleport="body">
        <div x-show="showDisposeModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="showDisposeModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showDisposeModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showDisposeModal" @click.away="showDisposeModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        {{-- Header modal (rounded-t-2xl) --}}
                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                     style="background-color: #fee2e2; color: #dc2626;">
                                    <span class="material-symbols-outlined text-[22px]">warning</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                                        Dispose Inventaris?
                                    </h3>
                                    <p class="mt-0.5 text-xs" style="color: var(--dash-text-muted);">
                                        Aset akan ditandai sebagai disposed dan tidak dapat diaktifkan kembali.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Form Body --}}
                        <form method="POST" action="{{ route('facility.inventory.dispose', $inventoryItem) }}">
                            @csrf
                            <div class="p-6 space-y-4">
                                <div class="p-3.5 rounded-xl text-xs leading-relaxed"
                                     style="background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c;">
                                    <strong>Peringatan Penting:</strong> Barang inventaris {{ $inventoryItem->item_name }} ({{ $inventoryItem->item_code }}) akan dinonaktifkan dari sistem secara permanen.
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                                        Alasan Disposal <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="reason" rows="3" required
                                              placeholder="Contoh: Barang rusak berat tidak dapat diperbaiki, masa pakai habis, hilang, dll."
                                              class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                              onfocus="this.style.borderColor='var(--dash-danger, #dc2626)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, #dc2626 20%, transparent)'"
                                              onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"></textarea>
                                </div>
                            </div>

                            {{-- Footer modal (rounded-b-2xl) --}}
                            <div class="rounded-b-2xl px-6 py-4 flex items-center justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showDisposeModal = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                    Dispose Inventaris
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection