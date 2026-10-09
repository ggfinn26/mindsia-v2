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
     x-data="{ tab: '{{ $activeTab }}' }">

    {{-- Page header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Payroll</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Kelola Komponen Gaji</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            Definisikan komponen gaji dan tetapkan nilai per pegawai.
        </p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Tab Nav --}}
    <div style="border-bottom: 2px solid var(--dash-card-border);">
        <nav class="-mb-px flex gap-6">
            <button @click="tab = 'components'"
                    :style="tab === 'components'
                        ? 'border-bottom: 2px solid var(--dash-primary); color: var(--dash-primary); margin-bottom: -2px;'
                        : 'border-bottom: 2px solid transparent; color: var(--dash-text-muted); margin-bottom: -2px;'"
                    class="pb-3 text-sm font-semibold transition-colors">
                Master Komponen
            </button>
            <button @click="tab = 'compensation'"
                    :style="tab === 'compensation'
                        ? 'border-bottom: 2px solid var(--dash-primary); color: var(--dash-primary); margin-bottom: -2px;'
                        : 'border-bottom: 2px solid transparent; color: var(--dash-text-muted); margin-bottom: -2px;'"
                    class="pb-3 text-sm font-semibold transition-colors">
                Kompensasi Pegawai
            </button>
        </nav>
    </div>

    {{-- ============================= --}}
    {{-- TAB 1 — Master Komponen --}}
    {{-- ============================= --}}
    <div x-show="tab === 'components'" class="space-y-6">

        @can('payroll.component.create')
        <div class="rounded-2xl p-5 space-y-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined mt-0.5 text-[20px]"
                      style="color: var(--dash-primary);">add_circle</span>
                <div>
                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Tambah komponen baru</h3>
                    <p class="mt-0.5 text-sm" style="color: var(--dash-text-muted);">
                        Kode komponen: huruf kapital dan garis bawah, contoh <code class="font-mono text-xs">GAJI_POKOK</code>.
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('payroll.components.store') }}">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Kode komponen <span style="color:#dc2626;">*</span>
                        </label>
                        <input name="component_code" value="{{ old('component_code') }}" type="text" maxlength="100" required
                               placeholder="BASE_SALARY"
                               class="block w-full rounded-lg px-3 py-2 text-sm uppercase focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Nama komponen <span style="color:#dc2626;">*</span>
                        </label>
                        <input name="component_name" value="{{ old('component_name') }}" type="text" maxlength="255" required
                               placeholder="Gaji Pokok"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Tipe <span style="color:#dc2626;">*</span>
                        </label>
                        <select name="component_type" required
                                class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                            @foreach($typeLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('component_type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Metode kalkulasi <span style="color:#dc2626;">*</span>
                        </label>
                        <select name="calculation_method" required
                                class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                            @foreach($methodLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('calculation_method') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input name="is_taxable" value="0" type="hidden">
                        <input name="is_taxable" value="1" type="checkbox" @checked(old('is_taxable'))
                               class="h-4 w-4 rounded">
                        <span class="text-sm font-medium" style="color: var(--dash-text-body);">Kena pajak</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input name="is_active" value="0" type="hidden">
                        <input name="is_active" value="1" type="checkbox" @checked(old('is_active', true))
                               class="h-4 w-4 rounded">
                        <span class="text-sm font-medium" style="color: var(--dash-text-body);">Aktif</span>
                    </label>
                    <button type="submit"
                            class="ml-auto inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Komponen
                    </button>
                </div>
            </form>
        </div>
        @endcan

        {{-- Tabel master komponen --}}
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Daftar komponen</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-fixed text-sm">
                    <colgroup>
                        <col class="w-[32%]">
                        <col class="w-[14%]">
                        <col class="w-[16%]">
                        <col class="w-[10%]">
                        <col class="w-[12%]">
                        <col class="w-[16%]">
                    </colgroup>
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Komponen</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Tipe</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Metode</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Pajak</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($components as $component)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor=''">
                                <td class="px-5 py-4">
                                    <p class="font-semibold truncate" style="color: var(--dash-text-heading);">
                                        {{ $component->component_name }}
                                    </p>
                                    <p class="mt-0.5 font-mono text-xs truncate" style="color: var(--dash-text-muted);">
                                        {{ $component->component_code }}
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    @if($component->component_type === 'earning')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: #f0fdf4; color: #059669;">
                                            Pendapatan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: #fee2e2; color: #dc2626;">
                                            Potongan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm" style="color: var(--dash-text-body);">
                                    {{ $methodLabels[$component->calculation_method] ?? $component->calculation_method }}
                                </td>
                                <td class="px-5 py-4 text-sm" style="color: var(--dash-text-muted);">
                                    {{ $component->is_taxable ? 'Ya' : 'Tidak' }}
                                </td>
                                <td class="px-5 py-4">
                                    @if($component->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @can('payroll.component.update')
                                        <div x-data="{ open: false }" class="relative inline-block text-left">
                                            <button @click="open = !open" @click.outside="open = false" type="button"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                                    style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">edit</span>
                                                Edit
                                            </button>
                                            <div x-show="open" x-cloak x-transition
                                                 class="absolute right-0 z-20 mt-2 w-80 rounded-2xl p-4 shadow-xl"
                                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                                <form method="POST" action="{{ route('payroll.components.update', $component) }}" class="space-y-3">
                                                    @csrf @method('PUT')
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">Kode</label>
                                                            <input name="component_code"
                                                                   value="{{ old('component_code', $component->component_code) }}"
                                                                   type="text" maxlength="100" required
                                                                   class="block w-full rounded-lg px-2.5 py-1.5 text-xs uppercase focus:outline-none"
                                                                   style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">Nama</label>
                                                            <input name="component_name"
                                                                   value="{{ old('component_name', $component->component_name) }}"
                                                                   type="text" maxlength="255" required
                                                                   class="block w-full rounded-lg px-2.5 py-1.5 text-xs focus:outline-none"
                                                                   style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">Tipe</label>
                                                            <select name="component_type" required
                                                                    class="block w-full rounded-lg px-2.5 py-1.5 text-xs focus:outline-none"
                                                                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                                                @foreach($typeLabels as $v => $l)
                                                                    <option value="{{ $v }}" @selected(old('component_type', $component->component_type) === $v)>{{ $l }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">Metode</label>
                                                            <select name="calculation_method" required
                                                                    class="block w-full rounded-lg px-2.5 py-1.5 text-xs focus:outline-none"
                                                                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                                                @foreach($methodLabels as $v => $l)
                                                                    <option value="{{ $v }}" @selected(old('calculation_method', $component->calculation_method) === $v)>{{ $l }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-4 rounded-xl p-2"
                                                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                                            <input name="is_taxable" value="0" type="hidden">
                                                            <input name="is_taxable" value="1" type="checkbox"
                                                                   @checked(old('is_taxable', $component->is_taxable))
                                                                   class="h-4 w-4 rounded">
                                                            <span class="text-xs font-medium" style="color: var(--dash-text-body);">Kena pajak</span>
                                                        </label>
                                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                                            <input name="is_active" value="0" type="hidden">
                                                            <input name="is_active" value="1" type="checkbox"
                                                                   @checked(old('is_active', $component->is_active))
                                                                   class="h-4 w-4 rounded">
                                                            <span class="text-xs font-medium" style="color: var(--dash-text-body);">Aktif</span>
                                                        </label>
                                                    </div>
                                                    <button type="submit"
                                                            class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                                                            style="background-color: var(--dash-primary);">
                                                        Simpan Perubahan
                                                    </button>
                                                </form>
                                                @can('payroll.component.delete')
                                                <form method="POST" action="{{ route('payroll.components.destroy', $component) }}"
                                                      class="mt-3 pt-3"
                                                      style="border-top: 1px solid var(--dash-card-border);">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            onclick="return confirm('Hapus atau nonaktifkan komponen ini?')"
                                                            class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-colors"
                                                            style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                                                            onmouseover="this.style.backgroundColor='#fecaca'"
                                                            onmouseout="this.style.backgroundColor='#fee2e2'">
                                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                                        Hapus / Nonaktifkan
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </div>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                          style="color: var(--dash-text-faint);">payments</span>
                                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada komponen gaji.</p>
                                    <p class="text-sm mt-1" style="color: var(--dash-text-faint);">
                                        Tambahkan komponen di atas sebelum mengatur nilai kompensasi pegawai.
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
    {{-- TAB 2 — Kompensasi Pegawai --}}
    {{-- ============================= --}}
    <div x-show="tab === 'compensation'" class="space-y-6">

        @canany(['payroll.employee_compensation.create', 'payroll.employee_compensation.update'])
        <div class="rounded-2xl p-5 space-y-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined mt-0.5 text-[20px]"
                      style="color: var(--dash-primary);">person_add</span>
                <div>
                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Tetapkan nilai kompensasi</h3>
                    <p class="mt-0.5 text-sm" style="color: var(--dash-text-muted);">
                        Jika komponen sudah ada untuk pegawai ini, nilai akan diperbarui.
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
                  }">
                @csrf
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Pegawai <span style="color:#dc2626;">*</span>
                        </label>
                        <select name="_employee_id" required
                                x-model="employeeId" @change="updateAction()"
                                class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                            <option value="">Pilih pegawai</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Komponen <span style="color:#dc2626;">*</span>
                        </label>
                        <select name="payroll_component_id" required
                                class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                            <option value="">Pilih komponen</option>
                            @foreach($components->where('is_active', true) as $comp)
                                <option value="{{ $comp->id }}">{{ $comp->component_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Nilai (Rp) <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="number" name="value" required min="0" step="0.01" placeholder="0"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Catatan</label>
                        <input type="text" name="notes"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endcanany

        {{-- Tabel kompensasi --}}
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center justify-between px-5 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border);">
                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                    Semua kompensasi pegawai
                </h3>
                <span class="text-sm" style="color: var(--dash-text-muted);">
                    {{ $allCompensations->count() }} entri
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-fixed text-sm">
                    <colgroup>
                        <col class="w-[22%]">
                        <col class="w-[22%]">
                        <col class="w-[13%]">
                        <col class="w-[15%]">
                        <col>{{-- auto --}}
                        <col class="w-[10%]">
                    </colgroup>
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Pegawai</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Komponen</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Tipe</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Nilai</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Catatan</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allCompensations as $comp)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor=''">
                                <td class="px-5 py-4">
                                    <p class="font-semibold truncate" style="color: var(--dash-text-heading);">
                                        {{ $comp->employee->full_name }}
                                    </p>
                                    <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-muted);">
                                        {{ $comp->employee->employee_code }}
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-medium truncate" style="color: var(--dash-text-body);">
                                        {{ $comp->payrollComponent->component_name }}
                                    </p>
                                    <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-muted);">
                                        {{ $comp->payrollComponent->component_code }}
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    @if($comp->payrollComponent->component_type === 'earning')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: #f0fdf4; color: #059669;">
                                            Pendapatan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: #fee2e2; color: #dc2626;">
                                            Potongan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right font-semibold" style="color: var(--dash-text-heading);">
                                    Rp {{ number_format($comp->value, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 truncate" style="color: var(--dash-text-muted);">
                                    {{ $comp->notes ?? '—' }}
                                </td>
                                <td class="px-3 py-4 text-right">
                                    @canany(['payroll.employee_compensation.delete'])
                                    <form method="POST"
                                          action="{{ route('employees.payroll.compensations.destroy', [$comp->employee, $comp]) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Hapus kompensasi ini?')"
                                                title="Hapus"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                                style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                                                onmouseover="this.style.backgroundColor='#fecaca'"
                                                onmouseout="this.style.backgroundColor='#fee2e2'">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </form>
                                    @endcanany
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                          style="color: var(--dash-text-faint);">money_off</span>
                                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data kompensasi.</p>
                                    <p class="text-sm mt-1" style="color: var(--dash-text-faint);">
                                        Gunakan form di atas untuk menetapkan nilai kompensasi per pegawai.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /tab compensation --}}

</div>
@endsection
