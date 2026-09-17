@extends('layouts.dashboard')

@section('title', 'Verifikasi & Koreksi Absensi')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ showAdjustModal: false, adjustLog: null }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Verifikasi & Koreksi Absensi</h1>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow sm:rounded-lg mb-4">
        <form method="GET" class="px-4 py-4 flex flex-wrap gap-4 items-end border-b border-gray-200">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ $date }}" class="border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md text-sm">Tampilkan</button>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diverifikasi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $log->employee->full_name ?? '-' }}
                                <div class="text-xs text-gray-400">{{ $log->employee->employee_code ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->check_in ? $log->check_in->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->check_out ? $log->check_out->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $log->status === 'present' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->status === 'late' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $log->status === 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ !in_array($log->status, ['present','late','absent']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($log->verified_at)
                                    <span class="text-green-600">✓ {{ $log->verified_at->format('H:i') }}</span>
                                @else
                                    <span class="text-gray-400">Belum</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @if(!$log->verified_at)
                                    <form action="{{ route('work-attendance.verify', $log->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">Verifikasi</button>
                                    </form>
                                @endif
                                <button @click="showAdjustModal = true; adjustLog = {{ $log->id }}" class="text-indigo-600 hover:text-indigo-900">Koreksi</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">
                                Tidak ada data absensi untuk tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Koreksi -->
    <div x-show="showAdjustModal" class="relative z-10" style="display: none;">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showAdjustModal = false"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <form :action="`{{ url('work-attendance') }}/${adjustLog}/adjust`" method="POST">
                    @csrf
                    <div class="px-6 py-5">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Koreksi Data Absensi</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Check In</label>
                                <input type="time" name="check_in" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Check Out</label>
                                <input type="time" name="check_out" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Catatan Koreksi</label>
                                <textarea name="adjustment_reason" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex flex-row-reverse gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md text-sm">Simpan Koreksi</button>
                        <button type="button" @click="showAdjustModal = false" class="bg-white border border-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
