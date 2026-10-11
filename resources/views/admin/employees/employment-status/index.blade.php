@extends('layouts.dashboard')
@section('title', 'Kelola Status Karyawan')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Status Karyawan</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola dan pantau kontrak karyawan aktif.</p>
        </div>
        
        <form x-data x-ref="filterForm" action="{{ route('employment-statuses.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="sort" value="{{ request('sort', 'end_date_asc') }}">
            
            <div class="relative rounded-md shadow-sm w-full md:w-[25rem]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-gray-400 text-sm">search</span>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..." 
                    style="padding-left: 2.5rem !important"
                    class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm focus:ring-blue-500 focus:border-blue-500"
                    @change="$refs.filterForm.submit()">
            </div>
            
            <select name="status" class="border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:ring-blue-500 focus:border-blue-500" @change="$refs.filterForm.submit()">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="expiring" {{ request('status') === 'expiring' ? 'selected' : '' }}>Habis (≤30 Hari)</option>
                <option value="permanent" {{ request('status') === 'permanent' ? 'selected' : '' }}>Permanen</option>
            </select>
            
            <select name="type" class="border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:ring-blue-500 focus:border-blue-500" @change="$refs.filterForm.submit()">
                <option value="">Semua Tipe</option>
                <option value="permanent" {{ request('type') === 'permanent' ? 'selected' : '' }}>Tetap</option>
                <option value="contract" {{ request('type') === 'contract' ? 'selected' : '' }}>Kontrak</option>
                <option value="probation" {{ request('type') === 'probation' ? 'selected' : '' }}>Probasi</option>
                <option value="intern" {{ request('type') === 'intern' ? 'selected' : '' }}>Magang</option>
                <option value="freelance" {{ request('type') === 'freelance' ? 'selected' : '' }}>Freelance</option>
            </select>
            
            @if(request()->anyFilled(['search', 'status', 'type']) || request('sort', 'end_date_asc') !== 'end_date_asc')
                <a href="{{ route('employment-statuses.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">Reset Filter</a>
            @endif
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        @if ($employees->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-gray-500">
                Belum ada data kontrak karyawan yang sesuai dengan kriteria.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @php
                                $currentSort = request('sort', 'end_date_asc');
                                
                                function getSortLink($column, $currentSort) {
                                    $map = [
                                        'name' => ['asc' => 'name_asc', 'desc' => 'name_desc'],
                                        'posisi' => ['asc' => 'posisi_asc', 'desc' => 'posisi_desc'],
                                        'tipe' => ['asc' => 'end_date_asc', 'desc' => 'end_date_desc'], // Masa kontrak sorts are end_date
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
                                        'name' => ['asc' => 'name_asc', 'desc' => 'name_desc'],
                                        'posisi' => ['asc' => 'posisi_asc', 'desc' => 'posisi_desc'],
                                        'tipe' => ['asc' => 'end_date_asc', 'desc' => 'end_date_desc'],
                                        'status' => ['asc' => 'status_asc', 'desc' => 'status_desc'],
                                    ];
                                    
                                    if ($currentSort === $map[$column]['asc']) return 'arrow_upward';
                                    if ($currentSort === $map[$column]['desc']) return 'arrow_downward';
                                    return 'unfold_more';
                                }
                            @endphp

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ getSortLink('name', $currentSort) }}" class="inline-flex items-center gap-1 group">
                                    Karyawan
                                    <span class="material-symbols-outlined text-[14px] {{ str_contains($currentSort, 'name') ? 'text-gray-900' : 'text-gray-400 group-hover:text-gray-600' }}">{{ getSortIcon('name', $currentSort) }}</span>
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ getSortLink('posisi', $currentSort) }}" class="inline-flex items-center gap-1 group">
                                    Posisi
                                    <span class="material-symbols-outlined text-[14px] {{ str_contains($currentSort, 'posisi') ? 'text-gray-900' : 'text-gray-400 group-hover:text-gray-600' }}">{{ getSortIcon('posisi', $currentSort) }}</span>
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ getSortLink('tipe', $currentSort) }}" class="inline-flex items-center gap-1 group">
                                    Tipe & Masa Kontrak
                                    <span class="material-symbols-outlined text-[14px] {{ str_contains($currentSort, 'date') ? 'text-gray-900' : 'text-gray-400 group-hover:text-gray-600' }}">{{ getSortIcon('tipe', $currentSort) }}</span>
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ getSortLink('status', $currentSort) }}" class="inline-flex items-center gap-1 group">
                                    Status
                                    <span class="material-symbols-outlined text-[14px] {{ str_contains($currentSort, 'status') ? 'text-gray-900' : 'text-gray-400 group-hover:text-gray-600' }}">{{ getSortIcon('status', $currentSort) }}</span>
                                </a>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tindakan
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($employees as $employee)
                            @php
                                $status = $employee->currentStatus;
                                $expiring = $status?->contract_end_date && $status->contract_end_date->lte(now()->addDays(30));
                                $expired  = $status?->contract_end_date && $status->contract_end_date->lt(now());

                                $rowClass = '';
                                if ($expired) $rowClass = 'bg-red-50';
                                elseif ($expiring) $rowClass = 'bg-yellow-50';
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('employees.show', $employee) }}" class="hover:text-blue-600 hover:underline">{{ $employee->full_name }}</a>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $employee->employee_code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $status?->position?->position_name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $contractTypes = [
                                            'permanent' => 'Tetap',
                                            'contract'  => 'Kontrak',
                                            'probation' => 'Probasi',
                                            'intern'    => 'Magang',
                                            'freelance' => 'Freelance',
                                        ];
                                    @endphp
                                    <div class="text-sm text-gray-900">{{ $contractTypes[$status?->type_employment] ?? $status?->type_employment ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">
                                        @if ($status?->contract_start_date)
                                            {{ $status->contract_start_date->format('d M Y') }}
                                            @if ($status->contract_end_date)
                                                → {{ $status->contract_end_date->format('d M Y') }}
                                            @else
                                                → Tidak ada batas
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($expired)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Habis
                                        </span>
                                    @elseif ($expiring)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ≤ 30 Hari
                                        </span>
                                    @elseif ($status?->contract_end_date)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Permanen
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @can('contract.manage')
                                        @if (!$status)
                                            <a href="{{ route('employment-statuses.create', $employee) }}" class="text-blue-600 hover:text-blue-900 ml-4">Buat Kontrak</a>
                                        @else
                                            @if ($status->contract_end_date)
                                                <a href="{{ route('employment-statuses.extend.create', $status) }}" class="text-yellow-600 hover:text-yellow-900 ml-4">Perpanjang</a>
                                            @endif
                                            <a href="{{ route('employment-statuses.change-position.create', $status) }}" class="text-gray-600 hover:text-gray-900 ml-4">Ubah Posisi</a>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if ($employees->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $employees->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
