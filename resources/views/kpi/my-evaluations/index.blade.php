@extends('layouts.dashboard')

@section('title', 'KPI Saya')
@section('header_title', 'KPI Saya')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div>
        <p class="text-sm font-medium text-[#725c00]">Personal Dashboard</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">KPI Saya</h2>
        <p class="mt-1 text-sm text-slate-600">Riwayat evaluasi kinerja Anda.</p>
    </div>

    @if ($evaluations->isEmpty())
        <div class="border border-slate-200 bg-white px-6 py-16 text-center">
            <span class="material-symbols-outlined text-4xl text-slate-300">track_changes</span>
            <p class="mt-3 font-semibold text-slate-700">Belum ada evaluasi KPI untuk Anda.</p>
            <p class="mt-1 text-sm text-slate-500">Evaluasi akan muncul di sini setelah evaluator membuat penilaian untuk Anda.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($evaluations as $eval)
                <div class="border border-slate-200 bg-white">
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-slate-900">{{ $eval->template?->template_name ?? '—' }}</p>
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $eval->isDraft() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $eval->isDraft() ? 'Draft' : 'Final' }}
                                </span>
                                @if ($eval->grade)
                                    <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-900">Grade {{ $eval->grade }}</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $eval->period_start_date?->format('d M Y') }} – {{ $eval->period_end_date?->format('d M Y') }}
                                <span class="mx-1 text-slate-300">·</span>
                                Evaluator: {{ $eval->evaluator?->full_name ?? '—' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-6 shrink-0">
                            @if ($eval->total_score !== null)
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Total Skor</p>
                                    <p class="font-jakarta text-xl font-bold text-slate-900">{{ number_format($eval->total_score, 1) }}</p>
                                </div>
                            @endif
                            <a href="{{ route('kpi.evaluations.show', $eval) }}"
                               class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#215aac]">
                                Lihat Detail
                            </a>
                        </div>
                    </div>

                    @if ($eval->isFinalized() && $eval->evaluator_notes)
                        <div class="border-t border-slate-100 bg-slate-50 px-5 py-3">
                            <p class="text-xs font-semibold text-slate-500">Catatan evaluator</p>
                            <p class="mt-0.5 text-sm text-slate-700">{{ $eval->evaluator_notes }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
