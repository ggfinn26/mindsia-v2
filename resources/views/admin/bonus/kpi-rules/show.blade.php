@extends('layouts.dashboard')

@section('title', 'Detail KPI Bonus Rule')
@section('header_title', 'KPI Bonus Rule')

@section('content')
@php
    $scopeLabels = ['global' => 'Global', 'role' => 'Per Role', 'position' => 'Per Posisi', 'employee' => 'Per Pegawai'];
    $rewardLabels = ['base_salary' => 'Persentase gaji pokok', 'fixed' => 'Nilai tetap'];
    $rewardTypeLabels = ['percentage' => 'Persentase (%)', 'fixed' => 'Nilai tetap (Rp)'];
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('bonus.kpi-rules.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> KPI Bonus Rules
            </a>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $rule->rule_name }}</h2>
            <p class="mt-0.5 font-mono text-sm text-slate-400">{{ $rule->rule_code }}</p>
        </div>
        <div class="flex gap-2">
            @can('bonus.kpi-rule.update')
                <form method="POST" action="{{ route('bonus.kpi-rules.toggle-active', $rule) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="inline-flex min-h-9 items-center gap-1 border {{ $rule->is_active ? 'border-amber-300 text-amber-800 hover:bg-amber-50' : 'border-emerald-300 text-emerald-800 hover:bg-emerald-50' }} px-3 text-xs font-semibold">
                        {{ $rule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            @endcan
            @can('bonus.kpi-rule.delete')
                <form method="POST" action="{{ route('bonus.kpi-rules.destroy', $rule) }}" onsubmit="return confirm('Hapus rule beserta semua tiernya?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex min-h-9 items-center border border-red-200 px-3 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                </form>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span><p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Rule Info --}}
    <div class="border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center justify-between">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Konfigurasi Rule</h3>
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $rule->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
        @can('bonus.kpi-rule.update')
        <form method="POST" action="{{ route('bonus.kpi-rules.update', $rule) }}" class="p-5">
            @csrf @method('PUT')
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Kode</label>
                    <input type="text" name="rule_code" value="{{ old('rule_code', $rule->rule_code) }}" required maxlength="100" class="block w-full border-slate-300 text-sm uppercase focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Nama</label>
                    <input type="text" name="rule_name" value="{{ old('rule_name', $rule->rule_name) }}" required maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Scope</label>
                    <select name="scope_type" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        @foreach ($scopeLabels as $val => $label)
                            <option value="{{ $val }}" @selected(old('scope_type', $rule->scope_type) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Dasar reward</label>
                    <select name="reward_basis" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        @foreach ($rewardLabels as $val => $label)
                            <option value="{{ $val }}" @selected(old('reward_basis', $rule->reward_basis) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold text-slate-700">Catatan</label>
                <textarea name="notes" rows="2" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">{{ old('notes', $rule->notes) }}</textarea>
            </div>
            <div class="mt-4">
                <button type="submit" class="inline-flex min-h-9 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan perubahan</button>
            </div>
        </form>
        @endcan
    </div>

    {{-- Tiers --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Tiers KPI Score</h3>
        </div>

        @can('bonus.kpi-rule.create')
        <form method="POST" action="{{ route('bonus.kpi-rules.tiers.store', $rule) }}" class="border-b border-slate-200 bg-slate-50 p-4">
            @csrf
            <p class="mb-3 text-xs font-semibold text-slate-600">Tambah tier</p>
            <div class="grid gap-3 sm:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Skor KPI min</label>
                    <input type="number" name="minimum_score" value="{{ old('minimum_score', 0) }}" required min="0" step="0.01" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Skor KPI maks</label>
                    <input type="number" name="maximum_score" value="{{ old('maximum_score') }}" min="0" step="0.01" placeholder="kosong=tak terbatas" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Tipe reward</label>
                    <select name="reward_type" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        <option value="percentage">Persentase</option>
                        <option value="fixed">Nilai tetap</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Nilai reward</label>
                    <input type="number" name="reward_value" value="{{ old('reward_value') }}" required min="0" step="0.01" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="inline-flex min-h-8 items-center gap-1 bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">
                    <span class="material-symbols-outlined text-[14px]">add</span> Tambah tier
                </button>
            </div>
        </form>
        @endcan

        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                <tr>
                    <th class="px-5 py-3">Range Skor KPI</th>
                    <th class="px-5 py-3">Tipe Reward</th>
                    <th class="px-5 py-3 text-right">Nilai</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($rule->tiers->sortBy('minimum_score') as $tier)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-5 py-3 font-medium text-slate-900">
                            {{ $tier->minimum_score }} – {{ $tier->maximum_score ?? '∞' }}
                        </td>
                        <td class="px-5 py-3 text-slate-700">{{ $rewardTypeLabels[$tier->reward_type] ?? $tier->reward_type }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-900">
                            {{ $tier->reward_type === 'percentage' ? $tier->reward_value . '%' : 'Rp ' . number_format($tier->reward_value, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            @can('bonus.kpi-rule.update')
                                <form method="POST" action="{{ route('bonus.kpi-rules.tiers.destroy', [$rule, $tier]) }}" onsubmit="return confirm('Hapus tier?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex min-h-8 items-center border border-red-200 px-2 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-400">Belum ada tier. Tambahkan tier di atas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
