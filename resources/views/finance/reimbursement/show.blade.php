@extends('layouts.dashboard')

@section('title', 'Detail Reimbursement')
@section('header_title', 'Detail Reimbursement')

@section('content')
@php
    $statusBadge = [
        'DRAFT'          => 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]',
        'PENDING_REVIEW' => 'bg-amber-100 text-amber-800 [.mode-dark_&]:bg-amber-500/10 [.mode-dark_&]:text-amber-400',
        'APPROVED'       => 'bg-emerald-100 text-emerald-800 [.mode-dark_&]:text-emerald-400 [.mode-dark_&]:bg-emerald-50 [.mode-dark_&]:bg-emerald-500/100/10 [.mode-dark_&]:text-emerald-400',
        'REJECTED'       => 'bg-red-100 text-red-800 [.mode-dark_&]:text-red-400 [.mode-dark_&]:bg-red-50 [.mode-dark_&]:bg-red-500/100/10 [.mode-dark_&]:text-red-400',
        'PAID'           => 'bg-blue-100 text-blue-800 [.mode-dark_&]:bg-blue-500/10 [.mode-dark_&]:text-blue-400',
    ];
@endphp

<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('reimbursements.index') }}" class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-[var(--dash-text-muted)] hover:text-[var(--dash-primary)] transition-colors">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Daftar
            </a>
            <h2 class="font-jakarta text-3xl font-bold tracking-tight text-[var(--dash-text-heading)]">{{ $reimbursement->business_purpose }}</h2>
            <div class="mt-2 flex items-center gap-3">
                <span class="inline-flex rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $statusBadge[$reimbursement->status] ?? 'bg-[var(--dash-input-bg)]' }} shadow-sm">{{ $reimbursement->status }}</span>
                <span class="text-sm font-medium text-[var(--dash-text-muted)] flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-[var(--dash-text-faint)]">calendar_month</span>
                    {{ $reimbursement->expense_period_start?->format('d M') }} – {{ $reimbursement->expense_period_end?->format('d M Y') }}
                </span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 sm:mt-6">
            @if ($reimbursement->isDraft())
                <a href="{{ route('reimbursements.edit', $reimbursement) }}" class="inline-flex min-h-[40px] items-center gap-2 rounded-lg border border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] px-4 text-sm font-bold text-[var(--dash-text-body)] shadow-sm hover:bg-[var(--dash-page-bg)] hover:text-[var(--dash-text-heading)] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                </a>
                <form method="POST" action="{{ route('reimbursements.submit', $reimbursement) }}" class="inline-flex">
                    @csrf
                    <button type="submit" class="inline-flex min-h-[40px] items-center gap-2 rounded-lg bg-[var(--dash-primary)] px-4 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">send</span> Ajukan
                    </button>
                </form>
            @elseif ($reimbursement->status === 'APPROVED')
                @can('finance.reimbursement.pay')
                    <div x-data="{ open: false }">
                        <button @click="open = true" class="inline-flex min-h-[40px] items-center gap-2 rounded-lg bg-emerald-600 px-4 text-sm font-bold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">payments</span> Tandai Dibayar
                        </button>

                        <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @keydown.escape.window="open = false" style="display:none">
                            <div @click.stop class="w-full max-w-sm rounded-2xl bg-[var(--dash-card-bg)] p-6 shadow-xl space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                                        <span class="material-symbols-outlined text-emerald-600 text-[20px]">payments</span>
                                    </div>
                                    <div>
                                        <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)]">Konfirmasi Pembayaran</h3>
                                        <p class="text-xs text-[var(--dash-text-muted)]">Tindakan ini tidak dapat dibatalkan.</p>
                                    </div>
                                </div>
                                <p class="text-sm text-[var(--dash-text-body)]">Tandai reimbursement <span class="font-semibold">{{ $reimbursement->business_purpose }}</span> sebagai sudah dibayar?</p>
                                <div class="flex justify-end gap-3 pt-2 border-t border-[var(--dash-card-border)]">
                                    <button @click="open = false" class="inline-flex min-h-[38px] items-center rounded-lg border border-[var(--dash-input-border)] px-4 text-sm font-semibold text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)]">
                                        Batal
                                    </button>
                                    <form method="POST" action="{{ route('reimbursements.mark-paid', $reimbursement) }}" class="inline-flex">
                                        @csrf
                                        <button type="submit" class="inline-flex min-h-[38px] items-center gap-2 rounded-lg bg-emerald-600 px-4 text-sm font-bold text-white hover:bg-emerald-700">
                                            <span class="material-symbols-outlined text-[16px]">check</span> Ya, Tandai Dibayar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 [.mode-dark_&]:border-emerald-500/20 bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 px-4 py-3 text-sm text-emerald-800 [.mode-dark_&]:text-emerald-400">
            <span class="material-symbols-outlined text-[20px]">check_circle</span><p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="border border-red-200 [.mode-dark_&]:border-red-500/20 bg-red-50 [.mode-dark_&]:bg-red-500/10 px-4 py-3 text-sm text-red-800 [.mode-dark_&]:text-red-400">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Info --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 shadow-sm">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Pegawai</p>
            <p class="mt-1 font-medium text-[var(--dash-text-heading)]">{{ $reimbursement->employee?->full_name }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Total</p>
            <p class="mt-1 font-jakarta text-lg font-black text-emerald-600">Rp {{ number_format($reimbursement->total_amount, 0, ',', '.') }}</p>
        </div>
        @if ($reimbursement->review_notes)
            <div class="rounded-lg bg-[var(--dash-page-bg)] p-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Catatan review</p>
                <p class="mt-1 text-sm text-[var(--dash-text-body)]">{{ $reimbursement->review_notes }}</p>
            </div>
        @endif
        @if ($reimbursement->rejection_notes)
            <div class="col-span-2 sm:col-span-3 rounded-lg border border-red-100 [.mode-dark_&]:border-red-500/20 bg-red-50 [.mode-dark_&]:bg-red-500/10 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-red-600">Alasan ditolak</p>
                <p class="mt-1 text-sm font-medium text-red-800 [.mode-dark_&]:text-red-400">{{ $reimbursement->rejection_notes }}</p>
            </div>
        @endif
    </div>

    {{-- Items --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="border-b border-[var(--dash-card-border)] px-6 py-4">
            <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)]">Rincian Pengeluaran</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-bold uppercase tracking-wider text-[var(--dash-text-muted)]">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4 text-right">Jumlah</th>
                        @if ($reimbursement->isDraft()) <th class="px-6 py-4 text-right w-20">Aksi</th> @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)]">
                    @forelse ($reimbursement->items as $item)
                        <tr class="hover:bg-[var(--dash-page-bg)] transition-colors">
                            <td class="px-6 py-4 text-[var(--dash-text-muted)] font-medium">{{ $item->expense_date?->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-semibold text-[var(--dash-text-heading)] uppercase text-[13px] tracking-wide">{{ str_replace('_', ' ', $item->category) }}</td>
                            <td class="px-6 py-4 text-[var(--dash-text-body)] font-medium">{{ $item->description }}
                                @if ($item->notes) <p class="mt-0.5 text-xs text-[var(--dash-text-faint)]">{{ $item->notes }}</p> @endif
                            </td>
                            <td class="px-6 py-4 text-right tabular-nums font-bold text-emerald-600">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            @if ($reimbursement->isDraft())
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('reimbursement-items.destroy', [$reimbursement, $item]) }}" onsubmit="return confirm('Hapus item ini?')" class="inline-flex">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg text-[var(--dash-text-faint)] hover:text-red-600 hover:bg-red-50 [.mode-dark_&]:bg-red-500/10 p-1.5 transition-colors" title="Hapus">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $reimbursement->isDraft() ? 5 : 4 }}" class="px-6 py-12 text-[var(--dash-text-muted)]enter">
                                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[var(--dash-page-bg)] mb-3">
                                    <span class="material-symbols-outlined text-[var(--dash-text-faint)] text-[var(--dash-text-muted)]xl">receipt_long</span>
                                </div>
                                <h3 class="text-sm font-medium text-[var(--dash-text-heading)]">Belum ada item pengeluaran</h3>
                                <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Silakan tambahkan rincian pengeluaran di bawah.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    @if ($reimbursement->isDraft())
        <div class="rounded-xl border border-[var(--dash-card-border)] bg-[var(--dash-page-bg)] px-6 py-5">
            <p class="mb-3 text-xs font-bold uppercase tracking-wider text-[var(--dash-text-body)]">Tambah Pengeluaran</p>
            <form method="POST" action="{{ route('reimbursement-items.store', $reimbursement) }}" class="grid gap-4 md:grid-cols-12 items-end">
                @csrf
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-xs font-bold text-[var(--dash-text-body)]">Tanggal</label>
                    <input type="date" name="expense_date" required class="block w-full rounded-lg border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] px-3 py-2.5 text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-xs font-bold text-[var(--dash-text-body)]">Kategori</label>
                    <select name="category" required class="block w-full rounded-lg border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] px-3 py-2.5 text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] shadow-sm">
                        <option value="">— Pilih —</option>
                        @foreach (['transport' => 'Transport', 'makan' => 'Makan', 'akomodasi' => 'Akomodasi', 'perlengkapan' => 'Perlengkapan', 'lainnya' => 'Lainnya'] as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="mb-1.5 block text-xs font-bold text-[var(--dash-text-body)]">Deskripsi</label>
                    <input type="text" name="description" required maxlength="255" placeholder="Detail pengeluaran..." class="block w-full rounded-lg border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] px-3 py-2.5 text-sm placeholder:text-[var(--dash-text-faint)] focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] shadow-sm">
                </div>
                <div class="md:col-span-3">
                    <label class="mb-1.5 block text-xs font-bold text-[var(--dash-text-body)]">Jumlah (Rp)</label>
                    <div class="flex rounded-lg shadow-sm" x-data="{
                            rawValue: '',
                            format(val) {
                                let num = val.toString().replace(/[^0-9]/g, '');
                                return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                            }
                        }">
                        <span class="inline-flex items-center rounded-l-lg border border-r-0 border-[var(--dash-input-border)] bg-[var(--dash-input-bg)] px-3 text-sm font-bold text-[var(--dash-text-muted)]">Rp</span>
                        <input type="text" :value="format(rawValue)" @input="rawValue = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(rawValue)" required placeholder="0" class="block w-full min-w-0 flex-1 rounded-none rounded-r-lg border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] px-3 py-2.5 text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]">
                        <input type="hidden" name="amount" x-model="rawValue">
                    </div>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="inline-flex min-h-[42px] w-full items-center justify-center gap-2 rounded-lg bg-[var(--dash-primary)] px-4 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all">
                        <span class="material-symbols-outlined text-[18px]">add</span> Catat
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Review Form --}}
    @if ($reimbursement->status === 'PENDING_REVIEW')
        @can('finance.reimbursement.review')
            <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 shadow-sm space-y-4">
                <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)] border-b border-[var(--dash-card-border)] pb-3">Review Reimbursement</h3>
                <form method="POST" action="{{ route('reimbursements.review', $reimbursement) }}" class="space-y-4 pt-1">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-[var(--dash-text-body)]">Catatan review</label>
                        <textarea name="review_notes" rows="2" class="block w-full rounded-lg border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-red-700 [.mode-dark_&]:text-red-400">Alasan penolakan <span class="font-medium text-[var(--dash-text-faint)]">(wajib jika ditolak)</span></label>
                        <textarea name="rejection_notes" rows="2" class="block w-full rounded-lg border-red-200 [.mode-dark_&]:border-red-500/20 bg-red-50 [.mode-dark_&]:bg-red-500/10/50 px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"></textarea>
                    </div>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit" name="action" value="approve" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-emerald-600 px-6 text-sm font-bold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span> Setujui
                        </button>
                        <button type="submit" name="action" value="reject" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg border border-red-200 [.mode-dark_&]:border-red-500/20 bg-[var(--dash-card-bg)] px-6 text-sm font-bold text-red-700 [.mode-dark_&]:text-red-400 shadow-sm hover:bg-red-50 [.mode-dark_&]:bg-red-500/10 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">cancel</span> Tolak
                        </button>
                    </div>
                </form>
            </div>
        @endcan
    @endif
</div>
@endsection
