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
    <form id="submit-form" action="{{ route('toefl.session.submit-section', $session) }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="structure">

        <div class="max-w-3xl mx-auto">

            {{-- Instructions panel (shown first, hidden when questions start) --}}
            <div id="instr-panel">
                @include('toefl.partials.structure-directions')
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
    data-mark-started-url="{{ route('toefl.session.structure.start', $session) }}"
    data-started-at="{{ $session->structure_started_at?->timestamp ?? '' }}"
    data-limit-seconds="{{ $timeLimit * 60 }}"
    data-csrf="{{ csrf_token() }}"
    data-answer-url="{{ route('toefl.session.answer', $session) }}"
    data-total="{{ $questions->count() }}"
></div>
@vite('resources/js/toefl-structure-exam.js')
@include('toefl.partials.exam-security')
@endsection
