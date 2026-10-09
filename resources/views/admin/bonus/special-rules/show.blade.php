@extends('layouts.dashboard')

@section('title', 'Detail Special Bonus Rule')
@section('header_title', 'Special Bonus Rule')

@section('content')
@php
    $scopeLabels = ['global' => 'Global', 'role' => 'Per Role', 'position' => 'Per Posisi', 'employee' => 'Per Pegawai'];
    $conditionModeLabels = ['all' => 'Semua kondisi (AND)', 'any' => 'Salah satu (OR)'];
    $operators = ['>=', '<=', '=', '>', '<', '!='];
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('bonus.special-rules.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[var(--dash-text-muted)] hover:text-[var(--dash-text-body)]">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Special Bonus Rules
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $rule->rule_name }}</h1>
            <p class="mt-0.5 font-mono text-sm text-[var(--dash-text-faint)]">{{ $rule->rule_code }}</p>
        </div>
        <div class="flex gap-2">
            @can('bonus.special-rule.update')
                <form method="POST" action="{{ route('bonus.special-rules.toggle-active', $rule) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                            style="background-color: {{ $rule->is_active ? '#dc2626' : '#059669' }};">
                        <span class="material-symbols-outlined text-[14px]">swap_horiz</span>{{ $rule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            @endcan
            @can('bonus.special-rule.delete')
                <form method="POST" action="{{ route('bonus.special-rules.destroy', $rule) }}" onsubmit="return confirm('Hapus rule beserta semua kondisinya?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: #dc2626;"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                    </button>
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
    <div class="border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[var(--dash-card-border)] px-5 py-3 flex items-center justify-between">
            <h3 class="font-jakarta text-sm font-bold text-[var(--dash-text-heading)]">Konfigurasi Rule</h3>
            <div class="flex items-center gap-3">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold bg-blue-100 text-blue-800">
                    {{ $conditionModeLabels[$rule->condition_mode] ?? $rule->condition_mode }}
                </span>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $rule->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]' }}">
                    {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>
        @can('bonus.special-rule.update')
        <form method="POST" action="{{ route('bonus.special-rules.update', $rule) }}" class="p-5">
            @csrf @method('PUT')
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[var(--dash-text-body)]">Kode</label>
                    <input type="text" name="rule_code" value="{{ old('rule_code', $rule->rule_code) }}" required maxlength="100" class="block w-full border-[var(--dash-input-border)] text-sm uppercase focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[var(--dash-text-body)]">Nama</label>
                    <input type="text" name="rule_name" value="{{ old('rule_name', $rule->rule_name) }}" required maxlength="255" class="block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[var(--dash-text-body)]">Scope</label>
                    <select name="scope_type" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                        @foreach ($scopeLabels as $val => $label)
                            <option value="{{ $val }}" @selected(old('scope_type', $rule->scope_type) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[var(--dash-text-body)]">Tipe reward</label>
                    <select name="reward_type" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                        <option value="fixed" @selected(old('reward_type', $rule->reward_type) === 'fixed')>Nilai tetap (Rp)</option>
                        <option value="percentage" @selected(old('reward_type', $rule->reward_type) === 'percentage')>Persentase (%)</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[var(--dash-text-body)]">Nilai reward</label>
                    <input type="number" name="reward_value" value="{{ old('reward_value', $rule->reward_value) }}" required min="0" step="0.01" class="block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[var(--dash-text-body)]">Mode kondisi</label>
                    <select name="condition_mode" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                        @foreach ($conditionModeLabels as $val => $label)
                            <option value="{{ $val }}" @selected(old('condition_mode', $rule->condition_mode) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold text-[var(--dash-text-body)]">Catatan</label>
                <textarea name="notes" rows="2" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">{{ old('notes', $rule->notes) }}</textarea>
            </div>
            <div class="mt-4">
                <button type="submit" class="inline-flex min-h-9 items-center rounded-lg gap-2 bg-[var(--dash-primary)] px-4 text-sm font-semibold text-white hover:opacity-90">Simpan perubahan</button>
            </div>
        </form>
        @endcan
    </div>

    {{-- Conditions --}}
    <div class="overflow-hidden border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[var(--dash-card-border)] px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-[var(--dash-text-heading)]">Kondisi Bonus</h3>
            <p class="mt-0.5 text-xs text-[var(--dash-text-muted)]">
                Mode: <strong>{{ $conditionModeLabels[$rule->condition_mode] }}</strong>.
                Jika data_source tidak dikenal saat kalkulasi → kondisi dianggap tidak terpenuhi (fail-safe).
            </p>
        </div>

        @can('bonus.special-rule.create')
        <form method="POST" action="{{ route('bonus.special-rules.conditions.store', $rule) }}" class="border-b border-[var(--dash-card-border)] bg-[var(--dash-page-bg)] p-4">
            @csrf
            <p class="mb-3 text-xs font-semibold text-[var(--dash-text-body)]">Tambah kondisi</p>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <label class="mb-1 block text-xs font-medium text-[var(--dash-text-body)]">Metric code</label>
                    <input type="text" name="metric_code" value="{{ old('metric_code') }}" required maxlength="100" placeholder="attendance_rate" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[var(--dash-text-body)]">Data source</label>
                    <input type="text" name="data_source" value="{{ old('data_source') }}" required maxlength="100" placeholder="employee_attendance" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[var(--dash-text-body)]">Operator</label>
                    <select name="operator" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                        @foreach ($operators as $op)
                            <option value="{{ $op }}" @selected(old('operator') === $op)>{{ $op }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[var(--dash-text-body)]">Target value</label>
                    <input type="number" name="target_value" value="{{ old('target_value') }}" required step="0.01" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[var(--dash-text-body)]">Period type</label>
                    <input type="text" name="period_type" value="{{ old('period_type', 'payroll_period') }}" required maxlength="50" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="inline-flex min-h-8 items-center rounded-lg gap-1 bg-[var(--dash-primary)] px-3 text-xs font-semibold text-white hover:opacity-90">
                    <span class="material-symbols-outlined text-[14px]">add</span> Tambah kondisi
                </button>
            </div>
        </form>
        @endcan

        <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
            <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-semibold text-[var(--dash-text-muted)]">
                <tr>
                    <th class="px-5 py-3">Metric Code</th>
                    <th class="px-5 py-3">Data Source</th>
                    <th class="px-5 py-3">Kondisi</th>
                    <th class="px-5 py-3">Period</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--dash-card-border)]">
                @forelse ($rule->conditions as $condition)
                    <tr class="{{ $condition->is_active ? '' : 'opacity-60' }} hover:bg-[var(--dash-page-bg)]">
                        <td class="px-5 py-3 font-mono text-xs text-[var(--dash-text-heading)]">{{ $condition->metric_code }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-[var(--dash-text-body)]">{{ $condition->data_source }}</td>
                        <td class="px-5 py-3 font-semibold text-[var(--dash-text-heading)]">{{ $condition->operator }} {{ $condition->target_value }}</td>
                        <td class="px-5 py-3 text-[var(--dash-text-body)]">{{ $condition->period_type }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $condition->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]' }}">
                                {{ $condition->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @can('bonus.special-rule.update')
                                <form method="POST" action="{{ route('bonus.special-rules.conditions.destroy', [$rule, $condition]) }}" onsubmit="return confirm('Hapus kondisi?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors" style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: #dc2626;" onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'" onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[14px]">delete</span>Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm text-[var(--dash-text-faint)]">Belum ada kondisi. Tambahkan kondisi di atas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
