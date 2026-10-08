@extends('layouts.dashboard')

@section('header_title', 'Assign Template per Posisi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Payroll → Template Slip</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Assign Template per Posisi</h1>
            <p class="mt-1 text-sm text-slate-500">Tentukan template slip untuk tiap posisi. Kosong = auto-detect berdasarkan nama posisi.</p>
        </div>
        <a href="{{ route('payroll.slip-templates.index') }}"
           class="inline-flex items-center gap-2 border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 shrink-0">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <form action="{{ route('payroll.slip-templates.assign.update') }}" method="POST"
          class="rounded-xl border border-slate-100 bg-white shadow-sm">
        @csrf
        <div>
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Posisi</th>
                        <th class="px-6 py-4">Template Slip</th>
                        <th class="px-6 py-4 text-slate-400">Auto-detect</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($positions as $position)
                    @php
                        $autoType = match(true) {
                            str_contains(strtolower($position->position_name), 'official tutor') => 'official_tutor',
                            str_contains(strtolower($position->position_name), 'regular tutor') => 'regular_tutor',
                            default => 'marketing',
                        };
                        $current = $position->slip_template_type ?? '';
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-3 font-medium text-slate-800">{{ $position->position_name }}</td>
                        <td class="px-6 py-3">
                            <select name="assignments[{{ $position->id }}]"
                                    class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-700 focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                                <option value="" {{ $current === '' ? 'selected' : '' }}>— Auto-detect —</option>
                                @foreach($typeLabels as $type => $label)
                                <option value="{{ $type }}" {{ $current === $type ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-3 text-xs text-slate-400">
                            @if(!$current)
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-500">
                                <span class="material-symbols-outlined text-[12px]">auto_fix</span>
                                {{ $typeLabels[$autoType] }}
                            </span>
                            @else
                            <span class="text-slate-300">Override aktif</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 flex justify-end">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#215aac] px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
                <span class="material-symbols-outlined text-[16px]">save</span> Simpan Assignment
            </button>
        </div>
    </form>

    <div class="rounded-xl border border-slate-100 bg-slate-50 p-5 text-xs text-slate-500 space-y-1">
        <p class="font-semibold text-slate-700">Cara kerja:</p>
        <p>• <strong>Auto-detect</strong> — sistem cek nama posisi mengandung "official tutor" atau "regular tutor", sisanya pakai Marketing</p>
        <p>• <strong>Override</strong> — pilih template spesifik untuk menimpa auto-detect</p>
        <p>• Perubahan berlaku saat slip berikutnya di-generate</p>
    </div>
</div>
@endsection
