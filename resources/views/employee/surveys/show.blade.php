@extends('layouts.dashboard')

@section('title', $employeeSurvey->survey->survey_name . ' - MINDSIA')
@section('header_title', $employeeSurvey->isSubmitted() ? 'Jawaban Survey' : 'Isi Survey')

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

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <a href="{{ route('employee.surveys.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Survey Saya
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Personal Dashboard</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">{{ $survey->survey_name }}</h1>
            @if($survey->survey_description)
                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">{{ $survey->survey_description }}</p>
            @endif
        </div>
        <div class="shrink-0">
            @if($isSubmitted)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                      style="background-color: #f0fdf4; color: #059669; border: 1px solid #bbf7d0;">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    Sudah Diisi
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                      style="background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a;">
                    <span class="material-symbols-outlined text-[16px]">pending</span>
                    Belum Diisi
                </span>
            @endif
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Card Form --}}
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
                            <span style="color: var(--dash-primary);">{{ $qIndex }}.</span> {{ $question->question_text }}
                        </p>

                        <div class="pl-5">
                            @if($question->question_type === 'text')
                                @if($isSubmitted)
                                    <div class="p-3.5 rounded-lg text-sm"
                                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                        {{ $answer?->answer_text ?? '-' }}
                                    </div>
                                @else
                                    <textarea name="answers[{{ $index }}][answer_text]" rows="3" required
                                              class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                              onfocus="this.style.borderColor='var(--dash-primary)'"
                                              onblur="this.style.borderColor='var(--dash-input-border)'"
                                              placeholder="Tuliskan jawaban Anda..."></textarea>
                                @endif

                            @elseif($question->question_type === 'scale')
                                @if($isSubmitted)
                                    <div class="inline-flex items-center gap-3 rounded-lg p-3 text-sm"
                                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                        <span class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">{{ $question->scale_min_label }}</span>
                                        <span class="text-xl font-bold font-jakarta" style="color: var(--dash-primary);">{{ $answer?->answer_value ?? '-' }}</span>
                                        <span class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">{{ $question->scale_max_label }}</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-4 flex-wrap" x-data="{ selected: null }">
                                        @if($question->scale_min_label)
                                            <span class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">{{ $question->scale_min_label }}</span>
                                        @endif

                                        <div class="flex items-center gap-2 flex-wrap">
                                            @for($i = $question->scale_min; $i <= $question->scale_max; $i++)
                                                <label class="relative flex cursor-pointer items-center justify-center rounded-lg w-10 h-10 transition-all font-semibold text-sm"
                                                       :style="selected === {{ $i }} ? 'background-color: var(--dash-primary); border: 1px solid var(--dash-primary); color: #fff;' : 'border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);'"
                                                       onmouseover="if (this.querySelector('input').checked !== true) this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                       onmouseout="if (this.querySelector('input').checked !== true) this.style.backgroundColor='var(--dash-card-bg)'">
                                                    <input type="radio" name="answers[{{ $index }}][answer_value]" value="{{ $i }}" required
                                                           class="sr-only" @change="selected = {{ $i }}">
                                                    <span>{{ $i }}</span>
                                                </label>
                                            @endfor
                                        </div>

                                        @if($question->scale_max_label)
                                            <span class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">{{ $question->scale_max_label }}</span>
                                        @endif
                                    </div>
                                @endif

                            @elseif($question->question_type === 'single_choice' || $question->question_type === 'multiple_choice')
                                @php
                                    $inputType = $question->question_type === 'single_choice' ? 'radio' : 'checkbox';
                                    $selectedChoiceIds = $isSubmitted && $answer ? $answer->selectedChoices->pluck('id')->toArray() : [];
                                @endphp

                                <div class="space-y-2">
                                    @foreach($question->choices as $choice)
                                        <label class="flex items-start gap-3 p-3.5 rounded-lg transition-colors {{ $isSubmitted ? '' : 'cursor-pointer' }}"
                                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                               @if(!$isSubmitted)
                                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                                               @endif>
                                            <input type="{{ $inputType }}"
                                                   name="answers[{{ $index }}][choice_ids][]"
                                                   value="{{ $choice->id }}"
                                                   class="mt-0.5"
                                                   {{ $isSubmitted ? 'disabled' : '' }}
                                                   {{ in_array($choice->id, $selectedChoiceIds) ? 'checked' : '' }}
                                                   {{ !$isSubmitted && $question->question_type === 'single_choice' ? 'required' : '' }}>
                                            <span class="text-sm font-medium" style="color: var(--dash-text-body);">{{ $choice->choice_text }}</span>
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
                            class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Kirim Jawaban
                    </button>
                </div>
            @endif
        </form>
    </div>

</div>
@endsection

