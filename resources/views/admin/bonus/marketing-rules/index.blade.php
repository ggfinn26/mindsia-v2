@extends('layouts.dashboard')

@section('title', 'Aturan Bonus Marketing - MINDSIA')
@section('header_title', 'Aturan Bonus Marketing')

@section('content')
@php
    $scopeLabels = ['global' => 'Global', 'role' => 'Per Role', 'position' => 'Per Posisi', 'employee' => 'Per Pegawai'];
    $basisLabels = ['marketing_mpi' => 'Marketing MPI', 'revenue' => 'Revenue'];
    $rewardLabels = ['base_salary' => 'Persentase gaji pokok', 'fixed' => 'Nilai tetap'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Gaji & Bonus</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Aturan Bonus Marketing</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Aturan bonus performa marketing berdasarkan MPI atau revenue. Konfigurasi tier reward diatur di halaman detail rule.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('bonus.kpi-rules.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">bar_chart</span>
                Aturan Bonus KPI
            </a>
            <a href="{{ route('bonus.special-rules.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">stars</span>
                Aturan Bonus Spesial
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

    {{-- Form Tambah Rule Baru --}}
    @can('bonus.marketing-rule.create')
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center gap-2.5 rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">add_circle</span>
            <div>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Tambah Aturan Baru</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Tier reward dapat dikonfigurasi setelah aturan berhasil dibuat.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('bonus.marketing-rules.store') }}" class="p-6">
            @csrf
            <div x-data="{ scope: '{{ old('scope_type', 'global') }}' }" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Kode Aturan <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <input type="text" name="rule_code" value="{{ old('rule_code') }}" required maxlength="100"
                               placeholder="Contoh: MARKETING_BONUS_GLOBAL"
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
                               placeholder="Contoh: Bonus Marketing Global"
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
                            Dasar Bonus <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="bonus_basis" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            @foreach ($basisLabels as $val => $label)
                                <option value="{{ $val }}" @selected(old('bonus_basis') === $val)>{{ $label }}</option>
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
                                <option value="{{ $val }}" @selected(old('reward_basis') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Revenue Basis <span style="color: var(--dash-text-faint);">(jika basis = revenue)</span>
                        </label>
                        <input type="text" name="revenue_basis" value="{{ old('revenue_basis') }}" maxlength="100"
                               placeholder="Contoh: payment_1"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
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

    {{-- Data Table Aturan Bonus --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">trending_up</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Daftar Aturan Bonus Marketing</h2>
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
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Dasar Bonus</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Dasar Reward</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center" style="color: var(--dash-text-muted);">Tiers</th>
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
                            <td class="px-6 py-4" style="color: var(--dash-text-body);">
                                {{ $basisLabels[$rule->bonus_basis] ?? $rule->bonus_basis }}
                            </td>
                            <td class="px-6 py-4" style="color: var(--dash-text-body);">
                                {{ $rewardLabels[$rule->reward_basis] ?? $rule->reward_basis }}
                            </td>
                            <td class="px-6 py-4 text-center font-semibold" style="color: var(--dash-text-heading);">
                                {{ $rule->tiers->count() }}
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
                                <a href="{{ route('bonus.marketing-rules.show', $rule) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[15px]">tune</span>
                                    Detail & Tiers
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-[44px] block mx-auto mb-2" style="color: var(--dash-text-faint);">trending_up</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada aturan bonus marketing.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Gunakan form di atas untuk menambahkan aturan bonus marketing baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
