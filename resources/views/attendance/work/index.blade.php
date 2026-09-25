@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto"
     x-data="attendanceCamera({ csrfToken: '{{ csrf_token() }}' })">

    <!-- Page header -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-900 font-bold tracking-tight">Kehadiran Harian</h1>
        <p class="mt-1 text-sm text-gray-500">Catat waktu masuk dan pulang kerja Anda, serta riwayat kehadiran.</p>
    </div>

    <!-- Action Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8 overflow-hidden">
        <div class="px-6 py-8 sm:p-10 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ now()->translatedFormat('H:i') }}</h2>
                <p class="text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>

                <div class="mt-4 text-sm text-gray-700">
                    Status Hari Ini:
                    @if(!$today || !$today->check_in)
                        <span class="font-medium text-gray-900">Belum Check-In</span>
                    @elseif($today->check_in && !$today->check_out)
                        <span class="font-medium text-emerald-600">Sudah Check-In pada {{ \Carbon\Carbon::parse($today->check_in)->format('H:i') }}</span>
                    @else
                        <span class="font-medium text-blue-600">Selesai (In: {{ \Carbon\Carbon::parse($today->check_in)->format('H:i') }}, Out: {{ \Carbon\Carbon::parse($today->check_out)->format('H:i') }})</span>
                    @endif
                </div>
            </div>

            <div class="w-full sm:w-auto">
                @if(!$today || !$today->check_in)
                    <button type="button"
                        @click="open('check-in', '{{ route('work-attendance.check-in') }}')"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <span class="material-symbols-outlined text-lg mr-2">login</span>
                        Catat Check-In
                    </button>
                @elseif($today->check_in && !$today->check_out)
                    <button type="button"
                        @click="open('check-out', '{{ route('work-attendance.check-out') }}')"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                        <span class="material-symbols-outlined text-lg mr-2">logout</span>
                        Catat Check-Out
                    </button>
                @else
                    <div class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500 text-sm font-medium">
                        Absensi Hari Ini Selesai
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Camera Modal -->
    @include('attendance._camera-modal')

    <!-- Table Riwayat -->
    <h3 class="text-lg font-bold text-gray-900 mb-4">Riwayat Kehadiran Bulan Ini</h3>
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-In</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-Out</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terlambat</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs ?? [] as $log)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($log->attendance_date)->translatedFormat('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log->attendance_date)->translatedFormat('l') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->check_out ? \Carbon\Carbon::parse($log->check_out)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($log->late_minutes > 0)
                                    <span class="text-rose-600 font-medium">{{ $log->late_minutes }} menit</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->status === 'present')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Hadir</span>
                                @elseif($log->status === 'absent')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">Alpha</span>
                                @elseif($log->status === 'leave')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">Cuti</span>
                                @elseif($log->status === 'sick')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Sakit</span>
                                @elseif($log->status === 'permission')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Izin</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">{{ ucfirst($log->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-4xl text-gray-300">schedule</span>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada riwayat kehadiran</h3>
                                <p class="mt-1 text-sm text-gray-500">Anda belum memiliki catatan absensi pada bulan ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($logs) && $logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
