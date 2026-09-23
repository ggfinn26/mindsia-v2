@extends('layouts.exam')

@section('title', 'Listening — TOEFL')

@section('content')

@php
    use Illuminate\Support\Facades\Storage;

    $audioInstructionA = asset('toefl-audio/SECTION-A-INSTRUCTIONS.mp3');
    $audioEndingA      = asset('toefl-audio/ENDING-PART-A.mp3');
    $audioInstructionB = asset('toefl-audio/SECTION-B-INSTRUCTIONS.mp3');
    $audioEndingB      = asset('toefl-audio/ENDING-OF-SECTION-B.mp3');
    $audioInstructionC = asset('toefl-audio/INSTRUCTIONS-PART-C.mp3');
    $audioEndingC      = asset('toefl-audio/ENDING-OF-LISTENING-SECTION.mp3');

    $partAQuestions = [];
    $partBGroups    = [];
    $partCGroups    = [];

    foreach ($questions as $q) {
        $order = $q->display_order;

        if ($order <= 30) {
            $dbAudio = ($q->passage_id && $q->passage && $q->passage->audio_media_id && $q->passage->audio)
                ? Storage::url($q->passage->audio->file_path) : null;
            $partAQuestions[] = [
                'q'        => $q,
                'audioUrl' => $dbAudio ?? asset('toefl-audio/SECTION-A-NUMBER-' . $order . '.mp3'),
            ];
        } elseif ($order <= 38) {
            $pid = $q->passage_id ?? 'b';
            if (!isset($partBGroups[$pid])) {
                $partBGroups[$pid] = ['questions' => [], 'passage' => $q->passage_id ? $q->passage : null];
            }
            $partBGroups[$pid]['questions'][] = $q;
        } else {
            $pid = $q->passage_id ?? 'c';
            if (!isset($partCGroups[$pid])) {
                $partCGroups[$pid] = ['questions' => [], 'passage' => $q->passage_id ? $q->passage : null];
            }
            $partCGroups[$pid]['questions'][] = $q;
        }
    }

    $resolveGroupAudio = function (array $group, string $prefix) {
        if ($group['passage'] && $group['passage']->audio_media_id && $group['passage']->audio) {
            return Storage::url($group['passage']->audio->file_path);
        }
        $qs   = $group['questions'];
        $from = $qs[0]->display_order;
        $to   = end($qs)->display_order;
        return asset("toefl-audio/{$prefix}-NUMBER-{$from}-TO-{$to}.mp3");
    };

    $partBGroups = array_values(array_map(
        fn ($g) => array_merge($g, ['audioUrl' => $resolveGroupAudio($g, 'SECTION-B')]),
        $partBGroups
    ));
    $partCGroups = array_values(array_map(
        fn ($g) => array_merge($g, ['audioUrl' => $resolveGroupAudio($g, 'SECTION-C')]),
        $partCGroups
    ));
@endphp

<div class="h-screen flex flex-col bg-gray-50">

    {{-- Header + Timer --}}
    <div class="bg-white border-b border-gray-200 shadow-sm px-6 py-3 flex items-center justify-between shrink-0">
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Section 1 / 3</p>
            <h2 class="text-base font-bold text-gray-800">Listening Comprehension</h2>
            <p id="phase-label" class="text-xs text-indigo-600 font-medium mt-0.5">Menunggu mulai…</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400 mb-0.5">Sisa Waktu</p>
            <p id="timer" class="text-2xl font-mono font-bold text-gray-800">--:--</p>
        </div>
    </div>

    {{-- Audio player bar --}}
    <div class="bg-indigo-900 text-white px-6 py-3 shrink-0">
        <div class="max-w-3xl mx-auto flex items-center gap-4">
            <div class="flex-1 min-w-0">
                <p class="text-xs text-indigo-300 mb-0.5">Sedang diputar</p>
                <p id="now-playing" class="text-sm font-semibold truncate">—</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div id="audio-spinner" class="w-4 h-4 border-2 border-indigo-300 border-t-white rounded-full animate-spin hidden"></div>
                <span id="audio-status" class="text-xs text-indigo-300 whitespace-nowrap"></span>
            </div>
        </div>
        <div class="max-w-3xl mx-auto mt-2">
            <div class="w-full bg-indigo-700 rounded-full h-1">
                <div id="audio-progress" class="bg-white h-1 rounded-full transition-all" style="width:0%"></div>
            </div>
        </div>
    </div>

    {{-- Main content area --}}
    <div class="flex-1 overflow-y-auto px-4 py-6">
    <form id="submit-form" action="{{ route('toefl.session.submit-section', $session) }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="listening">

        {{-- ── Instruction panels ── --}}
        <div id="instruction-panel" class="max-w-3xl mx-auto hidden">
            {{-- Part A --}}
            <div id="instr-a" class="hidden">
                <div class="bg-white rounded-xl border border-indigo-200 shadow-sm p-6 mb-4">
                    <p class="text-xs text-indigo-500 uppercase font-bold tracking-wider mb-1">Section 1 — Listening Comprehension</p>
                    <p class="text-xs text-gray-500 mb-4">Time: approximately 35 minutes (including the reading of the directions for each part)</p>
                    <p class="text-sm text-gray-700 leading-relaxed mb-4">
                        In this section of the test, you will have an opportunity to demonstrate your ability to understand conversations and talks in English. There are three parts to this section. Answer all the questions on the basis of what is <strong>stated</strong> or <strong>implied</strong> by the speakers you hear. Do not take notes or write in your test book at any time. <strong>Do not</strong> turn the pages until you are told to do so.
                    </p>
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs text-indigo-600 uppercase font-bold tracking-wider mb-2">Part A</p>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <span class="font-semibold underline">Directions:</span> In Part A you will hear short conversations between two people. After each conversation, you will hear a question about the conversation. The conversations and questions will not be repeated. After you hear a question, read the four possible answers in your test book and choose the best answer. Then, on your answer sheet, find the number of the question and fill in the space that corresponds to the letter of the answer you have chosen.
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="start-part-a-btn"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg text-sm transition-colors">
                        <span>Mulai Soal Part A</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </div>

            {{-- Part B --}}
            <div id="instr-b" class="hidden">
                <div class="bg-white rounded-xl border border-amber-200 shadow-sm p-6 mb-4">
                    <p class="text-xs text-amber-600 uppercase font-bold tracking-wider mb-3">Part B</p>
                    <p class="text-sm text-gray-700 leading-relaxed mb-3">
                        <span class="font-semibold">Directions:</span> In this part of the test, you will hear longer conversations. After each conversation, you will hear several questions. The conversations and questions will not be repeated.
                    </p>
                    <p class="text-sm text-gray-700 leading-relaxed mb-3">
                        After you hear a question, read the four possible answers in your test book and choose the best answer. Then, on your answer sheet, find the number of the question and fill in the space that corresponds to the letter of the answer you have chosen.
                    </p>
                    <p class="text-sm text-gray-700 font-medium">
                        Remember, you are not allowed to take notes or write in your test book.
                    </p>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="start-part-b-btn"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg text-sm transition-colors">
                        <span>Mulai Soal Part B</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </div>

            {{-- Part C --}}
            <div id="instr-c" class="hidden">
                <div class="bg-white rounded-xl border border-teal-200 shadow-sm p-6 mb-4">
                    <p class="text-xs text-teal-600 uppercase font-bold tracking-wider mb-3">Part C</p>
                    <p class="text-sm text-gray-700 leading-relaxed mb-3">
                        <span class="font-semibold underline">Directions:</span> In this part of the test, you will hear several talks. After each talk, you will hear some questions. The talks and questions will not be repeated.
                    </p>
                    <p class="text-sm text-gray-700 leading-relaxed">
                        After you hear a question, read the four possible answers in your test book and choose the best answer. Then, on your answer sheet, find the number of the question and fill in the space that corresponds to the letter of the answer you have chosen.
                    </p>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="start-part-c-btn"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-teal-500 hover:bg-teal-600 text-white font-medium rounded-lg text-sm transition-colors">
                        <span>Mulai Soal Part C</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Part A: sequential single-question ── --}}
        <div id="part-a-container" class="hidden max-w-3xl mx-auto">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                    <div id="part-a-progress-bar" class="bg-indigo-500 h-1.5 rounded-full transition-all" style="width:0%"></div>
                </div>
                <span id="part-a-counter" class="text-xs text-gray-500 shrink-0 font-mono">1 / {{ count($partAQuestions) }}</span>
            </div>

            @foreach ($partAQuestions as $i => $item)
                @php $q = $item['q']; $saved = $answers->get($q->id); @endphp
                <div id="part-a-q-{{ $i }}"
                    class="part-a-question bg-white rounded-xl border border-gray-200 shadow-sm p-5 {{ $i > 0 ? 'hidden' : '' }}"
                    data-audio="{{ $item['audioUrl'] }}">

                    <p class="text-xs text-gray-400 mb-3 font-semibold">Soal {{ $q->display_order }}</p>

                    @if ($q->question_text)
                        <p class="text-gray-800 font-medium mb-4 leading-relaxed">{{ $q->question_text }}</p>
                    @endif

                    @if ($q->image)
                        <img src="{{ asset('storage/' . $q->image->file_path) }}" class="mb-4 max-h-48 rounded" alt="">
                    @endif

                    <div class="space-y-2">
                        @foreach (['A','B','C','D'] as $opt)
                            @php $optKey = 'option_' . strtolower($opt); $checked = $saved && $saved->selected_option === $opt; @endphp
                            <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-indigo-50 transition-colors
                                {{ $checked ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200' }}">
                                <input type="radio" name="answer_{{ $q->id }}" value="{{ $opt }}"
                                    class="h-4 w-4 text-indigo-600 autosave-radio"
                                    data-question="{{ $q->id }}"
                                    {{ $checked ? 'checked' : '' }}>
                                <span class="font-semibold text-gray-500 w-5">{{ $opt }}.</span>
                                <span class="text-gray-700">{{ $q->$optKey }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="mt-4 flex justify-end">
                <button type="button" id="part-a-next"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg text-sm transition-colors">
                    Soal Berikutnya &rarr;
                </button>
            </div>
        </div>

        {{-- ── Part B groups ── --}}
        <div id="part-b-container" class="hidden max-w-3xl mx-auto space-y-4">
            @foreach ($partBGroups as $gi => $group)
                <div id="part-b-group-{{ $gi }}"
                    class="part-b-group hidden"
                    data-audio="{{ $group['audioUrl'] }}"
                    data-label="{{ $group['passage'] ? $group['passage']->title : 'Percakapan B'.($gi+1) }}">

                    @if ($group['passage'])
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                            <p class="text-xs uppercase font-semibold text-amber-700">{{ $group['passage']->title }}</p>
                            @if ($group['passage']->body_text)
                                <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $group['passage']->body_text }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="space-y-4">
                        @foreach ($group['questions'] as $q)
                            @php $saved = $answers->get($q->id); @endphp
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                                <p class="text-xs text-gray-400 mb-3 font-semibold">Soal {{ $q->display_order }}</p>
                                @if ($q->question_text)
                                    <p class="text-gray-800 font-medium mb-4 leading-relaxed">{{ $q->question_text }}</p>
                                @endif
                                @if ($q->image)
                                    <img src="{{ asset('storage/' . $q->image->file_path) }}" class="mb-4 max-h-48 rounded" alt="">
                                @endif
                                <div class="space-y-2">
                                    @foreach (['A','B','C','D'] as $opt)
                                        @php $optKey = 'option_' . strtolower($opt); $checked = $saved && $saved->selected_option === $opt; @endphp
                                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-amber-50 transition-colors
                                            {{ $checked ? 'border-amber-400 bg-amber-50' : 'border-gray-200' }}">
                                            <input type="radio" name="answer_{{ $q->id }}" value="{{ $opt }}"
                                                class="h-4 w-4 text-amber-600 autosave-radio"
                                                data-question="{{ $q->id }}"
                                                {{ $checked ? 'checked' : '' }}>
                                            <span class="font-semibold text-gray-500 w-5">{{ $opt }}.</span>
                                            <span class="text-gray-700">{{ $q->$optKey }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex justify-end b-nav-wrap">
                        @if ($gi < count($partBGroups) - 1)
                            <button type="button"
                                class="b-next-btn inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg text-sm transition-colors"
                                data-next="{{ $gi + 1 }}">
                                Percakapan Berikutnya &rarr;
                            </button>
                        @else
                            <button type="button" id="b-finish-btn"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg text-sm transition-colors">
                                Lanjut ke Part C &rarr;
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Part C groups ── --}}
        <div id="part-c-container" class="hidden max-w-3xl mx-auto space-y-4">
            @foreach ($partCGroups as $gi => $group)
                <div id="part-c-group-{{ $gi }}"
                    class="part-c-group hidden"
                    data-audio="{{ $group['audioUrl'] }}"
                    data-label="{{ $group['passage'] ? $group['passage']->title : 'Ceramah C'.($gi+1) }}">

                    @if ($group['passage'])
                        <div class="bg-teal-50 border border-teal-200 rounded-xl p-4 mb-4">
                            <p class="text-xs uppercase font-semibold text-teal-700">{{ $group['passage']->title }}</p>
                            @if ($group['passage']->body_text)
                                <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $group['passage']->body_text }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="space-y-4">
                        @foreach ($group['questions'] as $q)
                            @php $saved = $answers->get($q->id); @endphp
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                                <p class="text-xs text-gray-400 mb-3 font-semibold">Soal {{ $q->display_order }}</p>
                                @if ($q->question_text)
                                    <p class="text-gray-800 font-medium mb-4 leading-relaxed">{{ $q->question_text }}</p>
                                @endif
                                @if ($q->image)
                                    <img src="{{ asset('storage/' . $q->image->file_path) }}" class="mb-4 max-h-48 rounded" alt="">
                                @endif
                                <div class="space-y-2">
                                    @foreach (['A','B','C','D'] as $opt)
                                        @php $optKey = 'option_' . strtolower($opt); $checked = $saved && $saved->selected_option === $opt; @endphp
                                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-teal-50 transition-colors
                                            {{ $checked ? 'border-teal-400 bg-teal-50' : 'border-gray-200' }}">
                                            <input type="radio" name="answer_{{ $q->id }}" value="{{ $opt }}"
                                                class="h-4 w-4 text-teal-600 autosave-radio"
                                                data-question="{{ $q->id }}"
                                                {{ $checked ? 'checked' : '' }}>
                                            <span class="font-semibold text-gray-500 w-5">{{ $opt }}.</span>
                                            <span class="text-gray-700">{{ $q->$optKey }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex justify-end">
                        @if ($gi < count($partCGroups) - 1)
                            <button type="button"
                                class="c-next-btn inline-flex items-center gap-2 px-6 py-2.5 bg-teal-500 hover:bg-teal-600 text-white font-medium rounded-lg text-sm transition-colors"
                                data-next="{{ $gi + 1 }}">
                                Ceramah Berikutnya &rarr;
                            </button>
                        @else
                            <button type="button" id="c-finish-btn"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-teal-500 hover:bg-teal-600 text-white font-medium rounded-lg text-sm transition-colors">
                                Selesai Part C &rarr;
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </form>
    </div>

    {{-- Submit bar --}}
    <div class="bg-white border-t border-gray-200 px-6 py-3 flex justify-end shrink-0">
        <button type="button" id="submit-btn" disabled
            onclick="if(confirm('Submit section Listening? Tidak bisa diubah setelah submit.')) document.getElementById('submit-form').submit()"
            class="inline-flex items-center px-8 py-2.5 bg-indigo-500 hover:bg-indigo-600 disabled:opacity-40 disabled:cursor-not-allowed text-white font-medium rounded-lg text-sm transition-colors">
            Submit Listening &rarr;
        </button>
    </div>

</div>

<div id="exam-config" class="hidden"
    data-mark-started-url="{{ route('toefl.session.listening.start', $session) }}"
    data-answer-url="{{ route('toefl.session.answer', $session) }}"
    data-csrf="{{ csrf_token() }}"
    data-limit-seconds="{{ $timeLimit * 60 }}"
    data-url-instruction-a="{{ $audioInstructionA }}"
    data-url-ending-a="{{ $audioEndingA }}"
    data-url-instruction-b="{{ $audioInstructionB }}"
    data-url-ending-b="{{ $audioEndingB }}"
    data-url-instruction-c="{{ $audioInstructionC }}"
    data-url-ending-c="{{ $audioEndingC }}"
    data-part-a-count="{{ count($partAQuestions) }}"
    data-part-b-count="{{ count($partBGroups) }}"
    data-part-c-count="{{ count($partCGroups) }}"
></div>
@vite('resources/js/toefl-listening-exam.js')
@include('toefl.partials.exam-security')
@endsection
