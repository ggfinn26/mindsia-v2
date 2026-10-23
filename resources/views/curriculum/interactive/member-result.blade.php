@extends('layouts.dashboard')

@section('title', 'Hasil Latihan - ' . $item->item_name)
@section('header_title', 'Hasil Latihan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('member.curriculum.index') }}"
               class="dash-back flex items-center justify-center w-9 h-9 rounded-lg transition-colors cursor-pointer shrink-0"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
               title="Kembali ke Kurikulum">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5"
                   style="color: var(--dash-primary-text);">Hasil Evaluasi</p>
                <h1 class="font-jakarta text-xl sm:text-2xl font-bold tracking-tight"
                    style="color: var(--dash-text-heading);">Hasil Latihan - {{ $item->item_name }}</h1>
            </div>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('member.curriculum.interactive.show', [$classroom, $item]) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 font-semibold rounded-lg text-xs text-white transition-opacity hover:opacity-90 shadow-sm cursor-pointer"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">replay</span>
                Ulangi Latihan
            </a>
            <a href="{{ route('member.curriculum.interactive.history', [$classroom, $item]) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 font-semibold rounded-lg text-xs transition-colors cursor-pointer shadow-sm"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">history</span>
                Riwayat
            </a>
        </div>
    </div>

    {{-- Score card --}}
    <div class="rounded-2xl p-8 text-center shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: var(--dash-text-muted);">Skor Kamu</p>
        <p class="text-5xl font-bold font-jakarta"
           style="color: {{ $attempt->score >= 70 ? 'var(--dash-status-success-text)' : ($attempt->score >= 50 ? 'var(--dash-status-warning-text)' : 'var(--dash-status-danger-text)') }};">
            {{ $attempt->score }}<span class="text-2xl" style="color: var(--dash-text-faint);">%</span>
        </p>
        <p class="text-xs mt-2" style="color: var(--dash-text-muted);">
            Selesai {{ $attempt->completed_at->diffForHumans() }}
        </p>
    </div>

    {{-- Answer review --}}
    @php $answerMap = $attempt->answers->keyBy('question_id'); @endphp

    @foreach($item->interactiveBlocks as $bi => $block)
        @if($block->questions->isNotEmpty())
            <div class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                    Blok {{ $bi + 1 }}
                </h2>
                @foreach($block->questions as $qi => $question)
                    @php $ans = $answerMap->get($question->id); @endphp
                    <div class="rounded-2xl p-5 shadow-sm space-y-3"
                         style="background-color: var(--dash-card-bg); border: 1px solid {{ $ans?->is_correct ? 'var(--dash-status-success-text)' : 'var(--dash-status-danger-text)' }};">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-semibold font-jakarta" style="color: var(--dash-text-heading);">
                                {{ $qi + 1 }}. {{ $question->question_text }}
                            </p>
                            @if($ans?->is_correct)
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded shrink-0"
                                      style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                                    <span class="material-symbols-outlined text-[14px]">check</span> Benar
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded shrink-0"
                                      style="background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text);">
                                    <span class="material-symbols-outlined text-[14px]">close</span> Salah
                                </span>
                            @endif
                        </div>
                        <div class="space-y-1.5 pt-1">
                            @foreach(['A','B','C','D'] as $opt)
                                @php $val = $question->{'option_'.strtolower($opt)}; @endphp
                                @if($val)
                                    @php
                                        $isCorrect = $opt === $question->correct_option;
                                        $isSelectedWrong = $opt === $ans?->selected_option && ! $isCorrect;
                                    @endphp
                                    <div class="flex items-center gap-2 text-xs sm:text-sm py-2 px-3 rounded-lg"
                                         style="{{ $isCorrect ? 'background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); font-weight: 600;' : ($isSelectedWrong ? 'background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text); text-decoration: line-through;' : 'color: var(--dash-text-body);') }}">
                                        <span>{{ $opt }}.</span>
                                        <span>{{ $val }}</span>
                                        @if($isCorrect)
                                            <span class="ml-auto text-xs font-semibold">(Jawaban benar)</span>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @if($question->explanation)
                            <div class="mt-3 text-xs rounded-lg p-3"
                                 style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                                <span class="font-bold" style="color: var(--dash-text-heading);">Penjelasan:</span> {{ $question->explanation }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
</div>
@endsection
