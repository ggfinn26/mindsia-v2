@extends('layouts.dashboard')

@section('title', 'Detail Reimbursement')
@section('header_title', 'Detail Reimbursement')

@section('content')
@php
    $statusBadge = [
        'DRAFT'          => 'bg-slate-100 text-slate-700',
        'PENDING_REVIEW' => 'bg-amber-100 text-amber-800',
        'APPROVED'       => 'bg-emerald-100 text-emerald-800',
        'REJECTED'       => 'bg-red-100 text-red-800',
        'PAID'           => 'bg-blue-100 text-blue-800',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{
        deleteModalOpen: false,
        deleteUrl: '',
        deleteItemDesc: '',
        payModalOpen: false
     }">
    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('reimbursements.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Reimbursement Saya
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Keuangan</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">{{ $reimbursement->business_purpose }}</h1>
            <div class="mt-2 flex items-center gap-2">
                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusBadge[$reimbursement->status] ?? 'bg-slate-100 text-slate-700' }}">
                    {{ \App\Support\EnumLabels::get('reimbursement_status', $reimbursement->status) }}
                </span>
                <span class="text-xs" style="color: var(--dash-text-muted);">
                    {{ $reimbursement->expense_period_start?->format('d M') }} - {{ $reimbursement->expense_period_end?->format('d M Y') }}
                </span>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if ($reimbursement->isDraft())
                <a href="{{ route('reimbursements.edit', $reimbursement) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Edit
                </a>
                <form method="POST" action="{{ route('reimbursements.submit', $reimbursement) }}" class="inline-flex">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Ajukan
                    </button>
                </form>
            @elseif ($reimbursement->status === 'APPROVED')
                @can('finance.reimbursement.pay')
                    <button type="button" @click="payModalOpen = true"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                            style="background-color: #16a34a;">
                        <span class="material-symbols-outlined text-[18px]">payments</span>
                        Tandai Dibayar
                    </button>
                @endcan
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px] shrink-0">error</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Meta Info Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
        <div class="p-4 rounded-2xl shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-faint);">Pegawai</p>
            <p class="mt-1 text-sm font-semibold truncate" style="color: var(--dash-text-heading);">
                {{ $reimbursement->employee?->full_name ?? '-' }}
            </p>
        </div>
        <div class="p-4 rounded-2xl shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-faint);">Total Reimbursement</p>
            <p class="mt-1 text-sm font-bold truncate tabular-nums" style="color: var(--dash-primary-text);">
                Rp {{ number_format($reimbursement->total_amount, 0, ',', '.') }}
            </p>
        </div>
        <div class="col-span-2 sm:col-span-1 p-4 rounded-2xl shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-faint);">Status Saat Ini</p>
            <p class="mt-1 text-sm font-semibold truncate" style="color: var(--dash-text-heading);">
                {{ \App\Support\EnumLabels::get('reimbursement_status', $reimbursement->status) }}
            </p>
        </div>
    </div>

    {{-- Notes if any --}}
    @if ($reimbursement->review_notes)
        <div class="p-4 rounded-xl text-sm"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Catatan Review</p>
            <p class="text-sm">{{ $reimbursement->review_notes }}</p>
        </div>
    @endif

    @if ($reimbursement->rejection_notes)
        <div class="flex items-start gap-3 p-4 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[20px] shrink-0">error</span>
            <div>
                <h2 class="text-sm font-bold">Alasan Penolakan</h2>
                <p class="mt-0.5 text-sm">{{ $reimbursement->rejection_notes }}</p>
            </div>
        </div>
    @endif

    {{-- Items Card --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 rounded-t-2xl flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Rincian Pengeluaran</h2>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                  style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                {{ $reimbursement->items->count() }} Pengeluaran
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Tanggal & Kategori</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Deskripsi</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Jumlah</th>
                        @if ($reimbursement->isDraft())
                            <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse ($reimbursement->items as $item)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3">
                                <p class="text-xs font-medium" style="color: var(--dash-text-heading);">
                                    {{ $item->expense_date?->format('d M Y') }}
                                </p>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                                    {{ \App\Support\EnumLabels::get('reimbursement_category', $item->category) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs font-medium" style="color: var(--dash-text-body);">{{ $item->description }}</p>
                                @if ($item->notes)
                                    <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">{{ $item->notes }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-xs font-bold" style="color: var(--dash-primary-text);">
                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                            @if ($reimbursement->isDraft())
                                <td class="px-4 py-3 text-right">
                                    <button type="button"
                                            @click="deleteUrl = '{{ route('reimbursement-items.destroy', [$reimbursement, $item]) }}'; deleteItemDesc = '{{ addslashes($item->description) }}'; deleteModalOpen = true"
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
                            <td colspan="{{ $reimbursement->isDraft() ? 4 : 3 }}" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">receipt_long</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada item pengeluaran.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Tambahkan bukti pengeluaran melalui form di bawah.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Form Tambah Pengeluaran Baru --}}
        @if ($reimbursement->isDraft())
            <div class="p-6 rounded-b-2xl border-t"
                 style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg);">
                <h3 class="text-sm font-semibold mb-4" style="color: var(--dash-text-heading);">Tambah Pengeluaran</h3>
                <form method="POST" action="{{ route('reimbursement-items.store', $reimbursement) }}"
                      class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    @csrf
                    <div class="sm:col-span-3">
                        <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                               style="color: var(--dash-text-faint);">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="expense_date" required
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'" />
                    </div>
                    <div class="sm:col-span-3">
                        <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                               style="color: var(--dash-text-faint);">Kategori <span class="text-red-500">*</span></label>
                        <select name="category" required
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Kategori...</option>
                            @foreach (['transport' => 'Transport', 'makan' => 'Makan', 'akomodasi' => 'Akomodasi', 'perlengkapan' => 'Perlengkapan', 'lainnya' => 'Lainnya'] as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-3">
                        <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                               style="color: var(--dash-text-faint);">Deskripsi <span class="text-red-500">*</span></label>
                        <input type="text" name="description" required maxlength="255" placeholder="Detail pengeluaran..."
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                               style="color: var(--dash-text-faint);">Jumlah (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative" x-data="{
                                rawValue: '',
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
                            <input type="hidden" name="amount" x-model="rawValue" />
                        </div>
                    </div>
                    <div class="sm:col-span-1">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center h-[38px] rounded-lg text-white font-semibold transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-primary);"
                                title="Catat Pengeluaran">
                            <span class="material-symbols-outlined text-[20px]">add</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    {{-- Review Form Card --}}
    @if ($reimbursement->status === 'PENDING_REVIEW')
        @can('finance.reimbursement.review')
            <div class="rounded-2xl shadow-sm p-6 space-y-4"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="border-b pb-3" style="border-color: var(--dash-card-border);">
                    <h2 class="text-base font-semibold" style="color: var(--dash-text-heading);">Review Pengajuan Reimbursement</h2>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Pilih untuk menyetujui atau menolak pengajuan klaim ini.</p>
                </div>
                <form method="POST" action="{{ route('reimbursements.review', $reimbursement) }}" class="space-y-4 pt-1">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold" style="color: var(--dash-text-body);">Catatan Review</label>
                        <textarea name="review_notes" rows="2" placeholder="Catatan tambahan persetujuan..."
                                  class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                  style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                  onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                  onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"></textarea>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold" style="color: var(--dash-danger);">
                            Alasan Penolakan <span class="font-normal text-xs" style="color: var(--dash-text-faint);">(Wajib diisi jika memilih tolak)</span>
                        </label>
                        <textarea name="rejection_notes" rows="2" placeholder="Berikan alasan penolakan jika ditolak..."
                                  class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                  style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                  onfocus="this.style.borderColor='var(--dash-danger)'"
                                  onblur="this.style.borderColor='var(--dash-input-border)'"></textarea>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button type="submit" name="action" value="approve"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                                style="background-color: #16a34a;">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            Setujui Pengajuan
                        </button>
                        <button type="submit" name="action" value="reject"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-danger);">
                            <span class="material-symbols-outlined text-[18px]">cancel</span>
                            Tolak Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        @endcan
    @endif

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
                    <h3 class="text-sm font-bold" style="color: var(--dash-text-heading);">Hapus Item Pengeluaran</h3>
                    <p class="text-xs" style="color: var(--dash-text-muted);">Konfirmasi penghapusan</p>
                </div>
            </div>
            <div class="px-5 py-2">
                <p class="text-sm" style="color: var(--dash-text-body);">
                    Apakah Anda yakin ingin menghapus item pengeluaran <span class="font-semibold" style="color: var(--dash-text-heading);" x-text="deleteItemDesc"></span>?
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

    {{-- Modal Konfirmasi Tandai Dibayar (Custom Alpine.js, ZERO Browser Confirm) --}}
    <div x-show="payModalOpen"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;"
         @keydown.escape.window="payModalOpen = false">
        <div @click.stop
             class="w-full max-w-md rounded-2xl shadow-xl space-y-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="p-5 rounded-t-2xl flex items-center gap-3 border-b"
                 style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                     style="background-color: color-mix(in srgb, #16a34a 15%, transparent); color: #16a34a;">
                    <span class="material-symbols-outlined text-[22px]">payments</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold" style="color: var(--dash-text-heading);">Konfirmasi Pembayaran</h3>
                    <p class="text-xs" style="color: var(--dash-text-muted);">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
            <div class="px-5 py-2">
                <p class="text-sm" style="color: var(--dash-text-body);">
                    Tandai reimbursement <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $reimbursement->business_purpose }}</span> sejumlah <span class="font-bold tabular-nums" style="color: var(--dash-primary-text);">Rp {{ number_format($reimbursement->total_amount, 0, ',', '.') }}</span> sebagai sudah dibayar?
                </p>
            </div>
            <div class="p-4 rounded-b-2xl border-t flex justify-end gap-2.5"
                 style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <button type="button" @click="payModalOpen = false"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </button>
                <form method="POST" action="{{ route('reimbursements.mark-paid', $reimbursement) }}" class="inline-flex">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                            style="background-color: #16a34a;">
                        <span class="material-symbols-outlined text-[16px]">check</span>
                        Ya, Tandai Dibayar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
