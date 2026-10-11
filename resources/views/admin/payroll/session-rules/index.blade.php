@extends('layouts.dashboard')

@section('title', 'Aturan Kompensasi Sesi')
@section('header_title', 'Aturan Kompensasi Sesi')

@section('content')
@php
    $scopeLabels = ['global' => 'Global', 'role' => 'Role', 'position' => 'Posisi', 'employee' => 'Pegawai'];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Payroll</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Aturan Kompensasi Sesi</h2>
        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Atur tarif per sesi untuk tutor. Resolusi scope: employee > posisi > role > global.</p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Add Form --}}
    <form method="POST" action="{{ route('payroll.session-rules.store') }}" class="border border-slate-200 bg-white p-5">
        @csrf
        <div class="mb-4 flex items-start gap-3">
            <span class="material-symbols-outlined mt-0.5 text-[#215aac]">add_circle</span>
            <h3 class="font-jakarta text-base font-bold text-slate-900">Tambah aturan baru</h3>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Kode Aturan</label>
                <input type="text" name="rule_code" value="{{ old('rule_code') }}" required maxlength="100"
                       placeholder="TUTOR_REGULAR" class="block w-full border-slate-300 text-sm uppercase focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama Aturan</label>
                <input type="text" name="rule_name" value="{{ old('rule_name') }}" required
                       placeholder="Tarif Tutor Regular" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Scope</label>
                <select name="scope_type" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    @foreach ($scopeLabels as $val => $label)
                        <option value="{{ $val }}" @selected(old('scope_type') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Tarif per Sesi (Rp)</label>
                <input type="number" name="amount_per_session" value="{{ old('amount_per_session') }}" required min="0" step="0.01"
                       placeholder="0" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Catatan</label>
                <input type="text" name="notes" value="{{ old('notes') }}"
                       class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div class="flex items-end gap-3">
                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 pb-2.5">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="border-slate-300 text-[#215aac] focus:ring-[#215aac]">
                    Aktif
                </label>
                <button type="submit" class="inline-flex min-h-10 flex-1 items-center justify-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan
                </button>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Daftar aturan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Aturan</th>
                        <th class="px-5 py-3">Scope</th>
                        <th class="px-5 py-3">Target</th>
                        <th class="px-5 py-3 text-right">Tarif/Sesi</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rules as $rule)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $rule->rule_name }}</p>
                                <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $rule->rule_code }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $scopeLabels[$rule->scope_type] ?? $rule->scope_type }}</td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $rule->role?->name ?? $rule->position?->position_name ?? $rule->employee?->full_name ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-900">Rp {{ number_format($rule->amount_per_session, 0, ',', '.') }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $rule->is_active ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <form method="POST" action="{{ route('payroll.session-rules.destroy', $rule) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Nonaktifkan aturan ini?')"
                                            class="inline-flex min-h-9 items-center gap-1 border border-red-200 px-3 text-xs font-semibold text-red-700 hover:bg-red-50">
                                        Nonaktifkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">tune</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada aturan kompensasi sesi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
