@extends('layouts.dashboard')

@section('title', 'KPI Saya - MINDSIA')
@section('header_title', 'KPI Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Personal Dashboard</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">KPI Saya</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Riwayat evaluasi kinerja Anda.</p>
    </div>

    @if($evaluations->isEmpty())
    <div class="rounded-2xl px-5 py-16 text-center"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
              style="color: var(--dash-text-faint);">track_changes</span>
        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada evaluasi KPI untuk Anda.</p>
        <p class="text-xs mt-1" style="color: var(--dash-text-faint);">
            Evaluasi muncul setelah evaluator membuat penilaian untuk Anda.
        </p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($evaluations as $eval)
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center justify-between gap-4 px-5 py-4">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-semibold" style="color: var(--dash-text-heading);">
                            {{ $eval->template?->template_name ?? '-' }}
                        </p>
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
                            {{ $eval->isDraft() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $eval->isDraft() ? 'Draft' : 'Final' }}
                        </span>
                        @if($eval->grade)
                        <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-900">
                            Grade {{ $eval->grade }}
                        </span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs" style="color: var(--dash-text-muted);">
                        {{ $eval->period_start_date?->format('d M Y') }} – {{ $eval->period_end_date?->format('d M Y') }}
                        <span class="mx-1" style="color: var(--dash-text-faint);">·</span>
                        Evaluator: {{ $eval->evaluator?->full_name ?? '-' }}
                    </p>
                </div>

                <div class="flex items-center gap-5 shrink-0">
                    @if($eval->total_score !== null)
                    <div class="text-right">
                        <p class="text-xs" style="color: var(--dash-text-faint);">Total Skor</p>
                        <p class="font-jakarta text-xl font-bold" style="color: var(--dash-text-heading);">
                            {{ number_format($eval->total_score, 1) }}
                        </p>
                    </div>
                    @endif
                    <a href="{{ route('kpi.evaluations.show', $eval) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[13px]">visibility</span>Lihat
                    </a>
                </div>
            </div>

            @if($eval->isFinalized() && $eval->evaluator_notes)
            <div class="px-5 py-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <p class="text-xs font-semibold mb-0.5" style="color: var(--dash-text-faint);">Catatan evaluator</p>
                <p class="text-sm" style="color: var(--dash-text-body);">{{ $eval->evaluator_notes }}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
