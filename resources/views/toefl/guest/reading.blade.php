@extends('layouts.exam')

@section('title', 'Reading — TOEFL')

@section('content')

@php
    $questionsByPassage = $questions->groupBy('passage_id');
    $passageIds         = $questions->pluck('passage_id')->unique()->filter()->values();

    // Build passage groups with question range hints
    $passageGroups = [];
    foreach ($passageIds as $pid) {
        $pqs       = $questionsByPassage->get($pid, collect())->sortBy('display_order')->values();
        $firstNum  = $pqs->first()?->display_order;
        $lastNum   = $pqs->last()?->display_order;
        $hint      = $firstNum === $lastNum
            ? 'Soal ' . $firstNum
            : 'Soal ' . $firstNum . '–' . $lastNum;
        $passageGroups[] = [
            'passage'   => $pqs->first()?->passage,
            'questions' => $pqs,
            'hint'      => $hint,
        ];
    }
    $totalGroups = count($passageGroups);
@endphp

<div class="h-screen flex flex-col bg-gray-50">

    {{-- Header + Timer --}}
    <div class="bg-white border-b border-gray-200 shadow-sm px-6 py-3 flex items-center justify-between shrink-0">
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Section 3 / 3</p>
            <h2 class="text-base font-bold text-gray-800">Reading Comprehension</h2>
            <p id="phase-label" class="text-xs text-green-600 font-medium mt-0.5">Menunggu mulai…</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400 mb-0.5">Sisa Waktu</p>
            <p id="timer" class="text-2xl font-mono font-bold text-gray-800">--:--</p>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-6">
    <form id="submit-form" action="{{ route('toefl.guest.submit-section', $session) }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="reading">

        <div class="max-w-7xl mx-auto">

            {{-- Instructions panel --}}
            <div id="instr-panel">
                <div class="bg-white rounded-xl border border-green-200 shadow-sm p-6 mb-5">
                    <p class="text-xs text-green-700 uppercase font-bold tracking-wider mb-1">Section 3 — Reading Comprehension</p>
                    <p class="text-xs text-gray-500 mb-4">Time: 55 minutes (including the reading of the directions)</p>
                    <p class="text-sm text-gray-700 leading-relaxed mb-4">
                        This section is designed to measure your ability to read and understand short passages similar in topic and style to those students are likely to encounter in North American universities and colleges.
                    </p>
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <span class="font-semibold underline">Directions:</span> In this section you will read several passages. Each one is followed by a number of questions about it. Choose one best answer, (A), (B), (C), or (D), to each question. Answer all questions on the basis of what is <strong>stated</strong> or <strong>implied</strong> in the passage.
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="start-btn"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg text-sm transition-colors">
                        Mulai Soal &rarr;
                    </button>
                </div>
            </div>

            {{-- Passage groups (one at a time) --}}
            <div id="passages-panel" class="hidden">

                @foreach ($passageGroups as $gi => $group)
                    <div id="pg-{{ $gi }}" class="passage-group hidden">

                        {{-- Group header --}}
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs text-green-600 uppercase font-bold tracking-wider">
                                    Bacaan {{ $gi + 1 }} dari {{ $totalGroups }}
                                </p>
                                <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $group['hint'] }}</p>
                            </div>
                            <div class="flex gap-1">
                                @for ($i = 0; $i < $totalGroups; $i++)
                                    <div class="w-2 h-2 rounded-full {{ $i === $gi ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                                @endfor
                            </div>
                        </div>

                        {{-- Two-column layout --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
                            {{-- Passage (scrollable, sticky on large screens) --}}
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 lg:sticky lg:top-4 lg:self-start lg:max-h-[calc(100vh-12rem)] lg:overflow-y-auto">
                                @if ($group['passage'])
                                    @include('toefl.partials.passage-body', ['passage' => $group['passage']])
                                @endif
                            </div>

                            {{-- Questions --}}
                            <div class="space-y-4">
                                @foreach ($group['questions'] as $question)
                                    @php $saved = $answers->get($question->id); @endphp
                                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                                        <p class="text-xs text-gray-400 mb-2 font-semibold">Soal {{ $question->display_order }}</p>
                                        <p class="text-gray-800 font-medium mb-3 leading-relaxed text-sm">{{ $question->question_text }}</p>
                                        <div class="space-y-2">
                                            @foreach (['A','B','C','D'] as $opt)
                                                @php $optKey = 'option_' . strtolower($opt); $checked = $saved && $saved->selected_option === $opt; @endphp
                                                <label class="flex items-center gap-3 p-2.5 border rounded-lg cursor-pointer hover:bg-green-50 transition-colors text-sm
                                                    {{ $checked ? 'border-green-400 bg-green-50' : 'border-gray-200' }}">
                                                    <input type="radio" name="answer_{{ $question->id }}" value="{{ $opt }}"
                                                        class="h-4 w-4 text-green-600 autosave-radio"
                                                        data-question="{{ $question->id }}"
                                                        {{ $checked ? 'checked' : '' }}>
                                                    <span class="font-semibold text-gray-500 w-5">{{ $opt }}.</span>
                                                    <span class="text-gray-700">{{ $question->$optKey }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Navigation --}}
                        <div class="flex justify-end pb-4">
                            @if ($gi < $totalGroups - 1)
                                <button type="button"
                                    class="pg-next-btn inline-flex items-center gap-2 px-6 py-2.5 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg text-sm transition-colors"
                                    data-next="{{ $gi + 1 }}">
                                    Bacaan Berikutnya &rarr;
                                </button>
                            @else
                                <button type="button" onclick="submitReading()"
                                    class="inline-flex items-center px-8 py-2.5 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg text-sm transition-colors">
                                    Selesaikan Ujian &#10003;
                                </button>
                            @endif
                        </div>

                    </div>
                @endforeach

            </div>

        </div>
    </form>
    </div>

</div>

{{-- Submit confirmation modal --}}
<div id="submit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
        <h3 class="text-base font-bold text-gray-800 mb-2">Selesaikan Ujian?</h3>
        <p class="text-sm text-gray-500 mb-6">Jawaban tidak bisa diubah setelah submit. Pastikan semua soal sudah dijawab.</p>
        <div class="flex gap-3 justify-end">
            <button type="button" id="modal-cancel"
                class="px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" id="modal-confirm"
                class="px-6 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white text-sm font-medium transition-colors">
                Ya, Selesaikan
            </button>
        </div>
    </div>
</div>

<div id="exam-config" class="hidden"
    data-started-at="{{ $session->reading_started_at?->timestamp ?? '' }}"
    data-limit-seconds="{{ $timeLimit * 60 }}"
    data-csrf="{{ csrf_token() }}"
    data-answer-url="{{ route('toefl.guest.answer', $session) }}"
    data-total-groups="{{ $totalGroups }}"
></div>
@vite('resources/js/toefl-guest-reading-exam.js')
@include('toefl.partials.exam-security')
@endsection
