@extends('layouts.dashboard')

@section('title', 'Jadwal Interview - MINDSIA')
@section('header_title', 'Jadwal Interview')

@section('content')
<h1 class="sr-only">Jadwal Interview</h1>
<div class="max-w-4xl mx-auto space-y-4">

    @forelse($schedules as $schedule)
        @php
            $isPast = $schedule->scheduled_at->isPast();
            $isToday = $schedule->scheduled_at->isToday();
        @endphp
        <div class="bg-[var(--dash-card-bg)] rounded-2xl border {{ $isToday ? 'border-[#5586DB]' : 'border-[color:var(--dash-card-border)]' }} shadow-sm px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        @if($isToday)
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold text-white bg-[#5586DB] rounded-full uppercase tracking-wider">Hari Ini</span>
                        @elseif($isPast)
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold text-[color:var(--dash-text-muted)] bg-[var(--dash-nav-hover-bg)] rounded-full uppercase tracking-wider">Selesai</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold text-green-700 bg-green-50 rounded-full uppercase tracking-wider">Mendatang</span>
                        @endif

                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full uppercase tracking-wider
                            {{ $schedule->interview_type === 'online' ? 'text-purple-700 bg-purple-50' : 'text-orange-700 bg-orange-50' }}">
                            <span class="material-symbols-outlined text-[11px]">{{ $schedule->interview_type === 'online' ? 'videocam' : 'business' }}</span>
                            {{ $schedule->interview_type === 'online' ? 'Online' : 'Offline' }}
                        </span>
                    </div>

                    <h2 class="font-bold text-[color:var(--dash-text-heading)] font-jakarta text-base">
                        {{ $schedule->application->posting->title ?? 'Interview' }}
                    </h2>
                    @if($schedule->application->posting?->position)
                        <p class="text-xs text-[color:var(--dash-text-muted)] mt-0.5">{{ $schedule->application->posting->position->name }}</p>
                    @endif

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3">
                        <span class="text-sm font-semibold text-[color:var(--dash-text-body)] flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-[#5586DB]">calendar_month</span>
                            {{ $schedule->scheduled_at->translatedFormat('l, d F Y') }}
                        </span>
                        <span class="text-sm font-semibold text-[color:var(--dash-text-body)] flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-[#5586DB]">schedule</span>
                            {{ $schedule->scheduled_at->format('H:i') }} WIB
                        </span>
                    </div>

                    @if($schedule->interviewer)
                        <p class="text-xs text-[color:var(--dash-text-muted)] mt-2 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">person</span>
                            Pewawancara: {{ $schedule->interviewer->full_name ?? $schedule->interviewer->name ?? '-' }}
                        </p>
                    @endif

                    @if($schedule->interview_type === 'online' && $schedule->meeting_link)
                        <a href="{{ $schedule->meeting_link }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1.5 mt-3 px-4 py-1.5 text-xs font-semibold text-white bg-[#5586DB] hover:bg-[#4470c4] rounded-xl transition-colors">
                            <span class="material-symbols-outlined text-[13px]">videocam</span>
                            Buka Link Meeting
                        </a>
                    @elseif($schedule->interview_type === 'offline' && $schedule->location)
                        <p class="text-xs text-[color:var(--dash-text-body)] mt-2 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">location_on</span>
                            {{ $schedule->location }}
                        </p>
                    @endif

                    @if($schedule->notes)
                        <p class="text-xs text-[color:var(--dash-text-faint)] mt-2 italic">{{ $schedule->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[color:var(--dash-card-border)] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-gray-300 text-[48px]">event_busy</span>
            <p class="text-sm text-[color:var(--dash-text-faint)] mt-3">Belum ada jadwal interview.</p>
            <a href="{{ route('applicant.applications.index') }}"
               class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 text-sm font-semibold text-[#5586DB] bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-[16px]">visibility</span>
                Lihat Status Lamaran
            </a>
        </div>
    @endforelse

    <div class="pt-2">
        {{ $schedules->links() }}
    </div>

</div>
@endsection
