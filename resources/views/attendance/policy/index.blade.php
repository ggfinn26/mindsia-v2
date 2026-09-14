@extends('layouts.dashboard')

@section('title', 'Jadwal Kerja & Libur (Policy Absensi)')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '' }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Jadwal Kerja & Libur</h1>
        <a href="{{ route('attendance-policies.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            Tambah Policy
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Policy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cakupan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penempatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($policies as $policy)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $policy->policy_name }}
                                @if($policy->is_attendance_exempt)
                                    <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Bebas Absen</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ match($policy->attendance_scope) { 'branch' => 'Cabang', 'area' => 'Area', 'region' => 'Region', default => $policy->attendance_scope } }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $policy->branch->branch_name ?? $policy->area->name ?? $policy->region->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $policy->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $policy->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('attendance-policies.edit', $policy->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <button type="button" @click="showConfirmModal = true; confirmTitle = 'Hapus Policy'; confirmMessage = 'Yakin ingin menghapus policy {{ $policy->policy_name }}?'; confirmActionUrl = '{{ route('attendance-policies.destroy', $policy->id) }}'" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm">Belum ada policy absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($policies->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $policies->links() }}</div>
        @endif
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="showConfirmModal" class="relative z-10" style="display: none;">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showConfirmModal = false"></div>
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="px-6 py-5">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-100 mr-4">
                            <span class="material-symbols-outlined text-red-600 text-xl">warning</span>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900" x-text="confirmTitle"></h3>
                            <p class="text-sm text-gray-500 mt-1" x-text="confirmMessage"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 flex flex-row-reverse gap-2 rounded-b-lg">
                    <button type="button" @click="$refs.confirmForm.submit()" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md text-sm">Hapus</button>
                    <button type="button" @click="showConfirmModal = false" class="bg-white border border-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md text-sm">Batal</button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
