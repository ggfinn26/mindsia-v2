@extends('layouts.dashboard')

@section('title', 'Kelola Komponen Gaji')
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

<div class="max-w-7xl mx-auto space-y-6" x-data="{ tab: '{{ $activeTab }}' }">

    <div>
        <p class="text-sm font-medium text-[#725c00]">Payroll</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Kelola Komponen Gaji</h2>
        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Definisikan komponen gaji dan tetapkan nilai per pegawai.</p>
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

    {{-- Tab Nav --}}
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex gap-6">
            <button @click="tab = 'components'"
                    :class="tab === 'components' ? 'border-[#215aac] text-[#215aac]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                    class="border-b-2 pb-3 text-sm font-semibold transition-colors">
                Master Komponen
            </button>
            <button @click="tab = 'compensation'"
                    :class="tab === 'compensation' ? 'border-[#215aac] text-[#215aac]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                    class="border-b-2 pb-3 text-sm font-semibold transition-colors">
                Kompensasi Pegawai
            </button>
        </nav>
    </div>

    {{-- ============================= --}}
    {{-- TAB 1 — Master Komponen --}}
    {{-- ============================= --}}
    <div x-show="tab === 'components'">

        @can('payroll.component.create')
        <form method="POST" action="{{ route('payroll.components.store') }}" class="border border-slate-200 bg-white p-5">
            @csrf
            <div class="mb-4 flex items-start gap-3">
                <span class="material-symbols-outlined mt-0.5 text-[#215aac]">add_circle</span>
                <div>
                    <h3 class="font-jakarta text-base font-bold text-slate-900">Tambah komponen baru</h3>
                    <p class="mt-0.5 text-sm text-slate-500">Kode komponen: huruf kapital dan garis bawah, contoh <code class="font-mono text-xs">GAJI_POKOK</code>.</p>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Kode komponen <span class="text-rose-500">*</span></label>
                    <input name="component_code" value="{{ old('component_code') }}" type="text" maxlength="100" required
                           placeholder="BASE_SALARY"
                           class="block w-full border-slate-300 text-sm uppercase focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama komponen <span class="text-rose-500">*</span></label>
                    <input name="component_name" value="{{ old('component_name') }}" type="text" maxlength="255" required
                           placeholder="Gaji pokok"
                           class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Tipe <span class="text-rose-500">*</span></label>
                    <select name="component_type" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        @foreach ($typeLabels as $value => $label)
                            <option value="{{ $value }}" @selected(old('component_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Metode kalkulasi <span class="text-rose-500">*</span></label>
                    <select name="calculation_method" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        @foreach ($methodLabels as $value => $label)
                            <option value="{{ $value }}" @selected(old('calculation_method') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-6">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input name="is_taxable" value="0" type="hidden">
                    <input name="is_taxable" value="1" type="checkbox" @checked(old('is_taxable'))
                           class="h-4 w-4 border-slate-300 text-[#215aac] focus:ring-[#215aac]">
                    <span class="text-sm font-medium text-slate-700">Kena pajak</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input name="is_active" value="0" type="hidden">
                    <input name="is_active" value="1" type="checkbox" @checked(old('is_active', true))
                           class="h-4 w-4 border-slate-300 text-[#215aac] focus:ring-[#215aac]">
                    <span class="text-sm font-medium text-slate-700">Aktif</span>
                </label>
                <button type="submit"
                        class="ml-auto inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Komponen
                </button>
            </div>
        </form>
        @endcan

        <div class="overflow-hidden border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="font-jakarta text-base font-bold text-slate-900">Daftar komponen</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Komponen</th>
                            <th class="px-5 py-3">Tipe</th>
                            <th class="px-5 py-3">Metode</th>
                            <th class="px-5 py-3">Pajak</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($components as $component)
                            @php
                                $typeClass = $component->component_type === 'earning'
                                    ? 'bg-emerald-100 text-emerald-800'
                                    : 'bg-red-100 text-red-800';
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $component->component_name }}</p>
                                    <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $component->component_code }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $typeClass }}">
                                        {{ $typeLabels[$component->component_type] ?? $component->component_type }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $methodLabels[$component->calculation_method] ?? $component->calculation_method }}
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $component->is_taxable ? 'Ya' : 'Tidak' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $component->is_active ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $component->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @can('payroll.component.update')
                                        <div x-data="{ open: false }" class="relative inline-block text-left">
                                            <button @click="open = !open" @click.outside="open = false" type="button"
                                                    class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Edit
                                                <span class="material-symbols-outlined text-[16px]">expand_more</span>
                                            </button>
                                            <div x-show="open" x-cloak x-transition
                                                 class="absolute right-0 z-20 mt-2 w-80 border border-slate-200 bg-white p-4 shadow-lg">
                                                <form method="POST" action="{{ route('payroll.components.update', $component) }}" class="space-y-3">
                                                    @csrf @method('PUT')
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold text-slate-700">Kode</label>
                                                            <input name="component_code" value="{{ old('component_code', $component->component_code) }}"
                                                                   type="text" maxlength="100" required
                                                                   class="block w-full border-slate-300 text-sm uppercase focus:border-[#215aac] focus:ring-[#215aac]">
                                                        </div>
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold text-slate-700">Nama</label>
                                                            <input name="component_name" value="{{ old('component_name', $component->component_name) }}"
                                                                   type="text" maxlength="255" required
                                                                   class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                                                        </div>
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold text-slate-700">Tipe</label>
                                                            <select name="component_type" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                                                                @foreach ($typeLabels as $v => $l)
                                                                    <option value="{{ $v }}" @selected(old('component_type', $component->component_type) === $v)>{{ $l }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold text-slate-700">Metode</label>
                                                            <select name="calculation_method" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                                                                @foreach ($methodLabels as $v => $l)
                                                                    <option value="{{ $v }}" @selected(old('calculation_method', $component->calculation_method) === $v)>{{ $l }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-4 bg-slate-50 p-2 border border-slate-100">
                                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                                            <input name="is_taxable" value="0" type="hidden">
                                                            <input name="is_taxable" value="1" type="checkbox"
                                                                   @checked(old('is_taxable', $component->is_taxable))
                                                                   class="h-4 w-4 border-slate-300 text-[#215aac] focus:ring-[#215aac]">
                                                            <span class="text-xs font-medium text-slate-700">Kena pajak</span>
                                                        </label>
                                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                                            <input name="is_active" value="0" type="hidden">
                                                            <input name="is_active" value="1" type="checkbox"
                                                                   @checked(old('is_active', $component->is_active))
                                                                   class="h-4 w-4 border-slate-300 text-[#215aac] focus:ring-[#215aac]">
                                                            <span class="text-xs font-medium text-slate-700">Aktif</span>
                                                        </label>
                                                    </div>
                                                    <button type="submit"
                                                            class="inline-flex min-h-9 w-full items-center justify-center gap-2 bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">
                                                        Simpan Perubahan
                                                    </button>
                                                </form>
                                                @can('payroll.component.delete')
                                                <form method="POST" action="{{ route('payroll.components.destroy', $component) }}"
                                                      class="mt-3 pt-3 border-t border-slate-100">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            onclick="return confirm('Hapus atau nonaktifkan komponen ini?')"
                                                            class="inline-flex min-h-9 w-full items-center justify-center border border-red-200 px-3 text-xs font-semibold text-red-700 hover:bg-red-50">
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
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <span class="material-symbols-outlined text-3xl text-slate-300">payments</span>
                                    <p class="mt-2 font-semibold text-slate-700">Belum ada komponen gaji.</p>
                                    <p class="mt-1 text-sm text-slate-500">Tambahkan komponen di atas sebelum mengatur nilai kompensasi pegawai.</p>
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
    <div x-show="tab === 'compensation'">

        @canany(['payroll.employee_compensation.create', 'payroll.employee_compensation.update'])
        <form method="POST" action="" id="compensation-form" class="border border-slate-200 bg-white p-5">
            @csrf
            <div class="mb-4 flex items-start gap-3">
                <span class="material-symbols-outlined mt-0.5 text-[#215aac]">person_add</span>
                <div>
                    <h3 class="font-jakarta text-base font-bold text-slate-900">Tetapkan nilai kompensasi</h3>
                    <p class="mt-0.5 text-sm text-slate-500">Jika komponen sudah ada untuk pegawai ini, nilai akan diperbarui.</p>
                </div>
            </div>
            <div x-data="{
                employeeId: '',
                updateAction() {
                    const form = document.getElementById('compensation-form');
                    if (this.employeeId) {
                        form.action = '/employees/' + this.employeeId + '/compensations';
                    }
                }
            }" class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Pegawai <span class="text-rose-500">*</span></label>
                    <select name="_employee_id" required
                            x-model="employeeId" @change="updateAction()"
                            class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        <option value="">Pilih pegawai...</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Komponen <span class="text-rose-500">*</span></label>
                    <select name="payroll_component_id" required
                            class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        <option value="">Pilih komponen...</option>
                        @foreach ($components->where('is_active', true) as $comp)
                            <option value="{{ $comp->id }}">{{ $comp->component_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Nilai (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="value" required min="0" step="0.01" placeholder="0"
                           class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Catatan</label>
                    <input type="text" name="notes"
                           class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="inline-flex min-h-10 w-full items-center justify-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan
                    </button>
                </div>
            </div>
        </form>
        @endcanany

        <div class="overflow-hidden border border-slate-200 bg-white">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h3 class="font-jakarta text-base font-bold text-slate-900">Semua kompensasi pegawai</h3>
                <span class="text-sm text-slate-500">{{ $allCompensations->count() }} entri</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Pegawai</th>
                            <th class="px-5 py-3">Komponen</th>
                            <th class="px-5 py-3">Tipe</th>
                            <th class="px-5 py-3 text-right">Nilai</th>
                            <th class="px-5 py-3">Catatan</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($allCompensations as $comp)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $comp->employee->full_name }}</p>
                                    <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $comp->employee->employee_code }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-medium text-slate-800">{{ $comp->payrollComponent->component_name }}</p>
                                    <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $comp->payrollComponent->component_code }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $comp->payrollComponent->component_type === 'earning' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $comp->payrollComponent->component_type === 'earning' ? 'Pendapatan' : 'Potongan' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right font-semibold text-slate-900">
                                    Rp {{ number_format($comp->value, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-slate-500">{{ $comp->notes ?? '—' }}</td>
                                <td class="px-5 py-4 text-right">
                                    @canany(['payroll.employee_compensation.delete'])
                                    <form method="POST" action="{{ route('employees.payroll.compensations.destroy', [$comp->employee, $comp]) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus kompensasi ini?')"
                                                class="inline-flex min-h-9 items-center border border-red-200 px-3 text-xs font-semibold text-red-700 hover:bg-red-50">
                                            Hapus
                                        </button>
                                    </form>
                                    @endcanany
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <span class="material-symbols-outlined text-3xl text-slate-300">money_off</span>
                                    <p class="mt-2 font-semibold text-slate-700">Belum ada data kompensasi.</p>
                                    <p class="mt-1 text-sm text-slate-500">Gunakan form di atas untuk menetapkan nilai kompensasi per pegawai.</p>
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
