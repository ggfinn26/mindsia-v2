@extends('layouts.dashboard')

@section('title', 'Detail Estimasi Anggaran')
@section('header_title', 'Finance')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('budget-estimates.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700 transition-colors">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Detail Estimasi Anggaran</h1>
        </div>
        <div>
            @if ($budgetEstimate->status === 'draft')
                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800">Draft</span>
            @elseif ($budgetEstimate->status === 'ops_review')
                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Review OPS</span>
            @elseif ($budgetEstimate->status === 'finance_review')
                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Review Finance</span>
            @elseif ($budgetEstimate->status === 'accepted')
                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Diterima</span>
            @elseif ($budgetEstimate->status === 'sent')
                <span class="inline-flex items-center rounded-full bg-purple-50 px-2.5 py-0.5 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10">Terkirim</span>
            @elseif ($budgetEstimate->status === 'inventory_draft')
                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">Draft Inventory</span>
            @endif
        </div>
    </div>

    @if ($budgetEstimate->rejection_notes)
        <div class="flex items-start gap-3 rounded-xl border border-red-100 bg-red-50 p-4 shadow-sm">
            <span class="material-symbols-outlined text-red-500">error</span>
            <div>
                <h3 class="text-sm font-semibold text-red-800">Catatan Penolakan</h3>
                <p class="mt-1 text-sm text-red-700">{{ $budgetEstimate->rejection_notes }}</p>
            </div>
        </div>
    @endif

    {{-- Meta Info --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Judul</p>
            <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $budgetEstimate->title }}</p>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Bulan</p>
            <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ Carbon\Carbon::create(null, $budgetEstimate->period_month)->translatedFormat('F') }} {{ $budgetEstimate->period_year }}</p>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Estimasi</p>
            <p class="mt-1.5 text-sm font-semibold text-emerald-600">Rp {{ number_format($budgetEstimate->total_amount, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Cabang</p>
            <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $budgetEstimate->branch?->branch_name ?? '—' }}</p>
        </div>
    </div>

    {{-- Items --}}
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4 flex items-center justify-between">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Rincian Item Anggaran</h3>
            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">{{ $budgetEstimate->items->count() }} Item</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Item</th>
                        <th class="px-6 py-4 text-right">Qty</th>
                        <th class="px-6 py-4 text-right">Harga Est.</th>
                        <th class="px-6 py-4 text-right">Subtotal</th>
                        @if ($budgetEstimate->isDraft()) <th class="px-6 py-4"></th> @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($budgetEstimate->items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-900">{{ $item->item_name }}</p>
                                @if ($item->notes) <p class="mt-0.5 text-xs text-slate-500">{{ $item->notes }}</p> @endif
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-slate-700">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right tabular-nums text-slate-600">Rp {{ number_format($item->estimated_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right tabular-nums font-bold text-emerald-600">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                            @if ($budgetEstimate->isDraft())
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('budget-estimate-items.destroy', [$budgetEstimate, $item]) }}" onsubmit="return confirm('Hapus item ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 p-1.5 transition-colors" title="Hapus">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 mb-3">
                                    <span class="material-symbols-outlined text-slate-400 text-2xl">receipt_long</span>
                                </div>
                                <h3 class="text-sm font-medium text-slate-900">Belum ada item</h3>
                                <p class="mt-1 text-sm text-slate-500">Tambahkan rincian barang/kebutuhan di bawah.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($budgetEstimate->isDraft())
            @can('finance.budget_estimate.create')
            <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-5">
                <p class="mb-4 text-sm font-bold text-slate-800">Tambah Item Baru</p>
                <form method="POST" action="{{ route('budget-estimate-items.store', $budgetEstimate) }}" class="grid grid-cols-12 gap-3 items-end">
                    @csrf
                    <div class="col-span-12 md:col-span-4">
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Nama Item</label>
                        <input type="text" name="item_name" required maxlength="255" placeholder="Cth: Tinta Printer" class="block w-full rounded-lg border-slate-200 bg-white px-3 py-2.5 text-sm placeholder:text-slate-400 focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    </div>
                    <div class="col-span-6 md:col-span-2">
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Qty</label>
                        <input type="number" name="quantity" required min="1" value="1" class="block w-full rounded-lg border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    </div>
                    <div class="col-span-6 md:col-span-3">
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Harga Est.</label>
                        <div class="flex rounded-lg shadow-sm" x-data="{
                                rawValue: '{{ old('estimated_price') }}',
                                format(val) {
                                    let num = val.toString().replace(/[^0-9]/g, '');
                                    return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                                }
                            }">
                            <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-500">Rp</span>
                            <input type="text" :value="format(rawValue)" @input="rawValue = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(rawValue)" required placeholder="0" class="block w-full min-w-0 flex-1 rounded-none rounded-r-lg border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                            <input type="hidden" name="estimated_price" x-model="rawValue">
                        </div>
                    </div>
                    <div class="col-span-12 md:col-span-2">
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Catatan</label>
                        <input type="text" name="notes" maxlength="255" placeholder="Opsional" class="block w-full rounded-lg border-slate-200 bg-white px-3 py-2.5 text-sm placeholder:text-slate-400 focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    </div>
                    <div class="col-span-12 md:col-span-1">
                        <button type="submit" class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-[var(--dash-primary,#215aac)] text-sm font-semibold text-white shadow-sm hover:bg-[#194a91] transition-all">
                            <span class="material-symbols-outlined text-[20px]">add</span>
                        </button>
                    </div>
                </form>
            </div>
            @endcan
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-slate-200">
        @if ($budgetEstimate->isDraft())
            @can('finance.budget_estimate.create')
                <form method="POST" action="{{ route('budget-estimates.submit-ops', $budgetEstimate) }}">
                    @csrf
                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-[var(--dash-primary,#215aac)] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#194a91] transition-all">
                        <span class="material-symbols-outlined text-[18px]">send</span> Ajukan ke OPS
                    </button>
                </form>
            @endcan
        @elseif ($budgetEstimate->status === 'ops_review')
            @can('finance.budget_estimate.ops_review')
                <form method="POST" action="{{ route('budget-estimates.submit-finance', $budgetEstimate) }}">
                    @csrf
                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-[var(--dash-primary,#215aac)] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#194a91] transition-all">
                        <span class="material-symbols-outlined text-[18px]">forward</span> Teruskan ke Finance
                    </button>
                </form>
            @endcan
        @elseif ($budgetEstimate->status === 'finance_review')
            @can('finance.budget_estimate.finance_review')
                <form method="POST" action="{{ route('budget-estimates.review-finance', $budgetEstimate) }}" class="flex items-center gap-3 bg-white p-2 rounded-xl border border-slate-200 shadow-sm w-full sm:w-auto">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <input type="text" name="rejection_notes" placeholder="Catatan penolakan..." required class="block w-full min-w-[200px] rounded-lg border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-red-500 focus:bg-white focus:ring-red-500">
                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors">
                        Tolak
                    </button>
                </form>
                <form method="POST" action="{{ route('budget-estimates.review-finance', $budgetEstimate) }}">
                    @csrf
                    <input type="hidden" name="action" value="accept">
                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-emerald-600 px-6 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-700 transition-all">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span> Setujui Anggaran
                    </button>
                </form>
            @endcan
        @elseif ($budgetEstimate->status === 'accepted')
            @can('finance.budget_estimate.send')
                <form method="POST" action="{{ route('budget-estimates.send', $budgetEstimate) }}" onsubmit="return confirm('Kirim anggaran ini? Draft inventory akan dibuat otomatis.')">
                    @csrf
                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-purple-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-700 transition-all">
                        <span class="material-symbols-outlined text-[18px]">inventory_2</span> Kirim & Buat Draft Inventory
                    </button>
                </form>
            @endcan
        @endif
    </div>
</div>
@endsection
