@extends('layouts.dashboard')

@section('content')

@push('head-scripts')
<style>
    .badge-neutral { background-color: #f3f4f6; color: #4b5563; border-color: #e5e7eb; }
    .mode-dark .badge-neutral { background-color: rgba(156, 163, 175, 0.1); color: #9ca3af; border-color: rgba(156, 163, 175, 0.2); }
    
    .badge-info { background-color: #e0f7fb; color: #007a99; border-color: #cffafe; }
    .mode-dark .badge-info { background-color: rgba(0, 170, 204, 0.12); color: #00aacc; border-color: rgba(0, 170, 204, 0.2); }
    
    .badge-success { background-color: #f0fdf4; color: #059669; border-color: #d1fae5; }
    .mode-dark .badge-success { background-color: rgba(5, 150, 105, 0.12); color: #34d399; border-color: rgba(5, 150, 105, 0.2); }
    
    .badge-danger { background-color: #fee2e2; color: #dc2626; border-color: #fecaca; }
    .mode-dark .badge-danger { background-color: rgba(220, 38, 38, 0.15); color: #f87171; border-color: rgba(220, 38, 38, 0.2); }
    
    .badge { display: inline-flex; align-items: center; padding: 0.125rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; border-width: 1px; }
</style>
@endpush
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto"
     x-data="attendanceCamera({ csrfToken: '{{ csrf_token() }}' })">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-end mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl text-[var(--dash-text-heading)] font-bold tracking-tight">Absensi Sesi Kelas</h1>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Jadwal mengajar Anda hari ini dan status absensi.</p>
        </div>
    </div>

    <!-- Camera Modal -->
    @include('attendance._camera-modal')

    <!-- Table -->
    <div class="bg-[var(--dash-card-bg)] shadow-sm border border-[var(--dash-card-border)] rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)]">
                <thead class="bg-[var(--dash-page-bg)]">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--dash-text-muted)] uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--dash-text-muted)] uppercase tracking-wider">Kelas & Waktu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--dash-text-muted)] uppercase tracking-wider">Kehadiran</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[var(--dash-text-muted)] uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-6 py-3 text-right">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-[var(--dash-card-bg)] divide-y divide-[var(--dash-card-border)]">
                    @forelse($sessions ?? [] as $session)
                        @php
                            $log = $session->attendanceLog;
                            $classSchedule = $session->classSchedule;
                            $classRoom = $classSchedule ? $classSchedule->classRoom : null;
                        @endphp
                        <tr class="hover:bg-[var(--dash-page-bg)] transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--dash-text-muted)]">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-[var(--dash-text-heading)]">
                                    {{ $classRoom ? $classRoom->name : 'Kelas Tidak Diketahui' }}
                                </div>
                                <div class="text-sm text-[var(--dash-text-muted)] mt-0.5">
                                    @if($classSchedule)
                                        {{ \Carbon\Carbon::parse($classSchedule->schedule_date)->translatedFormat('d M Y') }}
                                        &bull; {{ \Carbon\Carbon::parse($classSchedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($classSchedule->end_time)->format('H:i') }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-[var(--dash-text-heading)]">
                                    In: {{ $log && $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('H:i') : '-' }}
                                </div>
                                <div class="text-sm text-[var(--dash-text-muted)] mt-0.5">
                                    Out: {{ $log && $log->check_out ? \Carbon\Carbon::parse($log->check_out)->format('H:i') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(!$log)
                                    <span class="badge badge-neutral">Belum Absen</span>
                                @elseif($log->status === 'checked_in')
                                    <span class="badge badge-info">Sedang Mengajar</span>
                                @elseif($log->status === 'present')
                                    <span class="badge badge-success">Selesai</span>
                                @elseif($log->status === 'absent')
                                    <span class="badge badge-danger">Alpha</span>
                                @else
                                    <span class="badge badge-neutral">{{ ucfirst($log->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if(!$log || $log->status === 'absent')
                                    <button type="button"
                                        @click="open('check-in', '{{ route('session-attendance.check-in', $session->id) }}')"
                                        class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded-lg text-white bg-[var(--dash-primary)] hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--dash-primary)] transition-colors">
                                        Check-in
                                    </button>
                                @elseif($log->status === 'checked_in')
                                    <button type="button"
                                        @click="open('check-out', '{{ route('session-attendance.check-out', $session->id) }}')"
                                        class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                                        Check-out
                                    </button>
                                @else
                                    <span class="text-[var(--dash-text-faint)] text-xs">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-4xl text-[var(--dash-text-faint)]">calendar_month</span>
                                <h3 class="mt-2 text-sm font-medium text-[var(--dash-text-heading)]">Tidak ada jadwal hari ini</h3>
                                <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Anda tidak memiliki jadwal kelas untuk mengajar pada hari ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
