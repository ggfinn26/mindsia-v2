@extends('layouts.dashboard')

@section('title', 'Special Bonus Rules')
@section('header_title', 'Special Bonus Rules')

@section('content')
@php
    $scopeLabels = ['global' => 'Global', 'role' => 'Per Role', 'position' => 'Per Posisi', 'employee' => 'Per Pegawai'];
    $rewardTypeLabels = ['fixed' => 'Nilai tetap (Rp)', 'percentage' => 'Persentase (%)'];
    $conditionModeLabels = ['all' => 'Semua kondisi (AND)', 'any' => 'Salah satu kondisi (OR)'];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Bonus Rules</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Special Bonus</h2>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Rule bonus dengan kondisi metric custom. Kondisi dikonfigurasi di halaman detail.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('bonus.marketing-rules.index') }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Marketing Bonus</a>
            <a href="{{ route('bonus.kpi-rules.index') }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">KPI Bonus</a>
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @can('bonus.special-rule.create')
    <form method="POST" action="{{ route('bonus.special-rules.store') }}" class="border border-slate-200 bg-white p-5">
        @csrf
        <div class="mb-4 flex items-start gap-3">
            <span class="material-symbols-outlined mt-0.5 text-[#215aac]">add_circle</span>
            <div>
                <h3 class="font-jakarta text-base font-bold text-slate-900">Tambah rule baru</h3>
                <p class="mt-0.5 text-sm text-slate-500">Kondisi metric ditambahkan setelah rule tersimpan.</p>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Kode rule</label>
                <input type="text" name="rule_code" value="{{ old('rule_code') }}" required maxlength="100" placeholder="SPECIAL_BONUS_X" class="block w-full border-slate-300 text-sm uppercase focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama rule</label>
                <input type="text" name="rule_name" value="{{ old('rule_name') }}" required maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Scope</label>
                <select name="scope_type" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    @foreach ($scopeLabels as $val => $label)
                        <option value="{{ $val }}" @selected(old('scope_type', 'global') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Tipe reward</label>
                <select name="reward_type" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    <option value="fixed" @selected(old('reward_type') === 'fixed')>Nilai tetap (Rp)</option>
                    <option value="percentage" @selected(old('reward_type') === 'percentage')>Persentase (%)</option>
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nilai reward</label>
                <input type="number" name="reward_value" value="{{ old('reward_value') }}" required min="0" step="0.01" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Mode kondisi</label>
                <select name="condition_mode" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    @foreach ($conditionModeLabels as $val => $label)
                        <option value="{{ $val }}" @selected(old('condition_mode', 'all') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-6">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Dasar reward <span class="text-slate-400">(jika tipe=persentase)</span></label>
                <select name="reward_basis" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    <option value="base_salary" @selected(old('reward_basis') === 'base_salary')>Gaji pokok</option>
                    <option value="fixed" @selected(old('reward_basis') === 'fixed')>Fixed</option>
                </select>
            </div>
            <label class="inline-flex items-end gap-2 text-sm font-medium text-slate-700 pb-0.5">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="border-slate-300 text-[#215aac] focus:ring-[#215aac]">
                Aktif
            </label>
            <button type="submit" class="inline-flex min-h-10 items-end gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">save</span> Buat rule
            </button>
        </div>
    </form>
    @endcan

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Semua special bonus rules</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Rule</th>
                        <th class="px-5 py-3">Scope</th>
                        <th class="px-5 py-3">Reward</th>
                        <th class="px-5 py-3">Mode Kondisi</th>
                        <th class="px-5 py-3 text-right">Kondisi</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rules as $rule)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $rule->rule_name }}</p>
                                <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $rule->rule_code }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-700">{{ $scopeLabels[$rule->scope_type] ?? $rule->scope_type }}</td>
                            <td class="px-5 py-4 text-slate-700">
                                {{ $rule->reward_type === 'percentage' ? $rule->reward_value . '%' : 'Rp ' . number_format($rule->reward_value, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-slate-700">{{ $conditionModeLabels[$rule->condition_mode] ?? $rule->condition_mode }}</td>
                            <td class="px-5 py-4 text-right text-slate-700">{{ $rule->conditions->count() }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $rule->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('bonus.special-rules.show', $rule) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Detail & Kondisi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">stars</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada special bonus rule.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
