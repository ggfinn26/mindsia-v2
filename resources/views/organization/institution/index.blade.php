@extends('layouts.dashboard')

@section('title', 'Kelola Institusi')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ showModal: false, isEdit: false, formData: { id: '', jenjang_institution: '', institution_name: '', regions_id: '' }, showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Institusi</h1>
            <p class="text-sm text-gray-500 mt-1">
                Ditemukan <span class="font-semibold text-gray-700">{{ number_format($totalCount, 0, ',', '.') }}</span> data institusi
                @if($totalCount > 2000)
                    (Menampilkan maksimal 100 halaman awal).
                @endif
            </p>
        </div>
        <button @click="showModal = true; isEdit = false; formData = { id: '', jenjang_institution: '', institution_name: '', regions_id: '' }" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            Tambah Institusi
        </button>
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

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6 p-4">
        <form method="GET" action="{{ route('institutions.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <!-- Retain current sort on filter -->
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif
            
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-gray-400 text-sm">search</span>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Cari nama institusi...">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Jenjang</label>
                <select name="jenjang" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangs as $jenjang)
                        <option value="{{ $jenjang }}" {{ request('jenjang') == $jenjang ? 'selected' : '' }}>{{ $jenjang }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Provinsi</label>
                <select name="province_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">Semua Provinsi</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}" {{ request('province_id') == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                    @endforeach
                </select>
            </div>
            
            @if(request('search') || request('jenjang') || request('province_id'))
                <a href="{{ route('institutions.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 text-center">Reset</a>
            @endif
            
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Cari</button>
        </form>
    </div>

    @php
        function sortUrl($column) {
            $currentSort = request('sort');
            $currentDir = request('direction', 'asc');
            $dir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
        }
    @endphp

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ sortUrl('institution_name') }}" class="hover:text-gray-700">Nama Institusi {!! request('sort', 'institution_name') === 'institution_name' ? (request('direction', 'asc') === 'asc' ? '↑' : '↓') : '' !!}</a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ sortUrl('jenjang_institution') }}" class="hover:text-gray-700">Jenjang {!! request('sort') === 'jenjang_institution' ? (request('direction') === 'asc' ? '↑' : '↓') : '' !!}</a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ sortUrl('region') }}" class="hover:text-gray-700">Region {!! request('sort') === 'region' ? (request('direction') === 'asc' ? '↑' : '↓') : '' !!}</a>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($institutions as $institution)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $institution->institution_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $institution->jenjang_institution }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $institution->region->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="showModal = true; isEdit = true; formData = { id: '{{ $institution->id }}', jenjang_institution: '{{ $institution->jenjang_institution }}', institution_name: '{{ addslashes($institution->institution_name) }}', regions_id: '{{ $institution->regions_id }}' }" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button type="button" @click="showConfirmModal = true; confirmTitle = 'Hapus Institusi'; confirmMessage = 'Yakin ingin menghapus institusi ini?'; confirmActionUrl = '{{ route('institutions.destroy', $institution->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'" class="text-red-600 hover:text-red-900">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data institusi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($institutions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $institutions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div x-show="showConfirmModal" class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="$refs.confirmForm.submit()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" x-text="confirmActionText"></button>
                    <button type="button" @click="showConfirmModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" @click="showModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div x-show="showModal" class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="isEdit ? '/institutions/' + formData.id : '{{ route('institutions.store') }}'" method="POST">
                    @csrf
                    <template x-if="isEdit">
                        @method('PUT')
                    </template>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" x-text="isEdit ? 'Edit Institusi' : 'Tambah Institusi'"></h3>
                        
                        <div class="mb-4">
                            <label for="institution_name" class="block text-sm font-medium text-gray-700">Nama Institusi</label>
                            <input type="text" name="institution_name" id="institution_name" x-model="formData.institution_name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div class="mb-4">
                            <label for="jenjang_institution" class="block text-sm font-medium text-gray-700">Jenjang</label>
                            <input type="text" name="jenjang_institution" id="jenjang_institution" x-model="formData.jenjang_institution" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div class="mb-4">
                            <label for="regions_id" class="block text-sm font-medium text-gray-700">Region</label>
                            <select name="regions_id" id="regions_id" x-model="formData.regions_id" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">-- Pilih Region --</option>
                                @foreach(\App\Models\Region::all() as $reg)
                                    <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
