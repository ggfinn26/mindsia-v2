@extends('layouts.dashboard')

@section('title', 'Isi Survey - MINDSIA')
@section('header_title', 'Survey')

@section('content')
@php
    $isSubmitted = $employeeSurvey->isSubmitted();
    if ($isSubmitted) {
        $employeeSurvey->load('answers.selectedChoices');
        $answersMap = $employeeSurvey->answers->keyBy('survey_question_id');
    }
    $survey = $employeeSurvey->survey;
@endphp
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('employee.surveys.index') }}"
               class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider transition-opacity hover:opacity-75"
               style="color: var(--dash-text-muted);">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Kembali ke Daftar
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">{{ $survey->survey_name }}</h1>
            @if($survey->survey_description)
                <p class="mt-2 text-sm" style="color: var(--dash-text-body);">
                    {{ $survey->survey_description }}
                </p>
            @endif
        </div>
        <div class="flex-shrink-0">
            @if($isSubmitted)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-bold uppercase tracking-wide"
                      style="background-color: #f0fdf4; color: #059669;">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Sudah Diisi
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-bold uppercase tracking-wide"
                      style="background-color: #fef3c7; color: #d97706;">
                    <span class="material-symbols-outlined text-[18px]">pending</span>
                    Belum Diisi
                </span>
            @endif
        </div>
    </div>

    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Survey form card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('employee.surveys.submit', $employeeSurvey) }}" method="POST">
            @csrf
            <div class="p-6 sm:p-8 space-y-8">
                @foreach($survey->questions as $index => $question)
                    @php
                        $qIndex = $loop->iteration;
                        $answer = $isSubmitted ? ($answersMap[$question->id] ?? null) : null;
                    @endphp
                    <div class="space-y-3">
                        <input type="hidden" name="answers[{{ $index }}][question_id]" value="{{ $question->id }}">

                        <p class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                            <span style="color: var(--dash-primary);">{{ $qIndex }}.</span>
                            {{ $question->question_text }}
                        </p>

                        <div class="pl-5">

                            {{-- Text --}}
                            @if($question->question_type === 'text')
                                @if($isSubmitted)
                                    <p class="text-sm font-medium px-3 py-2.5 rounded-xl"
                                       style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                        {{ $answer?->answer_text ?? '-' }}
                                    </p>
                                @else
                                    <textarea name="answers[{{ $index }}][answer_text]" rows="3" required
                                              placeholder="Tuliskan jawaban Anda..."
                                              class="block w-full rounded-xl text-sm px-3 py-2.5 focus:outline-none focus:ring-2"
                                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); --tw-ring-color: var(--dash-primary);"></textarea>
                                @endif

                            {{-- Scale --}}
                            @elseif($question->question_type === 'scale')
                                @if($isSubmitted)
                                    <div class="inline-flex items-center gap-3 px-4 py-2.5 rounded-xl"
                                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                        @if($question->scale_min_label)
                                            <span class="text-xs font-semibold uppercase tracking-wide"
                                                  style="color: var(--dash-text-muted);">{{ $question->scale_min_label }}</span>
                                        @endif
                                        <span class="text-lg font-black" style="color: var(--dash-primary);">
                                            {{ $answer?->answer_value ?? '-' }}
                                        </span>
                                        @if($question->scale_max_label)
                                            <span class="text-xs font-semibold uppercase tracking-wide"
                                                  style="color: var(--dash-text-muted);">{{ $question->scale_max_label }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex items-center gap-4" x-data="{ selected: null }">
                                        @if($question->scale_min_label)
                                            <span class="text-xs font-semibold uppercase tracking-wide"
                                                  style="color: var(--dash-text-muted);">{{ $question->scale_min_label }}</span>
                                        @endif
                                        <div class="flex items-center gap-2">
                                            @for($i = $question->scale_min; $i <= $question->scale_max; $i++)
                                                <label class="relative flex cursor-pointer items-center justify-center w-10 h-10 rounded-full border transition-colors"
                                                       :style="selected === {{ $i }}
                                                           ? 'background-color: var(--dash-primary); border-color: var(--dash-primary); color: #fff;'
                                                           : 'background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);'"
                                                       onmouseover="if({{ "document.querySelector('[data-scale-selected-{{ $index }}]')?.value" }} != '{{ $i }}') this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                       onmouseout="if({{ "document.querySelector('[data-scale-selected-{{ $index }}]')?.value" }} != '{{ $i }}') this.style.backgroundColor='var(--dash-input-bg)'">
                                                    <input type="radio"
                                                           name="answers[{{ $index }}][answer_value]"
                                                           value="{{ $i }}"
                                                           required
                                                           class="sr-only"
                                                           @change="selected = {{ $i }}">
                                                    <span class="text-sm font-bold pointer-events-none">{{ $i }}</span>
                                                </label>
                                            @endfor
                                        </div>
                                        @if($question->scale_max_label)
                                            <span class="text-xs font-semibold uppercase tracking-wide"
                                                  style="color: var(--dash-text-muted);">{{ $question->scale_max_label }}</span>
                                        @endif
                                    </div>
                                @endif

                            {{-- Single / Multiple choice --}}
                            @elseif($question->question_type === 'single_choice' || $question->question_type === 'multiple_choice')
                                @php
                                    $inputType = $question->question_type === 'single_choice' ? 'radio' : 'checkbox';
                                    $selectedChoiceIds = $isSubmitted && $answer ? $answer->selectedChoices->pluck('id')->toArray() : [];
                                @endphp
                                <div class="space-y-2">
                                    @foreach($question->choices as $choice)
                                        <label class="flex items-start gap-3 p-3 rounded-xl transition-colors {{ $isSubmitted ? '' : 'cursor-pointer' }}"
                                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);"
                                               @if(!$isSubmitted)
                                               onmouseover="this.style.borderColor='var(--dash-primary)'"
                                               onmouseout="this.style.borderColor='var(--dash-card-border)'"
                                               @endif>
                                            <input type="{{ $inputType }}"
                                                   name="answers[{{ $index }}][choice_ids][]"
                                                   value="{{ $choice->id }}"
                                                   class="{{ $inputType === 'radio' ? 'form-radio rounded-full' : 'form-checkbox rounded' }} mt-0.5"
                                                   style="accent-color: var(--dash-primary);"
                                                   {{ $isSubmitted ? 'disabled' : '' }}
                                                   {{ in_array($choice->id, $selectedChoiceIds) ? 'checked' : '' }}
                                                   {{ !$isSubmitted && $question->question_type === 'single_choice' ? 'required' : '' }}>
                                            <span class="text-sm font-medium" style="color: var(--dash-text-body);">
                                                {{ $choice->choice_text }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>

            @if(!$isSubmitted)
                <div class="px-6 py-4 sm:px-8 flex justify-end"
                     style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-8 py-2.5 rounded-xl text-sm font-bold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Submit Jawaban
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
