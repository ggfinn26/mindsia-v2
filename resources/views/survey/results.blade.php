@extends('layouts.dashboard')

@section('title', 'Hasil Survey - ' . $survey->survey_name)
@section('header_title', 'Hasil Survey')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6" x-data="{ activeTab: 'summary' }">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('surveys.index', ['tab' => 'results']) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke Hasil Survey
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Laporan & Analitik</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Laporan Survey</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">{{ $survey->survey_name }}</p>
    </div>

    {{-- Summary Stats --}}
    @php
        $totalMembers = $survey->memberSurveys->where(fn ($ms) => $ms->isSubmitted())->count();
        $totalEmployees = $survey->employeeSurveys->where(fn ($es) => $es->isSubmitted())->count();
        $totalSubmitted = $totalMembers + $totalEmployees;
        $totalAssigned = $survey->memberSurveys->count() + $survey->employeeSurveys->count();
        $totalUnsubmitted = max(0, $totalAssigned - $totalSubmitted);
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total Responden --}}
        <div class="rounded-2xl p-6 shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Responden</p>
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">group</span>
            </div>
            <p class="mt-2 font-jakarta text-3xl font-bold" style="color: var(--dash-text-heading);">{{ $totalSubmitted }}</p>
            <p class="mt-2 text-xs" style="color: var(--dash-text-faint);">
                Member: <span class="font-semibold" style="color: var(--dash-text-body);">{{ $totalMembers }}</span>
                <span class="mx-1">·</span>
                Pegawai: <span class="font-semibold" style="color: var(--dash-text-body);">{{ $totalEmployees }}</span>
            </p>
        </div>

        {{-- Belum Mengisi --}}
        <div class="rounded-2xl p-6 shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-600">Belum Mengisi</p>
                <span class="material-symbols-outlined text-[20px] text-amber-500">pending_actions</span>
            </div>
            <p class="mt-2 font-jakarta text-3xl font-bold text-amber-600">{{ $totalUnsubmitted }}</p>
            <p class="mt-2 text-xs" style="color: var(--dash-text-faint);">
                Dari total <span class="font-semibold" style="color: var(--dash-text-body);">{{ $totalAssigned }}</span> yang ditugaskan
            </p>
        </div>

        {{-- Total Pertanyaan --}}
        <div class="rounded-2xl p-6 shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Pertanyaan</p>
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-text-muted);">quiz</span>
            </div>
            <p class="mt-2 font-jakarta text-3xl font-bold" style="color: var(--dash-text-heading);">{{ $survey->questions->count() }}</p>
            <p class="mt-2 text-xs" style="color: var(--dash-text-faint);">Butir soal kuesioner</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex border-b" style="border-color: var(--dash-card-border);">
        <button type="button" @click="activeTab = 'summary'"
                :class="activeTab === 'summary' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80'"
                :style="activeTab === 'summary' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);'"
                class="px-5 py-3 text-sm transition-colors">
            <span class="inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">bar_chart</span>
                Ringkasan Statistik
            </span>
        </button>
        <button type="button" @click="activeTab = 'individual'"
                :class="activeTab === 'individual' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80'"
                :style="activeTab === 'individual' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);'"
                class="px-5 py-3 text-sm transition-colors">
            <span class="inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">person</span>
                Per Responden ({{ $totalSubmitted }})
            </span>
        </button>
    </div>

    {{-- Tab: Summary per question --}}
    <div x-show="activeTab === 'summary'" x-transition class="space-y-6">
        @forelse($survey->questions as $qi => $question)
            <div class="rounded-2xl overflow-hidden shadow-sm"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                {{-- Question Header --}}
                <div class="px-6 py-4"
                     style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <div class="flex items-start gap-3.5">
                        <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold text-white mt-0.5"
                              style="background-color: var(--dash-primary);">{{ $qi + 1 }}</span>
                        <div class="flex-1">
                            <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                                {{ $question->question_text }}
                            </h3>
                            <span class="mt-1.5 inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full"
                                  style="background-color: var(--dash-card-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">
                                @if($question->question_type === 'single_choice')
                                    Pilihan Tunggal
                                @elseif($question->question_type === 'multiple_choice')
                                    Pilihan Ganda
                                @elseif($question->question_type === 'scale')
                                    Skala ({{ $question->scale_min }} - {{ $question->scale_max }})
                                @else
                                    Teks Bebas
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Question Answers Breakdown --}}
                <div class="p-6">
                    @php
                        $memberAnswers = $survey->memberSurveys->filter(fn ($ms) => $ms->isSubmitted())->flatMap(fn ($ms) => $ms->answers->where('survey_question_id', $question->id));
                        $employeeAnswers = $survey->employeeSurveys->filter(fn ($es) => $es->isSubmitted())->flatMap(fn ($es) => $es->answers->where('survey_question_id', $question->id));
                        $allAnswers = $memberAnswers->concat($employeeAnswers);
                        $totalResponses = $allAnswers->count();
                    @endphp

                    @if($totalResponses === 0)
                        <p class="text-sm italic" style="color: var(--dash-text-faint);">Belum ada jawaban dari responden.</p>
                    @elseif($question->question_type === 'text')
                        <div class="space-y-3">
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                                {{ $totalResponses }} Tanggapan Masuk
                            </p>
                            @foreach($allAnswers as $answer)
                                <div class="p-3.5 rounded-xl text-sm leading-relaxed"
                                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                    {{ $answer->answer_text }}
                                </div>
                            @endforeach
                        </div>
                    @elseif($question->question_type === 'scale')
                        @php
                            $values = $allAnswers->pluck('answer_value')->filter();
                            $avg = $values->count() > 0 ? round($values->avg(), 1) : '-';
                        @endphp
                        <div class="space-y-6">
                            <div class="flex items-center gap-6">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Rata-rata</p>
                                    <p class="font-jakarta text-3xl font-bold mt-1" style="color: var(--dash-primary);">
                                        {{ $avg }}<span class="text-sm font-normal" style="color: var(--dash-text-faint);"> / {{ $question->scale_max }}</span>
                                    </p>
                                </div>
                                <div class="h-10 w-px" style="background-color: var(--dash-card-border);"></div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Respons</p>
                                    <p class="font-jakarta text-3xl font-bold mt-1" style="color: var(--dash-text-heading);">{{ $totalResponses }}</p>
                                </div>
                            </div>

                            {{-- Scale Bar Distribution --}}
                            <div class="space-y-2">
                                <div class="flex items-end gap-2 h-36 pt-6 pb-2 border-b relative" style="border-color: var(--dash-card-border);">
                                    @for($v = $question->scale_min; $v <= $question->scale_max; $v++)
                                        @php
                                            $count = $values->filter(fn ($val) => (int)$val === (int)$v)->count();
                                            $heightPct = $totalResponses > 0 ? ($count / $totalResponses) * 100 : 0;
                                        @endphp
                                        <div class="flex-1 flex flex-col items-center justify-end h-full relative group">
                                            <span class="text-xs font-bold mb-1" style="color: var(--dash-text-muted);">{{ $count }}</span>
                                            <div class="w-full max-w-[40px] rounded-t-md transition-all"
                                                 style="height: {{ max(4, $heightPct) }}%; background-color: {{ $heightPct > 0 ? 'var(--dash-primary)' : 'var(--dash-nav-hover-bg)' }};"></div>
                                            <span class="mt-2 text-xs font-bold" style="color: var(--dash-text-body);">{{ $v }}</span>
                                        </div>
                                    @endfor
                                </div>
                                @if($question->scale_min_label || $question->scale_max_label)
                                    <div class="flex justify-between text-xs" style="color: var(--dash-text-muted);">
                                        <span>{{ $question->scale_min_label ? $question->scale_min . ': ' . $question->scale_min_label : '' }}</span>
                                        <span>{{ $question->scale_max_label ? $question->scale_max . ': ' . $question->scale_max_label : '' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- single_choice / multiple_choice --}}
                        <div class="space-y-4">
                            @foreach($question->choices as $choice)
                                @php
                                    $choiceCount = $allAnswers->filter(fn ($a) => $a->selectedChoices->contains('id', $choice->id))->count();
                                    $pct = $totalResponses > 0 ? round(($choiceCount / $totalResponses) * 100) : 0;
                                @endphp
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-medium" style="color: var(--dash-text-body);">{{ $choice->choice_text }}</span>
                                        <span class="text-xs font-semibold" style="color: var(--dash-text-muted);">
                                            {{ $choiceCount }} suara <span class="font-normal" style="color: var(--dash-text-faint);">({{ $pct }}%)</span>
                                        </span>
                                    </div>
                                    <div class="h-2.5 w-full rounded-full overflow-hidden"
                                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                        <div class="h-full rounded-full transition-all"
                                             style="width: {{ $pct }}%; background-color: var(--dash-primary);"></div>
                                    </div>
                                </div>
                            @endforeach
                            <p class="text-xs font-semibold pt-1" style="color: var(--dash-text-faint);">
                                Total {{ $totalResponses }} respons masuk
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-2xl px-5 py-12 text-center"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">quiz</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada pertanyaan pada survey ini.</p>
            </div>
        @endforelse
    </div>

    {{-- Tab: Individual responses --}}
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
            <div class="rounded-2xl px-5 py-16 text-center shadow-sm"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">person_off</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada responden yang mengisi survey ini.</p>
            </div>
        @else
            @foreach($respondents as $ri => $respondent)
                <div class="rounded-2xl overflow-hidden shadow-sm"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="px-6 py-4 flex items-center justify-between"
                         style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                  style="background-color: var(--dash-card-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);">
                                {{ $ri + 1 }}
                            </span>
                            <span class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">
                                {{ $respondent->name }}
                            </span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $respondent->type === 'Member' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $respondent->type }}
                        </span>
                    </div>

                    <div class="p-6 space-y-4">
                        @foreach($survey->questions as $qi => $question)
                            @php
                                $answer = $respondent->answers->firstWhere('survey_question_id', $question->id);
                            @endphp
                            <div class="space-y-1.5 pb-3 border-b last:border-b-0 last:pb-0" style="border-color: var(--dash-card-border);">
                                <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                                    {{ $qi + 1 }}. {{ $question->question_text }}
                                </p>
                                @if($answer)
                                    @if($question->question_type === 'text')
                                        <p class="text-sm p-3 rounded-lg leading-relaxed"
                                           style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                            {{ $answer->answer_text }}
                                        </p>
                                    @elseif($question->question_type === 'scale')
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-xs font-bold text-white"
                                                  style="background-color: var(--dash-primary);">
                                                {{ $answer->answer_value }}
                                            </span>
                                            <span class="text-xs" style="color: var(--dash-text-muted);">/ {{ $question->scale_max }}</span>
                                        </div>
                                    @else
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($answer->selectedChoices as $choice)
                                                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold"
                                                      style="border: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body);">
                                                    {{ $choice->choice_text }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <p class="text-xs italic" style="color: var(--dash-text-faint);">Tidak dijawab</p>
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
