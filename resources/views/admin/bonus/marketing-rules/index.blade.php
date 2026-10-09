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
    <div>
        <p class="text-sm font-medium text-[var(--dash-primary)]">Bonus Rules</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Marketing Bonus</h2>
        <p class="mt-1 max-w-2xl text-sm leading-6 text-[var(--dash-text-body)]">Rule bonus berdasarkan performa marketing (MPI atau revenue). Tier reward dikonfigurasi di halaman detail.</p>
    </div>

    {{-- Tabs Navigation --}}
    <div class="border-b border-[var(--dash-card-border)]">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <a href="{{ route('bonus.marketing-rules.index') }}" class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium border-[var(--dash-primary)] text-[var(--dash-primary)]">
                Marketing Bonus
            </a>
            <a href="{{ route('bonus.kpi-rules.index') }}" class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium border-transparent text-[var(--dash-text-muted)] hover:border-[var(--dash-card-border)] hover:text-[var(--dash-text-heading)] transition-colors">
                KPI Bonus
            </a>
            <a href="{{ route('bonus.special-rules.index') }}" class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium border-transparent text-[var(--dash-text-muted)] hover:border-[var(--dash-card-border)] hover:text-[var(--dash-text-heading)] transition-colors">
                Special Bonus
            </a>
        </nav>
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
    <form method="POST" action="{{ route('bonus.marketing-rules.store') }}" class="border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5">
        @csrf
        <div class="mb-4 flex items-start gap-3">
            <span class="material-symbols-outlined mt-0.5 text-[var(--dash-primary)]">add_circle</span>
            <div>
                <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)]">Tambah rule baru</h3>
                <p class="mt-0.5 text-sm text-[var(--dash-text-muted)]">Tier reward ditambahkan setelah rule tersimpan.</p>
            </div>
        </div>
        <div x-data="{ scope: '{{ old('scope_type', 'global') }}' }" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Kode rule</label>
                <input type="text" name="rule_code" value="{{ old('rule_code') }}" required maxlength="100" placeholder="MARKETING_BONUS_GLOBAL" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm uppercase focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Nama rule</label>
                <input type="text" name="rule_name" value="{{ old('rule_name') }}" required maxlength="255" placeholder="Bonus Marketing Global" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Scope</label>
                <select name="scope_type" required x-model="scope" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                    @foreach ($scopeLabels as $val => $label)
                        <option value="{{ $val }}" @selected(old('scope_type', 'global') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div x-show="scope === 'position'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Posisi</label>
                <select name="position_id" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                    <option value="">Pilih posisi...</option>
                    @foreach ($positions as $pos)
                        <option value="{{ $pos->id }}" @selected(old('position_id') == $pos->id)>{{ $pos->position_name }}</option>
                    @endforeach
                </select>
            </div>
            <div x-show="scope === 'role'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Role</label>
                <select name="role_id" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                    <option value="">Pilih role...</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div x-show="scope === 'employee'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Pegawai</label>
                <select name="employee_id" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                    <option value="">Pilih pegawai...</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Dasar bonus</label>
                <select name="bonus_basis" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                    @foreach ($basisLabels as $val => $label)
                        <option value="{{ $val }}" @selected(old('bonus_basis') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Dasar reward</label>
                <select name="reward_basis" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                    @foreach ($rewardLabels as $val => $label)
                        <option value="{{ $val }}" @selected(old('reward_basis') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Revenue basis <span class="text-[var(--dash-text-faint)]">(jika basis=revenue)</span></label>
                <input type="text" name="revenue_basis" value="{{ old('revenue_basis') }}" maxlength="100" placeholder="payment_1" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
            </div>
        </div>
        <div class="mt-4">
            <label class="inline-flex items-center gap-2 text-sm font-medium text-[var(--dash-text-body)]">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] text-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                Aktif
            </label>
        </div>
        <div class="mt-4">
            <button type="submit" class="inline-flex min-h-10 items-center rounded-lg gap-2 bg-[var(--dash-primary)] px-4 text-sm font-semibold text-white hover:opacity-90">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Buat rule
            </button>
        </div>
    </form>
    @endcan

    <div class="overflow-hidden border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[var(--dash-card-border)] px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)]">Semua marketing bonus rules</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-semibold text-[var(--dash-text-muted)]">
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
                <tbody class="divide-y divide-[var(--dash-card-border)]">
                    @forelse ($rules as $rule)
                        <tr class="hover:bg-[var(--dash-page-bg)]">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[var(--dash-text-heading)]">{{ $rule->rule_name }}</p>
                                <p class="mt-0.5 font-mono text-xs text-[var(--dash-text-faint)]">{{ $rule->rule_code }}</p>
                            </td>
                            <td class="px-5 py-4 text-[var(--dash-text-body)]">{{ $scopeLabels[$rule->scope_type] ?? $rule->scope_type }}</td>
                            <td class="px-5 py-4 text-[var(--dash-text-body)]">{{ $basisLabels[$rule->bonus_basis] ?? $rule->bonus_basis }}</td>
                            <td class="px-5 py-4 text-[var(--dash-text-body)]">{{ $rewardLabels[$rule->reward_basis] ?? $rule->reward_basis }}</td>
                            <td class="px-5 py-4 text-right text-[var(--dash-text-body)]">{{ $rule->tiers->count() }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $rule->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]' }}">
                                    {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('bonus.marketing-rules.show', $rule) }}" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                    style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>Detail & Tiers
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-[var(--dash-text-faint)]">trending_up</span>
                                <p class="mt-2 font-semibold text-[var(--dash-text-body)]">Belum ada marketing bonus rule.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
