@extends('layouts.dashboard')

@section('title', 'Detail Jawaban — ' . $memberClass->registration->memberData->full_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('curriculum.interactive.results', [$classroom, $item]) }}"
                class="text-sm text-slate-500 hover:text-slate-700">← Semua Hasil</a>
            <h1 class="text-xl font-bold text-slate-900 mt-1">
                {{ $memberClass->registration->memberData->full_name }}
            </h1>
            <p class="text-sm text-slate-500">{{ $item->item_name }}</p>
        </div>
    </div>

    @forelse($attempts as $ai => $attempt)
        <div class="mb-8 bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <span class="text-sm font-semibold text-slate-700">
                    Percobaan {{ $ai + 1 }}
                    <span class="font-normal text-slate-400 ml-2">{{ $attempt->completed_at->format('d M Y, H:i') }}</span>
                </span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                    {{ $attempt->score >= 70 ? 'bg-green-100 text-green-700' : ($attempt->score >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                    {{ $attempt->score }}%
                </span>
            </div>

            @php $answerMap = $attempt->answers->keyBy('question_id'); @endphp

            <div class="p-5 space-y-4">
                @foreach($item->interactiveBlocks as $bi => $block)
                    @foreach($block->questions as $qi => $question)
                        @php $ans = $answerMap->get($question->id); @endphp
                        <div class="p-4 rounded-lg border
                            {{ $ans?->is_correct ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                            <div class="flex justify-between items-start mb-2">
                                <p class="text-sm text-slate-800">
                                    <span class="text-xs text-slate-400 mr-1">B{{ $bi+1 }}.S{{ $qi+1 }}</span>
                                    {{ $question->question_text }}
                                </p>
                                @if($ans?->is_correct)
                                    <span class="text-green-600 text-xs font-semibold ml-2 shrink-0">✓ Benar</span>
                                @elseif($ans?->selected_option)
                                    <span class="text-red-500 text-xs font-semibold ml-2 shrink-0">✗ Salah</span>
                                @else
                                    <span class="text-slate-400 text-xs ml-2 shrink-0">— Tidak dijawab</span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-600 space-y-0.5">
                                <p>Jawaban member: <span class="font-semibold">{{ $ans?->selected_option ?? '—' }}</span></p>
                                <p>Jawaban benar: <span class="font-semibold text-green-700">{{ $question->correct_option }}</span></p>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white border border-slate-200 rounded-lg p-8 text-center text-slate-400">
            Belum ada percobaan.
        </div>
    @endforelse
</div>
@endsection
