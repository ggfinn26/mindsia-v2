@extends('layouts.dashboard')

@section('title', 'Biaya Bulanan Cabang')
@section('header_title', 'Finance')

@section('content')
@php
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
        deleteModalOpen: false,
        deleteUrl: '',
        deleteDesc: '',
        lockModalOpen: false,
        unlockModalOpen: false
     }">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Keuangan</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Biaya Operasional Cabang</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola dan pantau pengeluaran operasional tiap cabang serta status penguncian periode.
            </p>
        </div>
        @if ($branchId)
            <div class="flex flex-wrap items-center gap-2">
                @can('finance.period.lock')
                    @if ($isLocked)
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                             style="background-color: color-mix(in srgb, var(--dash-danger) 12%, transparent); color: var(--dash-danger);">
                            <span class="material-symbols-outlined text-[16px]">lock</span>
                            Periode Terkunci
                        </div>
                        <button type="button" @click="unlockModalOpen = true"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            <span class="material-symbols-outlined text-[16px]">lock_open</span>
                            Buka Periode
                        </button>
                    @else
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                             style="background-color: color-mix(in srgb, #16a34a 12%, transparent); color: #16a34a;">
                            <span class="material-symbols-outlined text-[16px]">lock_open</span>
                            Periode Terbuka
                        </div>
                        <button type="button" @click="lockModalOpen = true"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-danger);">
                            <span class="material-symbols-outlined text-[16px]">lock</span>
                            Kunci Periode
                        </button>
                    @endif
                @else
                    @if ($isLocked)
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                             style="background-color: color-mix(in srgb, var(--dash-danger) 12%, transparent); color: var(--dash-danger);">
                            <span class="material-symbols-outlined text-[16px]">lock</span>
                            Periode Terkunci
                        </div>
                    @endif
                @endcan
            </div>
        @endif
    </div>

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

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('branch-monthly-costs.index') }}"
          class="p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            @if ($branches->count() > 1)
                <div>
                    <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Cabang</label>
                    <select name="branch_id"
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Cabang...</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected($branchId == $branch->id)>{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Bulan --}}
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Bulan Periode</label>
                <select name="month"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    @foreach (array_slice($months, 1, null, true) as $num => $label)
                        <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tahun --}}
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Tahun Periode</label>
                <input type="number" name="year" value="{{ $year }}" min="2020"
                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
            </div>

            {{-- Filter & Reset Actions --}}
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    Filter
                </button>
                <a href="{{ route('branch-monthly-costs.index') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    Reset
                </a>
            </div>
        </div>
    </form>

    @if ($branchId)
        {{-- Form Catat Biaya Baru --}}
        @if (!$isLocked)
            @can('finance.monthly_cost.create')
                <div class="rounded-2xl shadow-sm"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="px-6 py-4 rounded-t-2xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2"
                         style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <div>
                            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Catat Biaya Operasional Baru</h2>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                Cabang: {{ $branches->firstWhere('id', $branchId)?->branch_name ?? 'Cabang' }} - {{ $months[$month] ?? '' }} {{ $year }}
                            </p>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="p-4 m-6 mb-0 rounded-xl text-sm font-medium"
                             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('branch-monthly-costs.store') }}" class="p-6">
                        @csrf
                        <input type="hidden" name="branch_id" value="{{ $branchId }}">
                        <input type="hidden" name="period_year" value="{{ $year }}">
                        <input type="hidden" name="period_month" value="{{ $month }}">

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                            <div class="sm:col-span-3">
                                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-faint);">Kategori <span class="text-red-500">*</span></label>
                                <select name="category" required
                                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <option value="">Pilih Kategori...</option>
                                    @foreach (['sewa' => 'Sewa', 'listrik' => 'Listrik', 'air' => 'Air', 'internet' => 'Internet', 'gaji_non_employee' => 'Gaji Non-Karyawan', 'peralatan' => 'Peralatan', 'kebersihan' => 'Kebersihan', 'keamanan' => 'Keamanan', 'lainnya' => 'Lainnya'] as $val => $label)
                                        <option value="{{ $val }}" @selected(old('category') === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-4">
                                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-faint);">Deskripsi <span class="text-red-500">*</span></label>
                                <input type="text" name="description" value="{{ old('description') }}" required maxlength="255" placeholder="Detail pengeluaran..."
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                            </div>
                            <div class="sm:col-span-3">
                                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-faint);">Jumlah (Rp) <span class="text-red-500">*</span></label>
                                <div class="relative" x-data="{
                                        rawValue: '{{ old('amount') }}',
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
                            <div class="sm:col-span-2">
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5 h-[38px] rounded-lg text-white text-sm font-semibold transition-opacity hover:opacity-90"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                    Catat Biaya
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endcan
        @endif

        {{-- Table Card --}}
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Kategori & Deskripsi</th>
                            <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Dicatat Oleh</th>
                            <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                                style="color: var(--dash-text-muted);">Jumlah</th>
                            @if (!$isLocked)
                                <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                                    style="color: var(--dash-text-muted);">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                        @php $total = 0; @endphp
                        @forelse ($costs as $cost)
                            @php $total += $cost->amount; @endphp
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider mb-0.5"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                                        {{ \App\Support\EnumLabels::get('cost_category', $cost->category) }}
                                    </span>
                                    <p class="text-xs font-medium" style="color: var(--dash-text-heading);">
                                        {{ $cost->description ?? '-' }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-xs" style="color: var(--dash-text-muted);">
                                    {{ $cost->recordedBy?->full_name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums text-xs font-bold" style="color: var(--dash-primary-text);">
                                    Rp {{ number_format($cost->amount, 0, ',', '.') }}
                                </td>
                                @if (!$isLocked)
                                    <td class="px-4 py-3 text-right">
                                        @can('finance.monthly_cost.delete')
                                            <button type="button"
                                                    @click="deleteUrl = '{{ route('branch-monthly-costs.destroy', $cost) }}'; deleteDesc = '{{ addslashes($cost->description ?? $cost->category) }}'; deleteModalOpen = true"
                                                    class="inline-flex items-center justify-center p-1.5 rounded-lg text-white transition-opacity hover:opacity-90"
                                                    style="background-color: var(--dash-danger);"
                                                    title="Hapus Biaya">
                                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                            </button>
                                        @endcan
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isLocked ? 3 : 4 }}" class="px-4 py-16 text-center">
                                    <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                          style="color: var(--dash-text-faint);">account_balance_wallet</span>
                                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada catatan biaya operasional.</p>
                                    <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Periode {{ $months[$month] ?? '' }} {{ $year }} belum memiliki transaksi dicatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($costs->count())
                        <tfoot style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                                    Total Pengeluaran:
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums text-sm font-bold" style="color: var(--dash-text-heading);">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </td>
                                @if (!$isLocked)<td></td>@endif
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @else
        {{-- State Belum Pilih Cabang --}}
        <div class="p-16 rounded-2xl text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                  style="color: var(--dash-text-faint);">store</span>
            <h2 class="text-base font-bold" style="color: var(--dash-text-heading);">Pilih Cabang</h2>
            <p class="mt-1 text-sm max-w-sm mx-auto" style="color: var(--dash-text-muted);">
                Silakan pilih cabang dari filter di atas untuk mulai melihat atau mencatat pengeluaran operasional bulanan.
            </p>
        </div>
    @endif

    {{-- Modal Konfirmasi Hapus Biaya (Custom Alpine.js, ZERO Browser Confirm) --}}
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
                    <h3 class="text-sm font-bold" style="color: var(--dash-text-heading);">Hapus Biaya Operasional</h3>
                    <p class="text-xs" style="color: var(--dash-text-muted);">Konfirmasi penghapusan</p>
                </div>
            </div>
            <div class="px-5 py-2">
                <p class="text-sm" style="color: var(--dash-text-body);">
                    Apakah Anda yakin ingin menghapus catatan biaya <span class="font-semibold" style="color: var(--dash-text-heading);" x-text="deleteDesc"></span>?
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

    {{-- Modal Konfirmasi Kunci Periode (Custom Alpine.js, ZERO Browser Confirm) --}}
    @if ($branchId)
        <div x-show="lockModalOpen"
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             style="display: none;"
             @keydown.escape.window="lockModalOpen = false">
            <div @click.stop
                 class="w-full max-w-md rounded-2xl shadow-xl space-y-4"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-5 rounded-t-2xl flex items-center gap-3 border-b"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                         style="background-color: color-mix(in srgb, var(--dash-danger) 15%, transparent); color: var(--dash-danger);">
                        <span class="material-symbols-outlined text-[22px]">lock</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold" style="color: var(--dash-text-heading);">Kunci Periode Anggaran</h3>
                        <p class="text-xs" style="color: var(--dash-text-muted);">Mencegah modifikasi data pengeluaran</p>
                    </div>
                </div>
                <div class="px-5 py-2">
                    <p class="text-sm" style="color: var(--dash-text-body);">
                        Kunci periode <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $months[$month] ?? '' }} {{ $year }}</span> untuk cabang ini? Setelah dikunci, data pengeluaran baru tidak dapat ditambahkan atau dihapus tanpa izin khusus.
                    </p>
                </div>
                <div class="p-4 rounded-b-2xl border-t flex justify-end gap-2.5"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <button type="button" @click="lockModalOpen = false"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('period-locks.lock') }}" class="inline-flex">
                        @csrf
                        <input type="hidden" name="branch_id" value="{{ $branchId }}">
                        <input type="hidden" name="period_year" value="{{ $year }}">
                        <input type="hidden" name="period_month" value="{{ $month }}">
                        <input type="hidden" name="notes" value="Dikunci via halaman biaya bulanan">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-danger);">
                            <span class="material-symbols-outlined text-[16px]">lock</span>
                            Ya, Kunci Periode
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Konfirmasi Buka Kunci Periode (Custom Alpine.js, ZERO Browser Confirm) --}}
        <div x-show="unlockModalOpen"
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             style="display: none;"
             @keydown.escape.window="unlockModalOpen = false">
            <div @click.stop
                 class="w-full max-w-md rounded-2xl shadow-xl space-y-4"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-5 rounded-t-2xl flex items-center gap-3 border-b"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                         style="background-color: color-mix(in srgb, #16a34a 15%, transparent); color: #16a34a;">
                        <span class="material-symbols-outlined text-[22px]">lock_open</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold" style="color: var(--dash-text-heading);">Buka Kunci Periode</h3>
                        <p class="text-xs" style="color: var(--dash-text-muted);">Membuka kembali input biaya</p>
                    </div>
                </div>
                <div class="px-5 py-2">
                    <p class="text-sm" style="color: var(--dash-text-body);">
                        Buka kembali penguncian periode <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $months[$month] ?? '' }} {{ $year }}</span> untuk cabang ini?
                    </p>
                </div>
                <div class="p-4 rounded-b-2xl border-t flex justify-end gap-2.5"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <button type="button" @click="unlockModalOpen = false"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('period-locks.unlock') }}" class="inline-flex">
                        @csrf
                        <input type="hidden" name="branch_id" value="{{ $branchId }}">
                        <input type="hidden" name="period_year" value="{{ $year }}">
                        <input type="hidden" name="period_month" value="{{ $month }}">
                        <input type="hidden" name="notes" value="Dibuka via halaman biaya bulanan">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                                style="background-color: #16a34a;">
                            <span class="material-symbols-outlined text-[16px]">lock_open</span>
                            Ya, Buka Kunci
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
