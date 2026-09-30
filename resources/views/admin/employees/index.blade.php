@extends('layouts.dashboard')

@section('title', 'Daftar Pegawai')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Daftar Pegawai</h1>
        <a href="{{ route('employees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            Tambah Pegawai
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg mb-6 p-4">
        <form method="GET" action="{{ route('employees.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-gray-400 text-sm">search</span>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" style="padding-left: 2.5rem !important" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Cari nama atau kode pegawai...">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                <select name="is_active" data-with-semua class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Region</label>
                <select name="region_id" data-with-semua class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">Semua Region</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Cabang</label>
                <select name="branch_id" data-with-semua class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>

            @if(request('search') || request('is_active') !== null || request('region_id') || request('branch_id') || request('sort'))
                <a href="{{ route('employees.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 text-center">Reset</a>
            @endif
            
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Cari</button>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    @php
                        $currentSort = request('sort', 'name');
                        $q = request()->except('sort');
                        $sortLink = fn(string $key) => route('employees.index', array_merge($q, ['sort' => $currentSort === $key ? $key.'_desc' : $key]));
                        $sortIcon = fn(string $key) => str_starts_with($currentSort, $key)
                            ? ($currentSort === $key.'_desc' ? '↑' : '↓')
                            : '';
                    @endphp
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ $sortLink('code') }}" class="flex items-center gap-1 hover:text-gray-700">Kode Pegawai <span class="text-blue-500">{{ $sortIcon('code') }}</span></a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ $sortLink('name') }}" class="flex items-center gap-1 hover:text-gray-700">Nama Pegawai <span class="text-blue-500">{{ $sortIcon('name') }}</span></a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posisi / Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penempatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ $sortLink('newest') }}" class="flex items-center gap-1 hover:text-gray-700">Status <span class="text-blue-500">{{ $sortIcon('newest') }}</span></a>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employees as $emp)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $emp->employee_code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold uppercase mr-3">
                                        {{ substr($emp->full_name, 0, 1) }}
                                    </div>
                                    <a href="{{ route('employees.show', $emp->id) }}" class="text-blue-600 hover:underline">{{ $emp->full_name }}</a>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $emp->currentStatus?->position?->position_name ?? 'Belum ada jabatan' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($emp->branch)
                                    Cabang: {{ $emp->branch->branch_name }}
                                @elseif($emp->region)
                                    Region: {{ $emp->region->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $emp->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $emp->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('employees.show', $emp->id) }}" title="Lihat Detail" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 hover:bg-indigo-50 mr-1">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </a>
                                <a href="{{ route('employees.edit', $emp->id) }}" title="Edit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-yellow-600 hover:bg-yellow-50">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data pegawai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $employees->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
