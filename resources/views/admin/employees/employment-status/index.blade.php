@extends('layouts.dashboard')

@section('title', 'Kelola Status dan Kontrak Karyawan')
@section('header_title', 'Kelola Kontrak Karyawan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header (Pola A) --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Kepegawaian</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Kelola Kontrak Karyawan</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Kelola dan pantau masa kontrak serta status kerja karyawan aktif.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">badge</span>
                Daftar Pegawai
            </a>
        </div>
    </div>

    {{-- Flash Message --}}
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

    {{-- Search & Filter Card --}}
    <form method="GET" action="{{ route('employment-statuses.index') }}"
          class="p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <input type="hidden" name="sort" value="{{ request('sort', 'end_date_asc') }}">
        
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-5">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Pencarian Karyawan</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none" style="color: var(--dash-text-muted);">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai / kode..."
                           class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
            </div>

            <div class="md:col-span-3">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Status Kontrak</label>
                <select name="status"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="expiring" {{ request('status') === 'expiring' ? 'selected' : '' }}>Habis (≤30 Hari)</option>
                    <option value="permanent" {{ request('status') === 'permanent' ? 'selected' : '' }}>Permanen</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Tipe Kontrak</label>
                <select name="type"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Tipe</option>
                    <option value="permanent" {{ request('type') === 'permanent' ? 'selected' : '' }}>Tetap</option>
                    <option value="contract" {{ request('type') === 'contract' ? 'selected' : '' }}>Kontrak</option>
                    <option value="probation" {{ request('type') === 'probation' ? 'selected' : '' }}>Probasi</option>
                    <option value="intern" {{ request('type') === 'intern' ? 'selected' : '' }}>Magang</option>
                    <option value="freelance" {{ request('type') === 'freelance' ? 'selected' : '' }}>Freelance</option>
                </select>
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity w-full"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status', 'type']) || request('sort', 'end_date_asc') !== 'end_date_asc')
                <a href="{{ route('employment-statuses.index') }}"
                   class="inline-flex items-center justify-center px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                   title="Reset Filter">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Tabel Data Kontrak --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @php
            $currentSort = request('sort', 'end_date_asc');

            $getSortLink = function ($column, $currentSort) {
                $map = [
                    'name' => ['asc' => 'name_asc', 'desc' => 'name_desc'],
                    'posisi' => ['asc' => 'posisi_asc', 'desc' => 'posisi_desc'],
                    'tipe' => ['asc' => 'end_date_asc', 'desc' => 'end_date_desc'],
                    'status' => ['asc' => 'status_asc', 'desc' => 'status_desc'],
                ];

                $next = $map[$column]['asc'];
                if ($currentSort === $map[$column]['asc']) {
                    $next = $map[$column]['desc'];
                }
                return request()->fullUrlWithQuery(['sort' => $next]);
            };

            $getSortIcon = function ($column, $currentSort) {
                $map = [
                    'name' => ['asc' => 'name_asc', 'desc' => 'name_desc'],
                    'posisi' => ['asc' => 'posisi_asc', 'desc' => 'posisi_desc'],
                    'tipe' => ['asc' => 'end_date_asc', 'desc' => 'end_date_desc'],
                    'status' => ['asc' => 'status_asc', 'desc' => 'status_desc'],
                ];

                if ($currentSort === $map[$column]['asc']) return 'arrow_upward';
                if ($currentSort === $map[$column]['desc']) return 'arrow_downward';
                return 'unfold_more';
            };
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                            <a href="{{ $getSortLink('name', $currentSort) }}" class="inline-flex items-center gap-1 group" style="color: inherit;">
                                Karyawan
                                <span class="material-symbols-outlined text-[14px]" style="color: {{ str_contains($currentSort, 'name') ? 'var(--dash-primary)' : 'var(--dash-text-faint)' }};">{{ $getSortIcon('name', $currentSort) }}</span>
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                            <a href="{{ $getSortLink('posisi', $currentSort) }}" class="inline-flex items-center gap-1 group" style="color: inherit;">
                                Posisi
                                <span class="material-symbols-outlined text-[14px]" style="color: {{ str_contains($currentSort, 'posisi') ? 'var(--dash-primary)' : 'var(--dash-text-faint)' }};">{{ $getSortIcon('posisi', $currentSort) }}</span>
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                            <a href="{{ $getSortLink('tipe', $currentSort) }}" class="inline-flex items-center gap-1 group" style="color: inherit;">
                                Tipe & Masa Kontrak
                                <span class="material-symbols-outlined text-[14px]" style="color: {{ str_contains($currentSort, 'date') ? 'var(--dash-primary)' : 'var(--dash-text-faint)' }};">{{ $getSortIcon('tipe', $currentSort) }}</span>
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                            <a href="{{ $getSortLink('status', $currentSort) }}" class="inline-flex items-center gap-1 group" style="color: inherit;">
                                Status
                                <span class="material-symbols-outlined text-[14px]" style="color: {{ str_contains($currentSort, 'status') ? 'var(--dash-primary)' : 'var(--dash-text-faint)' }};">{{ $getSortIcon('status', $currentSort) }}</span>
                            </a>
                        </th>
                        <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                            Tindakan
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        @php
                            $status = $employee->currentStatus;
                            $expiring = $status?->contract_end_date && $status->contract_end_date->lte(now()->addDays(30));
                            $expired  = $status?->contract_end_date && $status->contract_end_date->lt(now());

                            $contractTypes = [
                                'permanent' => 'Tetap',
                                'contract'  => 'Kontrak',
                                'probation' => 'Probasi',
                                'intern'    => 'Magang',
                                'freelance' => 'Freelance',
                            ];
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="font-medium">
                                    <a href="{{ route('employees.show', $employee) }}"
                                       class="hover:underline"
                                       style="color: var(--dash-primary-text);">
                                        {{ $employee->full_name }}
                                    </a>
                                </div>
                                <div class="text-xs" style="color: var(--dash-text-faint);">{{ $employee->employee_code }}</div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap" style="color: var(--dash-text-body);">
                                {{ $status?->position?->position_name ?? 'Belum diatur' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="font-medium" style="color: var(--dash-text-heading);">
                                    {{ $contractTypes[$status?->type_employment] ?? $status?->type_employment ?? 'Belum diatur' }}
                                </div>
                                <div class="text-xs" style="color: var(--dash-text-muted);">
                                    @if ($status?->contract_start_date)
                                        {{ $status->contract_start_date->format('d M Y') }}
                                        @if ($status->contract_end_date)
                                            s/d {{ $status->contract_end_date->format('d M Y') }}
                                        @else
                                            (Tidak ada batas)
                                        @endif
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if ($expired)
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Habis</span>
                                @elseif ($expiring)
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">≤ 30 Hari</span>
                                @elseif ($status?->contract_end_date)
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Aktif</span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Permanen</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    @can('contract.manage')
                                        @if (!$status)
                                            <a href="{{ route('employment-statuses.create', $employee) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                               style="background-color: var(--dash-primary);">
                                                <span class="material-symbols-outlined text-[14px]">add</span>
                                                Buat Kontrak
                                            </a>
                                        @else
                                            @if ($status->contract_end_date)
                                                <a href="{{ route('employment-statuses.extend.create', $status) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                                                    Perpanjang
                                                </a>
                                            @endif
                                            <a href="{{ route('employment-statuses.change-position.create', $status) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">swap_horiz</span>
                                                Ubah Posisi
                                            </a>
                                            <a href="{{ route('contract-terminate.initiate', $status) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                               style="background-color: #dc2626;"
                                               title="Akhiri Kontrak">
                                                <span class="material-symbols-outlined text-[14px]">block</span>
                                                Akhiri
                                            </a>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">contract</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data kontrak.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Tidak ada data kontrak yang sesuai dengan kriteria filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
