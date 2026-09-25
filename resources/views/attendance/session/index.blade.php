@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto"
     x-data="attendanceCamera({ csrfToken: '{{ csrf_token() }}' })">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-end mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl text-gray-900 font-bold tracking-tight">Absensi Sesi Kelas</h1>
            <p class="mt-1 text-sm text-gray-500">Jadwal mengajar Anda hari ini dan status absensi.</p>
        </div>
    </div>

    <!-- Camera Modal -->
    @include('attendance._camera-modal')

    <!-- Table -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas & Waktu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kehadiran</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-6 py-3 text-right">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sessions ?? [] as $session)
                        @php
                            $log = $session->attendanceLog;
                            $classSchedule = $session->classSchedule;
                            $classRoom = $classSchedule ? $classSchedule->classRoom : null;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $classRoom ? $classRoom->name : 'Kelas Tidak Diketahui' }}
                                </div>
                                <div class="text-sm text-gray-500 mt-0.5">
                                    @if($classSchedule)
                                        {{ \Carbon\Carbon::parse($classSchedule->schedule_date)->translatedFormat('d M Y') }}
                                        &bull; {{ \Carbon\Carbon::parse($classSchedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($classSchedule->end_time)->format('H:i') }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    In: {{ $log && $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('H:i') : '-' }}
                                </div>
                                <div class="text-sm text-gray-500 mt-0.5">
                                    Out: {{ $log && $log->check_out ? \Carbon\Carbon::parse($log->check_out)->format('H:i') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(!$log)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">Belum Absen</span>
                                @elseif($log->status === 'checked_in')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Sedang Mengajar</span>
                                @elseif($log->status === 'present')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Selesai</span>
                                @elseif($log->status === 'absent')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">Alpha</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">{{ ucfirst($log->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if(!$log || $log->status === 'absent')
                                    <button type="button"
                                        @click="open('check-in', '{{ route('session-attendance.check-in', $session->id) }}')"
                                        class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        Check-in
                                    </button>
                                @elseif($log->status === 'checked_in')
                                    <button type="button"
                                        @click="open('check-out', '{{ route('session-attendance.check-out', $session->id) }}')"
                                        class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                                        Check-out
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-4xl text-gray-300">calendar_month</span>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada jadwal hari ini</h3>
                                <p class="mt-1 text-sm text-gray-500">Anda tidak memiliki jadwal kelas untuk mengajar pada hari ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
