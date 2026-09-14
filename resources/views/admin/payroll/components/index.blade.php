@extends('layouts.dashboard')

@section('title', 'Komponen Gaji')
@section('header_title', 'Komponen Gaji')

@section('content')
@php
    $typeLabels = [
        'earning' => 'Pendapatan',
        'deduction' => 'Potongan',
    ];

    $methodLabels = [
        'fixed' => 'Nilai tetap',
        'daily' => 'Harian',
        'session' => 'Per sesi',
        'percentage' => 'Persentase',
        'manual' => 'Manual',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Master payroll</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Kelola komponen gaji</h2>
        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Definisikan pendapatan dan potongan yang dipakai saat payroll digenerate. Komponen yang sudah masuk histori payroll akan dinonaktifkan saat dihapus.</p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <div class="flex gap-3">
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">error</span>
                <div>
                    <p class="font-semibold">Data belum bisa disimpan.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @can('payroll.component.create')
    <form method="POST" action="{{ route('payroll.components.store') }}" class="border border-slate-200 bg-white p-5">
        @csrf
        <div class="mb-4 flex items-start gap-3">
            <span class="material-symbols-outlined mt-0.5 text-[#215aac]" aria-hidden="true">add_circle</span>
            <div>
                <h3 class="font-jakarta text-base font-bold text-slate-900">Tambah komponen</h3>
                <p class="mt-1 text-sm text-slate-500">Kode komponen dipakai sebagai referensi kalkulasi, jadi buat singkat dan stabil.</p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div>
                <label for="component_code" class="mb-1.5 block text-sm font-medium text-slate-700">Kode komponen</label>
                <input id="component_code" name="component_code" value="{{ old('component_code') }}" type="text" maxlength="100" required placeholder="BASE_SALARY" class="block w-full border-slate-300 text-sm uppercase focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label for="component_name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama komponen</label>
                <input id="component_name" name="component_name" value="{{ old('component_name') }}" type="text" maxlength="255" required placeholder="Gaji pokok" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label for="component_type" class="mb-1.5 block text-sm font-medium text-slate-700">Tipe</label>
                <select id="component_type" name="component_type" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    @foreach ($typeLabels as $value => $label)
                        <option value="{{ $value }}" @selected(old('component_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="calculation_method" class="mb-1.5 block text-sm font-medium text-slate-700">Metode kalkulasi</label>
                <select id="calculation_method" name="calculation_method" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    @foreach ($methodLabels as $value => $label)
                        <option value="{{ $value }}" @selected(old('calculation_method') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-4">
            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                <input name="is_taxable" value="0" type="hidden">
                <input name="is_taxable" value="1" type="checkbox" @checked(old('is_taxable')) class="border-slate-300 text-[#215aac] focus:ring-[#215aac]">
                Kena pajak
            </label>
            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                <input name="is_active" value="0" type="hidden">
                <input name="is_active" value="1" type="checkbox" @checked(old('is_active', true)) class="border-slate-300 text-[#215aac] focus:ring-[#215aac]">
                Aktif
            </label>
        </div>

        <div class="mt-5">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91] focus:outline-none focus:ring-2 focus:ring-[#215aac] focus:ring-offset-2">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">save</span>
                Simpan komponen
            </button>
        </div>
    </form>
    @endcan

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Daftar komponen</h3>
            <p class="mt-1 text-sm text-slate-500">Komponen aktif akan dipertimbangkan oleh proses generate payroll sesuai metode kalkulasinya.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3">Komponen</th>
                        <th scope="col" class="px-5 py-3">Tipe</th>
                        <th scope="col" class="px-5 py-3">Metode</th>
                        <th scope="col" class="px-5 py-3">Pajak</th>
                        <th scope="col" class="px-5 py-3">Status</th>
                        <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($components as $component)
                        @php
                            $typeClass = $component->component_type === 'earning'
                                ? 'bg-emerald-100 text-emerald-800'
                                : 'bg-red-100 text-red-800';
                        @endphp
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $component->component_name }}</p>
                                <p class="mt-0.5 font-mono text-xs text-slate-500">{{ $component->component_code }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $typeClass }}">{{ $typeLabels[$component->component_type] ?? $component->component_type }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-700">{{ $methodLabels[$component->calculation_method] ?? $component->calculation_method }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ $component->is_taxable ? 'Ya' : 'Tidak' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $component->is_active ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700' }}">{{ $component->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                @can('payroll.component.update')
                                    <details class="relative inline-block text-left">
                                        <summary class="inline-flex min-h-9 cursor-pointer list-none items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#215aac] focus:ring-offset-2">
                                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">edit</span>
                                            Edit
                                        </summary>
                                        <div class="absolute right-0 z-10 mt-2 w-80 border border-slate-300 bg-white p-4 text-left shadow-lg">
                                            <form method="POST" action="{{ route('payroll.components.update', $component) }}" class="space-y-3">
                                                @csrf
                                                @method('PUT')
                                                <div>
                                                    <label for="edit-code-{{ $component->id }}" class="mb-1 block text-xs font-semibold text-slate-700">Kode</label>
                                                    <input id="edit-code-{{ $component->id }}" name="component_code" value="{{ old('component_code', $component->component_code) }}" type="text" maxlength="100" required class="block w-full border-slate-300 text-sm uppercase focus:border-[#215aac] focus:ring-[#215aac]">
                                                </div>
                                                <div>
                                                    <label for="edit-name-{{ $component->id }}" class="mb-1 block text-xs font-semibold text-slate-700">Nama</label>
                                                    <input id="edit-name-{{ $component->id }}" name="component_name" value="{{ old('component_name', $component->component_name) }}" type="text" maxlength="255" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label for="edit-type-{{ $component->id }}" class="mb-1 block text-xs font-semibold text-slate-700">Tipe</label>
                                                        <select id="edit-type-{{ $component->id }}" name="component_type" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                                                            @foreach ($typeLabels as $value => $label)
                                                                <option value="{{ $value }}" @selected(old('component_type', $component->component_type) === $value)>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label for="edit-method-{{ $component->id }}" class="mb-1 block text-xs font-semibold text-slate-700">Metode</label>
                                                        <select id="edit-method-{{ $component->id }}" name="calculation_method" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                                                            @foreach ($methodLabels as $value => $label)
                                                                <option value="{{ $value }}" @selected(old('calculation_method', $component->calculation_method) === $value)>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="flex flex-wrap gap-4">
                                                    <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                                        <input name="is_taxable" value="0" type="hidden">
                                                        <input name="is_taxable" value="1" type="checkbox" @checked(old('is_taxable', $component->is_taxable)) class="border-slate-300 text-[#215aac] focus:ring-[#215aac]">
                                                        Kena pajak
                                                    </label>
                                                    <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                                        <input name="is_active" value="0" type="hidden">
                                                        <input name="is_active" value="1" type="checkbox" @checked(old('is_active', $component->is_active)) class="border-slate-300 text-[#215aac] focus:ring-[#215aac]">
                                                        Aktif
                                                    </label>
                                                </div>
                                                <button type="submit" class="inline-flex min-h-9 w-full items-center justify-center bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91] focus:outline-none focus:ring-2 focus:ring-[#215aac] focus:ring-offset-2">Simpan perubahan</button>
                                            </form>
                                            <form method="POST" action="{{ route('payroll.components.destroy', $component) }}" class="mt-3 border-t border-slate-200 pt-3">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex min-h-9 w-full items-center justify-center border border-red-200 px-3 text-xs font-semibold text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Hapus atau nonaktifkan</button>
                                            </form>
                                        </div>
                                    </details>
                                @else
                                    <span class="text-xs font-medium text-slate-500">Tidak tersedia</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300" aria-hidden="true">price_change</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada komponen gaji.</p>
                                <p class="mt-1 text-sm text-slate-500">Tambahkan komponen pertama sebelum mengatur nilai kompensasi pegawai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
