@extends('layouts.dashboard')

@section('title', 'Listening — TOEFL')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Header + Timer --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6 flex justify-between items-center border-t-4 border-indigo-500">
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-0.5">Section 1 / 3</p>
            <h2 class="text-lg font-bold text-gray-800">Listening Comprehension</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $questions->count() }} soal &bull; {{ $timeLimit }} menit</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400 mb-0.5">Sisa Waktu</p>
            <p id="timer" class="text-3xl font-mono font-bold text-red-600">--:--</p>
        </div>
    </div>

    <form id="submit-form" action="{{ route('toefl.session.submit-section', $session) }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="listening">

        <div class="space-y-6">
            @php $currentPassageId = null; @endphp
            @foreach ($questions as $index => $question)

                @if ($question->passage_id && $question->passage_id !== $currentPassageId)
                    @php $currentPassageId = $question->passage_id; $passage = $question->passage; @endphp
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                        <p class="text-xs uppercase font-semibold text-amber-700 mb-2">{{ $passage->title }}</p>
                        @if ($passage->audio_media_id && $passage->audio)
                            <audio controls class="w-full mb-3">
                                <source src="{{ asset('storage/' . $passage->audio->file_path) }}" type="audio/mpeg">
                            </audio>
                        @endif
                        @if ($passage->body_text)
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $passage->body_text }}</p>
                        @endif
                    </div>
                @elseif (!$question->passage_id)
                    @php $currentPassageId = null; @endphp
                @endif

                @php $saved = $answers->get($question->id); @endphp
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <p class="text-xs text-gray-400 mb-2">Soal {{ $index + 1 }}</p>

                    @if ($question->image)
                        <img src="{{ asset('storage/' . $question->image->file_path) }}"
                            class="mb-4 max-h-48 rounded" alt="Gambar soal">
                    @endif

                    <p class="text-gray-800 font-medium mb-4 leading-relaxed">{{ $question->question_text }}</p>

                    <div class="space-y-2">
                        @foreach (['A','B','C','D'] as $opt)
                            @php $optKey = 'option_' . strtolower($opt); $checked = $saved && $saved->selected_option === $opt; @endphp
                            <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-indigo-50 transition-colors
                                {{ $checked ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200' }}">
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

        <div class="mt-6 flex justify-end">
            <button type="button"
                onclick="if(confirm('Submit section Listening? Tidak bisa diubah setelah submit.')) document.getElementById('submit-form').submit()"
                class="btn bg-indigo-500 hover:bg-indigo-600 text-white px-8 py-2.5">
                Submit Listening &rarr;
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    const startedAt = {{ $session->listening_started_at ? $session->listening_started_at->timestamp : 'null' }};
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
                l.classList.remove('border-indigo-400', 'bg-indigo-50');
                l.classList.add('border-gray-200');
            });
            this.closest('label').classList.add('border-indigo-400', 'bg-indigo-50');
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
