@extends('layouts.exam')

@section('title', 'Structure — TOEFL')

@section('content')
<div class="h-screen flex flex-col bg-gray-50">

    {{-- Header + Timer --}}
    <div class="bg-white border-b border-gray-200 shadow-sm px-6 py-3 flex items-center justify-between shrink-0">
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Section 2 / 3</p>
            <h2 class="text-base font-bold text-gray-800">Structure &amp; Written Expression</h2>
            <p id="phase-label" class="text-xs text-purple-600 font-medium mt-0.5">Menunggu mulai…</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400 mb-0.5">Sisa Waktu</p>
            <p id="timer" class="text-2xl font-mono font-bold text-gray-800">--:--</p>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-6">
    <form id="submit-form" action="{{ route('toefl.guest.submit-section', $session) }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="structure">

        <div class="max-w-3xl mx-auto">

            {{-- Instructions panel (shown first, hidden when questions start) --}}
            <div id="instr-panel">
                <div class="bg-white rounded-xl border border-purple-200 shadow-sm p-6 mb-5">
                    <p class="text-xs text-purple-600 uppercase font-bold tracking-wider mb-1">Section 2 — Structure and Written Expression</p>
                    <p class="text-xs text-gray-500 mb-4">Time: 25 minutes (including the reading of the directions)</p>
                    <p class="text-sm text-gray-700 leading-relaxed mb-4">
                        This section is designed to measure your ability to recognize language that is appropriate for standard written English. There are two types of questions in this section, with special directions for each type.
                    </p>
                    <div class="border-t border-gray-100 pt-4 mb-4">
                        <p class="text-xs text-purple-600 uppercase font-bold tracking-wider mb-2">Structure</p>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <span class="font-semibold underline">Directions:</span> These questions are incomplete sentences. Beneath each sentence you will see four words or phrases, marked (A), (B), (C), and (D). Choose the one word or phrase that best completes the sentence. Then, on your answer sheet, find the number of the question and fill in the space that corresponds to the letter of the answer you have chosen.
                        </p>
                    </div>
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs text-purple-600 uppercase font-bold tracking-wider mb-2">Written Expression</p>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <span class="font-semibold underline">Directions:</span> In these questions, each sentence has four underlined words or phrases. The four underlined parts of the sentence are marked (A), (B), (C), and (D). Identify the one underlined word or phrase that must be changed in order for the sentence to be correct. Then, on your answer sheet, find the number of the question and fill in the space that corresponds to the letter of the answer you have chosen.
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="start-btn"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg text-sm transition-colors">
                        Mulai Soal &rarr;
                    </button>
                </div>
            </div>

            {{-- Questions (hidden until started) --}}
            <div id="questions-panel" class="hidden">

                <div class="mb-4 flex items-center gap-3">
                    <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                        <div id="progress-bar" class="bg-purple-500 h-1.5 rounded-full transition-all" style="width:0%"></div>
                    </div>
                    <span id="q-counter" class="text-xs text-gray-500 shrink-0 font-mono">1 / {{ $questions->count() }}</span>
                </div>

                @foreach ($questions as $index => $question)
                    @php
                        $saved = $answers->get($question->id);
                        $isWE  = $question->question_type === 'written_expression';
                        $text  = e($question->question_text);

                        if ($isWE) {
                            // Replace [A]word → underlined word with (A) label
                            foreach (['A','B','C','D'] as $lbl) {
                                $optKey = 'option_' . strtolower($lbl);
                                $word   = e($question->$optKey);
                                $text   = str_replace(
                                    "[$lbl]$word",
                                    "<span class=\"inline-block border-b-2 border-purple-600 px-0.5 text-gray-900\">$word<sup class=\"text-purple-600 font-bold text-xs ml-0.5\">($lbl)</sup></span>",
                                    $text
                                );
                                // fallback: just remove the marker if word match fails
                                $text = str_replace("[$lbl]", '', $text);
                            }
                        } else {
                            foreach (['A','B','C','D'] as $lbl) {
                                $text = str_replace(
                                    "[$lbl]",
                                    "<span class=\"inline-block border-b-2 border-gray-700 px-1 font-semibold text-gray-900\">[$lbl]</span>",
                                    $text
                                );
                            }
                        }
                    @endphp
                    <div id="q-{{ $index }}"
                        class="structure-question hidden bg-white rounded-xl border border-gray-200 shadow-sm p-5">

                        <p class="text-xs text-gray-400 mb-3 font-semibold">Soal {{ $index + 1 }}</p>

                        @if ($question->image)
                            <img src="{{ asset('storage/' . $question->image->file_path) }}"
                                class="mb-4 max-h-40 rounded" alt="">
                        @endif

                        <p class="text-gray-800 font-medium mb-4 leading-relaxed">{!! $text !!}</p>

                        <div class="space-y-2">
                            @foreach (['A','B','C','D'] as $opt)
                                @php $optKey = 'option_' . strtolower($opt); $checked = $saved && $saved->selected_option === $opt; @endphp
                                <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-purple-50 transition-colors
                                    {{ $checked ? 'border-purple-400 bg-purple-50' : 'border-gray-200' }}">
                                    <input type="radio" name="answer_{{ $question->id }}" value="{{ $opt }}"
                                        class="h-4 w-4 text-purple-600 autosave-radio"
                                        data-question="{{ $question->id }}"
                                        {{ $checked ? 'checked' : '' }}>
                                    <span class="font-semibold text-gray-500 w-5">{{ $opt }}.</span>
                                    <span class="text-gray-700">{{ $question->$optKey }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="mt-4 flex justify-end">
                    <button type="button" id="next-btn"
                        class="inline-flex items-center px-6 py-2.5 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg text-sm transition-colors">
                        Soal Berikutnya &rarr;
                    </button>
                    <button type="button" id="submit-btn" style="display:none"
                        onclick="submitStructure()"
                        class="inline-flex items-center px-8 py-2.5 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg text-sm transition-colors">
                        Submit Structure &rarr;
                    </button>
                </div>

            </div>
        </div>
    </form>
    </div>

{{-- Submit confirmation modal --}}
<div id="submit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
        <h3 class="text-base font-bold text-gray-800 mb-2">Submit Section Structure?</h3>
        <p class="text-sm text-gray-500 mb-6">Jawaban tidak bisa diubah setelah submit. Pastikan semua soal sudah dijawab.</p>
        <div class="flex gap-3 justify-end">
            <button type="button" id="modal-cancel"
                class="px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" id="modal-confirm"
                class="px-6 py-2 rounded-lg bg-purple-500 hover:bg-purple-600 text-white text-sm font-medium transition-colors">
                Ya, Submit
            </button>
        </div>
    </div>
</div>

<div id="exam-config" class="hidden"
    data-started-at="{{ $session->structure_started_at?->timestamp ?? '' }}"
    data-limit-seconds="{{ $timeLimit * 60 }}"
    data-csrf="{{ csrf_token() }}"
    data-answer-url="{{ route('toefl.guest.answer', $session) }}"
    data-total="{{ $questions->count() }}"
></div>
@vite('resources/js/toefl-guest-structure-exam.js')
@include('toefl.partials.exam-security')
@endsection
