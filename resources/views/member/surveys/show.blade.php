@extends('layouts.dashboard')

@section('content')
@php
    $isSubmitted = $memberSurvey->isSubmitted();
    if ($isSubmitted) {
        $memberSurvey->load('answers.selectedChoices');
        $answersMap = $memberSurvey->answers->keyBy('survey_question_id');
    }
    $survey = $memberSurvey->survey;
@endphp
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('member.surveys.index') }}" class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-500 hover:text-[#215aac] transition-colors">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Daftar
            </a>
            <h1 class="font-jakarta text-3xl font-bold tracking-tight text-slate-900">{{ $survey->survey_name }}</h1>
            @if($survey->survey_description)
                <p class="mt-2 text-sm text-slate-600">{{ $survey->survey_description }}</p>
            @endif
        </div>
        <div class="flex items-center">
            @if($isSubmitted)
                <span class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-bold uppercase tracking-wide bg-emerald-100 text-emerald-800 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span> Sudah Diisi
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-bold uppercase tracking-wide bg-amber-100 text-amber-800 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">pending</span> Belum Diisi
                </span>
            @endif
        </div>
    </div>

    @if(session('error'))
        <div class="flex items-center gap-3 rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-red-600">error</span>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-800 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px] text-red-600 mt-0.5">error</span>
                <ul class="list-disc pl-5 font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <form action="{{ route('member.surveys.submit', $memberSurvey) }}" method="POST">
            @csrf
            <div class="p-6 sm:p-8 space-y-8">
                @foreach($survey->questions as $index => $question)
                    @php
                        $qIndex = $loop->iteration;
                        $answer = $isSubmitted ? ($answersMap[$question->id] ?? null) : null;
                    @endphp
                    <div class="space-y-3">
                        <input type="hidden" name="answers[{{ $index }}][question_id]" value="{{ $question->id }}">
                        
                        <p class="font-jakarta text-base font-bold text-slate-900">
                            <span class="text-[#215aac]">{{ $qIndex }}.</span> {{ $question->question_text }}
                        </p>

                        <div class="pl-5">
                            @if($question->question_type === 'text')
                                @if($isSubmitted)
                                    <p class="text-sm font-medium text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-200">{{ $answer?->answer_text ?? '-' }}</p>
                                @else
                                    <textarea name="answers[{{ $index }}][answer_text]" rows="3" required class="block w-full rounded-lg border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[#215aac] focus:ring-[#215aac] shadow-sm" placeholder="Tuliskan jawaban Anda..."></textarea>
                                @endif
                            
                            @elseif($question->question_type === 'scale')
                                @if($isSubmitted)
                                    <div class="inline-flex items-center gap-3 rounded-lg bg-slate-50 p-3 border border-slate-200">
                                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $question->scale_min_label }}</span>
                                        <span class="text-lg font-black text-[#215aac]">{{ $answer?->answer_value ?? '-' }}</span>
                                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $question->scale_max_label }}</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-4">
                                        @if($question->scale_min_label)
                                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $question->scale_min_label }}</span>
                                        @endif
                                        
                                        <div class="flex items-center gap-2">
                                            @for($i = $question->scale_min; $i <= $question->scale_max; $i++)
                                                <label class="relative flex cursor-pointer items-center justify-center rounded-full border border-slate-200 w-10 h-10 bg-white hover:bg-slate-50 focus-within:ring-2 focus-within:ring-[#215aac] focus-within:ring-offset-2 transition-colors">
                                                    <input type="radio" name="answers[{{ $index }}][answer_value]" value="{{ $i }}" required class="sr-only">
                                                    <span class="text-sm font-bold text-slate-700 pointer-events-none">{{ $i }}</span>
                                                </label>
                                            @endfor
                                        </div>

                                        @if($question->scale_max_label)
                                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $question->scale_max_label }}</span>
                                        @endif
                                    </div>
                                    <!-- Script to highlight selected scale option -->
                                    <script>
                                        document.addEventListener('DOMContentLoaded', () => {
                                            const inputs = document.querySelectorAll('input[name="answers[{{ $index }}][answer_value]"]');
                                            inputs.forEach(input => {
                                                input.addEventListener('change', (e) => {
                                                    inputs.forEach(i => i.parentElement.classList.remove('bg-[#215aac]', 'text-white', 'border-[#215aac]'));
                                                    inputs.forEach(i => i.nextElementSibling.classList.remove('text-white'));
                                                    if(e.target.checked) {
                                                        e.target.parentElement.classList.add('bg-[#215aac]', 'text-white', 'border-[#215aac]');
                                                        e.target.nextElementSibling.classList.add('text-white');
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                @endif
                            
                            @elseif($question->question_type === 'single_choice' || $question->question_type === 'multiple_choice')
                                @php
                                    $inputType = $question->question_type === 'single_choice' ? 'radio' : 'checkbox';
                                    $inputName = $question->question_type === 'single_choice' ? "answers[{$index}][choice_ids][]" : "answers[{$index}][choice_ids][]";
                                    $selectedChoiceIds = $isSubmitted && $answer ? $answer->selectedChoices->pluck('id')->toArray() : [];
                                @endphp
                                
                                <div class="space-y-2">
                                    @foreach($question->choices as $choice)
                                        <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors {{ $isSubmitted ? '' : 'cursor-pointer' }}">
                                            <input type="{{ $inputType }}" 
                                                   name="{{ $inputName }}" 
                                                   value="{{ $choice->id }}" 
                                                   class="{{ $inputType === 'radio' ? 'form-radio rounded-full' : 'form-checkbox rounded' }} mt-0.5 text-[#215aac] focus:ring-[#215aac] border-slate-300 bg-white"
                                                   {{ $isSubmitted ? 'disabled' : '' }}
                                                   {{ in_array($choice->id, $selectedChoiceIds) ? 'checked' : '' }}
                                                   {{ !$isSubmitted && $question->question_type === 'single_choice' ? 'required' : '' }}>
                                            <span class="text-sm font-medium text-slate-700">{{ $choice->choice_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(!$isSubmitted)
                <div class="bg-slate-50 px-6 py-4 sm:px-8 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[#215aac] px-8 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-all">
                        <span class="material-symbols-outlined text-[18px]">send</span> Submit Jawaban
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
