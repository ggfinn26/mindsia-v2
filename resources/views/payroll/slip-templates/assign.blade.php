@extends('layouts.dashboard')

@section('title', 'Assign Template per Posisi')
@section('header_title', 'Template Slip Gaji')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('payroll.slip-templates.index') }}" class="dash-back mb-2">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke Template Slip Gaji
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Gaji & Bonus</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Assign Template per Posisi</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Tentukan template slip untuk tiap posisi. Kosong = auto-detect berdasarkan nama posisi.
        </p>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: var(--dash-status-success-bg); border-color: rgba(5, 150, 105, 0.25); color: var(--dash-status-success-text);">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Assignment Form Card --}}
    <form action="{{ route('payroll.slip-templates.assign.update') }}" method="POST"
          class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        @csrf
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Posisi</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Template Slip</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Auto-detect</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($positions as $position)
                    @php
                        $autoType = match(true) {
                            str_contains(strtolower($position->position_name), 'official tutor') => 'official_tutor',
                            str_contains(strtolower($position->position_name), 'regular tutor') => 'regular_tutor',
                            default => 'marketing',
                        };
                        $current = $position->slip_template_type ?? '';
                    @endphp
                    <tr class="transition-colors"
                        style="border-bottom: 1px solid var(--dash-card-border); {{ $current ? 'background-color: var(--dash-primary-bg);' : '' }}"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='{{ $current ? 'var(--dash-primary-bg)' : 'transparent' }}'">
                        <td class="px-6 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                            {{ $position->position_name }}
                        </td>
                        <td class="px-6 py-3.5">
                            <select name="assignments[{{ $position->id }}]"
                                    class="w-full rounded-lg text-sm px-3 py-1.5 transition-colors"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); outline: none;"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                <option value="" {{ $current === '' ? 'selected' : '' }}>Pilih Auto-detect</option>
                                @foreach($typeLabels as $type => $label)
                                    <option value="{{ $type }}" {{ $current === $type ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-3.5 text-xs">
                            @if(!$current)
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 font-medium"
                                      style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                                    <span class="material-symbols-outlined text-[14px]">auto_fix</span>
                                    {{ $typeLabels[$autoType] ?? $autoType }}
                                </span>
                            @else
                                <span class="font-medium" style="color: var(--dash-primary-text);">Override aktif</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 flex justify-end"
             style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg px-6 py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">save</span> Simpan Assignment
            </button>
        </div>
    </form>

    {{-- Help Card --}}
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 text-xs space-y-1.5"
         style="color: var(--dash-text-muted);">
        <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">Cara kerja:</p>
        <p>• <strong>Auto-detect</strong> - sistem cek nama posisi mengandung "official tutor" atau "regular tutor", sisanya menggunakan Marketing</p>
        <p>• <strong>Override</strong> - pilih template spesifik untuk menimpa auto-detect</p>
        <p>• Perubahan berlaku saat slip berikutnya di-generate</p>
    </div>
</div>
@endsection
