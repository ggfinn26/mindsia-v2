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
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Bonus Rules</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Riwayat Perubahan</h1>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-[color:var(--dash-text-body)]">Audit log semua perubahan bonus rule. Dicatat otomatis oleh sistem.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('bonus.history.index') }}" class="flex gap-3 border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-4">
        <div>
            <label class="mb-1 block text-xs font-medium text-[color:var(--dash-text-body)]">Tipe bonus</label>
            <select name="bonus_type" class="block border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <option value="">Semua tipe</option>
                @foreach ($typeLabels as $val => $label)
                    <option value="{{ $val }}" @selected(request('bonus_type') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="inline-flex min-h-9 items-center bg-[var(--dash-primary,#215aac)] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Filter</button>
            @if (request()->filled('bonus_type') || request()->filled('rule_id'))
                <a href="{{ route('bonus.history.index') }}" class="inline-flex min-h-9 items-center border border-[color:var(--dash-input-border)] px-3 text-xs font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Reset</a>
            @endif
        </div>
    </form>

    <div class="overflow-hidden border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-nav-hover-bg)] text-left text-xs font-semibold text-[color:var(--dash-text-muted)]">
                    <tr>
                        <th class="px-5 py-3">Waktu</th>
                        <th class="px-5 py-3">Tipe</th>
                        <th class="px-5 py-3">Rule ID</th>
                        <th class="px-5 py-3">Aksi</th>
                        <th class="px-5 py-3">Diubah oleh</th>
                        <th class="px-5 py-3">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[color:var(--dash-card-border)]">
                    @forelse ($histories as $h)
                        <tr class="hover:bg-slate-50/70 align-top">
                            <td class="px-5 py-3 text-[color:var(--dash-text-muted)] whitespace-nowrap">{{ $h->created_at->format('d M Y H:i') }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $typeBadge[$h->bonus_type] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' }}">
                                    {{ $typeLabels[$h->bonus_type] ?? $h->bonus_type }}
                                </span>
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-[color:var(--dash-text-body)]">#{{ $h->rule_id }}</td>
                            <td class="px-5 py-3 font-medium text-[color:var(--dash-text-heading)]">{{ $h->action }}</td>
                            <td class="px-5 py-3 text-[color:var(--dash-text-body)]">{{ $h->changedBy?->full_name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @if ($h->new_values || $h->old_values)
                                    <details class="text-xs">
                                        <summary class="cursor-pointer text-[color:var(--dash-primary-text,#215aac)] hover:underline">Lihat diff</summary>
                                        <div class="mt-2 space-y-1">
                                            @if ($h->old_values)
                                                <p class="text-[color:var(--dash-text-muted)] font-mono break-all">- {{ json_encode($h->old_values, JSON_UNESCAPED_UNICODE) }}</p>
                                            @endif
                                            @if ($h->new_values)
                                                <p class="text-emerald-700 font-mono break-all">+ {{ json_encode($h->new_values, JSON_UNESCAPED_UNICODE) }}</p>
                                            @endif
                                        </div>
                                    </details>
                                @else
                                    <span class="text-[color:var(--dash-text-faint)]">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">history</span>
                                <p class="mt-2 font-semibold text-[color:var(--dash-text-body)]">Belum ada riwayat perubahan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($histories->hasPages())
            <div class="border-t border-[color:var(--dash-card-border)] px-5 py-4">
                {{ $histories->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
