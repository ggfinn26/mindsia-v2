@extends('layouts.dashboard')

@section('title', 'Biaya Bulanan Cabang')
@section('header_title', 'Finance')

@section('content')
@php $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[color:var(--dash-text-muted)]">Finance</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Biaya Bulanan Cabang</h1>
            <p class="mt-1 text-sm text-[color:var(--dash-text-muted)]">Kelola dan pantau pengeluaran operasional tiap cabang.</p>
        </div>
        @if ($branchId)
            @can('finance.period.lock')
                @if ($isLocked)
                    <div class="flex items-center gap-3">
                        <div class="inline-flex items-center gap-2 rounded-full bg-[var(--dash-nav-hover-bg)] px-3 py-1.5 text-xs font-medium text-[color:var(--dash-text-body)] ring-1 ring-inset ring-slate-500/10">
                            <span class="material-symbols-outlined text-[16px]">lock</span> Periode Terkunci
                        </div>
                        <form method="POST" action="{{ route('period-locks.unlock') }}">
                            @csrf
                            <input type="hidden" name="branch_id" value="{{ $branchId }}">
                            <input type="hidden" name="period_year" value="{{ $year }}">
                            <input type="hidden" name="period_month" value="{{ $month }}">
                            <input type="hidden" name="notes" value="Dibuka via halaman biaya bulanan">
                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-[color:var(--dash-input-border)] px-3 py-1.5 text-xs font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">
                                <span class="material-symbols-outlined text-[16px]">lock_open</span> Buka Periode
                            </button>
                        </form>
                    </div>
                @else
                    <form method="POST" action="{{ route('period-locks.lock') }}">
                        @csrf
                        <input type="hidden" name="branch_id" value="{{ $branchId }}">
                        <input type="hidden" name="period_year" value="{{ $year }}">
                        <input type="hidden" name="period_month" value="{{ $month }}">
                        <input type="hidden" name="notes" value="Dikunci via halaman biaya bulanan">
                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                            <span class="material-symbols-outlined text-[16px]">lock</span> Kunci Periode
                        </button>
                    </form>
                @endif
            @else
                @if ($isLocked)
                    <div class="inline-flex items-center gap-2 rounded-full bg-[var(--dash-nav-hover-bg)] px-3 py-1.5 text-xs font-medium text-[color:var(--dash-text-body)] ring-1 ring-inset ring-slate-500/10">
                        <span class="material-symbols-outlined text-[16px]">lock</span> Periode Terkunci
                    </div>
                @endif
            @endcan
        @endif
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-red-600">error</span>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" class="flex flex-wrap items-end gap-4 rounded-2xl border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 shadow-sm">
        @if ($branches->count() > 1)
            <div class="w-full sm:w-auto flex-1 min-w-[200px]">
                <label class="mb-1.5 block text-xs font-bold text-[color:var(--dash-text-body)]">Cabang</label>
                <select name="branch_id" class="block w-full rounded-lg border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] px-3 py-2 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:bg-[var(--dash-card-bg)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    <option value="">— Pilih Cabang —</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($branchId == $branch->id)>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="w-1/2 sm:w-auto">
            <label class="mb-1.5 block text-xs font-bold text-[color:var(--dash-text-body)]">Bulan</label>
            <select name="month" class="block w-full rounded-lg border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] px-3 py-2 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:bg-[var(--dash-card-bg)] focus:ring-[color:var(--dash-primary,#215aac)]">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-1/3 sm:w-auto">
            <label class="mb-1.5 block text-xs font-bold text-[color:var(--dash-text-body)]">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" class="block w-full sm:w-28 rounded-lg border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] px-3 py-2 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:bg-[var(--dash-card-bg)] focus:ring-[color:var(--dash-primary,#215aac)]">
        </div>
        <div class="w-full sm:w-auto">
            <button type="submit" class="inline-flex min-h-[38px] w-full items-center justify-center gap-2 rounded-lg bg-slate-800 px-5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 transition-colors">
                <span class="material-symbols-outlined text-[18px]">filter_alt</span> Filter
            </button>
        </div>
    </form>

    @if ($branchId)
        @if (!$isLocked)
            @can('finance.monthly_cost.create')
            <form method="POST" action="{{ route('branch-monthly-costs.store') }}" class="rounded-2xl border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 shadow-sm space-y-4">
                @csrf
                <input type="hidden" name="branch_id" value="{{ $branchId }}">
                <input type="hidden" name="period_year" value="{{ $year }}">
                <input type="hidden" name="period_month" value="{{ $month }}">
                
                @if ($errors->any())
                    <div class="rounded-lg border border-red-100 bg-red-50 p-4 text-sm text-red-800">
                        <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-[color:var(--dash-card-border)] pb-4">
                    <div>
                        <h3 class="font-jakarta text-base font-bold text-[color:var(--dash-text-heading)]">Catat Biaya Baru</h3>
                        <p class="text-xs text-[color:var(--dash-text-muted)] mt-0.5">Biaya akan dimasukkan ke periode yang sedang Anda lihat.</p>
                    </div>
                    <div class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700">
                        <span class="material-symbols-outlined text-[16px]">info</span>
                        Mencatat untuk: {{ $branches->firstWhere('id', $branchId)?->branch_name ?? 'Cabang' }} — {{ $months[$month] ?? '' }} {{ $year }}
                    </div>
                </div>
                
                <div class="grid gap-4 md:grid-cols-12 items-end">
                    <div class="md:col-span-3">
                        <label class="mb-1.5 block text-xs font-bold text-[color:var(--dash-text-body)]">Kategori</label>
                        <select name="category" required class="block w-full rounded-lg border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] px-3 py-2.5 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)] shadow-sm">
                            <option value="">— Pilih —</option>
                            @foreach (['sewa' => 'Sewa', 'listrik' => 'Listrik', 'air' => 'Air', 'internet' => 'Internet', 'gaji_non_employee' => 'Gaji Non-Karyawan', 'peralatan' => 'Peralatan', 'kebersihan' => 'Kebersihan', 'keamanan' => 'Keamanan', 'lainnya' => 'Lainnya'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('category') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs font-bold text-[color:var(--dash-text-body)]">Deskripsi</label>
                        <input type="text" name="description" value="{{ old('description') }}" required maxlength="255" placeholder="Detail pengeluaran..." class="block w-full rounded-lg border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] px-3 py-2.5 text-sm placeholder:text-[color:var(--dash-text-faint)] focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)] shadow-sm">
                    </div>
                    <div class="md:col-span-3">
                        <label class="mb-1.5 block text-xs font-bold text-[color:var(--dash-text-body)]">Jumlah (Rp)</label>
                        <div class="flex rounded-lg shadow-sm" x-data="{
                                rawValue: '{{ old('amount') }}',
                                format(val) {
                                    let num = val.toString().replace(/[^0-9]/g, '');
                                    return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                                }
                            }">
                            <span class="inline-flex items-center rounded-l-lg border border-r-0 border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] px-3 text-sm font-bold text-[color:var(--dash-text-muted)]">Rp</span>
                            <input type="text" :value="format(rawValue)" @input="rawValue = $event.target.value.replace(/[^0-9]/g, ''); $event.target.value = format(rawValue)" required placeholder="0" class="block w-full min-w-0 flex-1 rounded-none rounded-r-lg border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] px-3 py-2.5 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                            <input type="hidden" name="amount" x-model="rawValue">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="inline-flex min-h-[42px] w-full items-center justify-center gap-2 rounded-lg bg-[var(--dash-primary,#215aac)] px-4 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-all">
                            <span class="material-symbols-outlined text-[18px]">add</span> Catat
                        </button>
                    </div>
                </div>
            </form>
            @endcan
        @endif

        <div class="overflow-hidden rounded-2xl border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                    <thead class="bg-[var(--dash-nav-hover-bg)] text-left text-xs font-bold uppercase tracking-wider text-[color:var(--dash-text-muted)]">
                        <tr>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Deskripsi</th>
                            <th class="px-6 py-4">Dicatat oleh</th>
                            <th class="px-6 py-4 text-right">Jumlah</th>
                            @if (!$isLocked)
                                <th class="px-6 py-4 text-right w-20">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @php $total = 0; @endphp
                        @forelse ($costs as $cost)
                            @php $total += $cost->amount; @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-[color:var(--dash-text-heading)] uppercase text-[13px] tracking-wide">{{ str_replace('_', ' ', $cost->category) }}</td>
                                <td class="px-6 py-4 text-[color:var(--dash-text-body)] font-medium">{{ $cost->description ?? '—' }}</td>
                                <td class="px-6 py-4 text-[color:var(--dash-text-muted)] text-xs">{{ $cost->recordedBy?->full_name ?? '—' }}</td>
                                <td class="px-6 py-4 text-right tabular-nums font-bold text-emerald-600">Rp {{ number_format($cost->amount, 0, ',', '.') }}</td>
                                @if (!$isLocked)
                                    <td class="px-6 py-4 text-right">
                                        @can('finance.monthly_cost.delete')
                                            <form method="POST" action="{{ route('branch-monthly-costs.destroy', $cost) }}" onsubmit="return confirm('Yakin ingin menghapus biaya ini?')" class="inline-flex">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-lg text-[color:var(--dash-text-faint)] hover:text-red-600 hover:bg-red-50 p-1.5 transition-colors" title="Hapus">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isLocked ? 4 : 5 }}" class="px-6 py-12 text-center">
                                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[var(--dash-nav-hover-bg)] mb-3">
                                        <span class="material-symbols-outlined text-[color:var(--dash-text-faint)] text-2xl">account_balance_wallet</span>
                                    </div>
                                    <h3 class="text-sm font-medium text-[color:var(--dash-text-heading)]">Belum ada catatan biaya</h3>
                                    <p class="mt-1 text-sm text-[color:var(--dash-text-muted)]">Periode {{ $months[$month] ?? '' }} {{ $year }} masih kosong.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($costs->count())
                        <tfoot class="bg-[var(--dash-nav-hover-bg)] border-t border-[color:var(--dash-card-border)]">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-[color:var(--dash-text-muted)]">Total Pengeluaran:</td>
                                <td class="px-6 py-4 text-right tabular-nums font-black text-[color:var(--dash-text-heading)] text-base">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                @if (!$isLocked)<td></td>@endif
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center rounded-2xl border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] py-16 px-6 shadow-sm">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-[var(--dash-nav-hover-bg)] mb-4">
                <span class="material-symbols-outlined text-[color:var(--dash-text-faint)] text-3xl">store</span>
            </div>
            <h3 class="text-base font-bold text-[color:var(--dash-text-heading)]">Pilih Cabang</h3>
            <p class="mt-1 text-sm text-[color:var(--dash-text-muted)] text-center max-w-sm">Silakan pilih cabang dari filter di atas untuk mulai melihat atau mencatat pengeluaran bulanan.</p>
        </div>
    @endif
</div>
@endsection
