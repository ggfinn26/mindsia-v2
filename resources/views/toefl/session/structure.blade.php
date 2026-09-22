@extends('layouts.dashboard')

@section('title', 'Structure — TOEFL')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6 flex justify-between items-center border-t-4 border-purple-500">
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-0.5">Section 2 / 3</p>
            <h2 class="text-lg font-bold text-gray-800">Structure & Written Expression</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $questions->count() }} soal &bull; {{ $timeLimit }} menit</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400 mb-0.5">Sisa Waktu</p>
            <p id="timer" class="text-3xl font-mono font-bold text-red-600">--:--</p>
        </div>
    </div>

    <form id="submit-form" action="{{ route('toefl.session.submit-section', $session) }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="structure">

        <div class="space-y-5">
            @foreach ($questions as $index => $question)
                @php $saved = $answers->get($question->id); @endphp
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <p class="text-xs text-gray-400 mb-2">Soal {{ $index + 1 }}</p>

                    @if ($question->image)
                        <img src="{{ asset('storage/' . $question->image->file_path) }}"
                            class="mb-4 max-h-40 rounded" alt="">
                    @endif

                    {{-- Written Expression: render underlined spans from [A][B][C][D] --}}
                    @php
                        $text = e($question->question_text);
                        foreach (['A','B','C','D'] as $lbl) {
                            $text = str_replace(
                                "[$lbl]",
                                "<span class=\"inline-block border-b-2 border-gray-700 px-1 font-semibold text-gray-900 cursor-default\" title=\"Pilihan $lbl\">[$lbl]</span>",
                                $text
                            );
                        }
                    @endphp
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
        </div>

        <div class="mt-6 flex justify-end">
            <button type="button"
                onclick="if(confirm('Submit section Structure? Tidak bisa diubah setelah submit.')) document.getElementById('submit-form').submit()"
                class="btn bg-purple-500 hover:bg-purple-600 text-white px-8 py-2.5">
                Submit Structure &rarr;
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    const startedAt = {{ $session->structure_started_at ? $session->structure_started_at->timestamp : 'null' }};
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
                l.classList.remove('border-purple-400', 'bg-purple-50');
                l.classList.add('border-gray-200');
            });
            this.closest('label').classList.add('border-purple-400', 'bg-purple-50');
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
