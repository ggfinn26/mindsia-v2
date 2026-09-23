@extends('layouts.dashboard')

@section('title', 'Jadwal Kelas - MINDSIA')
@section('header_title', 'Jadwal Kelas')

@php
    $holidays = [
        ['2026-01-01', 'Tahun Baru Masehi'],
        ['2026-01-27', 'Isra Miraj Nabi Muhammad SAW'],
        ['2026-01-28', 'Tahun Baru Imlek 2577'],
        ['2026-03-03', 'Hari Raya Nyepi'],
        ['2026-03-20', 'Wafat Isa Almasih'],
        ['2026-04-02', 'Idul Fitri 1447 H'],
        ['2026-04-03', 'Idul Fitri 1447 H'],
        ['2026-05-01', 'Hari Buruh Internasional'],
        ['2026-05-14', 'Kenaikan Isa Almasih'],
        ['2026-05-24', 'Hari Raya Waisak 2570'],
        ['2026-06-01', 'Hari Lahir Pancasila'],
        ['2026-06-06', 'Idul Adha 1447 H'],
        ['2026-06-26', 'Tahun Baru Islam 1448 H'],
        ['2026-08-17', 'HUT Kemerdekaan RI'],
        ['2026-09-15', 'Maulid Nabi Muhammad SAW'],
        ['2026-12-25', 'Hari Raya Natal'],
    ];

    $colors = ['#5586DB', '#10B981', '#F59E0B', '#8B5CF6', '#06B6D4', '#F97316'];
    $classColors = [];
    $classLegend = [];
    $events = [];
    $colorIndex = 0;

    foreach ($memberClasses as $memberClass) {
        $roomId = $memberClass->classRoom->id ?? null;
        if ($roomId && ! isset($classColors[$roomId])) {
            $classColors[$roomId] = $colors[$colorIndex % count($colors)];
            $colorIndex++;
            $classLegend[$roomId] = [
                'name'  => $memberClass->classRoom->class_name ?? 'Kelas',
                'color' => $classColors[$roomId],
            ];
        }
        $color       = $classColors[$roomId] ?? '#5586DB';
        $className   = $memberClass->classRoom->class_name ?? 'Kelas';
        $programName = $memberClass->classRoom->program->program_name ?? '';

        foreach (optional($memberClass->classRoom)->schedules ?? [] as $schedule) {
            $dateStr = \Carbon\Carbon::parse($schedule->schedule_date)->format('Y-m-d');

            $events[] = [
                'date'  => $dateStr,
                'title' => $className,
                'color' => $color,
                'props' => [
                    'program'  => $programName,
                    'material' => $schedule->material_taught ?? '-',
                    'tutor'    => optional($memberClass->classRoom->tutor)->full_name ?? 'Belum Ditentukan',
                    'time'     => \Carbon\Carbon::parse($schedule->start_time)->format('H:i')
                                  .' - '
                                  .\Carbon\Carbon::parse($schedule->end_time)->format('H:i')
                                  .' WIB',
                ],
            ];
        }
    }

    $holidayMap = array_column($holidays, 1, 0);
    $calendarConfig = json_encode([
        'events'     => $events,
        'holidays'   => array_column($holidays, 0),
        'holidayMap' => $holidayMap,
    ]);
@endphp

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold font-jakarta text-gray-900">Jadwal Kelas</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kalender pertemuan untuk semua kelas aktif Anda.</p>
        </div>
        <div class="flex items-center gap-1.5 text-xs bg-red-50 border border-red-100 rounded-lg px-3 py-1.5 self-start sm:self-auto">
            <span class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></span>
            <span class="font-medium text-red-600">Hari Libur Nasional</span>
        </div>
    </div>

    @if($memberClasses->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 flex flex-col items-center justify-center text-center">
        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[#5586DB] text-3xl">calendar_today</span>
        </div>
        <h3 class="text-base font-bold text-gray-900 font-jakarta mb-1">Belum Ada Kelas Aktif</h3>
        <p class="text-sm text-gray-500 max-w-xs mx-auto mb-5">Anda belum terdaftar di kelas manapun. Silakan cek riwayat registrasi.</p>
        <a href="{{ route('member.dashboard') }}"
           class="px-5 py-2 bg-[#5586DB] text-white text-sm font-semibold rounded-lg hover:bg-[#4370BC] transition-colors">
            Kembali ke Dashboard
        </a>
    </div>

    @else

    {{-- Legend --}}
    @if(count($classLegend) > 0)
    <div class="flex flex-wrap gap-x-5 gap-y-2">
        @foreach($classLegend as $item)
        <span class="flex items-center gap-1.5 text-xs font-medium text-gray-600">
            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $item['color'] }}"></span>
            {{ $item['name'] }}
        </span>
        @endforeach
    </div>
    @endif

    {{-- Calendar card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="p-5 sm:p-6">

            {{-- Nav bar: prev/title/next centered, today button pinned right --}}
            <div class="relative flex items-center justify-center gap-1 mb-5">
                <button id="cal-prev"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                    <span class="material-symbols-outlined" style="font-size:20px">chevron_left</span>
                </button>
                <h3 id="cal-title" class="text-sm font-bold text-gray-900 font-jakarta w-44 text-center select-none"></h3>
                <button id="cal-next"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                    <span class="material-symbols-outlined" style="font-size:20px">chevron_right</span>
                </button>
                <button id="cal-today"
                    class="absolute right-0 px-3 py-1.5 text-xs font-semibold text-[#5586DB] border border-[#5586DB]/30 rounded-lg hover:bg-blue-50 transition-colors">
                    Hari Ini
                </button>
            </div>

            <div id="calendar" data-config="{{ $calendarConfig }}"></div>
        </div>
    </div>
    @endif

</div>
@endsection
