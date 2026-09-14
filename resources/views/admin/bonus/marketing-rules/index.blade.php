@extends('layouts.dashboard')

@section('title', 'Marketing Bonus Rules')
@section('header_title', 'Marketing Bonus Rules')

@section('content')
@php
    $scopeLabels = ['global' => 'Global', 'role' => 'Per Role', 'position' => 'Per Posisi', 'employee' => 'Per Pegawai'];
    $basisLabels = ['marketing_mpi' => 'Marketing MPI', 'revenue' => 'Revenue'];
    $rewardLabels = ['base_salary' => 'Persentase gaji pokok', 'fixed' => 'Nilai tetap'];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Bonus Rules</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Marketing Bonus</h2>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Rule bonus berdasarkan performa marketing (MPI atau revenue). Tier reward dikonfigurasi di halaman detail.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('bonus.kpi-rules.index') }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">KPI Bonus</a>
            <a href="{{ route('bonus.special-rules.index') }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Special Bonus</a>
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
    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @can('bonus.marketing-rule.create')
    <form method="POST" action="{{ route('bonus.marketing-rules.store') }}" class="border border-slate-200 bg-white p-5">
        @csrf
        <div class="mb-4 flex items-start gap-3">
            <span class="material-symbols-outlined mt-0.5 text-[#215aac]">add_circle</span>
            <div>
                <h3 class="font-jakarta text-base font-bold text-slate-900">Tambah rule baru</h3>
                <p class="mt-0.5 text-sm text-slate-500">Tier reward ditambahkan setelah rule tersimpan.</p>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Kode rule</label>
                <input type="text" name="rule_code" value="{{ old('rule_code') }}" required maxlength="100" placeholder="MARKETING_BONUS_GLOBAL" class="block w-full border-slate-300 text-sm uppercase focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama rule</label>
                <input type="text" name="rule_name" value="{{ old('rule_name') }}" required maxlength="255" placeholder="Bonus Marketing Global" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
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
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Dasar bonus</label>
                <select name="bonus_basis" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    @foreach ($basisLabels as $val => $label)
                        <option value="{{ $val }}" @selected(old('bonus_basis') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Dasar reward</label>
                <select name="reward_basis" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    @foreach ($rewardLabels as $val => $label)
                        <option value="{{ $val }}" @selected(old('reward_basis') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Revenue basis <span class="text-slate-400">(jika basis=revenue)</span></label>
                <input type="text" name="revenue_basis" value="{{ old('revenue_basis') }}" maxlength="100" placeholder="payment_1" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
        </div>
        <div class="mt-4">
            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="border-slate-300 text-[#215aac] focus:ring-[#215aac]">
                Aktif
            </label>
        </div>
        <div class="mt-4">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Buat rule
            </button>
        </div>
    </form>
    @endcan

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Semua marketing bonus rules</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Rule</th>
                        <th class="px-5 py-3">Scope</th>
                        <th class="px-5 py-3">Dasar Bonus</th>
                        <th class="px-5 py-3">Dasar Reward</th>
                        <th class="px-5 py-3 text-right">Tiers</th>
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
                            <td class="px-5 py-4 text-slate-700">{{ $basisLabels[$rule->bonus_basis] ?? $rule->bonus_basis }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ $rewardLabels[$rule->reward_basis] ?? $rule->reward_basis }}</td>
                            <td class="px-5 py-4 text-right text-slate-700">{{ $rule->tiers->count() }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $rule->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('bonus.marketing-rules.show', $rule) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Detail & Tiers
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">trending_up</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada marketing bonus rule.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
