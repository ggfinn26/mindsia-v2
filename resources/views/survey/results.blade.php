@extends('layouts.dashboard')

@section('title', 'Hasil Survey - ' . $survey->survey_name)

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ activeTab: 'summary' }">

    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('surveys.index', ['tab' => 'results']) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Daftar Survey
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Hasil Survey</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $survey->survey_name }}</p>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white shadow sm:rounded-lg border border-gray-200 px-6 py-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Total Responden</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $survey->memberSurveys->where(fn ($ms) => $ms->isSubmitted())->count() + $survey->employeeSurveys->where(fn ($es) => $es->isSubmitted())->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">Member: {{ $survey->memberSurveys->where(fn ($ms) => $ms->isSubmitted())->count() }} · Pegawai: {{ $survey->employeeSurveys->where(fn ($es) => $es->isSubmitted())->count() }}</p>
        </div>
        <div class="bg-white shadow sm:rounded-lg border border-gray-200 px-6 py-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Belum Mengisi</p>
            <p class="mt-1 text-2xl font-bold text-yellow-600">{{ $survey->memberSurveys->count() + $survey->employeeSurveys->count() - $survey->memberSurveys->where(fn ($ms) => $ms->isSubmitted())->count() - $survey->employeeSurveys->where(fn ($es) => $es->isSubmitted())->count() }}</p>
        </div>
        <div class="bg-white shadow sm:rounded-lg border border-gray-200 px-6 py-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Pertanyaan</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $survey->questions->count() }}</p>
        </div>
    </div>

    <!-- Tabs: Summary per question vs Individual responses -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button type="button" @click="activeTab = 'summary'" :class="activeTab === 'summary' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Ringkasan</button>
            <button type="button" @click="activeTab = 'individual'" :class="activeTab === 'individual' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Per Responden</button>
        </nav>
    </div>

    <!-- Tab: Summary per question -->
    <div x-show="activeTab === 'summary'" x-transition>
        <div class="space-y-4">
            @foreach($survey->questions as $qi => $question)
                <div class="bg-white shadow sm:rounded-lg border border-gray-200">
                    <div class="px-6 py-4">
                        <div class="flex items-start gap-3 mb-4">
                            <span class="flex-shrink-0 flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">{{ $qi + 1 }}</span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $question->question_text }}</p>
                                <span class="inline-flex mt-1 px-2 text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    @if($question->question_type === 'single_choice') Pilihan Tunggal
                                    @elseif($question->question_type === 'multiple_choice') Pilihan Ganda
                                    @elseif($question->question_type === 'scale') Skala ({{ $question->scale_min }}–{{ $question->scale_max }})
                                    @else Teks Bebas
                                    @endif
                                </span>
                            </div>
                        </div>

                        @php
                            $allAnswers = $question->answers ?? collect();
                            // Collect answers from both member and employee surveys
                            $memberAnswers = $survey->memberSurveys->filter(fn ($ms) => $ms->isSubmitted())->flatMap(fn ($ms) => $ms->answers->where('survey_question_id', $question->id));
                            $employeeAnswers = $survey->employeeSurveys->filter(fn ($es) => $es->isSubmitted())->flatMap(fn ($es) => $es->answers->where('survey_question_id', $question->id));
                            $allAnswers = $memberAnswers->concat($employeeAnswers);
                            $totalResponses = $allAnswers->count();
                        @endphp

                        @if($totalResponses === 0)
                            <p class="text-sm text-gray-500 ml-10">Belum ada jawaban.</p>
                        @elseif($question->question_type === 'text')
                            <div class="ml-10 space-y-2">
                                @foreach($allAnswers as $answer)
                                    <div class="bg-gray-50 rounded-md px-3 py-2 text-sm text-gray-700">
                                        {{ $answer->answer_text }}
                                    </div>
                                @endforeach
                            </div>
                        @elseif($question->question_type === 'scale')
                            @php
                                $values = $allAnswers->pluck('answer_value')->filter();
                                $avg = $values->count() > 0 ? round($values->avg(), 1) : '-';
                            @endphp
                            <div class="ml-10">
                                <p class="text-sm text-gray-600">Rata-rata: <span class="font-semibold text-gray-900">{{ $avg }}</span> dari {{ $question->scale_max }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $totalResponses }} respons</p>
                                <div class="mt-2 flex items-end gap-1 h-20">
                                    @for($v = $question->scale_min; $v <= $question->scale_max; $v++)
                                        @php $count = $values->filter(fn ($val) => $val === $v)->count(); @endphp
                                        <div class="flex-1 flex flex-col items-center">
                                            <div class="w-full bg-indigo-{{ min(100, max(100, 200 + $v * 50)) }} rounded-t" style="height: {{ $totalResponses > 0 ? max(4, ($count / $totalResponses) * 80) : 4 }}px"></div>
                                            <span class="text-xs text-gray-500 mt-1">{{ $v }}</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @else
                            {{-- single_choice / multiple_choice --}}
                            <div class="ml-10 space-y-2">
                                @foreach($question->choices as $choice)
                                    @php
                                        $choiceCount = $allAnswers->filter(fn ($a) => $a->selectedChoices->contains('id', $choice->id))->count();
                                        $pct = $totalResponses > 0 ? round(($choiceCount / $totalResponses) * 100) : 0;
                                    @endphp
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-700">{{ $choice->choice_text }}</span>
                                            <span class="text-gray-500">{{ $choiceCount }} ({{ $pct }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                                <p class="text-xs text-gray-400 mt-2">{{ $totalResponses }} respons</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tab: Individual responses -->
    <div x-show="activeTab === 'individual'" x-transition>
        @php
            $respondents = collect();
            foreach ($survey->memberSurveys as $ms) {
                if ($ms->isSubmitted()) {
                    $respondents->push((object) [
                        'name' => $ms->member?->full_name ?? '-',
                        'type' => 'Member',
                        'answers' => $ms->answers,
                    ]);
                }
            }
            foreach ($survey->employeeSurveys as $es) {
                if ($es->isSubmitted()) {
                    $respondents->push((object) [
                        'name' => $es->employee?->full_name ?? '-',
                        'type' => 'Pegawai',
                        'answers' => $es->answers,
                    ]);
                }
            }
        @endphp

        @if($respondents->isEmpty())
            <p class="text-sm text-gray-500 text-center py-8">Belum ada responden yang mengisi survey.</p>
        @else
            <div class="space-y-4">
                @foreach($respondents as $ri => $respondent)
                    <div class="bg-white shadow sm:rounded-lg border border-gray-200">
                        <div class="px-6 py-4">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex-shrink-0 flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">{{ $ri + 1 }}</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $respondent->name }}</p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $respondent->type === 'Member' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">{{ $respondent->type }}</span>
                                </div>
                            </div>

                            <div class="ml-10 space-y-3">
                                @foreach($survey->questions as $question)
                                    @php
                                        $answer = $respondent->answers->firstWhere('survey_question_id', $question->id);
                                    @endphp
                                    <div>
                                        <p class="text-xs font-medium text-gray-500">{{ $question->question_text }}</p>
                                        @if($answer)
                                            @if($question->question_type === 'text')
                                                <p class="text-sm text-gray-800 mt-0.5">{{ $answer->answer_text }}</p>
                                            @elseif($question->question_type === 'scale')
                                                <p class="text-sm text-gray-800 mt-0.5">{{ $answer->answer_value }}</p>
                                            @else
                                                <p class="text-sm text-gray-800 mt-0.5">{{ $answer->selectedChoices->pluck('choice_text')->join(', ') }}</p>
                                            @endif
                                        @else
                                            <p class="text-sm text-gray-400 mt-0.5">Tidak dijawab</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
