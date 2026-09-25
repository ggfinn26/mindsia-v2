@extends('layouts.dashboard')

@section('title', 'Hari Libur')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Hari Libur</h2>
            <p class="text-sm text-gray-500 mt-0.5">Hari libur nasional dan khusus yang mempengaruhi perhitungan hari kerja.</p>
        </div>
        @can('attendance.holiday.manage')
            <a href="{{ route('holidays.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Tambah Hari Libur
            </a>
        @endcan
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if ($holidays->isEmpty())
            <div class="text-center py-12 text-gray-400">
                <span class="material-symbols-outlined text-[48px] mb-2 block">event_busy</span>
                <p class="font-medium">Belum ada hari libur terdaftar.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Hari Libur</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Durasi</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($holidays as $holiday)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5 font-medium text-gray-900">{{ $holiday->holiday_name }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $holiday->holiday_start_date->format('d M Y') }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $holiday->holiday_end_date->format('d M Y') }}</td>
                                <td class="px-5 py-3.5 text-gray-600">
                                    @php $days = $holiday->holiday_start_date->diffInDays($holiday->holiday_end_date) + 1 @endphp
                                    {{ $days }} hari
                                </td>
                                <td class="px-5 py-3.5 text-right space-x-2">
                                    @can('attendance.holiday.manage')
                                        <a href="{{ route('holidays.edit', $holiday) }}" class="text-yellow-600 hover:underline text-xs font-medium">Edit</a>
                                        <form action="{{ route('holidays.destroy', $holiday) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Hapus hari libur ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-xs font-medium">Hapus</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($holidays->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $holidays->links() }}</div>
            @endif
        @endif
    </div>

</div>
@endsection
