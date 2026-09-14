@extends('layouts.dashboard')

@section('title', 'Biaya Operasional Cabang')
@section('header_title', 'Biaya Operasional')

@section('content')
@php $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Finance</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Biaya Operasional Cabang</h2>
        <div class="mt-1 flex items-center gap-3">
            <span class="text-sm text-slate-600">{{ $months[$month] }} {{ $year }}</span>
            @if ($isLocked)
                <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">
                    <span class="material-symbols-outlined text-[14px]">lock</span> Periode dikunci
                </span>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex gap-3 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <form method="GET" class="flex flex-wrap gap-3 border border-slate-200 bg-white p-4">
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Bulan</label>
            <select name="month" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" class="block w-24 border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
        </div>
        <div class="flex items-end">
            <button type="submit" class="inline-flex min-h-9 items-center bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Filter</button>
        </div>
    </form>

    @if (!$isLocked)
        @can('finance.monthly_cost.create')
        <form method="POST" action="{{ route('branch-monthly-costs.store') }}" class="border border-slate-200 bg-white p-5 space-y-3">
            @csrf
            @if ($errors->any())
                <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <p class="text-sm font-semibold text-slate-700">Catat biaya baru</p>
            <div class="grid gap-3 sm:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Kategori</label>
                    <input type="text" name="category" value="{{ old('category') }}" required placeholder="Listrik, Air, dll" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Deskripsi</label>
                    <input type="text" name="description" value="{{ old('description') }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Jumlah (Rp)</label>
                    <input type="number" name="amount" value="{{ old('amount') }}" required min="0" step="0.01" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <input type="hidden" name="period_year" value="{{ $year }}">
                <input type="hidden" name="period_month" value="{{ $month }}">
                <div class="flex items-end">
                    <button type="submit" class="inline-flex min-h-9 w-full items-center justify-center bg-[#215aac] text-xs font-semibold text-white hover:bg-[#194a91]">Catat</button>
                </div>
            </div>
        </form>
        @endcan
    @endif

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Deskripsi</th>
                        <th class="px-5 py-3">Dicatat oleh</th>
                        <th class="px-5 py-3 text-right">Jumlah</th>
                        @if (!$isLocked)
                            <th class="px-5 py-3 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $total = 0; @endphp
                    @forelse ($costs as $cost)
                        @php $total += $cost->amount; @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-3 font-semibold text-slate-900">{{ $cost->category }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $cost->description ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $cost->recordedBy?->full_name ?? '—' }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-semibold text-slate-900">Rp {{ number_format($cost->amount, 0, ',', '.') }}</td>
                            @if (!$isLocked)
                                <td class="px-5 py-3 text-right">
                                    @can('finance.monthly_cost.delete')
                                        <form method="POST" action="{{ route('branch-monthly-costs.destroy', $cost) }}" onsubmit="return confirm('Hapus biaya ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    @endcan
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isLocked ? 4 : 5 }}" class="px-5 py-8 text-center text-sm text-slate-400">Belum ada catatan biaya bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($costs->count())
                    <tfoot class="bg-slate-50">
                        <tr>
                            <td colspan="{{ $isLocked ? 3 : 3 }}" class="px-5 py-3 text-right text-xs font-semibold text-slate-500">Total:</td>
                            <td class="px-5 py-3 text-right tabular-nums font-bold text-slate-900">Rp {{ number_format($total, 0, ',', '.') }}</td>
                            @if (!$isLocked)<td></td>@endif
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
