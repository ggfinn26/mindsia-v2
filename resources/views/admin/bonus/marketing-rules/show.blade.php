@extends('layouts.dashboard')

@section('title', 'Detail Marketing Bonus Rule')
@section('header_title', 'Marketing Bonus Rule')

@section('content')
@php
    $scopeLabels = ['global' => 'Global', 'role' => 'Per Role', 'position' => 'Per Posisi', 'employee' => 'Per Pegawai'];
    $basisLabels = ['marketing_mpi' => 'Marketing MPI', 'revenue' => 'Revenue'];
    $rewardLabels = ['base_salary' => 'Persentase gaji pokok', 'fixed' => 'Nilai tetap'];
    $rewardTypeLabels = ['percentage' => 'Persentase (%)', 'fixed' => 'Nilai tetap (Rp)'];
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('bonus.marketing-rules.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[var(--dash-text-muted)] hover:text-[var(--dash-text-body)]">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Marketing Bonus Rules
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $rule->rule_name }}</h1>
            <p class="mt-0.5 font-mono text-sm text-[var(--dash-text-faint)]">{{ $rule->rule_code }}</p>
        </div>
        <div class="flex gap-2">
            @can('bonus.marketing-rule.update')
                <form method="POST" action="{{ route('bonus.marketing-rules.toggle-active', $rule) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                            style="background-color: {{ $rule->is_active ? '#dc2626' : '#059669' }};">
                        <span class="material-symbols-outlined text-[14px]">swap_horiz</span>{{ $rule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            @endcan
            @can('bonus.marketing-rule.delete')
                <form method="POST" action="{{ route('bonus.marketing-rules.destroy', $rule) }}" onsubmit="return confirm('Hapus rule ini beserta semua tiernya?')" class="inline">
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

    {{-- Rule Info --}}
    <div class="border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[var(--dash-card-border)] px-5 py-3 flex items-center justify-between">
            <h3 class="font-jakarta text-sm font-bold text-[var(--dash-text-heading)]">Konfigurasi Rule</h3>
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $rule->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]' }}">
                {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
        @can('bonus.marketing-rule.update')
        <form method="POST" action="{{ route('bonus.marketing-rules.update', $rule) }}" class="p-5">
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
                    <label class="mb-1 block text-xs font-semibold text-[var(--dash-text-body)]">Dasar bonus</label>
                    <select name="bonus_basis" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                        @foreach ($basisLabels as $val => $label)
                            <option value="{{ $val }}" @selected(old('bonus_basis', $rule->bonus_basis) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[var(--dash-text-body)]">Dasar reward</label>
                    <select name="reward_basis" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                        @foreach ($rewardLabels as $val => $label)
                            <option value="{{ $val }}" @selected(old('reward_basis', $rule->reward_basis) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[var(--dash-text-body)]">Revenue basis</label>
                    <input type="text" name="revenue_basis" value="{{ old('revenue_basis', $rule->revenue_basis) }}" maxlength="100" class="block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
            </div>
            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold text-[var(--dash-text-body)]">Catatan</label>
                <textarea name="notes" rows="2" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">{{ old('notes', $rule->notes) }}</textarea>
            </div>
            <div class="mt-4">
                <button type="submit" class="inline-flex min-h-9 items-center rounded-lg gap-2 bg-[var(--dash-primary)] px-4 text-sm font-semibold text-white hover:opacity-90">
                    Simpan perubahan
                </button>
            </div>
        </form>
        @else
        <div class="grid grid-cols-2 gap-4 p-5 sm:grid-cols-3 text-sm">
            <div><p class="text-xs text-[var(--dash-text-muted)]">Scope</p><p class="font-medium text-[var(--dash-text-heading)]">{{ $scopeLabels[$rule->scope_type] }}</p></div>
            <div><p class="text-xs text-[var(--dash-text-muted)]">Dasar Bonus</p><p class="font-medium text-[var(--dash-text-heading)]">{{ $basisLabels[$rule->bonus_basis] }}</p></div>
            <div><p class="text-xs text-[var(--dash-text-muted)]">Dasar Reward</p><p class="font-medium text-[var(--dash-text-heading)]">{{ $rewardLabels[$rule->reward_basis] }}</p></div>
        </div>
        @endcan
    </div>

    {{-- Tiers --}}
    <div class="overflow-hidden border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[var(--dash-card-border)] px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-[var(--dash-text-heading)]">Tiers</h3>
            <p class="mt-0.5 text-xs text-[var(--dash-text-muted)]">Tier dipilih berdasarkan masa kerja (bulan) DAN achievement%. Kedua dimensi harus masuk range.</p>
        </div>

        @can('bonus.marketing-rule.create')
        <form method="POST" action="{{ route('bonus.marketing-rules.tiers.store', $rule) }}" class="border-b border-[var(--dash-card-border)] bg-[var(--dash-page-bg)] p-4">
            @csrf
            <p class="mb-3 text-xs font-semibold text-[var(--dash-text-body)]">Tambah tier</p>
            <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-6">
                <div>
                    <label class="mb-1 block text-xs font-medium text-[var(--dash-text-body)]">Masa kerja min (bln)</label>
                    <input type="number" name="minimum_tenure_months" value="{{ old('minimum_tenure_months', 0) }}" required min="0" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[var(--dash-text-body)]">Masa kerja maks</label>
                    <input type="number" name="maximum_tenure_months" value="{{ old('maximum_tenure_months') }}" min="0" placeholder="kosong=tak terbatas" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[var(--dash-text-body)]">Achievement min (%)</label>
                    <input type="number" name="minimum_achievement_percentage" value="{{ old('minimum_achievement_percentage', 0) }}" required min="0" step="0.01" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[var(--dash-text-body)]">Achievement maks</label>
                    <input type="number" name="maximum_achievement_percentage" value="{{ old('maximum_achievement_percentage') }}" min="0" step="0.01" placeholder="kosong=tak terbatas" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[var(--dash-text-body)]">Tipe reward</label>
                    <select name="reward_type" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                        <option value="percentage">Persentase</option>
                        <option value="fixed">Nilai tetap</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[var(--dash-text-body)]">Nilai reward</label>
                    <input type="number" name="reward_value" value="{{ old('reward_value') }}" required min="0" step="0.01" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="inline-flex min-h-8 items-center rounded-lg gap-1 bg-[var(--dash-primary)] px-3 text-xs font-semibold text-white hover:opacity-90">
                    <span class="material-symbols-outlined text-[14px]">add</span> Tambah tier
                </button>
            </div>
        </form>
        @endcan

        <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
            <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-semibold text-[var(--dash-text-muted)]">
                <tr>
                    <th class="px-5 py-3">Masa Kerja</th>
                    <th class="px-5 py-3">Achievement%</th>
                    <th class="px-5 py-3">Tipe Reward</th>
                    <th class="px-5 py-3 text-right">Nilai</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--dash-card-border)]">
                @forelse ($rule->tiers->sortBy(['minimum_tenure_months', 'minimum_achievement_percentage']) as $tier)
                    <tr class="hover:bg-[var(--dash-page-bg)]">
                        <td class="px-5 py-3 text-[var(--dash-text-body)]">
                            {{ $tier->minimum_tenure_months }}–{{ $tier->maximum_tenure_months ?? '∞' }} bln
                        </td>
                        <td class="px-5 py-3 text-[var(--dash-text-body)]">
                            {{ $tier->minimum_achievement_percentage }}%–{{ $tier->maximum_achievement_percentage ? $tier->maximum_achievement_percentage . '%' : '∞' }}
                        </td>
                        <td class="px-5 py-3 text-[var(--dash-text-body)]">{{ $rewardTypeLabels[$tier->reward_type] ?? $tier->reward_type }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-[var(--dash-text-heading)]">
                            {{ $tier->reward_type === 'percentage' ? $tier->reward_value . '%' : 'Rp ' . number_format($tier->reward_value, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            @can('bonus.marketing-rule.update')
                                <form method="POST" action="{{ route('bonus.marketing-rules.tiers.destroy', [$rule, $tier]) }}" onsubmit="return confirm('Hapus tier ini?')" class="inline">
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-sm text-[var(--dash-text-faint)]">Belum ada tier. Tambahkan tier di atas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
