@extends('layouts.dashboard')

@section('title', 'Kelas Saya')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[color:var(--dash-text-heading)]">Kelas Saya</h1>
            <p class="text-sm text-[color:var(--dash-text-muted)] mt-1">Sesi dari kelas yang Anda ampu</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-[var(--dash-card-bg)] shadow sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[color:var(--dash-card-border)]">
                <thead class="bg-[var(--dash-nav-hover-bg)]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[color:var(--dash-text-muted)] uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[color:var(--dash-text-muted)] uppercase">Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[color:var(--dash-text-muted)] uppercase">Program</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[color:var(--dash-text-muted)] uppercase">Cabang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[color:var(--dash-text-muted)] uppercase">Jam</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[color:var(--dash-text-muted)] uppercase">Materi</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-[color:var(--dash-text-muted)] uppercase">H</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-[color:var(--dash-text-muted)] uppercase">A</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-[color:var(--dash-text-muted)] uppercase">T</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-[color:var(--dash-text-muted)] uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="bg-[var(--dash-card-bg)] divide-y divide-[color:var(--dash-card-border)]">
                    @forelse($schedules as $schedule)
                        <tr class="{{ $schedule->schedule_date->isToday() ? 'bg-blue-50' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap text-sm {{ $schedule->schedule_date->isToday() ? 'font-semibold text-blue-700' : 'text-[color:var(--dash-text-heading)]' }}">
                                {{ $schedule->schedule_date?->format('d M Y') }}
                                @if($schedule->schedule_date->isToday())
                                    <span class="ml-1 text-xs bg-blue-100 text-blue-700 px-1 rounded">Hari ini</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-[color:var(--dash-text-heading)]">
                                <a href="{{ route('classrooms.show', $schedule->classRoom) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $schedule->classRoom?->class_name ?? '-' }}
                                </a>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-[color:var(--dash-text-muted)]">{{ $schedule->classRoom?->program?->program_name ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-[color:var(--dash-text-muted)]">{{ $schedule->classRoom?->branch?->branch_name ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-[color:var(--dash-text-muted)]">{{ $schedule->start_time }} — {{ $schedule->end_time }}</td>
                            <td class="px-4 py-3 text-sm text-[color:var(--dash-text-muted)] max-w-xs truncate">{{ $schedule->material_taught ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $schedule->present_count }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ $schedule->absent_count }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $schedule->late_count }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('classrooms.show', $schedule->classRoom) }}" class="text-indigo-600 hover:text-indigo-900">Buka</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-sm text-[color:var(--dash-text-muted)] text-center">Anda belum mengampu kelas aktif apapun.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
        <div class="px-6 py-4 border-t border-[color:var(--dash-card-border)]">
            {{ $schedules->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
