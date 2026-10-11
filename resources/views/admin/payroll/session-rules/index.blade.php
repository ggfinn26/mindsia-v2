@extends('layouts.dashboard')

@section('title', 'Aturan Kompensasi Sesi - MINDSIA')
@section('header_title', 'Aturan Kompensasi Sesi')

@section('content')
@php
    $scopeLabels = [
        'global'   => 'Global',
        'role'     => 'Role',
        'position' => 'Posisi',
        'employee' => 'Pegawai',
    ];
    $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();
    $positions = \App\Models\Position::orderBy('position_name')->get();
    $employees = \App\Models\Employee::where('is_active', true)->orderBy('full_name')->get();
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
        searchRule: '',
        showConfirmModal: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmActionUrl: '',
        confirmMethod: 'DELETE',
        confirmActionText: 'Nonaktifkan',
        showEditModal: false,
        editRule: {
            id: null,
            rule_code: '',
            rule_name: '',
            scope_type: 'global',
            role_id: '',
            position_id: '',
            employee_id: '',
            amount_per_session: 0,
            notes: '',
            is_active: true
        },
        openEdit(rule) {
            this.editRule = {
                id: rule.id,
                rule_code: rule.rule_code,
                rule_name: rule.rule_name,
                scope_type: rule.scope_type,
                role_id: rule.role_id || '',
                position_id: rule.position_id || '',
                employee_id: rule.employee_id || '',
                amount_per_session: rule.amount_per_session,
                notes: rule.notes || '',
                is_active: !!rule.is_active
            };
            this.showEditModal = true;
        }
     }">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Gaji & Bonus</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Aturan Kompensasi Sesi</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Atur tarif kompensasi per sesi mengajar. Prioritas resolusi scope: Pegawai > Posisi > Role > Global.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('payroll.components.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">payments</span>
                Kelola Komponen Gaji
            </a>
            <a href="{{ route('payroll.periods.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                Periode Payroll
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <div class="flex items-center gap-2 mb-2 font-semibold">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>Terdapat kesalahan pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Tambah Aturan Baru --}}
    @can('payroll.session_compensation_rule.create')
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm"
         x-data="{ formScope: '{{ old('scope_type', 'global') }}' }">
        <div class="px-6 py-4 flex items-center gap-2"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">add_circle</span>
            <div>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Tambah Aturan Kompensasi Sesi</h2>
                <p class="text-xs" style="color: var(--dash-text-muted);">
                    Tentukan scope aturan dan tarif honorarium per sesi mengajar.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('payroll.session-rules.store') }}" class="p-6">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-body);">
                        Kode Aturan <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="text" name="rule_code" value="{{ old('rule_code') }}" required maxlength="100"
                           placeholder="Contoh: TUTOR_REGULAR"
                           class="w-full px-3 py-2 text-sm rounded-lg uppercase focus:outline-none transition-all font-mono"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-body);">
                        Nama Aturan <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="text" name="rule_name" value="{{ old('rule_name') }}" required maxlength="255"
                           placeholder="Contoh: Tarif Tutor Regular"
                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-body);">
                        Scope Aturan <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <select name="scope_type" required x-model="formScope"
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        @foreach ($scopeLabels as $val => $label)
                            <option value="{{ $val }}" @selected(old('scope_type') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Scope Target Dropdown (Role) --}}
                <div x-show="formScope === 'role'" x-cloak>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-body);">
                        Target Role <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <select name="role_id" :disabled="formScope !== 'role'"
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih role...</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Scope Target Dropdown (Posisi) --}}
                <div x-show="formScope === 'position'" x-cloak>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-body);">
                        Target Posisi <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <select name="position_id" :disabled="formScope !== 'position'"
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih posisi...</option>
                        @foreach ($positions as $pos)
                            <option value="{{ $pos->id }}" @selected(old('position_id') == $pos->id)>{{ $pos->position_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Scope Target Dropdown (Pegawai) --}}
                <div x-show="formScope === 'employee'" x-cloak>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-body);">
                        Target Pegawai <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <select name="employee_id" :disabled="formScope !== 'employee'"
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih pegawai...</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-body);">
                        Tarif per Sesi (Rp) <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="number" name="amount_per_session" value="{{ old('amount_per_session') }}" required min="0" step="0.01"
                           placeholder="0"
                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all font-mono"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-body);">
                        Catatan Tambahan
                    </label>
                    <input type="text" name="notes" value="{{ old('notes') }}"
                           placeholder="Catatan opsional aturan kompensasi sesi"
                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
            </div>

            <div class="mt-5 pt-4 flex flex-wrap items-center justify-between gap-4"
                 style="border-top: 1px solid var(--dash-card-border);">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))
                           class="h-4 w-4 rounded"
                           style="accent-color: var(--dash-primary);">
                    <span class="text-xs font-semibold" style="color: var(--dash-text-body);">Status Aktif</span>
                </label>

                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Aturan
                </button>
            </div>
        </form>
    </div>
    @endcan

    {{-- Card Table Daftar Aturan Sesi --}}
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div>
                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Daftar Aturan Kompensasi Sesi</h3>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                    Total {{ $rules->count() }} aturan konfigurasi honor per sesi tutor.
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="relative w-full sm:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[16px]"
                      style="color: var(--dash-text-muted);">search</span>
                <input type="text" x-model="searchRule" placeholder="Cari nama atau kode..."
                       class="w-full pr-3 py-1.5 text-xs rounded-lg focus:outline-none transition-all"
                       style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                            style="color: var(--dash-text-muted);">Aturan</th>
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                            style="color: var(--dash-text-muted);">Scope</th>
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                            style="color: var(--dash-text-muted);">Target Penerima</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-[11px]"
                            style="color: var(--dash-text-muted);">Tarif / Sesi</th>
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-[11px]"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rules as $rule)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            x-show="!searchRule || '{{ strtolower($rule->rule_name . ' ' . $rule->rule_code . ' ' . ($rule->role?->name ?? '') . ' ' . ($rule->position?->position_name ?? '') . ' ' . ($rule->employee?->full_name ?? '')) }}'.includes(searchRule.toLowerCase())"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $rule->rule_name }}</p>
                                <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-muted);">{{ $rule->rule_code }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-primary); border: 1px solid var(--dash-card-border);">
                                    {{ $scopeLabels[$rule->scope_type] ?? $rule->scope_type }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap" style="color: var(--dash-text-body);">
                                @if ($rule->scope_type === 'global')
                                    <span class="text-xs" style="color: var(--dash-text-muted);">Semua Tutor (Global)</span>
                                @else
                                    <span class="font-medium" style="color: var(--dash-text-heading);">
                                        {{ $rule->role?->name ?? $rule->position?->position_name ?? $rule->employee?->full_name ?? '-' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right font-semibold font-mono" style="color: var(--dash-text-heading);">
                                Rp {{ number_format($rule->amount_per_session, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if ($rule->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-primary-text); border: 1px solid var(--dash-card-border);">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: var(--dash-page-bg); color: var(--dash-text-faint); border: 1px solid var(--dash-card-border);">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @can('payroll.session_compensation_rule.update')
                                        <button type="button"
                                                @click="openEdit({
                                                    id: {{ $rule->id }},
                                                    rule_code: '{{ addslashes($rule->rule_code) }}',
                                                    rule_name: '{{ addslashes($rule->rule_name) }}',
                                                    scope_type: '{{ $rule->scope_type }}',
                                                    role_id: {{ $rule->role_id ?? 'null' }},
                                                    position_id: {{ $rule->position_id ?? 'null' }},
                                                    employee_id: {{ $rule->employee_id ?? 'null' }},
                                                    amount_per_session: {{ $rule->amount_per_session }},
                                                    notes: '{{ addslashes($rule->notes ?? '') }}',
                                                    is_active: {{ $rule->is_active ? 'true' : 'false' }}
                                                })"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[14px]">edit</span>
                                            Edit
                                        </button>
                                    @endcan

                                    @can('payroll.session_compensation_rule.delete')
                                        <button type="button"
                                                @click="showConfirmModal = true;
                                                        confirmTitle = 'Nonaktifkan Aturan Sesi';
                                                        confirmMessage = 'Apakah Anda yakin ingin menonaktifkan aturan sesi {{ addslashes($rule->rule_name) }}?';
                                                        confirmActionUrl = '{{ route('payroll.session-rules.destroy', $rule) }}';
                                                        confirmMethod = 'DELETE';
                                                        confirmActionText = 'Nonaktifkan';"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                                style="background-color: var(--dash-danger);">
                                            <span class="material-symbols-outlined text-[14px]">block</span>
                                            Nonaktifkan
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-4xl block mx-auto mb-2"
                                      style="color: var(--dash-text-faint);">tune</span>
                                <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">Belum ada aturan kompensasi sesi.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                                    Gunakan formulir di atas untuk membuat konfigurasi honorarium sesi mengajar tutor.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Edit Aturan Sesi --}}
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showEditModal" @click="showEditModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>

            <div x-show="showEditModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                
                {{-- Header Modal --}}
                <div class="px-6 py-4 rounded-t-2xl flex items-center justify-between"
                     style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">edit</span>
                        <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Edit Aturan Kompensasi Sesi</h3>
                    </div>
                    <button type="button" @click="showEditModal = false"
                            class="p-1 rounded-lg text-xs transition-colors"
                            style="color: var(--dash-text-muted);"
                            onmouseover="this.style.backgroundColor='var(--dash-card-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                {{-- Body Form Modal --}}
                <form :action="'/session-compensation-rules/' + editRule.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-body);">
                                    Kode Aturan <span style="color: var(--dash-danger);">*</span>
                                </label>
                                <input type="text" name="rule_code" x-model="editRule.rule_code" required maxlength="100"
                                       class="w-full px-3 py-2 text-sm rounded-lg uppercase focus:outline-none transition-all font-mono"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-body);">
                                    Nama Aturan <span style="color: var(--dash-danger);">*</span>
                                </label>
                                <input type="text" name="rule_name" x-model="editRule.rule_name" required maxlength="255"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-body);">
                                    Scope Aturan <span style="color: var(--dash-danger);">*</span>
                                </label>
                                <select name="scope_type" x-model="editRule.scope_type" required
                                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    @foreach ($scopeLabels as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="editRule.scope_type === 'role'" x-cloak>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-body);">
                                    Target Role <span style="color: var(--dash-danger);">*</span>
                                </label>
                                <select name="role_id" x-model="editRule.role_id" :disabled="editRule.scope_type !== 'role'"
                                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <option value="">Pilih role...</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="editRule.scope_type === 'position'" x-cloak>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-body);">
                                    Target Posisi <span style="color: var(--dash-danger);">*</span>
                                </label>
                                <select name="position_id" x-model="editRule.position_id" :disabled="editRule.scope_type !== 'position'"
                                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <option value="">Pilih posisi...</option>
                                    @foreach ($positions as $pos)
                                        <option value="{{ $pos->id }}">{{ $pos->position_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="editRule.scope_type === 'employee'" x-cloak>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-body);">
                                    Target Pegawai <span style="color: var(--dash-danger);">*</span>
                                </label>
                                <select name="employee_id" x-model="editRule.employee_id" :disabled="editRule.scope_type !== 'employee'"
                                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <option value="">Pilih pegawai...</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-body);">
                                    Tarif per Sesi (Rp) <span style="color: var(--dash-danger);">*</span>
                                </label>
                                <input type="number" name="amount_per_session" x-model="editRule.amount_per_session" required min="0" step="0.01"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all font-mono"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-body);">
                                    Catatan
                                </label>
                                <input type="text" name="notes" x-model="editRule.notes"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            </div>
                        </div>

                        <div class="flex items-center gap-6 pt-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input name="is_active" value="0" type="hidden">
                                <input name="is_active" value="1" type="checkbox" x-model="editRule.is_active"
                                       class="h-4 w-4 rounded"
                                       style="accent-color: var(--dash-primary);">
                                <span class="text-xs font-semibold" style="color: var(--dash-text-body);">Status Aktif</span>
                            </label>
                        </div>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                         style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                        <button type="button" @click="showEditModal = false"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Nonaktifkan --}}
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
                             style="background-color: color-mix(in srgb, var(--dash-danger) 15%, transparent); color: var(--dash-danger);">
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
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-danger);">
                        <span class="material-symbols-outlined text-[16px]">block</span>
                        <span x-text="confirmActionText"></span>
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
