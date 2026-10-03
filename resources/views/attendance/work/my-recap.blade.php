@extends('layouts.dashboard')

@section('title', 'Riwayat Absensi Saya')

@push('head-scripts')
<style>
@media print {
    aside, header, .no-print { display: none !important; }
    main { padding: 0 !important; overflow: visible !important; }
}
</style>
@endpush

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">

    <div class="sm:flex sm:justify-between sm:items-end mb-8 no-print">
        <div>
            <h1 class="text-2xl md:text-3xl text-gray-900 font-bold tracking-tight">Rekap Absensi Saya</h1>
            <p class="mt-1 text-sm text-gray-500">Ringkasan kehadiran per bulan untuk tahun {{ $year }}.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                <select name="year" data-no-enhance
                    class="block pl-3 pr-8 py-2 text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                    onchange="this.form.submit()">
                    @foreach(range(now()->year - 2, now()->year) as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <span class="material-symbols-outlined text-base">print</span> PDF
            </button>
        </div>
    </div>

    @if($recaps->isEmpty())
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg py-16 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada rekap</h3>
            <p class="mt-1 text-sm text-gray-500">Data rekap absensi untuk tahun {{ $year }} belum tersedia.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($recaps as $recap)
                @php
                    $effective = $recap->total_effective_working_days ?: 1;
                    $pct = min(100, round(($recap->total_present / $effective) * 100));
                    $pctColor = $pct >= 80 ? 'bg-emerald-500' : ($pct >= 60 ? 'bg-amber-500' : 'bg-rose-500');
                    $badgeColor = $pct >= 80 ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                             : ($pct >= 60 ? 'bg-amber-50 text-amber-700 border-amber-200'
                                                           : 'bg-rose-50 text-rose-700 border-rose-200');
                @endphp
                <div class="bg-white shadow-sm border border-gray-200 rounded-lg px-5 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-800">
                            {{ \Carbon\Carbon::create($year, $recap->period_month)->translatedFormat('F Y') }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeColor }}">
                            {{ $pct }}% hadir
                        </span>
                    </div>

                    {{-- Progress bar --}}
                    <div class="w-full bg-gray-100 rounded-full h-2 mb-3">
                        <div class="{{ $pctColor }} h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>

                    {{-- Stats row --}}
                    <div class="grid grid-cols-5 gap-2 text-center text-xs">
                        <div>
                            <div class="font-semibold text-emerald-600 text-base">{{ $recap->total_present }}</div>
                            <div class="text-gray-400">Hadir</div>
                        </div>
                        <div>
                            <div class="font-semibold text-rose-600 text-base">{{ $recap->total_absent }}</div>
                            <div class="text-gray-400">Alpha</div>
                        </div>
                        <div>
                            <div class="font-semibold text-amber-600 text-base">{{ $recap->total_late }}</div>
                            <div class="text-gray-400">Terlambat</div>
                        </div>
                        <div>
                            <div class="font-semibold text-indigo-600 text-base">
                                {{ $recap->total_sick + $recap->total_permission + $recap->total_leave }}
                            </div>
                            <div class="text-gray-400">Izin/S/C</div>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-500 text-base">{{ $recap->total_holiday }}</div>
                            <div class="text-gray-400">Libur</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
