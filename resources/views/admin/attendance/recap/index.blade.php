@extends('layouts.dashboard')

@section('title', 'Rekap Absensi Pegawai')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Rekap Absensi Pegawai</h1>

    {{-- Filter --}}
    <form method="GET" class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                <select name="month" class="block w-full border border-gray-300 rounded-md py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected($m == $month)>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                <select name="year" class="block w-full border border-gray-300 rounded-md py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach(range(now()->year - 1, now()->year + 1) as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Cabang</label>
                <select name="branch_id" data-with-semua class="block w-full border border-gray-300 rounded-md py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($branch->id == $branchId)>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md text-sm">Filter</button>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($recaps->isEmpty())
            <div class="text-center py-12 text-gray-400">
                <span class="material-symbols-outlined text-[48px] mb-2 block">how_to_reg</span>
                <p class="font-medium">Belum ada data rekap untuk periode ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pegawai</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cabang</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Hadir</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Tidak Hadir</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Telat</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Izin/Sakit/Cuti</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Libur</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($recaps as $recap)
                            @php
                                $effective = $recap->total_effective_working_days ?: 1;
                                $pct = round(($recap->total_present / $effective) * 100);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5">
                                    <div class="font-medium text-gray-900">{{ $recap->employee->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $recap->employee->position?->position_name ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $recap->employee->branch?->branch_name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-center font-medium text-green-700">{{ $recap->total_present }}</td>
                                <td class="px-5 py-3.5 text-center font-medium text-red-600">{{ $recap->total_absent }}</td>
                                <td class="px-5 py-3.5 text-center text-yellow-700">
                                    {{ $recap->total_late }}
                                    @if($recap->total_late_minutes > 0)
                                        <span class="text-xs text-gray-400">({{ $recap->total_late_minutes }}m)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center text-blue-600">
                                    {{ $recap->total_sick + $recap->total_permission + $recap->total_leave }}
                                </td>
                                <td class="px-5 py-3.5 text-center text-gray-500">{{ $recap->total_holiday }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $pct >= 80 ? 'bg-green-100 text-green-800' : ($pct >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($recaps->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $recaps->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
