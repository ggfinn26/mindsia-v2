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

    $holidayEvents = array_map(fn ($h) => [
        'title'           => $h[1],
        'start'           => $h[0],
        'allDay'          => true,
        'backgroundColor' => '#fef2f2',
        'borderColor'     => '#fca5a5',
        'textColor'       => '#dc2626',
        'extendedProps'   => ['isHoliday' => true],
    ], $holidays);

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
            $dateStr       = \Carbon\Carbon::parse($schedule->schedule_date)->format('Y-m-d');
            $startDateTime = \Carbon\Carbon::parse($dateStr.' '.$schedule->start_time)->toIso8601String();
            $endDateTime   = \Carbon\Carbon::parse($dateStr.' '.$schedule->end_time)->toIso8601String();

            $events[] = [
                'title'           => $className,
                'start'           => $startDateTime,
                'end'             => $endDateTime,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'extendedProps'   => [
                    'program'  => $programName,
                    'material' => $schedule->material_taught ?? '-',
                    'tutor'    => optional($memberClass->classRoom->tutor)->full_name ?? 'Belum Ditentukan',
                    'time'     => \Carbon\Carbon::parse($schedule->start_time)->format('H:i').' - '.\Carbon\Carbon::parse($schedule->end_time)->format('H:i').' WIB',
                    'color'    => $color,
                ],
            ];
        }
    }
@endphp

@section('content')
<style>
    /* ── Base ── */
    .fc-theme-standard td,
    .fc-theme-standard th { border-color: #f1f5f9; }
    .fc-theme-standard .fc-scrollgrid { border: none; }

    /* ── Toolbar ── */
    .fc .fc-button-primary {
        background-color: #5586DB;
        border-color: #5586DB;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 600;
        font-size: 0.8125rem;
        padding: 0.375rem 0.875rem;
        border-radius: 0.5rem;
        box-shadow: none;
    }
    .fc .fc-button-primary:hover { background-color: #4370BC; border-color: #4370BC; }
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active { background-color: #3b60a3; border-color: #3b60a3; }
    .fc .fc-button-primary:focus { box-shadow: 0 0 0 3px rgba(85,134,219,0.3); }
    .fc-toolbar-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700;
        font-size: 1.0625rem !important;
        color: #0f172a;
    }
    .fc-toolbar.fc-header-toolbar { margin-bottom: 1.25rem !important; }

    /* ── Month view: day numbers ── */
    .fc-col-header-cell-cushion {
        padding: 10px 8px !important;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #64748b;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .fc-daygrid-day-number {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #475569;
        font-weight: 500;
        padding: 6px 8px !important;
        font-size: 0.8125rem;
        line-height: 1.75rem;
    }
    .fc-day-today .fc-daygrid-day-number {
        background-color: #5586DB;
        color: #fff !important;
        border-radius: 50%;
        width: 1.75rem;
        height: 1.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 !important;
        margin: 4px;
    }
    .fc-day-today { background-color: #eff6ff !important; }

    /* ── Events (all views) ── */
    .fc-event {
        border: none !important;
        border-radius: 5px;
        padding: 1px 5px;
        font-size: 0.6875rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 600;
        cursor: pointer;
        transition: filter 0.1s;
    }
    .fc-event:hover { filter: brightness(0.88); }

    /* ── Week view (timeGridWeek) ── */
    .fc-timegrid-col.fc-day-today { background-color: #eff6ff !important; }
    .fc-timegrid-slot { height: 2.75rem; }
    .fc-timegrid-slot-minor { border-top-style: dashed !important; border-color: #f8fafc !important; }
    .fc-timegrid-slot-label-cushion,
    .fc-timegrid-axis-cushion {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.6875rem;
        color: #94a3b8;
        font-weight: 500;
        padding-right: 10px !important;
    }
    /* Now indicator: brand blue */
    .fc-timegrid-now-indicator-line { border-color: #5586DB; border-width: 2px; }
    .fc-timegrid-now-indicator-arrow {
        border-top-color: #5586DB;
        border-bottom-color: #5586DB;
    }
    /* All-day label */
    .fc-timegrid-axis-cushion { color: #cbd5e1; font-size: 0.625rem; text-transform: uppercase; letter-spacing: 0.04em; }
    /* Timegrid event block */
    .fc-timegrid-event {
        border-radius: 6px !important;
        padding: 3px 6px !important;
    }
    .fc-timegrid-event .fc-event-main { display: flex; flex-direction: column; gap: 1px; }

    /* ── List view (listWeek) ── */
    .fc-list { border: none !important; }
    .fc-list-table { border-collapse: separate; border-spacing: 0; }

    /* Day header row */
    .fc-list-day th { background: transparent !important; border: none !important; padding: 0; }
    .fc-list-day-cushion {
        padding: 6px 16px !important;
        background: #f8fafc !important;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.6875rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-radius: 0;
    }
    .fc-list-day-side-text {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 600;
        color: #94a3b8;
        font-size: 0.6875rem;
    }
    /* Today highlight in list */
    .fc-list-day.fc-day-today .fc-list-day-cushion { background: #eff6ff !important; }
    .fc-list-day.fc-day-today .fc-list-day-text,
    .fc-list-day.fc-day-today .fc-list-day-side-text { color: #5586DB; }

    /* Event rows */
    .fc-list-event td { border-top: 1px solid #f8fafc !important; border-bottom: none !important; }
    .fc-list-event:hover td { background-color: #f8fafc !important; cursor: pointer; }
    .fc-list-event-graphic { padding: 10px 0 10px 16px !important; width: 16px; }
    .fc-list-event-dot {
        border-width: 5px !important;
        border-radius: 50%;
        display: inline-block;
        width: 0;
        height: 0;
    }
    .fc-list-event-time {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 500;
        white-space: nowrap;
        padding: 10px 12px !important;
        min-width: 5.5rem;
    }
    .fc-list-event-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.875rem;
        font-weight: 600;
        color: #1e293b;
        padding: 10px 16px 10px 0 !important;
    }

    /* Empty list week */
    .fc-list-empty { background: transparent; }
    .fc-list-empty-cushion {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.875rem;
        color: #94a3b8;
        padding: 2.5rem;
        text-align: center;
    }
</style>

<script src="{{ asset('js/fullcalendar/index.global.min.js') }}"></script>
<script src="{{ asset('js/fullcalendar/locales-all.global.min.js') }}"></script>

<div class="max-w-6xl mx-auto space-y-5">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold font-jakarta text-gray-900">Jadwal Kelas</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kalender pertemuan untuk semua kelas aktif Anda.</p>
        </div>
        <div class="flex items-center gap-1.5 text-xs text-gray-500 bg-red-50 border border-red-100 rounded-lg px-3 py-1.5 self-start sm:self-auto">
            <span class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></span>
            <span class="font-medium text-red-600">Hari Libur Nasional</span>
        </div>
    </div>

    @if($memberClasses->isEmpty())
    {{-- Empty State --}}
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
    {{-- Class Color Legend --}}
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

    {{-- Calendar Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="p-5 sm:p-6">
            <div id="calendar"></div>
        </div>
    </div>
    @endif

</div>

<script>
    var calendarEvents = @json(array_merge($events, $holidayEvents));
    var holidayDates   = @json(array_column($holidays, 0));

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    function initClassCalendar() {
        if (typeof FullCalendar === 'undefined') {
            setTimeout(initClassCalendar, 50);
            return;
        }

        var el = document.getElementById('calendar');
        if (!el || el.innerHTML !== '') return;

        var cal = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,timeGridWeek,listWeek'
            },
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week:  'Minggu',
                list:  'Daftar'
            },
            locale: 'id',
            nowIndicator: true,
            slotMinTime: '07:00:00',
            slotMaxTime: '21:00:00',
            allDayText: 'Libur',
            noEventsContent: 'Tidak ada jadwal minggu ini.',
            events: calendarEvents,

            // Week view: Google Calendar-style column header
            dayHeaderContent: function(arg) {
                if (arg.view.type !== 'timeGridWeek') return true;
                var day  = arg.date.toLocaleDateString('id-ID', { weekday: 'short' }).toUpperCase();
                var num  = arg.date.getDate();
                var blue = '#5586DB';
                return { html:
                    '<div style="display:flex;flex-direction:column;align-items:center;padding:8px 4px;gap:3px">' +
                    '<span style="font-size:0.625rem;font-weight:700;letter-spacing:0.07em;color:' + (arg.isToday ? blue : '#64748b') + '">' + day + '</span>' +
                    '<span style="font-size:0.9375rem;font-weight:700;width:30px;height:30px;display:flex;align-items:center;justify-content:center;border-radius:50%;' +
                    (arg.isToday ? 'background:' + blue + ';color:#fff' : 'color:#0f172a') + '">' + num + '</span>' +
                    '</div>'
                };
            },

            // Event content: richer display in week + list views
            eventContent: function(arg) {
                var p    = arg.event.extendedProps;
                var view = arg.view.type;

                if (view === 'timeGridWeek') {
                    if (p.isHoliday) {
                        return { html: '<span style="font-size:0.6rem;font-weight:600;padding:1px 3px">' + esc(arg.event.title) + '</span>' };
                    }
                    return { html:
                        '<div style="line-height:1.35;padding:1px 0">' +
                        '<div style="font-size:0.6875rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + esc(arg.event.title) + '</div>' +
                        (p.tutor ? '<div style="font-size:0.6rem;opacity:0.85;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + esc(p.tutor) + '</div>' : '') +
                        '</div>'
                    };
                }

                if (view === 'listWeek') {
                    if (p.isHoliday) {
                        return { html:
                            '<div style="font-family:\'Plus Jakarta Sans\',sans-serif">' +
                            '<div style="font-size:0.875rem;font-weight:600;color:#dc2626">' + esc(arg.event.title) + '</div>' +
                            '<div style="font-size:0.6875rem;color:#f87171;margin-top:1px">Hari Libur Nasional</div>' +
                            '</div>'
                        };
                    }
                    var sub = [p.program, p.tutor].filter(Boolean).map(esc).join(' · ');
                    return { html:
                        '<div style="font-family:\'Plus Jakarta Sans\',sans-serif">' +
                        '<div style="font-size:0.875rem;font-weight:600;color:#1e293b">' + esc(arg.event.title) + '</div>' +
                        (sub ? '<div style="font-size:0.6875rem;color:#94a3b8;margin-top:1px">' + sub + '</div>' : '') +
                        '</div>'
                    };
                }

                return true; // default for month view
            },

            // Subtle red tint on holiday cells
            dayCellDidMount: function(info) {
                var d = info.date.toISOString().split('T')[0];
                if (holidayDates.includes(d)) {
                    info.el.style.backgroundColor = '#fff8f8';
                }
            },

            eventClick: function(info) {
                info.jsEvent.preventDefault();

                if (info.event.extendedProps.isHoliday) {
                    openModal(
                        info.event.title,
                        '<div class="flex items-center gap-2 text-sm text-gray-600">' +
                        '<span class="material-symbols-outlined text-gray-400" style="font-size:18px">event_busy</span>' +
                        '<span>Hari ini adalah hari libur nasional.</span></div>',
                        '#ef4444'
                    );
                    return;
                }

                var p     = info.event.extendedProps;
                var color = p.color || '#5586DB';
                var body  =
                    row('school',    'Program', p.program) +
                    row('person',    'Tutor',   p.tutor) +
                    row('schedule',  'Waktu',   p.time) +
                    row('menu_book', 'Materi',  p.material);

                openModal(info.event.title, body, color);
            }
        });

        cal.render();
    }

    function row(icon, label, value) {
        return '<div class="flex gap-3">' +
            '<span class="material-symbols-outlined text-gray-400 mt-0.5 flex-shrink-0" style="font-size:18px">' + esc(icon) + '</span>' +
            '<div>' +
            '<div class="text-xs text-gray-400 mb-0.5">' + esc(label) + '</div>' +
            '<div class="font-semibold text-gray-800">' + esc(value || '-') + '</div>' +
            '</div></div>';
    }

    function openModal(title, bodyHtml, accentColor) {
        var modal = document.createElement('dialog');
        modal.className = 'p-0 rounded-2xl shadow-2xl border-0 overflow-hidden w-full max-w-sm m-auto backdrop:bg-gray-900/50';
        modal.innerHTML =
            '<div class="h-1" style="background-color:' + esc(accentColor) + '"></div>' +
            '<div class="p-6">' +
            '<div class="flex justify-between items-start mb-5">' +
            '<h3 class="font-bold text-base font-jakarta text-gray-900 leading-snug pr-4">' + esc(title) + '</h3>' +
            '<button type="button" aria-label="Tutup" class="close-btn flex-shrink-0 w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">' +
            '<span class="material-symbols-outlined text-gray-500" style="font-size:15px">close</span>' +
            '</button></div>' +
            '<div class="space-y-4 text-sm">' + bodyHtml + '</div>' +
            '</div>';

        document.body.appendChild(modal);
        modal.showModal();

        modal.querySelector('.close-btn').addEventListener('click', () => modal.close());
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.close(); });
        modal.addEventListener('close', () => setTimeout(() => modal.remove(), 150));
    }

    initClassCalendar();
    document.addEventListener('livewire:navigated', initClassCalendar);
    document.addEventListener('turbolinks:load', initClassCalendar);
</script>
@endsection
