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

@push('head-scripts')
<style>
    .cal-clickable-cell:hover {
        background-color: var(--dash-nav-hover-bg) !important;
    }
</style>
@endpush

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Portal Siswa</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Jadwal Kelas</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kalender pertemuan untuk semua kelas aktif Anda.
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs rounded-lg px-3 py-1.5 self-start sm:self-auto shadow-sm"
             style="background-color: var(--dash-status-danger-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
            <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
            <span class="font-semibold">Hari Libur Nasional</span>
        </div>
    </div>

    @if($memberClasses->isEmpty())
    {{-- Empty State Card --}}
    <div class="rounded-2xl p-12 flex flex-col items-center justify-center text-center shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
              style="color: var(--dash-text-faint);">calendar_today</span>
        <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Belum Ada Kelas Aktif</h3>
        <p class="text-sm max-w-md mx-auto mb-6" style="color: var(--dash-text-muted);">
            Anda belum terdaftar di kelas manapun. Silakan cek riwayat registrasi program Anda.
        </p>
        <a href="{{ route('member.dashboard') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Dashboard
        </a>
    </div>
    @else

    {{-- Legend --}}
    @if(count($classLegend) > 0)
    <div class="flex flex-wrap gap-x-5 gap-y-2">
        @foreach($classLegend as $item)
        <span class="flex items-center gap-1.5 text-xs font-semibold" style="color: var(--dash-text-muted);">
            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $item['color'] }}"></span>
            {{ $item['name'] }}
        </span>
        @endforeach
    </div>
    @endif

    {{-- Calendar Card --}}
    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="p-5 sm:p-6">

            {{-- Nav bar: prev/title/next centered, today button pinned right --}}
            <div class="relative flex items-center justify-center gap-2 mb-6">
                <button id="cal-prev" type="button"
                    class="w-9 h-9 flex items-center justify-center rounded-lg transition-colors cursor-pointer"
                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                    title="Bulan sebelumnya">
                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                </button>
                <h3 id="cal-title" class="text-base font-bold font-jakarta w-48 text-center select-none"
                    style="color: var(--dash-text-heading);"></h3>
                <button id="cal-next" type="button"
                    class="w-9 h-9 flex items-center justify-center rounded-lg transition-colors cursor-pointer"
                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                    title="Bulan berikutnya">
                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                </button>
                <button id="cal-today" type="button"
                    class="absolute right-0 px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                    style="color: var(--dash-primary); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Hari Ini
                </button>
            </div>

            <div id="calendar" data-config="{{ $calendarConfig }}"></div>
        </div>
    </div>
    @endif

</div>
@endsection
