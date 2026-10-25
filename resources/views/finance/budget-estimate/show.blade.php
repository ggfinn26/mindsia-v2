@extends('layouts.dashboard')

@section('title', 'Detail Estimasi Anggaran')
@section('header_title', 'Finance')

@section('content')
@php
    $statusBadge = [
        'draft'          => 'bg-slate-100 text-slate-700',
        'ops_review'     => 'bg-blue-100 text-blue-800',
        'finance_review' => 'bg-amber-100 text-amber-800',
        'accepted'       => 'bg-emerald-100 text-emerald-800',
        'sent'           => 'bg-purple-100 text-purple-800',
        'inventory_draft'=> 'bg-teal-100 text-teal-800',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{
        deleteModalOpen: false,
        deleteUrl: '',
        deleteItemName: '',
        sendModalOpen: false
     }">
    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('budget-estimates.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Anggaran Cabang
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Keuangan</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">{{ $budgetEstimate->title }}</h1>
            <div class="mt-2 flex items-center gap-2">
                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusBadge[$budgetEstimate->status] ?? 'bg-slate-100 text-slate-700' }}">
                    {{ \App\Support\EnumLabels::get('budget_status', $budgetEstimate->status) }}
                </span>
                <span class="text-xs" style="color: var(--dash-text-muted);">
                    {{ Carbon\Carbon::create(null, $budgetEstimate->period_month)->translatedFormat('F') }} {{ $budgetEstimate->period_year }}
                    &bull; {{ $budgetEstimate->branch?->branch_name ?? '-' }}
                </span>
            </div>
        </div>
        @if ($budgetEstimate->isDraft())
            <div class="flex items-center gap-2">
                <a href="{{ route('budget-estimates.edit', $budgetEstimate) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Edit Anggaran
                </a>
            </div>
        @endif
    </div>

    {{-- Rejection Notes Alert --}}
    @if ($budgetEstimate->rejection_notes)
        <div class="flex items-start gap-3 p-4 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[20px] shrink-0">error</span>
            <div>
                <h2 class="text-sm font-bold">Catatan Penolakan</h2>
                <p class="mt-0.5 text-sm">{{ $budgetEstimate->rejection_notes }}</p>
            </div>
        </div>
    @endif

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Meta Info Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="p-4 rounded-2xl shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-faint);">Cabang</p>
            <p class="mt-1 text-sm font-semibold truncate" style="color: var(--dash-text-heading);">
                {{ $budgetEstimate->branch?->branch_name ?? '-' }}
            </p>
        </div>
        <div class="p-4 rounded-2xl shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-faint);">Periode</p>
            <p class="mt-1 text-sm font-semibold truncate" style="color: var(--dash-text-heading);">
                {{ Carbon\Carbon::create(null, $budgetEstimate->period_month)->translatedFormat('F') }} {{ $budgetEstimate->period_year }}
            </p>
        </div>
        <div class="p-4 rounded-2xl shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-faint);">Total Estimasi</p>
            <p class="mt-1 text-sm font-bold truncate tabular-nums" style="color: var(--dash-primary-text);">
                Rp {{ number_format($budgetEstimate->total_amount, 0, ',', '.') }}
            </p>
        </div>
        <div class="p-4 rounded-2xl shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-faint);">Diajukan Oleh</p>
            <p class="mt-1 text-sm font-semibold truncate" style="color: var(--dash-text-heading);">
                {{ $budgetEstimate->submittedBy?->full_name ?? '-' }}
            </p>
        </div>
    </div>

    {{-- Items Card --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 rounded-t-2xl flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Rincian Item Anggaran</h2>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                  style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                {{ $budgetEstimate->items->count() }} Item
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Item & Catatan</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                            style="color: var(--dash-text-muted);">Qty</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Harga Est.</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Subtotal</th>
                        @if ($budgetEstimate->isDraft())
                            <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse ($budgetEstimate->items as $item)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3">
                                <p class="font-medium text-xs" style="color: var(--dash-text-heading);">{{ $item->item_name }}</p>
                                @if ($item->notes)
                                    <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">{{ $item->notes }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-xs font-semibold" style="color: var(--dash-text-heading);">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-xs" style="color: var(--dash-text-muted);">
                                Rp {{ number_format($item->estimated_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-xs font-bold" style="color: var(--dash-primary-text);">
                                Rp {{ number_format($item->total_price, 0, ',', '.') }}
                            </td>
                            @if ($budgetEstimate->isDraft())
                                <td class="px-4 py-3 text-right">
                                    <button type="button"
                                            @click="deleteUrl = '{{ route('budget-estimate-items.destroy', [$budgetEstimate, $item]) }}'; deleteItemName = '{{ addslashes($item->item_name) }}'; deleteModalOpen = true"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-white transition-opacity hover:opacity-90"
                                            style="background-color: var(--dash-danger);"
                                            title="Hapus Item">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $budgetEstimate->isDraft() ? 5 : 4 }}" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">receipt_long</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada item anggaran.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Tambahkan rincian barang atau kebutuhan melalui form di bawah.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Form Tambah Item Baru --}}
        @if ($budgetEstimate->isDraft())
            @can('finance.budget_estimate.create')
                <div class="p-6 rounded-b-2xl border-t"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg);">
                    <h3 class="text-sm font-semibold mb-4" style="color: var(--dash-text-heading);">Tambah Item Baru</h3>
                    <form method="POST" action="{{ route('budget-estimate-items.store', $budgetEstimate) }}"
                          class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        @csrf
                        <div class="sm:col-span-4">
                            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                                   style="color: var(--dash-text-faint);">Nama Item <span class="text-red-500">*</span></label>
                            <input type="text" name="item_name" required maxlength="255" placeholder="Cth: Tinta Printer"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                                   style="color: var(--dash-text-faint);">Qty <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" required min="1" value="1"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                        </div>
                        <div class="sm:col-span-3">
                            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                                   style="color: var(--dash-text-faint);">Harga Est. (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative" x-data="{
                                    rawValue: '{{ old('estimated_price') }}',
                                    format(val) {
                                        let num = val.toString().replace(/[^0-9]/g, '');
                                        return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                                    }
                                 }">
                                <input type="text" :value="format(rawValue)"
                                       @input="rawValue = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(rawValue)"
                                       required placeholder="0"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                                <input type="hidden" name="estimated_price" x-model="rawValue" />
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                                   style="color: var(--dash-text-faint);">Catatan</label>
                            <input type="text" name="notes" maxlength="255" placeholder="Opsional"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                        </div>
                        <div class="sm:col-span-1">
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center h-[38px] rounded-lg text-white font-semibold transition-opacity hover:opacity-90"
                                    style="background-color: var(--dash-primary);"
                                    title="Tambah Item">
                                <span class="material-symbols-outlined text-[20px]">add</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endcan
        @endif
    </div>

    {{-- Workflow Approval Actions --}}
    <div class="p-5 rounded-2xl flex flex-wrap items-center gap-3"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @if ($budgetEstimate->isDraft())
            @can('finance.budget_estimate.create')
                <form method="POST" action="{{ route('budget-estimates.submit-ops', $budgetEstimate) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Ajukan ke OPS
                    </button>
                </form>
            @endcan
        @elseif ($budgetEstimate->status === 'ops_review')
            @can('finance.budget_estimate.ops_review')
                <form method="POST" action="{{ route('budget-estimates.submit-finance', $budgetEstimate) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">forward</span>
                        Teruskan ke Finance
                    </button>
                </form>
            @endcan
        @elseif ($budgetEstimate->status === 'finance_review')
            @can('finance.budget_estimate.finance_review')
                <form method="POST" action="{{ route('budget-estimates.review-finance', $budgetEstimate) }}"
                      class="flex flex-wrap items-center gap-2 flex-1">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <input type="text" name="rejection_notes" placeholder="Catatan penolakan jika ditolak..." required
                           class="flex-1 min-w-[200px] px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-danger)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'" />
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-danger);">
                        <span class="material-symbols-outlined text-[18px]">cancel</span>
                        Tolak Anggaran
                    </button>
                </form>
                <form method="POST" action="{{ route('budget-estimates.review-finance', $budgetEstimate) }}">
                    @csrf
                    <input type="hidden" name="action" value="accept">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                            style="background-color: #16a34a;">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        Setujui Anggaran
                    </button>
                </form>
            @endcan
        @elseif ($budgetEstimate->status === 'accepted')
            @can('finance.budget_estimate.send')
                <button type="button" @click="sendModalOpen = true"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: #7c3aed;">
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                    Kirim & Buat Draft Inventory
                </button>
            @endcan
        @endif
    </div>

    {{-- Modal Konfirmasi Hapus Item (Custom Alpine.js, ZERO Browser Confirm) --}}
    <div x-show="deleteModalOpen"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;"
         @keydown.escape.window="deleteModalOpen = false">
        <div @click.stop
             class="w-full max-w-md rounded-2xl shadow-xl space-y-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="p-5 rounded-t-2xl flex items-center gap-3 border-b"
                 style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                     style="background-color: color-mix(in srgb, var(--dash-danger) 15%, transparent); color: var(--dash-danger);">
                    <span class="material-symbols-outlined text-[22px]">delete</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold" style="color: var(--dash-text-heading);">Hapus Item Anggaran</h3>
                    <p class="text-xs" style="color: var(--dash-text-muted);">Konfirmasi penghapusan item</p>
                </div>
            </div>
            <div class="px-5 py-2">
                <p class="text-sm" style="color: var(--dash-text-body);">
                    Apakah Anda yakin ingin menghapus item <span class="font-semibold" style="color: var(--dash-text-heading);" x-text="deleteItemName"></span> dari estimasi anggaran ini?
                </p>
            </div>
            <div class="p-4 rounded-b-2xl border-t flex justify-end gap-2.5"
                 style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <button type="button" @click="deleteModalOpen = false"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </button>
                <form :action="deleteUrl" method="POST" class="inline-flex">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-danger);">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Kirim & Buat Draft Inventory (Custom Alpine.js, ZERO Browser Confirm) --}}
    <div x-show="sendModalOpen"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;"
         @keydown.escape.window="sendModalOpen = false">
        <div @click.stop
             class="w-full max-w-md rounded-2xl shadow-xl space-y-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="p-5 rounded-t-2xl flex items-center gap-3 border-b"
                 style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                     style="background-color: color-mix(in srgb, #7c3aed 15%, transparent); color: #7c3aed;">
                    <span class="material-symbols-outlined text-[22px]">inventory_2</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold" style="color: var(--dash-text-heading);">Kirim & Buat Inventory</h3>
                    <p class="text-xs" style="color: var(--dash-text-muted);">Konfirmasi pengiriman anggaran</p>
                </div>
            </div>
            <div class="px-5 py-2">
                <p class="text-sm" style="color: var(--dash-text-body);">
                    Kirim anggaran <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $budgetEstimate->title }}</span> sekarang? Draft inventory akan dibuat secara otomatis.
                </p>
            </div>
            <div class="p-4 rounded-b-2xl border-t flex justify-end gap-2.5"
                 style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <button type="button" @click="sendModalOpen = false"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </button>
                <form method="POST" action="{{ route('budget-estimates.send', $budgetEstimate) }}" class="inline-flex">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                            style="background-color: #7c3aed;">
                        <span class="material-symbols-outlined text-[16px]">send</span>
                        Ya, Kirim Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
