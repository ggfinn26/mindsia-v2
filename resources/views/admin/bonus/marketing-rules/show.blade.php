@extends('layouts.dashboard')

@section('title', 'Detail Aturan Bonus Marketing - MINDSIA')
@section('header_title', 'Aturan Bonus Marketing')

@section('content')
@php
    $scopeLabels = ['global' => 'Global', 'role' => 'Per Role', 'position' => 'Per Posisi', 'employee' => 'Per Pegawai'];
    $basisLabels = ['marketing_mpi' => 'Marketing MPI', 'revenue' => 'Revenue'];
    $rewardLabels = ['base_salary' => 'Persentase gaji pokok', 'fixed' => 'Nilai tetap'];
    $rewardTypeLabels = ['percentage' => 'Persentase (%)', 'fixed' => 'Nilai tetap (Rp)'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'POST', confirmActionText: 'Hapus' }">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('bonus.marketing-rules.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span> Kembali ke Aturan Bonus Marketing
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Gaji & Bonus</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $rule->rule_name }}</h1>
            <p class="mt-1 font-mono text-xs" style="color: var(--dash-text-muted);">{{ $rule->rule_code }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @can('bonus.marketing-rule.update')
                <form method="POST" action="{{ route('bonus.marketing-rules.toggle-active', $rule) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]" style="color: {{ $rule->is_active ? 'var(--dash-text-muted)' : 'var(--dash-primary-text)' }};">
                            {{ $rule->is_active ? 'pause_circle' : 'play_circle' }}
                        </span>
                        {{ $rule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            @endcan

            @can('bonus.marketing-rule.delete')
                <button type="button"
                        @click="showConfirmModal = true; confirmTitle = 'Hapus Aturan Bonus Marketing'; confirmMessage = 'Apakah Anda yakin ingin menghapus aturan {{ addslashes($rule->rule_name) }} beserta seluruh tier yang tersimpan? Tindakan ini tidak dapat dibatalkan.'; confirmActionUrl = '{{ route('bonus.marketing-rules.destroy', $rule) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus Aturan'"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-danger);">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    Hapus
                </button>
            @endcan
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-status-success-bg, rgba(5, 150, 105, 0.12)); border: 1px solid rgba(5, 150, 105, 0.25); color: var(--dash-status-success-text, #059669);">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-status-danger-bg, rgba(220, 38, 38, 0.12)); border: 1px solid rgba(220, 38, 38, 0.25); color: var(--dash-danger, #dc2626);">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: var(--dash-status-danger-bg, rgba(220, 38, 38, 0.12)); border: 1px solid rgba(220, 38, 38, 0.25); color: var(--dash-danger, #dc2626);">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card 1: Konfigurasi Aturan --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">settings</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Konfigurasi Aturan</h2>
            </div>
            @if ($rule->is_active)
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold"
                      style="background-color: var(--dash-status-success-bg, rgba(5, 150, 105, 0.12)); color: var(--dash-status-success-text, #059669);">
                    Aktif
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold"
                      style="background-color: var(--dash-status-default-bg, rgba(148, 163, 184, 0.12)); color: var(--dash-status-default-text, #94a3b8);">
                    Nonaktif
                </span>
            @endif
        </div>

        @can('bonus.marketing-rule.update')
        <form method="POST" action="{{ route('bonus.marketing-rules.update', $rule) }}" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Kode Aturan <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <input type="text" name="rule_code" value="{{ old('rule_code', $rule->rule_code) }}" required maxlength="100"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg uppercase focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Nama Aturan <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <input type="text" name="rule_name" value="{{ old('rule_name', $rule->rule_name) }}" required maxlength="255"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Scope <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="scope_type" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            @foreach ($scopeLabels as $val => $label)
                                <option value="{{ $val }}" @selected(old('scope_type', $rule->scope_type) === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Dasar Bonus <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="bonus_basis" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            @foreach ($basisLabels as $val => $label)
                                <option value="{{ $val }}" @selected(old('bonus_basis', $rule->bonus_basis) === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Dasar Reward <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="reward_basis" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            @foreach ($rewardLabels as $val => $label)
                                <option value="{{ $val }}" @selected(old('reward_basis', $rule->reward_basis) === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Revenue Basis
                        </label>
                        <input type="text" name="revenue_basis" value="{{ old('revenue_basis', $rule->revenue_basis) }}" maxlength="100"
                               placeholder="Contoh: payment_1"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Catatan
                    </label>
                    <textarea name="notes" rows="2"
                              class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                              onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                              onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('notes', $rule->notes) }}</textarea>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
        @else
        <div class="grid grid-cols-2 gap-4 p-6 sm:grid-cols-3 text-sm">
            <div>
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Scope</p>
                <p class="mt-1 font-semibold" style="color: var(--dash-text-heading);">{{ $scopeLabels[$rule->scope_type] ?? $rule->scope_type }}</p>
            </div>
            <div>
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Dasar Bonus</p>
                <p class="mt-1 font-semibold" style="color: var(--dash-text-heading);">{{ $basisLabels[$rule->bonus_basis] ?? $rule->bonus_basis }}</p>
            </div>
            <div>
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Dasar Reward</p>
                <p class="mt-1 font-semibold" style="color: var(--dash-text-heading);">{{ $rewardLabels[$rule->reward_basis] ?? $rule->reward_basis }}</p>
            </div>
            @if ($rule->notes)
            <div class="col-span-full">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Catatan</p>
                <p class="mt-1" style="color: var(--dash-text-body);">{{ $rule->notes }}</p>
            </div>
            @endif
        </div>
        @endcan
    </div>

    {{-- Card 2: Tiers Reward --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Tiers Reward</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                    Tier dipilih berdasarkan rentang masa kerja (bulan) dan persentase achievement (%). Kedua dimensi harus memenuhi syarat.
                </p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md"
                  style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                {{ $rule->tiers->count() }} Tier
            </span>
        </div>

        {{-- Form Tambah Tier --}}
        @can('bonus.marketing-rule.create')
        <form method="POST" action="{{ route('bonus.marketing-rules.tiers.store', $rule) }}"
              class="p-6" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            @csrf
            <div class="flex items-center gap-2 mb-3">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary-text);">add_circle</span>
                <p class="text-xs font-bold" style="color: var(--dash-text-heading);">Tambah Tier Reward Baru</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-6">
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">
                        Masa Kerja Min (Bln) <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="number" name="minimum_tenure_months" value="{{ old('minimum_tenure_months', 0) }}" required min="0"
                           class="w-full text-sm px-3 py-2 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">
                        Masa Kerja Maks
                    </label>
                    <input type="number" name="maximum_tenure_months" value="{{ old('maximum_tenure_months') }}" min="0" placeholder="Tak terbatas"
                           class="w-full text-sm px-3 py-2 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">
                        Achievement Min (%) <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="number" name="minimum_achievement_percentage" value="{{ old('minimum_achievement_percentage', 0) }}" required min="0" step="0.01"
                           class="w-full text-sm px-3 py-2 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">
                        Achievement Maks
                    </label>
                    <input type="number" name="maximum_achievement_percentage" value="{{ old('maximum_achievement_percentage') }}" min="0" step="0.01" placeholder="Tak terbatas"
                           class="w-full text-sm px-3 py-2 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">
                        Tipe Reward <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <select name="reward_type" required
                            class="w-full text-sm px-3 py-2 rounded-lg focus:outline-none transition-all cursor-pointer"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="percentage">Persentase (%)</option>
                        <option value="fixed">Nilai tetap (Rp)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">
                        Nilai Reward <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="number" name="reward_value" value="{{ old('reward_value') }}" required min="0" step="0.01"
                           placeholder="Contoh: 10 atau 500000"
                           class="w-full text-sm px-3 py-2 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Tier
                </button>
            </div>
        </form>
        @endcan

        {{-- Table Tiers --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Masa Kerja</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Achievement %</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Tipe Reward</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right" style="color: var(--dash-text-muted);">Nilai</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse ($rule->tiers->sortBy(['minimum_tenure_months', 'minimum_achievement_percentage']) as $tier)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-6 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                                {{ $tier->minimum_tenure_months }} - {{ $tier->maximum_tenure_months ?? '∞' }} bln
                            </td>
                            <td class="px-6 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                                {{ $tier->minimum_achievement_percentage }}% - {{ $tier->maximum_achievement_percentage ? $tier->maximum_achievement_percentage . '%' : '∞' }}
                            </td>
                            <td class="px-6 py-3.5" style="color: var(--dash-text-body);">
                                {{ $rewardTypeLabels[$tier->reward_type] ?? $tier->reward_type }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-semibold" style="color: var(--dash-text-heading);">
                                {{ $tier->reward_type === 'percentage' ? $tier->reward_value . '%' : 'Rp ' . number_format($tier->reward_value, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                @can('bonus.marketing-rule.update')
                                    <button type="button"
                                            @click="showConfirmModal = true; confirmTitle = 'Hapus Tier Reward'; confirmMessage = 'Apakah Anda yakin ingin menghapus tier reward masa kerja {{ $tier->minimum_tenure_months }} - {{ $tier->maximum_tenure_months ?? '∞' }} bln ini?'; confirmActionUrl = '{{ route('bonus.marketing-rules.tiers.destroy', [$rule, $tier]) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus Tier'"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90"
                                            style="background-color: var(--dash-danger);">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                        Hapus
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center">
                                <span class="material-symbols-outlined text-[40px] block mx-auto mb-2" style="color: var(--dash-text-faint);">layers</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada tier reward.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Tambahkan tier reward menggunakan form di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Reusable Alpine Modal Konfirmasi Aksi --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="showConfirmModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: rgba(220, 38, 38, 0.12); color: var(--dash-danger);">
                            <span class="material-symbols-outlined text-[24px]">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showConfirmModal = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="button" @click="$refs.confirmForm.submit()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-danger);"
                            x-text="confirmActionText">
                    </button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
