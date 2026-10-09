@extends('layouts.dashboard')

@section('title', 'Sesi & Absensi — Semua Kelas')
@section('header_title', 'Sesi & Absensi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Sesi & Absensi</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Semua kelas dalam scope akses Anda</p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter bar --}}
    <div class="rounded-2xl px-5 py-4"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" action="{{ route('class-schedules.index') }}">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nama kelas..."
                       class="col-span-2 lg:col-span-1 rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">

                <select name="branch_id"
                        class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" @selected(($filters['branch_id'] ?? '') == $b->id)>{{ $b->branch_name }}</option>
                    @endforeach
                </select>

                <select name="tutor_id"
                        class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Semua Tutor</option>
                    @foreach($tutors as $t)
                        <option value="{{ $t->id }}" @selected(($filters['tutor_id'] ?? '') == $t->id)>{{ $t->full_name }}</option>
                    @endforeach
                </select>

                <select name="program_id"
                        class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Semua Program</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" @selected(($filters['program_id'] ?? '') == $p->id)>{{ $p->program_name }}</option>
                    @endforeach
                </select>

                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                       class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">

                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                       class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>

            <input type="hidden" name="sort" value="{{ $sort }}">

            <div class="flex gap-3 mt-3 items-center">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>Filter
                </button>
                <a href="{{ route('class-schedules.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[9%]">
                <col class="w-[18%]">
                <col class="w-[11%]">
                <col class="w-[9%]">
                <col class="w-[11%]">
                <col class="w-[10%]">
                <col class="w-[17%]">
                <col class="w-[4%]">
                <col class="w-[4%]">
                <col class="w-[4%]">
                <col class="w-[3%]">
            </colgroup>
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    @php
                        $nextDateSort = $sort === 'date_desc' ? 'date_asc' : 'date_desc';
                        $nextClassSort = $sort === 'class_asc' ? 'class_desc' : 'class_asc';
                    @endphp

                    <th class="px-4 py-3 text-left">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $nextDateSort, 'page' => 1]) }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider transition-opacity hover:opacity-70"
                           style="color: var(--dash-text-muted);">
                            Tanggal
                            <span class="material-symbols-outlined text-[14px]">
                                @if($sort === 'date_asc') arrow_upward
                                @elseif($sort === 'date_desc') arrow_downward
                                @else unfold_more @endif
                            </span>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $nextClassSort, 'page' => 1]) }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider transition-opacity hover:opacity-70"
                           style="color: var(--dash-text-muted);">
                            Kelas
                            <span class="material-symbols-outlined text-[14px]">
                                @if($sort === 'class_asc') arrow_upward
                                @elseif($sort === 'class_desc') arrow_downward
                                @else unfold_more @endif
                            </span>
                        </a>
                    </th>
                    @foreach(['Program','Cabang','Tutor','Jam','Materi'] as $col)
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">{{ $col }}</th>
                    @endforeach
                    <th class="px-2 py-3 text-center text-xs font-semibold uppercase tracking-wider"
                        style="color: var(--dash-text-muted);" title="Hadir">H</th>
                    <th class="px-2 py-3 text-center text-xs font-semibold uppercase tracking-wider"
                        style="color: var(--dash-text-muted);" title="Absen">A</th>
                    <th class="px-2 py-3 text-center text-xs font-semibold uppercase tracking-wider"
                        style="color: var(--dash-text-muted);" title="Telat">T</th>
                    <th class="px-2 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                        style="color: var(--dash-text-muted);"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                    @php $isToday = $schedule->schedule_date->isToday(); @endphp
                    <tr style="border-bottom: 1px solid var(--dash-card-border); {{ $isToday ? 'background-color: var(--dash-primary-bg);' : '' }}"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='{{ $isToday ? 'var(--dash-primary-bg)' : '' }}'">

                        <td class="px-4 py-3 text-sm" style="color: {{ $isToday ? 'var(--dash-primary-text)' : 'var(--dash-text-body)' }}; font-weight: {{ $isToday ? '600' : '400' }};">
                            {{ $schedule->schedule_date?->format('d M Y') }}
                            @if($isToday)
                                <span class="block text-xs mt-0.5" style="color: var(--dash-primary);">Hari ini</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-medium truncate">
                            <a href="{{ route('classrooms.show', $schedule->classRoom) }}"
                               class="hover:underline" style="color: var(--dash-primary);">
                                {{ $schedule->classRoom?->class_name ?? '-' }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm truncate" style="color: var(--dash-text-muted);">
                            {{ $schedule->classRoom?->program?->program_name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm truncate" style="color: var(--dash-text-muted);">
                            {{ $schedule->classRoom?->branch?->branch_name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm truncate" style="color: var(--dash-text-muted);">
                            {{ $schedule->classRoom?->tutor?->full_name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm" style="color: var(--dash-text-muted);">
                            {{ $schedule->start_time }} — {{ $schedule->end_time }}
                        </td>
                        <td class="px-4 py-3 text-sm truncate" style="color: var(--dash-text-muted);">
                            {{ $schedule->material_taught ?? '-' }}
                        </td>
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold"
                                  style="background-color: #f0fdf4; color: #059669;">{{ $schedule->present_count }}</span>
                        </td>
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold"
                                  style="background-color: #fee2e2; color: #dc2626;">{{ $schedule->absent_count }}</span>
                        </td>
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold"
                                  style="background-color: #fef3c7; color: #d97706;">{{ $schedule->late_count }}</span>
                        </td>
                        <td class="px-2 py-3 text-right">
                            <a href="{{ route('classrooms.show', $schedule->classRoom) }}"
                               class="inline-flex items-center justify-center w-7 h-7 rounded-lg transition-colors"
                               title="Buka"
                               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-muted); background-color: var(--dash-card-bg);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-6 py-16 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">event_busy</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Tidak ada sesi ditemukan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($schedules->hasPages())
            <div class="px-5 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
