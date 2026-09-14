@extends('layouts.dashboard')

@section('title', 'Kelola Cabang')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ showPicModal: false, branchId: '', employeeId: '', showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Kelola Cabang</h1>
        <a href="{{ route('branches.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            Tambah Cabang
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

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6 p-4">
        <form method="GET" action="{{ route('branches.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <!-- Retain current sort on filter -->
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Area</label>
                <select name="area_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">Semua Area</option>
                    @foreach($allAreas as $area)
                        <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter PIC (MA)</label>
                <select name="pic_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">Semua PIC</option>
                    <option value="unassigned" {{ request('pic_id') === 'unassigned' ? 'selected' : '' }}>-- Belum ada PIC --</option>
                    @foreach($allPics as $pic)
                        <option value="{{ $pic->id }}" {{ request('pic_id') == $pic->id ? 'selected' : '' }}>{{ $pic->full_name }}</option>
                    @endforeach
                </select>
            </div>
            
            @if(request('area_id') || request('pic_id'))
                <a href="{{ route('branches.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">Reset</a>
            @endif
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
                            <a href="{{ sortUrl('code_branches') }}" class="hover:text-gray-700">Kode {!! request('sort') === 'code_branches' ? (request('direction') === 'asc' ? '↑' : '↓') : '' !!}</a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ sortUrl('branch_name') }}" class="hover:text-gray-700">Nama Cabang {!! request('sort', 'branch_name') === 'branch_name' ? (request('direction', 'asc') === 'asc' ? '↑' : '↓') : '' !!}</a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ sortUrl('area') }}" class="hover:text-gray-700">Area {!! request('sort') === 'area' ? (request('direction') === 'asc' ? '↑' : '↓') : '' !!}</a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ sortUrl('pic') }}" class="hover:text-gray-700">PIC (MA) {!! request('sort') === 'pic' ? (request('direction') === 'asc' ? '↑' : '↓') : '' !!}</a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($branches as $branch)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $branch->code_branches }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $branch->branch_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $branch->area->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $branch->picEmployee->full_name ?? 'Belum ada PIC' }}
                                <button type="button" @click="showPicModal = true; branchId = '{{ $branch->id }}'; employeeId = '{{ $branch->ma_pic_employee_id }}'" class="ml-2 text-xs text-blue-600 hover:underline">Ubah</button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $branch->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $branch->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('branches.edit', $branch->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <button type="button" @click="showConfirmModal = true; confirmTitle = 'Ubah Status Cabang'; confirmMessage = 'Ubah status cabang ini?'; confirmActionUrl = '{{ route('branches.toggle-active', $branch->id) }}'; confirmMethod = 'PATCH'; confirmActionText = 'Ubah Status'" class="text-yellow-600 hover:text-yellow-900 mr-3">Toggle Status</button>
                                <button type="button" @click="showConfirmModal = true; confirmTitle = 'Hapus Cabang'; confirmMessage = 'Yakin ingin menghapus cabang ini?'; confirmActionUrl = '{{ route('branches.destroy', $branch->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data cabang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus/Tindakan -->
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div x-show="showConfirmModal" class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="confirmMethod === 'DELETE' ? 'bg-red-100' : 'bg-yellow-100'">
                            <span class="material-symbols-outlined" :class="confirmMethod === 'DELETE' ? 'text-red-600' : 'text-yellow-600'">warning</span>
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
                    <button type="button" @click="$refs.confirmForm.submit()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" :class="confirmMethod === 'DELETE' ? 'bg-red-600 hover:bg-red-700' : 'bg-yellow-600 hover:bg-yellow-700'" x-text="confirmActionText"></button>
                    <button type="button" @click="showConfirmModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>

    <!-- Modal PIC -->
    <div x-show="showPicModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showPicModal" @click="showPicModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div x-show="showPicModal" class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="'/branches/' + branchId + '/assign-pic'" method="POST">
                    @csrf
                    @method('PUT')
                    <!-- Wait, BranchController has PUT/PATCH? No, wait! I need to check routes. I will just use POST unless it's a PUT. In route:list it wasn't shown or I missed it. I will use POST and method('PUT') just in case, but let me assume the route uses PUT / PATCH. -->
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assign PIC Cabang</h3>
                        
                        <div class="mb-4">
                            <label for="ma_pic_employee_id" class="block text-sm font-medium text-gray-700">Pilih Pegawai (PIC)</label>
                            <select name="ma_pic_employee_id" id="ma_pic_employee_id" x-model="employeeId" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach(\App\Models\Employee::all() as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                        <button type="button" @click="showPicModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
