@extends('layouts.dashboard')

@section('title', 'Kelola Komponen Gaji - MINDSIA')
@section('header_title', 'Kelola Komponen Gaji')

@section('content')
@php
    $typeLabels = [
        'earning'   => 'Pendapatan',
        'deduction' => 'Potongan',
    ];
    $methodLabels = [
        'fixed'      => 'Nilai tetap',
        'daily'      => 'Harian',
        'session'    => 'Per sesi',
        'percentage' => 'Persentase',
        'manual'     => 'Manual',
    ];
    $activeTab = request('tab', 'components');
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
        tab: '{{ $activeTab }}',
        searchComponent: '',
        searchCompensation: '',
        showConfirmModal: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmActionUrl: '',
        confirmMethod: 'DELETE',
        confirmActionText: 'Hapus',
        showEditModal: false,
        editComponent: {
            id: null,
            component_code: '',
            component_name: '',
            component_type: 'earning',
            calculation_method: 'fixed',
            is_taxable: false,
            is_active: true
        },
        openEdit(comp) {
            this.editComponent = {
                id: comp.id,
                component_code: comp.component_code,
                component_name: comp.component_name,
                component_type: comp.component_type,
                calculation_method: comp.calculation_method,
                is_taxable: !!comp.is_taxable,
                is_active: !!comp.is_active
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
                style="color: var(--dash-text-heading);">Kelola Komponen Gaji</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Definisikan master komponen gaji dan tetapkan nilai kompensasi per pegawai.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('payroll.session-rules.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">tune</span>
                Aturan Kompensasi Sesi
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

    {{-- Tab Navigation --}}
    <div style="border-bottom: 1px solid var(--dash-card-border);">
        <nav class="-mb-px flex gap-6">
            <button @click="tab = 'components'"
                    :style="tab === 'components' ? 'color: var(--dash-primary); border-bottom: 2px solid var(--dash-primary);' : 'color: var(--dash-text-muted); border-bottom: 2px solid transparent;'"
                    class="pb-3 text-sm font-semibold transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">category</span>
                Master Komponen
                <span class="px-2 py-0.5 text-xs rounded-full"
                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-heading);">
                    {{ $components->count() }}
                </span>
            </button>
            <button @click="tab = 'compensation'"
                    :style="tab === 'compensation' ? 'color: var(--dash-primary); border-bottom: 2px solid var(--dash-primary);' : 'color: var(--dash-text-muted); border-bottom: 2px solid transparent;'"
                    class="pb-3 text-sm font-semibold transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">badge</span>
                Kompensasi Pegawai
                <span class="px-2 py-0.5 text-xs rounded-full"
                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-heading);">
                    {{ $allCompensations->count() }}
                </span>
            </button>
        </nav>
    </div>

    {{-- ============================= --}}
    {{-- TAB 1 - Master Komponen --}}
    {{-- ============================= --}}
    <div x-show="tab === 'components'" class="space-y-6">

        @can('payroll.component.create')
        {{-- Card Form Tambah Komponen --}}
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
            <div class="px-6 py-4 flex items-center gap-2"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">add_circle</span>
                <div>
                    <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Tambah Komponen Baru</h2>
                    <p class="text-xs" style="color: var(--dash-text-muted);">
                        Kode komponen huruf kapital dan garis bawah, contoh: <code class="font-mono text-xs font-semibold">GAJI_POKOK</code>.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('payroll.components.store') }}" class="p-6">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                               style="color: var(--dash-text-body);">
                            Kode Komponen <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <input name="component_code" value="{{ old('component_code') }}" type="text" maxlength="100" required
                               placeholder="Contoh: GAJI_POKOK"
                               class="w-full px-3 py-2 text-sm rounded-lg uppercase focus:outline-none transition-all font-mono"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                               style="color: var(--dash-text-body);">
                            Nama Komponen <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <input name="component_name" value="{{ old('component_name') }}" type="text" maxlength="255" required
                               placeholder="Contoh: Gaji Pokok"
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                               style="color: var(--dash-text-body);">
                            Tipe <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="component_type" required
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            @foreach ($typeLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('component_type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                               style="color: var(--dash-text-body);">
                            Metode Kalkulasi <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="calculation_method" required
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            @foreach ($methodLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('calculation_method') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-5 pt-4 flex flex-wrap items-center justify-between gap-4"
                     style="border-top: 1px solid var(--dash-card-border);">
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input name="is_taxable" value="0" type="hidden">
                            <input name="is_taxable" value="1" type="checkbox" @checked(old('is_taxable'))
                                   class="h-4 w-4 rounded"
                                   style="accent-color: var(--dash-primary);">
                            <span class="text-xs font-semibold" style="color: var(--dash-text-body);">Kena Pajak</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input name="is_active" value="0" type="hidden">
                            <input name="is_active" value="1" type="checkbox" @checked(old('is_active', true))
                                   class="h-4 w-4 rounded"
                                   style="accent-color: var(--dash-primary);">
                            <span class="text-xs font-semibold" style="color: var(--dash-text-body);">Status Aktif</span>
                        </label>
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Komponen
                    </button>
                </div>
            </form>
        </div>
        @endcan

        {{-- Table Container Komponen --}}
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                 style="border-bottom: 1px solid var(--dash-card-border);">
                <div>
                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Daftar Komponen Gaji</h3>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                        Total {{ $components->count() }} komponen terdaftar dalam master payroll.
                    </p>
                </div>

                {{-- Search Bar --}}
                <div class="relative w-full sm:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[16px]"
                          style="color: var(--dash-text-muted);">search</span>
                    <input type="text" x-model="searchComponent" placeholder="Cari nama atau kode..."
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
                                style="color: var(--dash-text-muted);">Komponen</th>
                            <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                                style="color: var(--dash-text-muted);">Tipe</th>
                            <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                                style="color: var(--dash-text-muted);">Metode</th>
                            <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                                style="color: var(--dash-text-muted);">Pajak</th>
                            <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                                style="color: var(--dash-text-muted);">Status</th>
                            <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-[11px]"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($components as $component)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                x-show="!searchComponent || '{{ strtolower($component->component_name . ' ' . $component->component_code) }}'.includes(searchComponent.toLowerCase())"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $component->component_name }}</p>
                                    <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-muted);">{{ $component->component_code }}</p>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($component->component_type === 'earning')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-primary); border: 1px solid var(--dash-card-border);">
                                            <span class="material-symbols-outlined text-[13px]">arrow_upward</span>
                                            {{ $typeLabels[$component->component_type] ?? $component->component_type }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); color: var(--dash-danger); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent);">
                                            <span class="material-symbols-outlined text-[13px]">arrow_downward</span>
                                            {{ $typeLabels[$component->component_type] ?? $component->component_type }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap" style="color: var(--dash-text-body);">
                                    {{ $methodLabels[$component->calculation_method] ?? $component->calculation_method }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap" style="color: var(--dash-text-body);">
                                    @if ($component->is_taxable)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold" style="color: var(--dash-primary);">
                                            <span class="material-symbols-outlined text-[14px]">check</span> Ya
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs" style="color: var(--dash-text-muted);">
                                            <span class="material-symbols-outlined text-[14px]">close</span> Tidak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($component->is_active)
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
                                        @can('payroll.component.update')
                                            <button type="button"
                                                    @click="openEdit({
                                                        id: {{ $component->id }},
                                                        component_code: '{{ addslashes($component->component_code) }}',
                                                        component_name: '{{ addslashes($component->component_name) }}',
                                                        component_type: '{{ $component->component_type }}',
                                                        calculation_method: '{{ $component->calculation_method }}',
                                                        is_taxable: {{ $component->is_taxable ? 'true' : 'false' }},
                                                        is_active: {{ $component->is_active ? 'true' : 'false' }}
                                                    })"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                    style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">edit</span>
                                                Edit
                                            </button>
                                        @endcan

                                        @can('payroll.component.delete')
                                            <button type="button"
                                                    @click="showConfirmModal = true;
                                                            confirmTitle = 'Hapus / Nonaktifkan Komponen';
                                                            confirmMessage = 'Apakah Anda yakin ingin menghapus atau menonaktifkan komponen {{ addslashes($component->component_name) }}?';
                                                            confirmActionUrl = '{{ route('payroll.components.destroy', $component) }}';
                                                            confirmMethod = 'DELETE';
                                                            confirmActionText = 'Hapus / Nonaktifkan';"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                                    style="background-color: var(--dash-danger);">
                                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                                Hapus
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <span class="material-symbols-outlined text-4xl block mx-auto mb-2"
                                          style="color: var(--dash-text-faint);">payments</span>
                                    <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">Belum ada komponen gaji.</p>
                                    <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                                        Tambahkan komponen pada formulir di atas untuk mengatur struktur penggajian.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /tab components --}}

    {{-- ============================= --}}
    {{-- TAB 2 - Kompensasi Pegawai --}}
    {{-- ============================= --}}
    <div x-show="tab === 'compensation'" class="space-y-6">

        @canany(['payroll.employee_compensation.create', 'payroll.employee_compensation.update'])
        {{-- Card Form Tetapkan Kompensasi --}}
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
            <div class="px-6 py-4 flex items-center gap-2"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">person_add</span>
                <div>
                    <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Tetapkan Nilai Kompensasi Pegawai</h2>
                    <p class="text-xs" style="color: var(--dash-text-muted);">
                        Jika komponen gaji sudah pernah ditetapkan untuk pegawai tersebut, nilai lama akan diperbarui.
                    </p>
                </div>
            </div>

            <form method="POST" action="" id="compensation-form"
                  x-data="{
                    employeeId: '',
                    updateAction() {
                        const form = document.getElementById('compensation-form');
                        if (this.employeeId) {
                            form.action = '/employees/' + this.employeeId + '/compensations';
                        }
                    }
                  }"
                  class="p-6">
                @csrf
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                               style="color: var(--dash-text-body);">
                            Pegawai <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="_employee_id" required
                                x-model="employeeId" @change="updateAction()"
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
                            Komponen Gaji <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <select name="payroll_component_id" required
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih komponen...</option>
                            @foreach ($components->where('is_active', true) as $comp)
                                <option value="{{ $comp->id }}">{{ $comp->component_name }} ({{ $comp->component_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                               style="color: var(--dash-text-body);">
                            Nilai (Rp) <span style="color: var(--dash-danger);">*</span>
                        </label>
                        <input type="number" name="value" required min="0" step="0.01" placeholder="0"
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
                        <input type="text" name="notes" placeholder="Catatan opsional penetapan kompensasi"
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Simpan Kompensasi
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endcanany

        {{-- Table Container Semua Kompensasi Pegawai --}}
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                 style="border-bottom: 1px solid var(--dash-card-border);">
                <div>
                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Semua Kompensasi Pegawai</h3>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                        Total {{ $allCompensations->count() }} penetapan kompensasi aktif.
                    </p>
                </div>

                {{-- Search Bar --}}
                <div class="relative w-full sm:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[16px]"
                          style="color: var(--dash-text-muted);">search</span>
                    <input type="text" x-model="searchCompensation" placeholder="Cari pegawai atau komponen..."
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
                                style="color: var(--dash-text-muted);">Pegawai</th>
                            <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                                style="color: var(--dash-text-muted);">Komponen</th>
                            <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                                style="color: var(--dash-text-muted);">Tipe</th>
                            <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-[11px]"
                                style="color: var(--dash-text-muted);">Nilai</th>
                            <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]"
                                style="color: var(--dash-text-muted);">Catatan</th>
                            <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-[11px]"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($allCompensations as $comp)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                x-show="!searchCompensation || '{{ strtolower($comp->employee->full_name . ' ' . $comp->employee->employee_code . ' ' . $comp->payrollComponent->component_name) }}'.includes(searchCompensation.toLowerCase())"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $comp->employee->full_name }}</p>
                                    <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-muted);">{{ $comp->employee->employee_code }}</p>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <p class="font-medium" style="color: var(--dash-text-heading);">{{ $comp->payrollComponent->component_name }}</p>
                                    <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-muted);">{{ $comp->payrollComponent->component_code }}</p>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($comp->payrollComponent->component_type === 'earning')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-primary); border: 1px solid var(--dash-card-border);">
                                            Pendapatan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); color: var(--dash-danger); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent);">
                                            Potongan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right font-semibold font-mono" style="color: var(--dash-text-heading);">
                                    Rp {{ number_format($comp->value, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-xs" style="color: var(--dash-text-muted);">
                                    {{ $comp->notes ?: '-' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('employees.payroll.compensations.index', $comp->employee) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[14px]">tune</span>
                                            Kelola
                                        </a>

                                        @can('payroll.employee_compensation.delete')
                                            <button type="button"
                                                    @click="showConfirmModal = true;
                                                            confirmTitle = 'Hapus Kompensasi Pegawai';
                                                            confirmMessage = 'Hapus penetapan komponen {{ addslashes($comp->payrollComponent->component_name) }} untuk {{ addslashes($comp->employee->full_name) }}?';
                                                            confirmActionUrl = '{{ route('employees.payroll.compensations.destroy', [$comp->employee, $comp]) }}';
                                                            confirmMethod = 'DELETE';
                                                            confirmActionText = 'Hapus';"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                                    style="background-color: var(--dash-danger);">
                                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                                Hapus
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <span class="material-symbols-outlined text-4xl block mx-auto mb-2"
                                          style="color: var(--dash-text-faint);">money_off</span>
                                    <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">Belum ada data kompensasi pegawai.</p>
                                    <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                                        Gunakan formulir di atas untuk menetapkan komponen gaji khusus per pegawai.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /tab compensation --}}

    {{-- ============================= --}}
    {{-- Modal Edit Komponen Gaji --}}
    {{-- ============================= --}}
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
                        <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Edit Komponen Gaji</h3>
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
                <form :action="'/payroll/component/' + editComponent.id" method="POST" id="edit-component-form">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-body);">
                                    Kode Komponen <span style="color: var(--dash-danger);">*</span>
                                </label>
                                <input type="text" name="component_code" x-model="editComponent.component_code" required maxlength="100"
                                       class="w-full px-3 py-2 text-sm rounded-lg uppercase focus:outline-none transition-all font-mono"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-body);">
                                    Nama Komponen <span style="color: var(--dash-danger);">*</span>
                                </label>
                                <input type="text" name="component_name" x-model="editComponent.component_name" required maxlength="255"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-body);">
                                    Tipe <span style="color: var(--dash-danger);">*</span>
                                </label>
                                <select name="component_type" x-model="editComponent.component_type" required
                                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    @foreach ($typeLabels as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide"
                                       style="color: var(--dash-text-body);">
                                    Metode Kalkulasi <span style="color: var(--dash-danger);">*</span>
                                </label>
                                <select name="calculation_method" x-model="editComponent.calculation_method" required
                                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    @foreach ($methodLabels as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 pt-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input name="is_taxable" value="0" type="hidden">
                                <input name="is_taxable" value="1" type="checkbox" x-model="editComponent.is_taxable"
                                       class="h-4 w-4 rounded"
                                       style="accent-color: var(--dash-primary);">
                                <span class="text-xs font-semibold" style="color: var(--dash-text-body);">Kena Pajak</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input name="is_active" value="0" type="hidden">
                                <input name="is_active" value="1" type="checkbox" x-model="editComponent.is_active"
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

    {{-- ============================= --}}
    {{-- Modal Konfirmasi Aksi (Hapus/Nonaktifkan) --}}
    {{-- ============================= --}}
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
                        <span class="material-symbols-outlined text-[16px]">delete</span>
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
