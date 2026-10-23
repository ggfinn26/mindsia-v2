@extends('layouts.dashboard')

@section('title', 'Detail Survey - ' . $survey->survey_name)
@section('header_title', 'Detail Survey')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6"
    x-data="{
        activeTab: 'detail',
        showAssignModal: false,
        assignType: 'member',
        selectedIds: [],
        searchQuery: ''
    }">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Page Header (Pola B3) --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('surveys.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>
                Kembali ke Daftar Survey
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Detail Survey</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">{{ $survey->survey_name }}</h1>
        </div>
        <div class="flex items-center gap-2 shrink-0 flex-wrap">
            @can('survey.result.view')
                <a href="{{ route('surveys.results', $survey) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">analytics</span>
                    Hasil Survey
                </a>
            @endcan
            @can('survey.form.update')
                <a href="{{ route('surveys.edit', $survey) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                    Edit
                </a>
            @endcan
            @can('survey.assignment.create')
                <button type="button"
                        @click="showAssignModal = true; assignType = 'member'; selectedIds = []; searchQuery = ''"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    Assign Survey
                </button>
            @endcan
        </div>
    </div>

    {{-- Survey Info Card --}}
    <div class="rounded-2xl p-6 shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Batas Waktu</p>
                <p class="text-sm font-medium flex items-center gap-1.5" style="color: var(--dash-text-heading);">
                    @if($survey->deadline_at)
                        <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-faint);">calendar_month</span>
                        {{ $survey->deadline_at->format('d M Y H:i') }}
                    @else
                        <span class="italic" style="color: var(--dash-text-faint);">Tanpa Batas</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Status</p>
                <div>
                    @if($survey->isExpired())
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-700">
                            Berakhir
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">
                            Aktif
                        </span>
                    @endif
                </div>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Deskripsi</p>
                <p class="text-sm leading-relaxed" style="color: var(--dash-text-body);">{{ $survey->survey_description ?: '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex border-b" style="border-color: var(--dash-card-border);">
        <button type="button" @click="activeTab = 'detail'"
                :class="activeTab === 'detail' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80'"
                :style="activeTab === 'detail' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);'"
                class="px-5 py-3 text-sm transition-colors">
            <span class="inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">quiz</span>
                Pertanyaan ({{ $survey->questions->count() }})
            </span>
        </button>
        <button type="button" @click="activeTab = 'members'"
                :class="activeTab === 'members' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80'"
                :style="activeTab === 'members' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);'"
                class="px-5 py-3 text-sm transition-colors">
            <span class="inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">groups</span>
                Member ({{ $survey->memberSurveys->count() }})
            </span>
        </button>
        <button type="button" @click="activeTab = 'employees'"
                :class="activeTab === 'employees' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80'"
                :style="activeTab === 'employees' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);'"
                class="px-5 py-3 text-sm transition-colors">
            <span class="inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">badge</span>
                Pegawai ({{ $survey->employeeSurveys->count() }})
            </span>
        </button>
    </div>

    {{-- Tab: Questions --}}
    <div x-show="activeTab === 'detail'" x-transition class="space-y-4">
        @forelse($survey->questions as $qi => $question)
            <div class="rounded-2xl p-6 shadow-sm"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-start gap-4">
                    <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold text-white"
                          style="background-color: var(--dash-primary);">{{ $qi + 1 }}</span>
                    <div class="flex-1 space-y-3">
                        <div class="flex items-start justify-between gap-4">
                            <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                                {{ $question->question_text }}
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full shrink-0"
                                  style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);">
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

                        @if($question->question_type === 'scale')
                            <div class="p-3 rounded-xl flex items-center justify-between text-xs font-medium"
                                 style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                                <span>{{ $question->scale_min_label ?: $question->scale_min }} (Min: {{ $question->scale_min }})</span>
                                <div class="flex-1 mx-4 border-t border-dashed" style="border-color: var(--dash-card-border);"></div>
                                <span>{{ $question->scale_max_label ?: $question->scale_max }} (Max: {{ $question->scale_max }})</span>
                            </div>
                        @endif

                        @if($question->choices->isNotEmpty())
                            <div class="space-y-1.5 pt-1">
                                @foreach($question->choices as $ci => $choice)
                                    <div class="flex items-center gap-2 text-sm" style="color: var(--dash-text-body);">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded text-xs font-bold shrink-0"
                                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                                            {{ chr(65 + $ci) }}
                                        </span>
                                        <span>{{ $choice->choice_text }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
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

    {{-- Tab: Members --}}
    <div x-show="activeTab === 'members'" x-transition>
        <div class="rounded-2xl overflow-hidden shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider w-16"
                                style="color: var(--dash-text-muted);">No</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Nama Member</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                        @forelse($survey->memberSurveys as $msi => $memberSurvey)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3.5 text-xs font-mono" style="color: var(--dash-text-faint);">{{ $msi + 1 }}</td>
                                <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                                    {{ $memberSurvey->member?->full_name ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($memberSurvey->isSubmitted())
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">
                                            Sudah Isi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-amber-100 text-amber-700">
                                            Belum Isi
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center">
                                    <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                          style="color: var(--dash-text-faint);">group_off</span>
                                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada member yang ditugaskan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab: Employees --}}
    <div x-show="activeTab === 'employees'" x-transition>
        <div class="rounded-2xl overflow-hidden shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider w-16"
                                style="color: var(--dash-text-muted);">No</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Nama Pegawai</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                        @forelse($survey->employeeSurveys as $esi => $employeeSurvey)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3.5 text-xs font-mono" style="color: var(--dash-text-faint);">{{ $esi + 1 }}</td>
                                <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                                    {{ $employeeSurvey->employee?->full_name ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($employeeSurvey->isSubmitted())
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">
                                            Sudah Isi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-amber-100 text-amber-700">
                                            Belum Isi
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center">
                                    <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                          style="color: var(--dash-text-faint);">badge</span>
                                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada pegawai yang ditugaskan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Assign Modal (Custom Alpine.js, Card TANPA overflow-hidden) --}}
    <template x-teleport="body">
        <div x-show="showAssignModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="showAssignModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showAssignModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showAssignModal" @click.away="showAssignModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        {{-- Header modal (rounded-t-2xl) --}}
                        <div class="rounded-t-2xl px-6 py-5 flex items-center justify-between"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                     style="background-color: color-mix(in srgb, var(--dash-primary) 15%, transparent);">
                                    <span class="material-symbols-outlined text-[22px]" style="color: var(--dash-primary);">person_add</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Assign Survey</h3>
                                    <p class="text-xs" style="color: var(--dash-text-muted);">Tugaskan survey ke target member atau pegawai.</p>
                                </div>
                            </div>
                            <button type="button" @click="showAssignModal = false"
                                    class="p-1 rounded-lg transition-colors hover:opacity-80"
                                    style="color: var(--dash-text-muted);">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>

                        {{-- Body Modal --}}
                        <div class="p-6 space-y-4">
                            {{-- Assign Type Toggle --}}
                            <div class="flex gap-2">
                                <button type="button" @click="assignType = 'member'; selectedIds = []; searchQuery = ''"
                                        :class="assignType === 'member' ? 'font-bold text-white' : 'font-medium'"
                                        :style="assignType === 'member' ? 'background-color: var(--dash-primary);' : 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);'"
                                        class="px-4 py-2 rounded-lg text-sm transition-colors">
                                    Target Member
                                </button>
                                <button type="button" @click="assignType = 'employee'; selectedIds = []; searchQuery = ''"
                                        :class="assignType === 'employee' ? 'font-bold text-white' : 'font-medium'"
                                        :style="assignType === 'employee' ? 'background-color: var(--dash-primary);' : 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);'"
                                        class="px-4 py-2 rounded-lg text-sm transition-colors">
                                    Target Pegawai
                                </button>
                            </div>

                            {{-- Search Input (Search Bar Pattern) --}}
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                                      style="color: var(--dash-text-muted);">search</span>
                                <input type="text" x-model="searchQuery" placeholder="Cari berdasarkan nama..."
                                       class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            </div>

                            {{-- Select All Bar --}}
                            <div class="flex items-center gap-2.5 pb-3 border-b" style="border-color: var(--dash-card-border);">
                                <input type="checkbox" id="selectAllSurvey"
                                       @change="
                                           const items = assignType === 'member' ? {{ $members->pluck('id')->toJson() }} : {{ $employees->pluck('id')->toJson() }};
                                           selectedIds = $event.target.checked ? items : [];
                                       "
                                       :checked="selectedIds.length > 0 && (assignType === 'member' ? selectedIds.length === {{ $members->count() }} : selectedIds.length === {{ $employees->count() }})"
                                       class="rounded cursor-pointer"
                                       style="accent-color: var(--dash-primary);">
                                <label for="selectAllSurvey" class="text-sm font-semibold cursor-pointer" style="color: var(--dash-text-heading);">
                                    Pilih Semua
                                </label>
                            </div>

                            {{-- Scrollable List --}}
                            <div class="max-h-64 overflow-y-auto space-y-1 pr-1 custom-scrollbar">
                                <template x-if="assignType === 'member'">
                                    <div>
                                        @foreach($members as $member)
                                            <label class="flex items-center gap-2.5 px-3 py-2 rounded-lg cursor-pointer transition-colors"
                                                   style="color: var(--dash-text-body);"
                                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                   onmouseout="this.style.backgroundColor='transparent'"
                                                   x-show="!searchQuery || '{{ strtolower(addslashes($member->full_name)) }}'.includes(searchQuery.toLowerCase())">
                                                <input type="checkbox" value="{{ $member->id }}" x-model.number="selectedIds"
                                                       class="rounded cursor-pointer"
                                                       style="accent-color: var(--dash-primary);">
                                                <span class="text-sm font-medium">{{ $member->full_name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </template>
                                <template x-if="assignType === 'employee'">
                                    <div>
                                        @foreach($employees as $employee)
                                            <label class="flex items-center gap-2.5 px-3 py-2 rounded-lg cursor-pointer transition-colors"
                                                   style="color: var(--dash-text-body);"
                                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                   onmouseout="this.style.backgroundColor='transparent'"
                                                   x-show="!searchQuery || '{{ strtolower(addslashes($employee->full_name)) }}'.includes(searchQuery.toLowerCase())">
                                                <input type="checkbox" value="{{ $employee->id }}" x-model.number="selectedIds"
                                                       class="rounded cursor-pointer"
                                                       style="accent-color: var(--dash-primary);">
                                                <span class="text-sm font-medium">{{ $employee->full_name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </template>
                            </div>

                            <p class="text-xs font-semibold" style="color: var(--dash-text-muted);">
                                <span x-text="selectedIds.length" class="font-bold" style="color: var(--dash-primary-text);"></span> data dipilih
                            </p>
                        </div>

                        {{-- Footer Modal (rounded-b-2xl) --}}
                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showAssignModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <button type="button" @click="$refs.assignForm.submit()"
                                    :disabled="selectedIds.length === 0"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[18px]">send</span>
                                Assign
                            </button>
                        </div>

                        {{-- Hidden Form for submission --}}
                        <form x-ref="assignForm" :action="assignType === 'member' ? '{{ route('surveys.assign-members', $survey) }}' : '{{ route('surveys.assign-employees', $survey) }}'" method="POST" class="hidden">
                            @csrf
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" :name="assignType === 'member' ? 'member_ids[]' : 'employee_ids[]'" :value="id">
                            </template>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
