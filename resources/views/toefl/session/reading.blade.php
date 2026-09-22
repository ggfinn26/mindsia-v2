@extends('layouts.dashboard')

@section('title', 'Reading — TOEFL')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-5 flex justify-between items-center border-t-4 border-green-500">
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-0.5">Section 3 / 3</p>
            <h2 class="text-lg font-bold text-gray-800">Reading Comprehension</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $questions->count() }} soal &bull; {{ $timeLimit }} menit</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400 mb-0.5">Sisa Waktu</p>
            <p id="timer" class="text-3xl font-mono font-bold text-red-600">--:--</p>
        </div>
    </div>

    <form id="submit-form" action="{{ route('toefl.session.submit-section', $session) }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="reading">

        @php
            $questionsByPassage = $questions->groupBy('passage_id');
            $passageIds = $questions->pluck('passage_id')->unique()->filter();
        @endphp

        @foreach ($passageIds as $passageId)
            @php
                $passageQuestions = $questionsByPassage->get($passageId, collect());
                $passage = $passageQuestions->first()?->passage;
            @endphp
            @if (!$passage) @continue @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
                {{-- Passage --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 lg:sticky lg:top-4 lg:max-h-[80vh] lg:overflow-y-auto">
                    <p class="text-xs uppercase font-semibold text-green-700 mb-3">{{ $passage->title }}</p>
                    <div class="text-sm text-gray-700 leading-relaxed prose prose-sm max-w-none">
                        @foreach (explode("\n", $passage->body_text ?? '') as $para)
                            @if (trim($para)) <p>{{ $para }}</p> @endif
                        @endforeach
                    </div>
                </div>

                {{-- Questions for this passage --}}
                <div class="space-y-4">
                    @foreach ($passageQuestions->sortBy('display_order') as $index => $question)
                        @php $saved = $answers->get($question->id); @endphp
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                            <p class="text-xs text-gray-400 mb-2">Soal {{ $index + 1 }}</p>
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
        @endforeach

        {{-- Questions without passage --}}
        @if ($questionsByPassage->has(null))
            <div class="space-y-5 mb-8">
                @foreach ($questionsByPassage->get(null)->sortBy('display_order') as $index => $question)
                    @php $saved = $answers->get($question->id); @endphp
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                        <p class="text-xs text-gray-400 mb-2">Soal {{ $index + 1 }}</p>
                        <p class="text-gray-800 font-medium mb-4 leading-relaxed">{{ $question->question_text }}</p>
                        <div class="space-y-2">
                            @foreach (['A','B','C','D'] as $opt)
                                @php $optKey = 'option_' . strtolower($opt); $checked = $saved && $saved->selected_option === $opt; @endphp
                                <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-green-50 transition-colors
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
        @endif

        <div class="flex justify-end">
            <button type="button"
                onclick="if(confirm('Selesaikan ujian? Jawaban akan dikirim dan tidak bisa diubah.')) document.getElementById('submit-form').submit()"
                class="btn bg-green-500 hover:bg-green-600 text-white px-8 py-2.5">
                Selesaikan Ujian &#10003;
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    const startedAt = {{ $session->reading_started_at ? $session->reading_started_at->timestamp : 'null' }};
    const limitSeconds = {{ $timeLimit * 60 }};
    const csrfToken = '{{ csrf_token() }}';
    const answerUrl = '{{ route('toefl.session.answer', $session) }}';
    const submitForm = document.getElementById('submit-form');

    function updateTimer() {
        const elapsed = Math.floor(Date.now() / 1000) - startedAt;
        const remaining = Math.max(0, limitSeconds - elapsed);
        const m = String(Math.floor(remaining / 60)).padStart(2, '0');
        const s = String(remaining % 60).padStart(2, '0');
        const el = document.getElementById('timer');
        el.textContent = m + ':' + s;
        if (remaining <= 300) el.classList.add('text-red-600');
        if (remaining <= 0) submitForm.submit();
    }

    if (startedAt) { updateTimer(); setInterval(updateTimer, 1000); }

    document.querySelectorAll('.autosave-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            this.closest('.space-y-2').querySelectorAll('label').forEach(l => {
                l.classList.remove('border-green-400', 'bg-green-50');
                l.classList.add('border-gray-200');
            });
            this.closest('label').classList.add('border-green-400', 'bg-green-50');
            this.closest('label').classList.remove('border-gray-200');
            fetch(answerUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ question_id: this.dataset.question, selected_option: this.value }),
            });
        });
    });
})();
</script>
@endsection
