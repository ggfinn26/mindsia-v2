@extends('layouts.dashboard')

@section('title', 'KPI Saya - MINDSIA')
@section('header_title', 'KPI Saya')

@section('content')
<h1 class="sr-only">KPI Saya</h1>
<div class="max-w-4xl mx-auto pt-8 sm:pt-16 space-y-4">

    @if ($evaluations->isEmpty())
        <div class="rounded-2xl px-6 py-24 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined text-4xl" style="color: var(--dash-text-faint);">track_changes</span>
            <p class="mt-3 font-semibold text-sm" style="color: var(--dash-text-heading);">Belum ada evaluasi KPI untuk Anda.</p>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Evaluasi muncul setelah evaluator membuat penilaian untuk Anda.</p>
        </div>
    @else
        @foreach ($evaluations as $eval)
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                <div class="flex items-center justify-between gap-4 px-6 py-5">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                                {{ $eval->template?->template_name ?? '-' }}
                            </p>
                            <span class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-semibold"
                                  style="{{ $eval->isDraft()
                                      ? 'background-color:#fef3c7;color:#b45309;'
                                      : 'background-color:#f0fdf4;color:#059669;' }}">
                                {{ $eval->isDraft() ? 'Draft' : 'Final' }}
                            </span>
                            @if ($eval->grade)
                                <span class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-bold"
                                      style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
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
                        @if ($eval->total_score !== null)
                            <div class="text-right">
                                <p class="text-xs" style="color: var(--dash-text-muted);">Total Skor</p>
                                <p class="font-jakarta text-xl font-bold" style="color: var(--dash-primary);">
                                    {{ number_format($eval->total_score, 1) }}
                                </p>
                            </div>
                        @endif
                        <a href="{{ route('kpi.evaluations.show', $eval) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                           style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                            Lihat Detail
                        </a>
                    </div>
                </div>

                @if ($eval->isFinalized() && $eval->evaluator_notes)
                    <div class="px-6 py-3 border-t" style="border-color: var(--dash-card-border); background-color: var(--dash-primary-bg);">
                        <p class="text-xs font-semibold mb-0.5" style="color: var(--dash-text-muted);">Catatan evaluator</p>
                        <p class="text-sm" style="color: var(--dash-text-body);">{{ $eval->evaluator_notes }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    @endif

</div>
@endsection
