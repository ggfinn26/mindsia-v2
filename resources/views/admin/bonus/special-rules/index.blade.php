@extends('layouts.dashboard')

@section('title', 'Aturan Bonus Spesial - MINDSIA')
@section('header_title', 'Aturan Bonus Spesial')

@section('content')
@php
    $scopeLabels = ['global' => 'Global', 'role' => 'Per Role', 'position' => 'Per Posisi', 'employee' => 'Per Pegawai'];
    $rewardTypeLabels = ['fixed' => 'Nilai tetap (Rp)', 'percentage' => 'Persentase (%)'];
    $conditionModeLabels = ['all' => 'Semua kondisi (AND)', 'any' => 'Salah satu kondisi (OR)'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Gaji & Bonus</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Aturan Bonus Spesial</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Aturan bonus dengan kondisi custom metric (kehadiran, keterlambatan, atau performa khusus). Konfigurasi parameter metric diatur di halaman detail rule.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('bonus.marketing-rules.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                Aturan Bonus Marketing
            </a>
            <a href="{{ route('bonus.kpi-rules.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">bar_chart</span>
                Aturan Bonus KPI
            </a>
            <a href="{{ route('bonus.history.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">history</span>
                Riwayat Perubahan
            </a>
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

    {{-- Form Tambah Rule Spesial Baru --}}
    @can('bonus.special-rule.create')
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center gap-2.5 rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">add_circle</span>
            <div>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Tambah Aturan Spesial Baru</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Kondisi metric custom dapat ditambahkan setelah aturan berhasil dibuat.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('bonus.special-rules.store') }}" class="p-6">
            @csrf
            <div x-data="{ scope: '{{ old('scope_type', 'global') }}' }" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Kode Aturan <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <input type="text" name="rule_code" value="{{ old('rule_code') }}" required maxlength="100"
                               placeholder="Contoh: SPECIAL_BONUS_RAJIN"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg uppercase focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Nama Aturan <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <input type="text" name="rule_name" value="{{ old('rule_name') }}" required maxlength="255"
                               placeholder="Contoh: Bonus Kehadiran Sempurna"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Scope <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="scope_type" required x-model="scope"
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            @foreach ($scopeLabels as $val => $label)
                                <option value="{{ $val }}" @selected(old('scope_type', 'global') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="scope === 'position'" x-cloak>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Posisi <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="position_id"
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Posisi</option>
                            @foreach ($positions as $pos)
                                <option value="{{ $pos->id }}" @selected(old('position_id') == $pos->id)>{{ $pos->position_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="scope === 'role'" x-cloak>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Role <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="role_id"
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="scope === 'employee'" x-cloak>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Pegawai <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="employee_id"
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih Pegawai</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Tipe Reward <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="reward_type" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="fixed" @selected(old('reward_type') === 'fixed')>Nilai tetap (Rp)</option>
                            <option value="percentage" @selected(old('reward_type') === 'percentage')>Persentase (%)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Nilai Reward <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <input type="number" name="reward_value" value="{{ old('reward_value') }}" required min="0" step="0.01"
                               placeholder="Contoh: 500000 atau 10"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Mode Kondisi <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="condition_mode" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            @foreach ($conditionModeLabels as $val => $label)
                                <option value="{{ $val }}" @selected(old('condition_mode', 'all') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Dasar Reward <span style="color: var(--dash-text-faint);">(jika tipe = persentase)</span>
                        </label>
                        <select name="reward_basis" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="base_salary" @selected(old('reward_basis') === 'base_salary')>Gaji pokok</option>
                            <option value="fixed" @selected(old('reward_basis') === 'fixed')>Fixed</option>
                        </select>
                    </div>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <label class="inline-flex items-center gap-2.5 text-sm font-medium cursor-pointer" style="color: var(--dash-text-heading);">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))
                               class="rounded focus:ring-0"
                               style="color: var(--dash-primary);">
                        Aktifkan aturan ini
                    </label>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Buat Aturan
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endcan

    {{-- Data Table Aturan Bonus Spesial --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">stars</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Daftar Aturan Bonus Spesial</h2>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md"
                  style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                {{ $rules->count() }} Aturan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Aturan</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Scope</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Reward</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Mode Kondisi</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center" style="color: var(--dash-text-muted);">Kondisi</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse ($rules as $rule)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-6 py-4">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $rule->rule_name }}</p>
                                <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-muted);">{{ $rule->rule_code }}</p>
                            </td>
                            <td class="px-6 py-4" style="color: var(--dash-text-body);">
                                {{ $scopeLabels[$rule->scope_type] ?? $rule->scope_type }}
                            </td>
                            <td class="px-6 py-4 font-semibold" style="color: var(--dash-text-heading);">
                                {{ $rule->reward_type === 'percentage' ? $rule->reward_value . '%' : 'Rp ' . number_format($rule->reward_value, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4" style="color: var(--dash-text-body);">
                                {{ $conditionModeLabels[$rule->condition_mode] ?? $rule->condition_mode }}
                            </td>
                            <td class="px-6 py-4 text-center font-semibold" style="color: var(--dash-text-heading);">
                                {{ $rule->conditions->count() }}
                            </td>
                            <td class="px-6 py-4">
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
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('bonus.special-rules.show', $rule) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[15px]">tune</span>
                                    Detail & Kondisi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-[44px] block mx-auto mb-2" style="color: var(--dash-text-faint);">stars</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada aturan bonus spesial.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Gunakan form di atas untuk menambahkan aturan bonus spesial baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
