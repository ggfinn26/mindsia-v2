@extends('layouts.dashboard')

@section('title', 'Status Karyawan - MINDSIA')
@section('header_title', 'Status Karyawan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto">

    {{-- Page header + filter --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Status Karyawan</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Kelola dan pantau kontrak karyawan aktif.
            </p>
        </div>

        <form x-data x-ref="filterForm" action="{{ route('employment-statuses.index') }}" method="GET"
              class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-end gap-3">
            <input type="hidden" name="sort" value="{{ request('sort', 'end_date_asc') }}">

            {{-- Search --}}
            <div class="relative w-full sm:w-auto sm:min-w-[220px]">
                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none material-symbols-outlined text-[18px]"
                      style="color: var(--dash-text-faint);">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama..."
                       style="padding-left: 2.5rem !important;"
                       @change="$refs.filterForm.submit()">
            </div>

            {{-- Status --}}
            <select name="status" @change="$refs.filterForm.submit()" class="w-full sm:w-auto">
                <option value="">Semua Status</option>
                <option value="active"    @if(request('status') === 'active') selected @endif>Aktif</option>
                <option value="expiring"  @if(request('status') === 'expiring') selected @endif>Habis (≤30 Hari)</option>
                <option value="permanent" @if(request('status') === 'permanent') selected @endif>Permanen</option>
            </select>

            {{-- Tipe --}}
            <select name="type" @change="$refs.filterForm.submit()" class="w-full sm:w-auto">
                <option value="">Semua Tipe</option>
                <option value="permanent"  @if(request('type') === 'permanent') selected @endif>Tetap</option>
                <option value="contract"   @if(request('type') === 'contract') selected @endif>Kontrak</option>
                <option value="probation"  @if(request('type') === 'probation') selected @endif>Probasi</option>
                <option value="intern"     @if(request('type') === 'intern') selected @endif>Magang</option>
                <option value="freelance"  @if(request('type') === 'freelance') selected @endif>Freelance</option>
            </select>

            @if(request()->anyFilled(['search', 'status', 'type']) || request('sort', 'end_date_asc') !== 'end_date_asc')
                <a href="{{ route('employment-statuses.index') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-muted); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    Reset
                </a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium mb-6"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @if($employees->isEmpty())
            <div class="px-6 py-16 text-center">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">badge</span>
                <p class="text-sm" style="color: var(--dash-text-muted);">
                    Belum ada data kontrak yang sesuai kriteria.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                @php
                    $currentSort = request('sort', 'end_date_asc');

                    function getSortLink($column, $currentSort) {
                        $map = [
                            'name'   => ['asc' => 'name_asc',   'desc' => 'name_desc'],
                            'posisi' => ['asc' => 'posisi_asc', 'desc' => 'posisi_desc'],
                            'tipe'   => ['asc' => 'end_date_asc', 'desc' => 'end_date_desc'],
                            'status' => ['asc' => 'status_asc', 'desc' => 'status_desc'],
                        ];
                        $next = $map[$column]['asc'];
                        if ($currentSort === $map[$column]['asc']) {
                            $next = $map[$column]['desc'];
                        }
                        return request()->fullUrlWithQuery(['sort' => $next]);
                    }

                    function getSortIcon($column, $currentSort) {
                        $map = [
                            'name'   => ['asc' => 'name_asc',   'desc' => 'name_desc'],
                            'posisi' => ['asc' => 'posisi_asc', 'desc' => 'posisi_desc'],
                            'tipe'   => ['asc' => 'end_date_asc', 'desc' => 'end_date_desc'],
                            'status' => ['asc' => 'status_asc', 'desc' => 'status_desc'],
                        ];
                        if ($currentSort === $map[$column]['asc']) return 'arrow_downward';
                        if ($currentSort === $map[$column]['desc']) return 'arrow_upward';
                        return 'unfold_more';
                    }
                @endphp
                <table class="min-w-full">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border);">
                            {{-- Karyawan: always --}}
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">
                                <a href="{{ getSortLink('name', $currentSort) }}"
                                   class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                    Karyawan
                                    <span class="material-symbols-outlined text-[14px]"
                                          style="color: {{ str_contains($currentSort, 'name') ? 'var(--dash-primary)' : 'var(--dash-text-faint)' }};">
                                        {{ getSortIcon('name', $currentSort) }}
                                    </span>
                                </a>
                            </th>
                            {{-- Posisi: hidden on mobile --}}
                            <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">
                                <a href="{{ getSortLink('posisi', $currentSort) }}"
                                   class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                    Posisi
                                    <span class="material-symbols-outlined text-[14px]"
                                          style="color: {{ str_contains($currentSort, 'posisi') ? 'var(--dash-primary)' : 'var(--dash-text-faint)' }};">
                                        {{ getSortIcon('posisi', $currentSort) }}
                                    </span>
                                </a>
                            </th>
                            {{-- Tipe & Masa: hidden on mobile --}}
                            <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">
                                <a href="{{ getSortLink('tipe', $currentSort) }}"
                                   class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                    Tipe & Masa Kontrak
                                    <span class="material-symbols-outlined text-[14px]"
                                          style="color: {{ str_contains($currentSort, 'date') ? 'var(--dash-primary)' : 'var(--dash-text-faint)' }};">
                                        {{ getSortIcon('tipe', $currentSort) }}
                                    </span>
                                </a>
                            </th>
                            {{-- Status: always --}}
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">
                                <a href="{{ getSortLink('status', $currentSort) }}"
                                   class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                    Status
                                    <span class="material-symbols-outlined text-[14px]"
                                          style="color: {{ str_contains($currentSort, 'status') ? 'var(--dash-primary)' : 'var(--dash-text-faint)' }};">
                                        {{ getSortIcon('status', $currentSort) }}
                                    </span>
                                </a>
                            </th>
                            {{-- Tindakan: always --}}
                            <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            @php
                                $status   = $employee->currentStatus;
                                $expiring = $status?->contract_end_date && $status->contract_end_date->lte(now()->addDays(30));
                                $expired  = $status?->contract_end_date && $status->contract_end_date->lt(now());

                                $contractTypes = [
                                    'permanent' => 'Tetap',
                                    'contract'  => 'Kontrak',
                                    'probation' => 'Probasi',
                                    'intern'    => 'Magang',
                                    'freelance' => 'Freelance',
                                ];

                                // Row highlight: inline style so dark mode gets correct bg
                                $rowBg = $expired
                                    ? 'background-color: rgba(220,38,38,0.06);'
                                    : ($expiring ? 'background-color: rgba(217,119,6,0.06);' : '');
                            @endphp
                            <tr style="border-bottom: 1px solid var(--dash-card-border); {{ $rowBg }}"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='{{ $expired ? 'rgba(220,38,38,0.06)' : ($expiring ? 'rgba(217,119,6,0.06)' : '') }}'">

                                {{-- Karyawan --}}
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('employees.show', $employee) }}"
                                       class="text-sm font-semibold hover:opacity-75 transition-opacity"
                                       style="color: var(--dash-primary);">
                                        {{ $employee->full_name }}
                                    </a>
                                    <div class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                        {{ $employee->employee_code }}
                                        {{-- Posisi inline on mobile --}}
                                        <span class="sm:hidden">&nbsp;· {{ $status?->position?->position_name ?? '—' }}</span>
                                    </div>
                                    {{-- Masa kontrak inline on mobile --}}
                                    @if($status?->contract_start_date)
                                        <div class="md:hidden text-xs mt-0.5" style="color: var(--dash-text-faint);">
                                            {{ $status->contract_start_date->format('d M Y') }}
                                            @if($status->contract_end_date)
                                                → {{ $status->contract_end_date->format('d M Y') }}
                                            @else
                                                → Tidak ada batas
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                {{-- Posisi --}}
                                <td class="hidden sm:table-cell px-6 py-4 text-sm whitespace-nowrap"
                                    style="color: var(--dash-text-body);">
                                    {{ $status?->position?->position_name ?? '—' }}
                                </td>

                                {{-- Tipe & Masa --}}
                                <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm" style="color: var(--dash-text-heading);">
                                        {{ $contractTypes[$status?->type_employment] ?? $status?->type_employment ?? '—' }}
                                    </div>
                                    <div class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                        @if($status?->contract_start_date)
                                            {{ $status->contract_start_date->format('d M Y') }}
                                            @if($status->contract_end_date)
                                                → {{ $status->contract_end_date->format('d M Y') }}
                                            @else
                                                → Tidak ada batas
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </div>
                                </td>

                                {{-- Status badge --}}
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    @if($expired)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: #fee2e2; color: #dc2626;">Habis</span>
                                    @elseif($expiring)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: #fef3c7; color: #d97706;">≤ 30 Hari</span>
                                    @elseif($status?->contract_end_date)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border-color: var(--dash-card-border);">
                                            Permanen
                                        </span>
                                    @endif
                                </td>

                                {{-- Tindakan --}}
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right">
                                    @can('contract.manage')
                                        <div class="inline-flex items-center gap-1.5">
                                            @if(!$status)
                                                <a href="{{ route('employment-statuses.create', $employee) }}"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                    <span class="material-symbols-outlined text-[14px]">add_circle</span>
                                                    <span class="hidden sm:inline">Buat Kontrak</span>
                                                </a>
                                            @else
                                                @if($status->contract_end_date)
                                                    <a href="{{ route('employment-statuses.extend.create', $status) }}"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                        <span class="material-symbols-outlined text-[14px]">more_time</span>
                                                        <span class="hidden sm:inline">Perpanjang</span>
                                                    </a>
                                                @endif
                                                <a href="{{ route('employment-statuses.change-position.create', $status) }}"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                    <span class="material-symbols-outlined text-[14px]">swap_horiz</span>
                                                    <span class="hidden sm:inline">Ubah Posisi</span>
                                                </a>
                                            @endif
                                        </div>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($employees->hasPages())
                <div class="px-4 sm:px-6 py-4"
                     style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    {{ $employees->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
