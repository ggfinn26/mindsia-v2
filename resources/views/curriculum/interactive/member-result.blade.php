@extends('layouts.dashboard')

@section('title', 'Hasil Latihan — ' . $item->item_name)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('member.curriculum.index') }}" class="text-sm text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">← Kurikulum</a>
        <h1 class="text-xl font-bold text-[color:var(--dash-text-heading)] font-jakarta mt-1">Hasil Latihan — {{ $item->item_name }}</h1>
    </div>

    {{-- Score card --}}
    <div class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-2xl p-6 mb-6 text-center shadow-sm">
        <p class="text-sm text-[color:var(--dash-text-muted)] mb-1">Skor Kamu</p>
        <p class="text-5xl font-bold {{ $attempt->score >= 70 ? 'text-green-600' : ($attempt->score >= 50 ? 'text-amber-500' : 'text-red-500') }}">
            {{ $attempt->score }}<span class="text-2xl text-[color:var(--dash-text-faint)]">%</span>
        </p>
        <p class="text-sm text-[color:var(--dash-text-faint)] mt-2">Selesai {{ $attempt->completed_at->diffForHumans() }}</p>
        <div class="mt-4 flex gap-2 justify-center">
            <a href="{{ route('member.curriculum.interactive.show', [$classroom, $item]) }}"
                class="px-4 py-2 font-medium rounded-lg transition-colors bg-[#5586DB] hover:bg-[#4370BC] text-white text-sm">Ulangi Latihan</a>
            <a href="{{ route('member.curriculum.interactive.history', [$classroom, $item]) }}"
                class="px-4 py-2 font-medium rounded-lg transition-colors bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] text-[color:var(--dash-text-body)] text-sm">Riwayat</a>
        </div>
    </div>

    {{-- Answer review --}}
    @php $answerMap = $attempt->answers->keyBy('question_id'); @endphp

    @foreach($item->interactiveBlocks as $bi => $block)
        @if($block->questions->isNotEmpty())
            <div class="mb-6">
                <h2 class="text-sm font-semibold text-[color:var(--dash-text-body)] uppercase tracking-wide mb-3">Blok {{ $bi + 1 }}</h2>
                @foreach($block->questions as $qi => $question)
                    @php $ans = $answerMap->get($question->id); @endphp
                    <div class="bg-[var(--dash-card-bg)] border rounded-2xl p-5 mb-3
                        {{ $ans?->is_correct ? 'border-green-300 bg-green-50' : 'border-red-200 bg-red-50' }}">
                        <div class="flex items-start justify-between mb-2">
                            <p class="text-sm font-medium text-[color:var(--dash-text-heading)] font-jakarta">{{ $qi + 1 }}. {{ $question->question_text }}</p>
                            @if($ans?->is_correct)
                                <span class="ml-2 text-green-600 text-xs font-semibold shrink-0">✓ Benar</span>
                            @else
                                <span class="ml-2 text-red-500 text-xs font-semibold shrink-0">✗ Salah</span>
                            @endif
                        </div>
                        <div class="space-y-1">
                            @foreach(['A','B','C','D'] as $opt)
                                @php $val = $question->{'option_'.strtolower($opt)}; @endphp
                                @if($val)
                                    <div class="flex items-center gap-2 text-sm py-1 px-2 rounded
                                        {{ $opt === $question->correct_option ? 'bg-green-100 font-medium text-green-800' : '' }}
                                        {{ $opt === $ans?->selected_option && $opt !== $question->correct_option ? 'bg-red-100 text-red-700 line-through' : '' }}">
                                        <span>{{ $opt }}.</span>
                                        <span>{{ $val }}</span>
                                        @if($opt === $question->correct_option)
                                            <span class="ml-1 text-green-600 text-xs">(Jawaban benar)</span>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @if($question->explanation)
                            <p class="mt-3 text-xs text-[color:var(--dash-text-body)] bg-[var(--dash-nav-hover-bg)] rounded p-2">
                                <span class="font-semibold">Penjelasan:</span> {{ $question->explanation }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
</div>
@endsection
