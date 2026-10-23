@extends('layouts.dashboard')

@section('title', 'Target Pemasaran - MINDSIA')
@section('header_title', 'Target Pemasaran')

@section('content')
@php
    $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
@endphp
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6" x-data="targetPage()">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Pemasaran</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Target Pemasaran</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola target kelas dan omzet per pegawai maupun default per posisi.
            </p>
        </div>

        {{-- Period filter --}}
        <form method="GET" action="{{ route('marketing-target.index') }}" class="flex items-center gap-2">
            <select name="month" onchange="this.form.submit()"
                class="px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                onfocus="this.style.borderColor='var(--dash-primary)'"
                onblur="this.style.borderColor='var(--dash-input-border)'">
                @foreach ($monthNames as $num => $name)
                    <option value="{{ $num }}" {{ $month === $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="year" onchange="this.form.submit()"
                class="px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                onfocus="this.style.borderColor='var(--dash-primary)'"
                onblur="this.style.borderColor='var(--dash-input-border)'">
                @foreach (range(now()->year - 1, now()->year + 1) as $y)
                    <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="border-b" style="border-color: var(--dash-card-border);">
        <nav class="flex gap-4 sm:gap-6" aria-label="Tabs">
            <button type="button"
                @click="tab = 'global'"
                :style="tab === 'global' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'border-color: transparent; color: var(--dash-text-muted);'"
                class="py-3 text-sm font-semibold border-b-2 focus:outline-none transition-colors -mb-px inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">public</span>
                Default Global
                <span class="ml-1.5 px-2 py-0.5 text-[10px] font-bold rounded-full"
                      style="background-color: color-mix(in srgb, var(--dash-primary) 15%, transparent); color: var(--dash-primary);">Berlaku Seterusnya</span>
            </button>
            <button type="button"
                @click="tab = 'position'"
                :style="tab === 'position' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'border-color: transparent; color: var(--dash-text-muted);'"
                class="py-3 text-sm font-semibold border-b-2 focus:outline-none transition-colors -mb-px inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">badge</span>
                Target Per Posisi
            </button>
            <button type="button"
                @click="tab = 'employee'"
                :style="tab === 'employee' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'border-color: transparent; color: var(--dash-text-muted);'"
                class="py-3 text-sm font-semibold border-b-2 focus:outline-none transition-colors -mb-px inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">person</span>
                Target Per Pegawai
                <span class="ml-1.5 px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-700">Override</span>
            </button>
        </nav>
    </div>

    {{-- Tab: Default Global --}}
    <div x-show="tab === 'global'" x-cloak class="space-y-4">
        <p class="text-sm font-medium" style="color: var(--dash-text-muted);">
            Default target berlaku untuk semua periode yang tidak memiliki target spesifik. Fallback terakhir sebelum otomatisasi.
        </p>
        @forelse ($positions as $position)
            @php $globalExisting = $globalDefaults[$position->id] ?? null; @endphp
            <div class="rounded-2xl p-5"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <form method="POST" action="{{ route('marketing-target.global-default') }}"
                    @submit.prevent="confirmSave($el)">
                    @csrf
                    <input type="hidden" name="position_id" value="{{ $position->id }}">

                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <p class="font-bold text-sm" style="color: var(--dash-text-heading);">{{ $position->position_name }}</p>
                            @if($globalExisting)
                                <p class="text-xs mt-0.5 font-medium" style="color: var(--dash-primary-text);">Tersimpan - berlaku semua periode</p>
                            @else
                                <p class="text-xs mt-0.5 font-medium" style="color: var(--dash-text-faint);">Belum diatur</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Target Kelas</label>
                            <input type="number" name="classes_target" min="0"
                                value="{{ $globalExisting?->classes_target ?? 0 }}"
                                class="w-32 px-3 py-2 text-sm rounded-lg focus:outline-none transition-all text-center"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                        <div x-data="{ rawOmzet: '{{ $globalExisting?->omzet_target ?? 0 }}' }">
                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Target Omzet</label>
                            <input type="hidden" name="omzet_target" x-model="rawOmzet">
                            <input type="text"
                                :value="formatRupiah(rawOmzet)"
                                @input="rawOmzet = parseRupiah($event.target.value)"
                                placeholder="Rp 0"
                                class="w-48 px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        @empty
            <div class="rounded-2xl p-12 text-center"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Tidak ada posisi ditemukan.</p>
            </div>
        @endforelse
    </div>

    {{-- Tab: Target Per Posisi --}}
    <div x-show="tab === 'position'" x-cloak class="space-y-4">
        <p class="text-sm font-medium" style="color: var(--dash-text-muted);">
            Default target untuk semua marketer dengan posisi ini di periode <strong>{{ $monthNames[$month] }} {{ $year }}</strong>.
        </p>
        @forelse ($positions as $position)
            @php
                $existing = $positionTargets[$position->id] ?? null;
            @endphp
            <div class="rounded-2xl p-5"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <form method="POST" action="{{ route('marketing-target.position-default') }}"
                    @submit.prevent="confirmSave($el)">
                    @csrf
                    <input type="hidden" name="position_id" value="{{ $position->id }}">
                    <input type="hidden" name="period_month" value="{{ $month }}">
                    <input type="hidden" name="period_year" value="{{ $year }}">

                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <p class="text-sm font-bold" style="color: var(--dash-text-heading);">{{ $position->position_name }}</p>
                            @if ($existing)
                                <p class="text-xs mt-0.5 font-medium" style="color: var(--dash-text-muted);">Diperbarui: {{ $existing->updated_at->format('d M Y') }}</p>
                            @else
                                <p class="text-xs mt-0.5 font-medium" style="color: #d97706;">Belum diset - fallback ke auto</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Target Kelas / Bulan</label>
                            <input type="number" name="classes_target" min="0"
                                value="{{ old('classes_target', $existing?->classes_target ?? 0) }}"
                                class="w-32 px-3 py-2 text-sm rounded-lg focus:outline-none transition-all text-center"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                        <div x-data="{ rawOmzet: '{{ old('omzet_target', $existing?->omzet_target ?? 0) }}' }">
                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Target Omzet</label>
                            <input type="hidden" name="omzet_target" x-model="rawOmzet">
                            <input type="text"
                                :value="formatRupiah(rawOmzet)"
                                @input="rawOmzet = parseRupiah($event.target.value)"
                                placeholder="Rp 0"
                                class="w-48 px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                        <div>
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[16px]">save</span>
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @empty
            <div class="rounded-2xl p-12 text-center"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada posisi terdaftar.</p>
            </div>
        @endforelse
    </div>

    {{-- Tab: Target Per Pegawai --}}
    <div x-show="tab === 'employee'" x-cloak class="space-y-4">
        <p class="text-sm font-medium" style="color: var(--dash-text-muted);">
            Override target individu untuk periode <strong>{{ $monthNames[$month] }} {{ $year }}</strong>.
            Kosongkan jika ingin mengikuti default posisi.
        </p>

        {{-- Filter Pegawai --}}
        <form method="GET" action="{{ route('marketing-target.index') }}"
              class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
              style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Cari Pegawai</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                          style="color: var(--dash-text-muted);">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai..."
                        class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
            </div>
            <div class="w-full sm:w-48">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Cabang</label>
                <select name="branch_id" onchange="this.form.submit()"
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Cabang</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    Filter
                </button>
                @if(request()->hasAny(['search', 'branch_id']))
                    <a href="{{ route('marketing-target.index', ['month' => $month, 'year' => $year]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                       style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Pegawai & Posisi</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Target Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Target Omzet</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Sumber</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            @php
                                $existing = $employeeTargets[$employee->id] ?? null;
                                $positionName = $employee->currentStatus?->position?->position_name ?? '-';
                                $source = $existing ? 'employee' : ($positionTargets[$employee->currentStatus?->position_id ?? 0] ?? null ? 'position' : 'auto');
                                $sourceLabel = match($source) {
                                    'employee' => ['Override', 'bg-indigo-100 text-indigo-700'],
                                    'position' => ['Posisi', 'bg-blue-100 text-blue-700'],
                                    default    => ['Auto', 'bg-gray-100 text-gray-500'],
                                };
                            @endphp
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'"
                                x-data="{ rawOmzet: '{{ $existing?->omzet_target ?? '' }}' }">
                                <td class="px-4 py-3">
                                    <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $employee->full_name }}</p>
                                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $positionName }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <form method="POST" action="{{ route('marketing-target.employee') }}"
                                        @submit.prevent="confirmSave($el)" class="flex items-center gap-2 justify-center">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                        <input type="hidden" name="period_month" value="{{ $month }}">
                                        <input type="hidden" name="period_year" value="{{ $year }}">
                                        <input type="number" name="classes_target" min="0"
                                            value="{{ $existing?->classes_target ?? '' }}"
                                            placeholder="-"
                                            class="w-20 px-2 py-1.5 text-sm rounded-lg text-center focus:outline-none transition-all"
                                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'"
                                            onblur="this.style.borderColor='var(--dash-input-border)'">
                                </td>
                                <td class="px-4 py-3">
                                        <input type="hidden" name="omzet_target" x-model="rawOmzet">
                                        <input type="text"
                                            :value="formatRupiah(rawOmzet)"
                                            @input="rawOmzet = parseRupiah($event.target.value)"
                                            placeholder="Rp 0"
                                            class="w-36 px-2.5 py-1.5 text-sm rounded-lg focus:outline-none transition-all"
                                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'"
                                            onblur="this.style.borderColor='var(--dash-input-border)'">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full {{ $sourceLabel[1] }}">
                                        {{ $sourceLabel[0] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                            style="background-color: var(--dash-primary);">
                                            <span class="material-symbols-outlined text-[14px]">save</span>
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">badge</span>
                                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada pegawai aktif.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($employees->hasPages())
                <div class="px-5 py-4 border-t" style="border-color: var(--dash-card-border);">
                    {{ $employees->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Confirm Modal (Custom Dashboard Modal Dialog) --}}
    <div
        x-show="confirm.open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @keydown.escape.window="confirm.open = false"
        style="display: none;">
        <div x-show="confirm.open"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
             @click="confirm.open = false"></div>
        <div x-show="confirm.open"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-sm rounded-2xl shadow-2xl z-10"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             @click.stop>
            <div class="rounded-t-2xl px-6 py-5"
                 style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                         style="background-color: color-mix(in srgb, var(--dash-primary) 15%, transparent); color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[22px]">save</span>
                    </div>
                    <div>
                        <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Simpan Target?</h3>
                        <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                            Data target akan diperbarui untuk periode yang dipilih.
                        </p>
                    </div>
                </div>
            </div>
            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                <button type="button" @click="confirm.open = false"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </button>
                <button type="button" @click="submitConfirmed()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Ya, Simpan
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function targetPage() {
    const params = new URLSearchParams(window.location.search);
    const initTab = (params.has('search') || params.has('branch_id') || params.has('page')) ? 'employee' : 'position';
    return {
        tab: initTab,
        confirm: { open: false, form: null },
        confirmSave(form) {
            this.confirm.form = form;
            this.confirm.open = true;
        },
        submitConfirmed() {
            this.confirm.open = false;
            if (this.confirm.form) {
                this.confirm.form.submit();
            }
        },
        formatRupiah(value) {
            if (value === null || value === '') return '';
            let val = value.toString().replace(/[^0-9]/g, '');
            if (!val) return '';
            return 'Rp ' + parseInt(val, 10).toLocaleString('id-ID');
        },
        parseRupiah(value) {
            return value.toString().replace(/[^0-9]/g, '');
        }
    };
}
</script>
@endsection
