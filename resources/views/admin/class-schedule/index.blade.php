@extends('layouts.dashboard')

@section('title', 'Sesi & Absensi — Semua Kelas')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sesi & Absensi</h1>
            <p class="text-sm text-gray-500 mt-1">Semua kelas dalam scope akses Anda</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('class-schedules.index') }}" class="bg-white shadow sm:rounded-lg mb-4 p-4">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                   placeholder="Cari nama kelas..."
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none col-span-2 lg:col-span-1">

            <select name="branch_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Cabang</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" @selected(($filters['branch_id'] ?? '') == $b->id)>{{ $b->branch_name }}</option>
                @endforeach
            </select>

            <select name="tutor_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Tutor</option>
                @foreach($tutors as $t)
                    <option value="{{ $t->id }}" @selected(($filters['tutor_id'] ?? '') == $t->id)>{{ $t->full_name }}</option>
                @endforeach
            </select>

            <select name="program_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Program</option>
                @foreach($programs as $p)
                    <option value="{{ $p->id }}" @selected(($filters['program_id'] ?? '') == $p->id)>{{ $p->program_name }}</option>
                @endforeach
            </select>

            <div class="flex gap-2 items-center">
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                       class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 w-full">
            </div>

            <div class="flex gap-2 items-center">
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                       class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 w-full">
            </div>
        </div>

        <div class="flex gap-3 mt-3 items-center">
            <select name="sort" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="date_desc" @selected($sort === 'date_desc')>Tanggal - Terbaru</option>
                <option value="date_asc" @selected($sort === 'date_asc')>Tanggal - Terlama</option>
                <option value="class_asc" @selected($sort === 'class_asc')>Nama Kelas A-Z</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Filter</button>
            <a href="{{ route('class-schedules.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50">Reset</a>
        </div>
    </form>

    <div class="bg-white shadow sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cabang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materi</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">H</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">A</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">T</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($schedules as $schedule)
                        <tr class="{{ $schedule->schedule_date->isToday() ? 'bg-blue-50' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap text-sm {{ $schedule->schedule_date->isToday() ? 'font-semibold text-blue-700' : 'text-gray-900' }}">
                                {{ $schedule->schedule_date?->format('d M Y') }}
                                @if($schedule->schedule_date->isToday())
                                    <span class="ml-1 text-xs bg-blue-100 text-blue-700 px-1 rounded">Hari ini</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                <a href="{{ route('classrooms.show', $schedule->classRoom) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $schedule->classRoom?->class_name ?? '-' }}
                                </a>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $schedule->classRoom?->program?->program_name ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $schedule->classRoom?->branch?->branch_name ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $schedule->classRoom?->tutor?->full_name ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $schedule->start_time }} — {{ $schedule->end_time }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">{{ $schedule->material_taught ?? '-' }}</td>
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
                            <td colspan="11" class="px-6 py-4 text-sm text-gray-500 text-center">Tidak ada sesi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $schedules->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
