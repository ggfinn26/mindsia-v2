@extends('layouts.dashboard')

@section('title', $item->item_name)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('class-curriculum.index', $classroom) }}" class="text-sm text-slate-500 hover:text-slate-700">← Kurikulum</a>
            <h1 class="text-xl font-bold text-slate-900 mt-1">{{ $item->item_name }}</h1>
        </div>
        <a href="{{ route('member.curriculum.interactive.history', [$classroom, $item]) }}"
            class="text-sm text-indigo-600 hover:underline">Riwayat Latihan</a>
    </div>

    @if($item->interactiveBlocks->isEmpty())
        <div class="bg-white border border-slate-200 rounded-lg p-8 text-center text-slate-400">
            Modul ini belum memiliki konten.
        </div>
    @else
        {{-- Block navigation --}}
        <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1">
            @foreach($item->interactiveBlocks as $i => $block)
                <button type="button"
                    class="block-nav-btn flex-shrink-0 px-3 py-1.5 rounded-full text-sm border transition-colors
                        {{ $i === 0 ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-300 hover:border-indigo-400' }}"
                    data-block="{{ $i }}">Blok {{ $i + 1 }}</button>
            @endforeach
        </div>

        <form id="practice-form" action="{{ route('member.curriculum.interactive.submit', [$classroom, $item]) }}" method="POST">
            @csrf

            @foreach($item->interactiveBlocks as $i => $block)
                <div class="block-panel {{ $i > 0 ? 'hidden' : '' }}" data-block="{{ $i }}">
                    {{-- Image --}}
                    @if($block->image)
                        <div class="mb-4 rounded-lg overflow-hidden border border-slate-200">
                            <img src="{{ Storage::url($block->image->file_path) }}"
                                alt="{{ $block->image->original_name }}"
                                class="w-full object-contain max-h-80">
                        </div>
                    @endif

                    {{-- Instruction text --}}
                    @if($block->instruction_text)
                        <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $block->instruction_text }}
                        </div>
                    @endif

                    {{-- Audio --}}
                    @if($block->audio)
                        <div class="mb-4 p-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center gap-3">
                            <span class="material-symbols-outlined text-indigo-500">volume_up</span>
                            <audio controls class="flex-1" controlsList="nodownload">
                                <source src="{{ Storage::url($block->audio->file_path) }}">
                            </audio>
                        </div>
                    @endif

                    {{-- Questions --}}
                    @forelse($block->questions as $qi => $question)
                        <div class="mb-6 bg-white border border-slate-200 rounded-lg p-5">
                            <p class="text-sm text-slate-500 mb-2">Soal {{ $qi + 1 }}</p>
                            @if($question->question_text)
                                <p class="text-slate-800 mb-4 leading-relaxed">{{ $question->question_text }}</p>
                            @endif
                            <div class="space-y-2">
                                @foreach(['A','B','C','D'] as $opt)
                                    @php $val = $question->{'option_'.strtolower($opt)}; @endphp
                                    @if($val)
                                        <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 hover:border-indigo-300 cursor-pointer transition-colors has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                            <input type="radio"
                                                name="answers[{{ $question->id }}]"
                                                value="{{ $opt }}"
                                                class="mt-0.5 accent-indigo-600">
                                            <span class="text-sm text-slate-700">
                                                <span class="font-medium">{{ $opt }}.</span>
                                                {{ $val }}
                                            </span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 italic">Blok ini tidak memiliki soal.</p>
                    @endforelse

                    {{-- Navigation --}}
                    <div class="flex justify-between mt-6">
                        @if($i > 0)
                            <button type="button" class="btn-prev btn bg-white border border-slate-300 text-slate-700 text-sm" data-prev="{{ $i - 1 }}">← Sebelumnya</button>
                        @else
                            <div></div>
                        @endif

                        @if($i < $item->interactiveBlocks->count() - 1)
                            <button type="button" class="btn-next btn bg-indigo-600 text-white text-sm" data-next="{{ $i + 1 }}">Selanjutnya →</button>
                        @else
                            <button type="submit"
                                class="btn bg-green-600 hover:bg-green-700 text-white text-sm"
                                onclick="return confirm('Selesaikan latihan dan kirim semua jawaban?')">
                                Selesai & Kirim Jawaban
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </form>
    @endif
</div>

<script>
(function () {
    const panels = document.querySelectorAll('.block-panel');
    const navBtns = document.querySelectorAll('.block-nav-btn');

    function showBlock(idx) {
        panels.forEach((p, i) => p.classList.toggle('hidden', i !== idx));
        navBtns.forEach((b, i) => {
            b.classList.toggle('bg-indigo-600', i === idx);
            b.classList.toggle('text-white', i === idx);
            b.classList.toggle('border-indigo-600', i === idx);
            b.classList.toggle('bg-white', i !== idx);
            b.classList.toggle('text-slate-600', i !== idx);
            b.classList.toggle('border-slate-300', i !== idx);
        });
    }

    navBtns.forEach(b => b.addEventListener('click', () => showBlock(+b.dataset.block)));
    document.querySelectorAll('.btn-next').forEach(b => b.addEventListener('click', () => showBlock(+b.dataset.next)));
    document.querySelectorAll('.btn-prev').forEach(b => b.addEventListener('click', () => showBlock(+b.dataset.prev)));
}());
</script>
@endsection
