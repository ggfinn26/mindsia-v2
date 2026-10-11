@extends('layouts.dashboard')

@section('title', 'Hasil Survey - ' . $survey->survey_name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ activeTab: 'summary' }">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('surveys.index', ['tab' => 'results']) }}" class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-500 hover:text-[#215aac] transition-colors">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Daftar Hasil Survey
            </a>
            <h1 class="font-jakarta text-3xl font-bold tracking-tight text-slate-900">Laporan Survey</h1>
            <p class="mt-2 text-sm text-slate-600">{{ $survey->survey_name }}</p>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @php
            $totalMembers = $survey->memberSurveys->where(fn ($ms) => $ms->isSubmitted())->count();
            $totalEmployees = $survey->employeeSurveys->where(fn ($es) => $es->isSubmitted())->count();
            $totalSubmitted = $totalMembers + $totalEmployees;
            $totalUnsubmitted = $survey->memberSurveys->count() + $survey->employeeSurveys->count() - $totalSubmitted;
        @endphp
        <div class="flex flex-col rounded-xl border border-slate-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Responden</p>
            <p class="mt-2 font-jakarta text-3xl font-black text-slate-900">{{ $totalSubmitted }}</p>
            <p class="mt-2 text-xs font-semibold text-slate-400">Member: {{ $totalMembers }} &bull; Pegawai: {{ $totalEmployees }}</p>
        </div>
        <div class="flex flex-col rounded-xl border border-amber-100 bg-amber-50 p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-amber-600">Belum Mengisi</p>
            <p class="mt-2 font-jakarta text-3xl font-black text-amber-600">{{ $totalUnsubmitted }}</p>
            <p class="mt-2 text-xs font-semibold text-amber-500/70">Orang</p>
        </div>
        <div class="flex flex-col rounded-xl border border-slate-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pertanyaan</p>
            <p class="mt-2 font-jakarta text-3xl font-black text-slate-900">{{ $survey->questions->count() }}</p>
            <p class="mt-2 text-xs font-semibold text-slate-400">Soal</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex space-x-1 rounded-xl bg-slate-100 p-1">
        <button type="button" @click="activeTab = 'summary'" :class="activeTab === 'summary' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200'" class="flex-1 rounded-lg py-2.5 text-sm font-bold transition-all">
            Ringkasan Statistik
        </button>
        <button type="button" @click="activeTab = 'individual'" :class="activeTab === 'individual' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200'" class="flex-1 rounded-lg py-2.5 text-sm font-bold transition-all">
            Per Responden
        </button>
    </div>

    <!-- Tab: Summary per question -->
    <div x-show="activeTab === 'summary'" x-transition class="space-y-6">
        @foreach($survey->questions as $qi => $question)
            <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-5">
                    <div class="flex items-start gap-4">
                        <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-[#215aac]/10 text-sm font-black text-[#215aac]">{{ $qi + 1 }}</span>
                        <div class="pt-1">
                            <p class="font-jakarta text-base font-bold text-slate-900">{{ $question->question_text }}</p>
                            <span class="mt-2 inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                @if($question->question_type === 'single_choice') Pilihan Tunggal
                                @elseif($question->question_type === 'multiple_choice') Pilihan Ganda
                                @elseif($question->question_type === 'scale') Skala ({{ $question->scale_min }}–{{ $question->scale_max }})
                                @else Teks Bebas
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6 sm:pl-[72px]">
                    @php
                        $memberAnswers = $survey->memberSurveys->filter(fn ($ms) => $ms->isSubmitted())->flatMap(fn ($ms) => $ms->answers->where('survey_question_id', $question->id));
                        $employeeAnswers = $survey->employeeSurveys->filter(fn ($es) => $es->isSubmitted())->flatMap(fn ($es) => $es->answers->where('survey_question_id', $question->id));
                        $allAnswers = $memberAnswers->concat($employeeAnswers);
                        $totalResponses = $allAnswers->count();
                    @endphp

                    @if($totalResponses === 0)
                        <p class="text-sm font-medium italic text-slate-400">Belum ada jawaban.</p>
                    @elseif($question->question_type === 'text')
                        <div class="space-y-3">
                            @foreach($allAnswers as $answer)
                                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4 text-sm text-slate-700">
                                    {{ $answer->answer_text }}
                                </div>
                            @endforeach
                        </div>
                    @elseif($question->question_type === 'scale')
                        @php
                            $values = $allAnswers->pluck('answer_value')->filter();
                            $avg = $values->count() > 0 ? round($values->avg(), 1) : '-';
                        @endphp
                        <div>
                            <div class="mb-6 flex items-center gap-4">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Rata-rata</p>
                                    <p class="font-jakarta text-3xl font-black text-[#215aac]">{{ $avg }}<span class="text-lg text-slate-400">/{{ $question->scale_max }}</span></p>
                                </div>
                                <div class="h-10 w-px bg-slate-200"></div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Respons</p>
                                    <p class="font-jakarta text-3xl font-black text-slate-900">{{ $totalResponses }}</p>
                                </div>
                            </div>
                            <div class="flex items-end gap-2 h-32 pt-4 border-b border-slate-200 relative">
                                @for($v = $question->scale_min; $v <= $question->scale_max; $v++)
                                    @php 
                                        $count = $values->filter(fn ($val) => $val === $v)->count(); 
                                        $heightPct = $totalResponses > 0 ? ($count / $totalResponses) * 100 : 0;
                                    @endphp
                                    <div class="flex-1 flex flex-col items-center justify-end h-full relative group">
                                        <div class="absolute -top-8 bg-slate-800 text-white text-xs font-bold rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">{{ $count }}</div>
                                        <div class="w-full max-w-[40px] rounded-t-md transition-all {{ $heightPct > 0 ? 'bg-[#215aac]' : 'bg-slate-100' }}" style="height: {{ max(4, $heightPct) }}%"></div>
                                        <span class="mt-2 text-xs font-bold text-slate-500">{{ $v }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @else
                        {{-- single_choice / multiple_choice --}}
                        <div class="space-y-5">
                            @foreach($question->choices as $choice)
                                @php
                                    $choiceCount = $allAnswers->filter(fn ($a) => $a->selectedChoices->contains('id', $choice->id))->count();
                                    $pct = $totalResponses > 0 ? round(($choiceCount / $totalResponses) * 100) : 0;
                                @endphp
                                <div>
                                    <div class="mb-2 flex items-center justify-between text-sm">
                                        <span class="font-bold text-slate-700">{{ $choice->choice_text }}</span>
                                        <span class="font-semibold text-slate-500">{{ $choiceCount }} <span class="font-normal text-slate-400">({{ $pct }}%)</span></span>
                                    </div>
                                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full bg-[#215aac] transition-all" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider pt-2">{{ $totalResponses }} Respons</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
    </div>

    <!-- Tab: Individual responses -->
    <div x-show="activeTab === 'individual'" x-transition class="space-y-4">
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
            <div class="rounded-xl border border-slate-100 bg-white py-16 text-center shadow-sm">
                <span class="material-symbols-outlined text-4xl text-slate-300">person_off</span>
                <p class="mt-4 text-sm font-medium text-slate-500">Belum ada responden yang mengisi survey ini.</p>
            </div>
        @else
            @foreach($respondents as $ri => $respondent)
                <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-slate-200 text-sm font-black text-slate-600">{{ $ri + 1 }}</span>
                            <div>
                                <p class="font-jakarta text-base font-bold text-slate-900">{{ $respondent->name }}</p>
                                <span class="mt-0.5 inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $respondent->type === 'Member' ? 'bg-[#215aac]/10 text-[#215aac]' : 'bg-emerald-100 text-emerald-800' }}">{{ $respondent->type }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-5 sm:pl-[72px] space-y-6">
                        @foreach($survey->questions as $question)
                            @php
                                $answer = $respondent->answers->firstWhere('survey_question_id', $question->id);
                            @endphp
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">{{ $question->question_text }}</p>
                                @if($answer)
                                    @if($question->question_type === 'text')
                                        <p class="text-sm font-medium text-slate-800 bg-slate-50 p-3 rounded-lg border border-slate-100">{{ $answer->answer_text }}</p>
                                    @elseif($question->question_type === 'scale')
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#215aac] text-sm font-black text-white shadow-sm">{{ $answer->answer_value }}</span>
                                    @else
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($answer->selectedChoices as $choice)
                                                <span class="inline-flex items-center rounded-md border border-slate-200 bg-white px-3 py-1.5 text-sm font-bold text-slate-700 shadow-sm">
                                                    {{ $choice->choice_text }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <p class="text-sm font-medium italic text-slate-400">Tidak dijawab</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
