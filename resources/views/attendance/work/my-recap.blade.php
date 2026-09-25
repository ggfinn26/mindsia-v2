@extends('layouts.dashboard')

@section('title', 'Riwayat Absensi Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-end mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl text-gray-900 font-bold tracking-tight">Rekap Absensi Tahunan</h1>
            <p class="mt-1 text-sm text-gray-500">Ringkasan kehadiran Anda per bulan untuk tahun {{ $year }}.</p>
        </div>

        <div class="mt-4 sm:mt-0">
            <form method="GET" class="flex items-center gap-2">
                <label for="year" class="sr-only">Tahun</label>
                <select id="year" name="year" data-no-enhance class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md transition-colors">
                    @foreach(range(now()->year - 1, now()->year + 1) as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Tampilkan
                </button>
            </form>
        </div>
    </div>

    @if($recaps->isEmpty())
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg py-16 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada rekap</h3>
            <p class="mt-1 text-sm text-gray-500">Data rekap absensi untuk tahun {{ $year }} belum tersedia.</p>
        </div>
    @else
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Alpha</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Terlambat</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Izin/Sakit/Cuti</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Libur</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rasio</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recaps as $recap)
                            @php
                                $effective = $recap->total_effective_working_days ?: 1;
                                $pct = round(($recap->total_present / $effective) * 100);
                                $pct = min($pct, 100); // Cap at 100% just in case
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::create($year, $recap->period_month)->translatedFormat('F Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium {{ $recap->total_present > 0 ? 'text-emerald-600' : 'text-gray-500' }}">
                                        {{ $recap->total_present }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium {{ $recap->total_absent > 0 ? 'text-rose-600' : 'text-gray-500' }}">
                                        {{ $recap->total_absent }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm {{ $recap->total_late > 0 ? 'text-amber-600 font-medium' : 'text-gray-500 font-medium' }}">
                                        {{ $recap->total_late }}
                                    </div>
                                    @if($recap->total_late_minutes > 0)
                                        <div class="text-xs text-gray-400 mt-0.5">({{ $recap->total_late_minutes }}m)</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium {{ ($recap->total_sick + $recap->total_permission + $recap->total_leave) > 0 ? 'text-indigo-600' : 'text-gray-500' }}">
                                        {{ $recap->total_sick + $recap->total_permission + $recap->total_leave }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium text-gray-500">{{ $recap->total_holiday }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                        {{ $pct >= 80 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($pct >= 60 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
