@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" x-data="{
    showConfirmModal: false,
    confirmTitle: '',
    confirmMessage: '',
    confirmActionUrl: '',
    confirmMethod: 'POST',
    confirmActionText: 'Konfirmasi'
}">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <!-- Left: Title -->
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Job Permintaan</h1>
        </div>

        <!-- Right: Actions -->
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            @can('recruitment.request.create')
            <a href="{{ route('recruitment.permintaan.create') }}" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                <span class="hidden xs:block ml-2">Tambah Permintaan</span>
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-5">
        <form method="GET" action="{{ route('recruitment.permintaan.index') }}" class="flex flex-wrap gap-2 items-center">
            <select name="status" class="form-select text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="pending_hr_review" {{ request('status') === 'pending_hr_review' ? 'selected' : '' }}>Pending HR Review</option>
                <option value="pending_ops_approval" {{ request('status') === 'pending_ops_approval' ? 'selected' : '' }}>Pending Ops Approval</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="fulfilled" {{ request('status') === 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
            </select>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <!-- Table header -->
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Cabang</th>
                        <th class="px-6 py-3 text-left">Posisi</th>
                        <th class="px-6 py-3 text-left">Jenis Request</th>
                        <th class="px-6 py-3 text-center">Headcount</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($permintaans as $permintaan)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $permintaan->branch->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ optional($permintaan->detail)->position->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap capitalize">{{ str_replace('_', ' ', optional($permintaan->detail)->request_type ?? '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                {{ optional($permintaan->detail)->current_headcount ?? 0 }} / {{ optional($permintaan->detail)->headcount ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'pending_hr_review' => 'bg-yellow-100 text-yellow-800',
                                        'pending_ops_approval' => 'bg-blue-100 text-blue-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'fulfilled' => 'bg-purple-100 text-purple-800',
                                    ];
                                    $color = $statusColors[$permintaan->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ str_replace('_', ' ', Str::title($permintaan->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('recruitment.permintaan.show', $permintaan->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                @can('recruitment.request.edit')
                                    <a href="{{ route('recruitment.permintaan.edit', $permintaan->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                @endcan
                                @can('recruitment.request.delete')
                                    <button type="button" @click="showConfirmModal = true; confirmTitle = 'Hapus Permintaan'; confirmMessage = 'Yakin ingin menghapus permintaan ini?'; confirmActionUrl = '{{ route('recruitment.permintaan.destroy', $permintaan->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'" class="text-red-600 hover:text-red-900">Hapus</button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data job permintaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $permintaans->links() }}
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
</div>
@endsection
