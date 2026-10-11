@extends('layouts.dashboard')

@section('title', 'Data Pegawai - MINDSIA')
@section('header_title', 'Data Pegawai')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">SDM & Karyawan</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Data Pegawai</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola data profil, jabatan, penempatan, dan status kepegawaian.
            </p>
        </div>
        <a href="{{ route('employees.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Pegawai
        </a>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- Filter Card --}}
    <form method="GET" action="{{ route('employees.index') }}"
          class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex-1 min-w-[240px]">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Pencarian</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                      style="color: var(--dash-text-muted);">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIK..."
                       class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                       style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
            </div>
        </div>

        <div class="w-full sm:w-40">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Filter Status</label>
            <select name="is_active" data-with-semua
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'"
                    onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div class="w-full sm:w-48">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Filter Region</label>
            <select name="region_id" data-with-semua
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'"
                    onchange="this.form.submit()">
                <option value="">Semua Region</option>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-full sm:w-48">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Filter Cabang</label>
            <select name="branch_id" data-with-semua
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'"
                    onchange="this.form.submit()">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                Cari
            </button>
            @if(request('search') || request('is_active') !== null || request('region_id') || request('branch_id') || request('sort'))
            <a href="{{ route('employees.index') }}"
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

    {{-- Tabel Container --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    @php
                        $currentSort = request('sort', 'name');
                        $q = request()->except('sort');
                        $sortLink = fn(string $key) => route('employees.index', array_merge($q, ['sort' => $currentSort === $key ? $key.'_desc' : $key]));
                    @endphp
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ $sortLink('code') }}" class="inline-flex items-center gap-1 hover:opacity-80 transition-opacity" style="color: var(--dash-text-muted);">
                                Kode Pegawai
                                @if(str_starts_with($currentSort, 'code'))
                                    <span class="material-symbols-outlined text-[14px]" style="color: var(--dash-primary);">{{ $currentSort === 'code_desc' ? 'arrow_downward' : 'arrow_upward' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ $sortLink('name') }}" class="inline-flex items-center gap-1 hover:opacity-80 transition-opacity" style="color: var(--dash-text-muted);">
                                Nama Pegawai
                                @if(str_starts_with($currentSort, 'name'))
                                    <span class="material-symbols-outlined text-[14px]" style="color: var(--dash-primary);">{{ $currentSort === 'name_desc' ? 'arrow_downward' : 'arrow_upward' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Posisi / Jabatan</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Penempatan</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ $sortLink('tenure') }}" class="inline-flex items-center gap-1 hover:opacity-80 transition-opacity" style="color: var(--dash-text-muted);">
                                Masa Kerja
                                @if(str_starts_with($currentSort, 'tenure'))
                                    <span class="material-symbols-outlined text-[14px]" style="color: var(--dash-primary);">{{ $currentSort === 'tenure_desc' ? 'arrow_downward' : 'arrow_upward' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ $sortLink('newest') }}" class="inline-flex items-center gap-1 hover:opacity-80 transition-opacity" style="color: var(--dash-text-muted);">
                                Status
                                @if(str_starts_with($currentSort, 'newest'))
                                    <span class="material-symbols-outlined text-[14px]" style="color: var(--dash-primary);">{{ $currentSort === 'newest_desc' ? 'arrow_downward' : 'arrow_upward' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs font-semibold" style="color: var(--dash-text-heading);">
                            {{ $emp->employee_code }}
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full flex items-center justify-center font-bold text-xs uppercase shrink-0"
                                     style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                                    {{ mb_substr($emp->full_name, 0, 1) }}
                                </div>
                                <a href="{{ route('employees.show', $emp->id) }}"
                                   class="font-semibold text-sm hover:underline"
                                   style="color: var(--dash-text-heading);">
                                    {{ $emp->full_name }}
                                </a>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-xs" style="color: var(--dash-text-body);">
                            {{ $emp->currentStatus?->position?->position_name ?? 'Belum ada jabatan' }}
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-xs" style="color: var(--dash-text-muted);">
                            @if($emp->is_hq)
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">Headquarters</span>
                            @elseif($emp->branch)
                                Cabang: {{ $emp->branch->branch_name }}
                            @elseif($emp->region)
                                Region: {{ $emp->region->name }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-xs" style="color: var(--dash-text-muted);">
                            @if($emp->join_date)
                                @php
                                    $diff = $emp->join_date->diff(now());
                                    if ($diff->y >= 1) {
                                        $tenure = $diff->y . ' thn' . ($diff->m > 0 ? ' ' . $diff->m . ' bln' : '');
                                    } elseif ($diff->m >= 1) {
                                        $tenure = $diff->m . ' bulan';
                                    } else {
                                        $tenure = $diff->days . ' hari';
                                    }
                                @endphp
                                {{ $tenure }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($emp->is_active)
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Aktif</span>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-right">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                <a href="{{ route('employees.show', $emp->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    Lihat
                                </a>
                                <a href="{{ route('employees.edit', $emp->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">badge</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data pegawai.</p>
                            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Tidak ada data pegawai yang sesuai dengan kriteria filter.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $employees->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
