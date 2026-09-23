@extends('layouts.exam')

@section('title', 'Listening Comprehension')

@section('content')
<div class="h-screen flex flex-col bg-gray-50" id="listening-root">

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 shadow-sm px-6 py-3 flex items-center justify-between shrink-0">
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Section 1 / 3</p>
            <h2 class="text-base font-bold text-gray-800">Listening Comprehension</h2>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400 mb-0.5">Sisa Waktu</p>
            <p id="timer" class="text-2xl font-mono font-bold text-gray-800">--:--</p>
        </div>
    </div>

    {{-- Build audio playlist: instructions + passages + per-question audios --}}
    @php
        use Illuminate\Support\Facades\Storage;

        $playlist = [];

        // Part A instruction
        if (isset($directionAudios['A']) && $directionAudios['A']->file_path) {
            $playlist[] = [
                'type'  => 'instruction',
                'part'  => 'A',
                'label' => 'Petunjuk Part A',
                'url'   => Storage::url($directionAudios['A']->file_path),
            ];
        }

        $currentPassageId = null;
        $partBStarted = false;
        $partCStarted = false;

        foreach ($questions as $question) {
            $partNum = null;
            if ($question->display_order <= 30) $partNum = 'A';
            elseif ($question->display_order <= 38) $partNum = 'B';
            else $partNum = 'C';

            // Part B instruction before first Part B question
            if ($partNum === 'B' && !$partBStarted) {
                $partBStarted = true;
                if (isset($directionAudios['B']) && $directionAudios['B']->file_path) {
                    $playlist[] = [
                        'type'  => 'instruction',
                        'part'  => 'B',
                        'label' => 'Petunjuk Part B',
                        'url'   => Storage::url($directionAudios['B']->file_path),
                    ];
                }
            }

            // Part C instruction (no dedicated audio, but mark boundary)
            if ($partNum === 'C' && !$partCStarted) {
                $partCStarted = true;
            }

            // Passage audio (Part B/C — one per passage group)
            if ($question->passage_id && $question->passage_id !== $currentPassageId) {
                $currentPassageId = $question->passage_id;
                $passage = $question->passage;
                if ($passage && $passage->audio_media_id && $passage->audio) {
                    $playlist[] = [
                        'type'       => 'passage',
                        'passage_id' => $passage->id,
                        'label'      => $passage->title,
                        'url'        => Storage::url($passage->audio->file_path),
                    ];
                }
            }

            // Per-question audio (Part A — each question has own passage with audio)
            if ($partNum === 'A' && $question->passage_id) {
                $passage = $question->passage;
                if ($passage && $passage->audio_media_id && $passage->audio) {
                    $playlist[] = [
                        'type'        => 'question',
                        'question_id' => $question->id,
                        'label'       => 'Soal ' . $question->display_order,
                        'url'         => Storage::url($passage->audio->file_path),
                    ];
                }
            }
        }
    @endphp

    {{-- Audio player --}}
    <div class="bg-indigo-900 text-white px-6 py-4 shrink-0">
        <div class="max-w-3xl mx-auto flex items-center gap-4">
            <div class="flex-1">
                <p class="text-xs text-indigo-300 mb-0.5">Sedang diputar</p>
                <p id="now-playing" class="text-sm font-semibold truncate">Memuat…</p>
            </div>
            <div class="flex items-center gap-2">
                <div id="audio-spinner" class="w-5 h-5 border-2 border-indigo-300 border-t-white rounded-full animate-spin hidden"></div>
                <span id="audio-status" class="text-xs text-indigo-300"></span>
            </div>
        </div>
        <div class="max-w-3xl mx-auto mt-2">
            <div class="w-full bg-indigo-700 rounded-full h-1.5">
                <div id="audio-progress" class="bg-white h-1.5 rounded-full transition-all" style="width:0%"></div>
            </div>
        </div>
    </div>

    {{-- Questions area --}}
    <div class="flex-1 overflow-y-auto px-4 py-6">
    <form id="submit-form" action="{{ route('toefl.guest.submit-section', $session) }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="listening">

        <div class="max-w-3xl mx-auto space-y-4">
            @foreach ($questions as $index => $question)
                @php $saved = $answers->get($question->id); @endphp
                <div id="q-{{ $question->id }}"
                    class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 transition-all duration-300 opacity-40 pointer-events-none"
                    data-question-id="{{ $question->id }}">
                    <p class="text-xs text-gray-400 mb-3 font-semibold">Soal {{ $question->display_order }}</p>

                    @if ($question->question_text)
                        <p class="text-gray-800 font-medium mb-4 leading-relaxed">{{ $question->question_text }}</p>
                    @endif

                    <div class="space-y-2">
                        @foreach (['A','B','C','D'] as $opt)
                            @php $optKey = 'option_' . strtolower($opt); $checked = $saved && $saved->selected_option === $opt; @endphp
                            <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-indigo-50 transition-colors {{ $checked ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200' }}">
                                <input type="radio" name="answer_{{ $question->id }}" value="{{ $opt }}"
                                    class="h-4 w-4 text-indigo-600 autosave-radio"
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
    data-started-at="{{ $session->listening_started_at?->timestamp ?? '' }}"
    data-limit-seconds="{{ $timeLimit * 60 }}"
    data-csrf="{{ csrf_token() }}"
    data-answer-url="{{ route('toefl.guest.answer', $session) }}"
    data-playlist="{{ e(json_encode($playlist)) }}"
></div>
@vite('resources/js/toefl-guest-listening-exam.js')
@include('toefl.partials.exam-security')
@endsection
