@extends('layouts.dashboard')

@section('header_title', 'Assign Template per Posisi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-[color:var(--dash-text-muted)]">Payroll → Template Slip</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-[color:var(--dash-text-heading)]">Assign Template per Posisi</h1>
            <p class="mt-1 text-sm text-[color:var(--dash-text-muted)]">Tentukan template slip untuk tiap posisi. Kosong = auto-detect berdasarkan nama posisi.</p>
        </div>
        <a href="{{ route('payroll.slip-templates.index') }}"
           class="inline-flex items-center gap-2 border border-[color:var(--dash-input-border)] px-4 py-2 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] shrink-0">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <form action="{{ route('payroll.slip-templates.assign.update') }}" method="POST"
          class="rounded-2xl border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        @csrf
        <div>
            <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-nav-hover-bg)] text-left text-xs font-bold uppercase tracking-wider text-[color:var(--dash-text-muted)]">
                    <tr>
                        <th class="px-6 py-4">Posisi</th>
                        <th class="px-6 py-4">Template Slip</th>
                        <th class="px-6 py-4 text-[color:var(--dash-text-faint)]">Auto-detect</th>
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
                        <td class="px-6 py-3 font-medium text-[color:var(--dash-text-heading)]">{{ $position->position_name }}</td>
                        <td class="px-6 py-3">
                            <select name="assignments[{{ $position->id }}]"
                                    class="w-full rounded-lg border border-[color:var(--dash-card-border)] px-3 py-1.5 text-sm text-[color:var(--dash-text-body)] focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)]">
                                <option value="" {{ $current === '' ? 'selected' : '' }}>— Auto-detect —</option>
                                @foreach($typeLabels as $type => $label)
                                <option value="{{ $type }}" {{ $current === $type ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-3 text-xs text-[color:var(--dash-text-faint)]">
                            @if(!$current)
                            <span class="inline-flex items-center gap-1 rounded-full bg-[var(--dash-nav-hover-bg)] px-2 py-0.5 font-medium text-[color:var(--dash-text-muted)]">
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
        <div class="border-t border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] px-6 py-4 flex justify-end">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-[var(--dash-primary,#215aac)] px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
                <span class="material-symbols-outlined text-[16px]">save</span> Simpan Assignment
            </button>
        </div>
    </form>

    <div class="rounded-xl border border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] p-5 text-xs text-[color:var(--dash-text-muted)] space-y-1">
        <p class="font-semibold text-[color:var(--dash-text-body)]">Cara kerja:</p>
        <p>• <strong>Auto-detect</strong> — sistem cek nama posisi mengandung "official tutor" atau "regular tutor", sisanya pakai Marketing</p>
        <p>• <strong>Override</strong> — pilih template spesifik untuk menimpa auto-detect</p>
        <p>• Perubahan berlaku saat slip berikutnya di-generate</p>
    </div>
</div>
@endsection
