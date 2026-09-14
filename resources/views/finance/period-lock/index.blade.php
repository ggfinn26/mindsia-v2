@extends('layouts.dashboard')

@section('title', 'Kunci Periode Keuangan')
@section('header_title', 'Kunci Periode Keuangan')

@section('content')
@php $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Finance</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Kunci Periode Keuangan</h2>
        <p class="mt-1 text-sm text-slate-600">Periode yang dikunci tidak bisa diubah entri biaya operasionalnya.</p>
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

    @can('finance.period.lock')
    <div class="grid gap-4 sm:grid-cols-2">
        <form method="POST" action="{{ route('period-locks.lock') }}" class="border border-slate-200 bg-white p-5 space-y-3">
            @csrf
            <p class="font-jakarta text-sm font-bold text-slate-900">Kunci Periode</p>
            <div class="grid gap-3 grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Bulan</label>
                    <select name="period_month" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        @foreach (array_slice($months, 1, null, true) as $num => $label)
                            <option value="{{ $num }}" @selected(now()->month == $num)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Tahun</label>
                    <input type="number" name="period_year" value="{{ now()->year }}" required min="2020" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div class="col-span-2">
                    <label class="mb-1 block text-xs font-medium text-slate-700">Cabang ID</label>
                    <input type="number" name="branch_id" required placeholder="ID cabang" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div class="col-span-2">
                    <label class="mb-1 block text-xs font-medium text-slate-700">Catatan</label>
                    <input type="text" name="notes" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </div>
            <button type="submit" class="inline-flex min-h-9 items-center gap-1 bg-red-600 px-4 text-xs font-semibold text-white hover:bg-red-700">
                <span class="material-symbols-outlined text-[16px]">lock</span> Kunci
            </button>
        </form>

        <form method="POST" action="{{ route('period-locks.unlock') }}" class="border border-slate-200 bg-white p-5 space-y-3">
            @csrf
            <p class="font-jakarta text-sm font-bold text-slate-900">Buka Periode</p>
            <div class="grid gap-3 grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Bulan</label>
                    <select name="period_month" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        @foreach (array_slice($months, 1, null, true) as $num => $label)
                            <option value="{{ $num }}" @selected(now()->month == $num)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Tahun</label>
                    <input type="number" name="period_year" value="{{ now()->year }}" required min="2020" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div class="col-span-2">
                    <label class="mb-1 block text-xs font-medium text-slate-700">Cabang ID</label>
                    <input type="number" name="branch_id" required placeholder="ID cabang" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div class="col-span-2">
                    <label class="mb-1 block text-xs font-medium text-slate-700">Alasan dibuka</label>
                    <input type="text" name="notes" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </div>
            <button type="submit" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-4 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                <span class="material-symbols-outlined text-[16px]">lock_open</span> Buka
            </button>
        </form>
    </div>
    @endcan

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Riwayat Lock/Unlock</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Cabang</th>
                        <th class="px-5 py-3">Periode</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Dikunci oleh</th>
                        <th class="px-5 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($locks as $lock)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-3 font-semibold text-slate-900">{{ $lock->branch?->branch_name }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ $months[$lock->period_month] }} {{ $lock->period_year }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold {{ $lock->is_locked ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    <span class="material-symbols-outlined text-[12px]">{{ $lock->is_locked ? 'lock' : 'lock_open' }}</span>
                                    {{ $lock->is_locked ? 'Dikunci' : 'Dibuka' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $lock->lockedBy?->full_name ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $lock->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-400">Belum ada riwayat lock/unlock.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($locks->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $locks->links() }}</div>
        @endif
    </div>
</div>
@endsection
