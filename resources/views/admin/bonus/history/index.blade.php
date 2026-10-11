@extends('layouts.dashboard')

@section('title', 'Riwayat Perubahan Bonus Rules')
@section('header_title', 'Riwayat Bonus Rules')

@section('content')
@php
    $typeLabels = ['marketing' => 'Marketing', 'kpi' => 'KPI', 'special' => 'Special'];
    $typeBadge = [
        'marketing' => 'bg-purple-100 text-purple-800',
        'kpi'       => 'bg-blue-100 text-blue-800',
        'special'   => 'bg-amber-100 text-amber-800',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Bonus Rules</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Riwayat Perubahan</h2>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Audit log semua perubahan bonus rule. Dicatat otomatis oleh sistem.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('bonus.history.index') }}" class="flex gap-3 border border-slate-200 bg-white p-4">
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Tipe bonus</label>
            <select name="bonus_type" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">Semua tipe</option>
                @foreach ($typeLabels as $val => $label)
                    <option value="{{ $val }}" @selected(request('bonus_type') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="inline-flex min-h-9 items-center bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Filter</button>
            @if (request()->filled('bonus_type') || request()->filled('rule_id'))
                <a href="{{ route('bonus.history.index') }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
            @endif
        </div>
    </form>

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Waktu</th>
                        <th class="px-5 py-3">Tipe</th>
                        <th class="px-5 py-3">Rule ID</th>
                        <th class="px-5 py-3">Aksi</th>
                        <th class="px-5 py-3">Diubah oleh</th>
                        <th class="px-5 py-3">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($histories as $h)
                        <tr class="hover:bg-slate-50/70 align-top">
                            <td class="px-5 py-3 text-slate-500 whitespace-nowrap">{{ $h->created_at->format('d M Y H:i') }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $typeBadge[$h->bonus_type] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $typeLabels[$h->bonus_type] ?? $h->bonus_type }}
                                </span>
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-600">#{{ $h->rule_id }}</td>
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $h->action }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $h->changedBy?->full_name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @if ($h->new_values || $h->old_values)
                                    <details class="text-xs">
                                        <summary class="cursor-pointer text-[#215aac] hover:underline">Lihat diff</summary>
                                        <div class="mt-2 space-y-1">
                                            @if ($h->old_values)
                                                <p class="text-slate-500 font-mono break-all">- {{ json_encode($h->old_values, JSON_UNESCAPED_UNICODE) }}</p>
                                            @endif
                                            @if ($h->new_values)
                                                <p class="text-emerald-700 font-mono break-all">+ {{ json_encode($h->new_values, JSON_UNESCAPED_UNICODE) }}</p>
                                            @endif
                                        </div>
                                    </details>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">history</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada riwayat perubahan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($histories->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $histories->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
