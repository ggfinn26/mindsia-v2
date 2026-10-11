@extends('layouts.dashboard')

@section('title', 'Riwayat Absensi Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">

    {{-- Header + filter --}}
    <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl text-gray-900 font-bold tracking-tight">Rekap Absensi Saya</h1>
            <p class="mt-1 text-sm text-gray-500">
                @if($month)
                    Log harian {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
                @else
                    Ringkasan kehadiran per bulan untuk tahun {{ $year }}
                @endif
            </p>
        </div>

        {{-- Filter bar --}}
        <form method="GET" class="flex flex-wrap items-end gap-2">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Tahun</label>
                <select name="year" data-no-enhance
                    class="border border-gray-300 rounded-md py-2 px-3 text-sm"
                    onchange="this.form.submit()">
                    @foreach(range(now()->year - 2, now()->year) as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Bulan</label>
                <select name="month" data-no-enhance
                    class="border border-gray-300 rounded-md py-2 px-3 text-sm"
                    onchange="this.form.submit()">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected($m == $month)>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- PDF dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <label class="block text-xs text-gray-500 mb-1">&nbsp;</label>
                <button type="button" @click="open = !open"
                    class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                    Export PDF
                    <span class="material-symbols-outlined text-base">expand_more</span>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak
                    class="absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-20">
                    <a href="{{ route('work-attendance.my-recap.pdf', ['year' => $year, 'month' => $month ?: now()->month, 'range' => 'monthly']) }}"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                        <span class="material-symbols-outlined text-base text-indigo-500">calendar_today</span>
                        Bulan ini
                    </a>
                    <a href="{{ route('work-attendance.my-recap.pdf', ['year' => $year, 'month' => $month ?: now()->month, 'range' => 'semester']) }}"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                        <span class="material-symbols-outlined text-base text-indigo-500">date_range</span>
                        Semester
                    </a>
                    <a href="{{ route('work-attendance.my-recap.pdf', ['year' => $year, 'range' => 'annual']) }}"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                        <span class="material-symbols-outlined text-base text-indigo-500">event_note</span>
                        Tahunan ({{ $year }})
                    </a>
                </div>
            </div>
        </form>
    </div>

    @if($recaps->isEmpty())
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg py-16 text-center">
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada rekap</h3>
            <p class="mt-1 text-sm text-gray-500">Data rekap absensi untuk tahun {{ $year }} belum tersedia.</p>
        </div>
    @else

        {{-- Summary cards (always shown) --}}
        <div class="space-y-2 mb-6">
            @foreach($recaps as $recap)
                @php
                    $effective  = $recap->total_effective_working_days ?: 1;
                    $pct        = min(100, round(($recap->total_present / $effective) * 100));
                    $pctColor   = $pct >= 80 ? 'bg-emerald-500' : ($pct >= 60 ? 'bg-amber-500' : 'bg-rose-500');
                    $badgeColor = $pct >= 80
                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                        : ($pct >= 60 ? 'bg-amber-50 text-amber-700 border-amber-200'
                                      : 'bg-rose-50 text-rose-700 border-rose-200');
                    $isActive   = $month && $month == $recap->period_month;
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['month' => $recap->period_month, 'sort' => $sort]) }}"
                    class="block bg-white border rounded-lg px-4 py-3 transition-all hover:shadow-sm
                        {{ $isActive ? 'border-indigo-400 ring-1 ring-indigo-400' : 'border-gray-200' }}">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm font-semibold text-gray-800">
                            {{ \Carbon\Carbon::create($year, $recap->period_month)->translatedFormat('F Y') }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $badgeColor }}">
                            {{ $pct }}% hadir
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mb-2">
                        <div class="{{ $pctColor }} h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="grid grid-cols-5 text-center text-xs gap-1">
                        <div><span class="font-semibold text-emerald-600">{{ $recap->total_present }}</span><div class="text-gray-400">Hadir</div></div>
                        <div><span class="font-semibold text-rose-500">{{ $recap->total_absent }}</span><div class="text-gray-400">Alpha</div></div>
                        <div><span class="font-semibold text-amber-500">{{ $recap->total_late }}</span><div class="text-gray-400">Telat</div></div>
                        <div><span class="font-semibold text-indigo-500">{{ $recap->total_sick + $recap->total_permission + $recap->total_leave }}</span><div class="text-gray-400">Izin/S/C</div></div>
                        <div><span class="font-semibold text-gray-400">{{ $recap->total_holiday }}</span><div class="text-gray-400">Libur</div></div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Daily log table — shown only when a month is selected --}}
        @if($month && $logs !== null)
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700">
                        Log Harian — {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
                    </h2>
                    <div class="flex gap-2 text-xs text-gray-500">
                        Sort:
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'date']) }}"
                            class="{{ $sort === 'date' ? 'font-semibold text-indigo-600' : 'hover:text-gray-800' }}">Tanggal</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'late_desc']) }}"
                            class="{{ $sort === 'late_desc' ? 'font-semibold text-indigo-600' : 'hover:text-gray-800' }}">Terlambat ↓</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'status']) }}"
                            class="{{ $sort === 'status' ? 'font-semibold text-indigo-600' : 'hover:text-gray-800' }}">Status</a>
                    </div>
                </div>
                @if($logs->isEmpty())
                    <div class="py-10 text-center text-sm text-gray-400">
                        Tidak ada log absensi harian untuk bulan ini.<br>
                        <span class="text-xs text-gray-300">(Posisi dengan absensi otomatis tidak menghasilkan log harian.)</span>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase">Masuk</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase">Keluar</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase">Terlambat</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($logs as $log)
                                    @php
                                        $statusMap = [
                                            'present'    => ['Hadir',        'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                            'checked_in' => ['Sedang Hadir', 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                                            'absent'     => ['Alpha',        'bg-rose-50 text-rose-700 border-rose-200'],
                                            'sick'       => ['Sakit',        'bg-amber-50 text-amber-700 border-amber-200'],
                                            'permission' => ['Izin',         'bg-blue-50 text-blue-700 border-blue-200'],
                                            'leave'      => ['Cuti',         'bg-purple-50 text-purple-700 border-purple-200'],
                                            'holiday'    => ['Libur',        'bg-teal-50 text-teal-700 border-teal-200'],
                                        ];
                                        [$label, $cls] = $statusMap[$log->status] ?? [ucfirst($log->status), 'bg-gray-100 text-gray-700 border-gray-200'];
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3 font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($log->attendance_date)->translatedFormat('d M, l') }}
                                        </td>
                                        <td class="px-5 py-3 text-center text-gray-600">
                                            {{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-5 py-3 text-center text-gray-600">
                                            {{ $log->check_out ? \Carbon\Carbon::parse($log->check_out)->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            @if($log->late_minutes > 0)
                                                <span class="text-amber-600 font-medium">{{ $log->late_minutes }}m</span>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $cls }}">{{ $label }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($logs->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100">{{ $logs->links() }}</div>
                    @endif
                @endif
            </div>
        @endif

    @endif
</div>
@endsection
