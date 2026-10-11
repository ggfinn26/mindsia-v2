@extends('layouts.dashboard')

@section('title', 'Sesi & Absensi')
@section('header_title', 'Sesi & Absensi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Akademik</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Sesi & Absensi</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Semua sesi kelas dan rekap kehadiran member dalam scope akses Anda.
            </p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('class-schedules.index') }}"
          class="p-4 sm:p-5 rounded-2xl space-y-4"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Search Bar --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Pencarian Kelas</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                          style="color: var(--dash-text-muted);">search</span>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Cari nama kelas..."
                           class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
            </div>

            {{-- Branch --}}
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Cabang</label>
                <select name="branch_id"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" @selected(($filters['branch_id'] ?? '') == $b->id)>{{ $b->branch_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tutor --}}
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Tutor</label>
                <select name="tutor_id"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Tutor</option>
                    @foreach($tutors as $t)
                        <option value="{{ $t->id }}" @selected(($filters['tutor_id'] ?? '') == $t->id)>{{ $t->full_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Program --}}
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Program</label>
                <select name="program_id"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Program</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" @selected(($filters['program_id'] ?? '') == $p->id)>{{ $p->program_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end pt-2 border-t"
             style="border-color: var(--dash-card-border);">
            {{-- Date From --}}
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'" />
            </div>

            {{-- Date To --}}
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'" />
            </div>

            {{-- Sort --}}
            <div>
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Urutan</label>
                <select name="sort"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="date_desc" @selected($sort === 'date_desc')>Tanggal - Terbaru</option>
                    <option value="date_asc" @selected($sort === 'date_asc')>Tanggal - Terlama</option>
                    <option value="class_asc" @selected($sort === 'class_asc')>Nama Kelas A - Z</option>
                    <option value="class_desc" @selected($sort === 'class_desc')>Nama Kelas Z - A</option>
                </select>
            </div>

            {{-- Filter & Reset Actions --}}
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    Filter
                </button>
                <a href="{{ route('class-schedules.index') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Table Card --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Tanggal & Waktu</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Kelas & Program</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Tutor</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Materi</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                            style="color: var(--dash-text-muted);">Presensi Siswa</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($schedules as $schedule)
                        @php
                            $isToday = $schedule->schedule_date?->isToday();
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border); {{ $isToday ? 'background-color: color-mix(in srgb, var(--dash-primary) 6%, transparent);' : '' }}"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='{{ $isToday ? 'color-mix(in srgb, var(--dash-primary) 6%, transparent)' : 'transparent' }}'">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-medium text-xs {{ $isToday ? 'font-semibold' : '' }}"
                                          style="color: {{ $isToday ? 'var(--dash-primary)' : 'var(--dash-text-heading)' }};">
                                        {{ $schedule->schedule_date?->format('d M Y') }}
                                    </span>
                                    @if($isToday)
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-full uppercase tracking-wider"
                                              style="background-color: color-mix(in srgb, var(--dash-primary) 15%, transparent); color: var(--dash-primary);">
                                            Hari ini
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] font-mono mt-0.5" style="color: var(--dash-text-muted);">
                                    {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                </p>
                            </td>
                            <td class="px-4 py-3 font-medium">
                                <a href="{{ route('classrooms.show', $schedule->classRoom) }}"
                                   class="font-semibold block hover:underline transition-colors text-xs"
                                   style="color: var(--dash-primary-text);">
                                    {{ $schedule->classRoom?->class_name ?? '-' }}
                                </a>
                                <p class="text-[11px] mt-0.5 flex items-center gap-1" style="color: var(--dash-text-muted);">
                                    <span>{{ $schedule->classRoom?->program?->program_name ?? '-' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $schedule->classRoom?->branch?->branch_name ?? '-' }}</span>
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs" style="color: var(--dash-text-body);">
                                <span class="font-medium">{{ $schedule->classRoom?->tutor?->full_name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs max-w-xs" style="color: var(--dash-text-body);">
                                <span class="line-clamp-2" title="{{ $schedule->material_taught ?? '-' }}">
                                    {{ $schedule->material_taught ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="inline-flex items-center justify-center gap-1">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-xs font-semibold rounded-md"
                                          style="background-color: color-mix(in srgb, #16a34a 12%, transparent); color: #16a34a;" title="Hadir">
                                        <span class="text-[10px] opacity-75">H:</span>{{ $schedule->present_count }}
                                    </span>
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-xs font-semibold rounded-md"
                                          style="background-color: color-mix(in srgb, var(--dash-danger) 12%, transparent); color: var(--dash-danger);" title="Absen">
                                        <span class="text-[10px] opacity-75">A:</span>{{ $schedule->absent_count }}
                                    </span>
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-xs font-semibold rounded-md"
                                          style="background-color: color-mix(in srgb, #d97706 12%, transparent); color: #d97706;" title="Terlambat">
                                        <span class="text-[10px] opacity-75">T:</span>{{ $schedule->late_count }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <a href="{{ route('classrooms.show', $schedule->classRoom) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                    Buka
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">event_busy</span>
                                <h3 class="font-jakarta text-base font-semibold" style="color: var(--dash-text-heading);">Tidak ada sesi ditemukan</h3>
                                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Coba ubah kriteria pencarian atau filter tanggal di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
