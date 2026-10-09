@extends('layouts.dashboard')

@section('title', 'Daftar Pegawai - MINDSIA')
@section('header_title', 'Daftar Pegawai')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Daftar Pegawai</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Kelola data seluruh pegawai aktif dan nonaktif.
            </p>
        </div>
        <a href="{{ route('employees.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity self-start sm:self-auto"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            Tambah Pegawai
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium mb-6"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium mb-6"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter bar --}}
    <div class="rounded-2xl p-4 mb-6"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" action="{{ route('employees.index') }}"
              class="flex flex-col sm:flex-row flex-wrap gap-3 items-end">

            {{-- Search --}}
            <div class="w-full sm:flex-1 sm:min-w-[200px]">
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                       style="color: var(--dash-text-muted);">Pencarian</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none material-symbols-outlined text-[18px]"
                          style="color: var(--dash-text-faint);">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama atau kode pegawai..."
                           style="padding-left: 2.5rem !important;">
                </div>
            </div>

            {{-- Status --}}
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                       style="color: var(--dash-text-muted);">Status</label>
                <select name="is_active" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="1" @if(request('is_active') === '1') selected @endif>Aktif</option>
                    <option value="0" @if(request('is_active') === '0') selected @endif>Nonaktif</option>
                </select>
            </div>

            {{-- Region --}}
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                       style="color: var(--dash-text-muted);">Region</label>
                <select name="region_id" onchange="this.form.submit()">
                    <option value="">Semua Region</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}"
                                @if(request('region_id') == $region->id) selected @endif>
                            {{ $region->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Cabang --}}
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                       style="color: var(--dash-text-muted);">Cabang</label>
                <select name="branch_id" onchange="this.form.submit()">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}"
                                @if(request('branch_id') == $branch->id) selected @endif>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">search</span>
                    Cari
                </button>
                @if(request('search') || request('is_active') !== null && request('is_active') !== '' || request('region_id') || request('branch_id') || request('sort'))
                    <a href="{{ route('employees.index') }}"
                       class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-muted); background-color: var(--dash-card-bg);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            @php
                $currentSort = request('sort', 'name');
                $q = request()->except('sort');
                $sortLink = fn(string $key) => route('employees.index', array_merge($q, ['sort' => $currentSort === $key ? $key.'_desc' : $key]));
                $sortIcon = fn(string $key) => str_starts_with($currentSort, $key)
                    ? ($currentSort === $key.'_desc' ? '↑' : '↓')
                    : '';
            @endphp
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        {{-- Kode: hidden on mobile --}}
                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ $sortLink('code') }}"
                               class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                Kode
                                <span style="color: var(--dash-primary);">{{ $sortIcon('code') }}</span>
                            </a>
                        </th>
                        {{-- Nama: always shown --}}
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ $sortLink('name') }}"
                               class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                Pegawai
                                <span style="color: var(--dash-primary);">{{ $sortIcon('name') }}</span>
                            </a>
                        </th>
                        {{-- Posisi: always shown --}}
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Posisi</th>
                        {{-- Penempatan: hidden on mobile --}}
                        <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Penempatan</th>
                        {{-- Masa Kerja: hidden on mobile --}}
                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ $sortLink('tenure') }}"
                               class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                Masa Kerja
                                <span style="color: var(--dash-primary);">{{ $sortIcon('tenure') }}</span>
                            </a>
                        </th>
                        {{-- Status: always shown --}}
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ $sortLink('newest') }}"
                               class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                Status
                                <span style="color: var(--dash-primary);">{{ $sortIcon('newest') }}</span>
                            </a>
                        </th>
                        {{-- Aksi: always shown --}}
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">

                            {{-- Kode --}}
                            <td class="hidden sm:table-cell px-6 py-4 text-sm whitespace-nowrap font-mono"
                                style="color: var(--dash-text-muted);">
                                {{ $emp->employee_code }}
                            </td>

                            {{-- Nama + avatar --}}
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold uppercase"
                                         style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                                        {{ substr($emp->full_name, 0, 1) }}
                                    </div>
                                    <a href="{{ route('employees.show', $emp->id) }}"
                                       class="text-sm font-semibold hover:opacity-75 transition-opacity"
                                       style="color: var(--dash-primary);">
                                        {{ $emp->full_name }}
                                    </a>
                                </div>
                            </td>

                            {{-- Posisi --}}
                            <td class="px-4 sm:px-6 py-4 text-sm"
                                style="color: var(--dash-text-muted);">
                                {{ $emp->currentStatus?->position?->position_name ?? '—' }}
                            </td>

                            {{-- Penempatan: hidden on mobile --}}
                            <td class="hidden md:table-cell px-6 py-4 text-sm whitespace-nowrap"
                                style="color: var(--dash-text-muted);">
                                @if($emp->branch)
                                    {{ $emp->branch->branch_name }}
                                @elseif($emp->region)
                                    {{ $emp->region->name }}
                                @else
                                    —
                                @endif
                            </td>

                            {{-- Masa Kerja: hidden on mobile --}}
                            <td class="hidden lg:table-cell px-6 py-4 text-sm whitespace-nowrap"
                                style="color: var(--dash-text-muted);">
                                @if($emp->join_date)
                                    @php
                                        $diff = $emp->join_date->diff(now());
                                        if ($diff->y >= 1) {
                                            echo $diff->y . ' thn' . ($diff->m > 0 ? ' ' . $diff->m . ' bln' : '');
                                        } elseif ($diff->m >= 1) {
                                            echo $diff->m . ' bulan';
                                        } else {
                                            echo $diff->days . ' hari';
                                        }
                                    @endphp
                                @else
                                    —
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                @if($emp->is_active)
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                                @else
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: #fee2e2; color: #dc2626;">Nonaktif</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('employees.show', $emp->id) }}"
                                       title="Lihat Detail"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                       style="color: var(--dash-primary);"
                                       onmouseover="this.style.backgroundColor='var(--dash-primary-bg)'"
                                       onmouseout="this.style.backgroundColor=''">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <a href="{{ route('employees.edit', $emp->id) }}"
                                       title="Edit"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                       style="color: #d97706;"
                                       onmouseover="this.style.backgroundColor='#fef3c7'"
                                       onmouseout="this.style.backgroundColor=''">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">group</span>
                                <p class="text-sm font-semibold mb-1" style="color: var(--dash-text-heading);">
                                    Belum ada data pegawai.
                                </p>
                                <p class="text-sm" style="color: var(--dash-text-muted);">
                                    Coba ubah filter atau tambah pegawai baru.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="px-4 sm:px-6 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $employees->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
