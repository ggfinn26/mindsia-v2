@extends('layouts.dashboard')

@section('title', $toeflTest->test_name)
@section('header_title', $toeflTest->test_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto space-y-6" x-data="toeflTestShow">

    {{-- Page Header (Pola B3) --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('toefl-tests.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Bank Test
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">TOEFL</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $toeflTest->test_name }}</h1>
            <div class="flex items-center flex-wrap gap-2 mt-2">
                @if ($toeflTest->status === 'published')
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background-color: #dcfce7; color: #15803d;">Published</span>
                @else
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background-color: #fef9c3; color: #a16207;">Draft</span>
                @endif
                @if ($toeflTest->is_trial)
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background-color: #dbeafe; color: #1d4ed8;">Trial</span>
                @endif
                @if ($toeflTest->level)
                    @php
                        $levelStyle = match($toeflTest->level) {
                            'beginner'     => 'background-color:#dcfce7;color:#15803d;',
                            'intermediate' => 'background-color:#fef3c7;color:#92400e;',
                            'advanced'     => 'background-color:#fee2e2;color:#b91c1c;',
                            default        => 'background-color:var(--dash-nav-hover-bg);color:var(--dash-text-muted);',
                        };
                    @endphp
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="{{ $levelStyle }}">{{ ucfirst($toeflTest->level) }}</span>
                @endif
            </div>
            <p class="text-sm mt-1.5" style="color: var(--dash-text-muted);">
                Listening {{ $toeflTest->listening_time_limit }}m &bull;
                Structure {{ $toeflTest->structure_time_limit }}m &bull;
                Reading {{ $toeflTest->reading_time_limit }}m
                - Total {{ $toeflTest->listening_time_limit + $toeflTest->structure_time_limit + $toeflTest->reading_time_limit }} menit
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap shrink-0">
            @if ($toeflTest->status === 'draft')
                @can('toefl.test.update')
                    <a href="{{ route('toefl-tests.edit', $toeflTest) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">edit</span>Edit
                    </a>
                @endcan
                @can('toefl.test.publish')
                    <button type="button" @click="showPublishModal = true"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: #059669;">
                        <span class="material-symbols-outlined text-[16px]">publish</span>Publish
                    </button>
                @endcan
            @endif
            @if ($toeflTest->status === 'published')
                @can('toefl.test.publish')
                    <button type="button" @click="showUnpublishModal = true"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">unpublished</span>Kembalikan ke Draft
                    </button>
                @endcan
            @endif
            @if ($toeflTest->status === 'published' && $toeflTest->is_trial)
                <button type="button"
                    x-data
                    @click="navigator.clipboard.writeText('{{ route('toefl.guest.entry', $toeflTest) }}'); $el.querySelector('span[data-label]').textContent = 'Tersalin!'; setTimeout(() => $el.querySelector('span[data-label]').textContent = 'Copy Link Trial', 2000)"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                    style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">content_copy</span>
                    <span data-label>Copy Link Trial</span>
                </button>
            @endif
            @can('toefl.test.delete')
                @if (!$toeflTest->sessions()->exists())
                    <div x-data="{ showDeleteConfirm: false }" class="inline-block">
                        <button type="button" @click="showDeleteConfirm = true"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: #dc2626;">
                            <span class="material-symbols-outlined text-[16px]">delete</span>Hapus
                        </button>
                        <template x-teleport="body">
                            <div x-show="showDeleteConfirm" style="display: none;" class="relative z-50">
                                <div x-show="showDeleteConfirm"
                                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                                     @click="showDeleteConfirm = false"></div>
                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                    <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-0">
                                        <div x-show="showDeleteConfirm" @click.away="showDeleteConfirm = false"
                                             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                                             class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                            <div class="rounded-t-2xl px-6 py-5" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                                <div class="flex items-start gap-3.5">
                                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                                        <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                                    </div>
                                                    <div>
                                                        <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Test?</h3>
                                                        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Test yang sudah memiliki sesi tidak bisa dihapus. Tindakan ini tidak dapat dibatalkan.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3" style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                                <button type="button" @click="showDeleteConfirm = false"
                                                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg"
                                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</button>
                                                <form action="{{ route('toefl-tests.destroy', $toeflTest) }}" method="POST" class="m-0">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                                            style="background-color: #dc2626;">
                                                        <span class="material-symbols-outlined text-[16px]">delete</span>Ya, Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                @endif
            @endcan
        </div>
    </div>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Section Tabs --}}
    <div class="rounded-2xl" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex flex-col sm:flex-row" style="border-bottom: 1px solid var(--dash-card-border);">
            @foreach (['listening' => 'Listening', 'structure' => 'Structure', 'reading' => 'Reading'] as $key => $label)
                @php
                    $count   = $toeflTest->questions->where('section', $key)->count();
                    $mcCount = $toeflTest->questions->where('section', $key)->where('question_type', 'multiple_choice')->count();
                    $weCount = $toeflTest->questions->where('section', $key)->where('question_type', 'written_expression')->count();
                @endphp
                <button type="button" @click="activeTab = '{{ $key }}'"
                    :style="activeTab === '{{ $key }}' ? 'color: var(--dash-primary); border-bottom: 2px solid var(--dash-primary);' : ''"
                    :class="activeTab !== '{{ $key }}' ? 'opacity-60' : ''"
                    class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium focus:outline-none transition-colors"
                    style="color: var(--dash-text-muted);">
                    {{ $label }}
                    <span class="px-1.5 py-0.5 text-xs rounded-full"
                        :style="activeTab === '{{ $key }}' ? 'background-color: color-mix(in srgb, var(--dash-primary) 15%, transparent); color: var(--dash-primary);' : 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);'">
                        {{ $count }}
                    </span>
                    @if ($key === 'structure' && $count > 0)
                        <span class="text-xs font-normal" style="color: var(--dash-text-faint);">({{ $mcCount }}S + {{ $weCount }}WE)</span>
                    @endif
                </button>
            @endforeach
        </div>

        @foreach (['listening', 'structure', 'reading'] as $section)
            @php
                $sectionQuestions = $toeflTest->questions->where('section', $section)->sortBy('display_order');
                $sectionPassages  = $toeflTest->passages->where('section', $section)->sortBy('display_order');
            @endphp
            <div x-show="activeTab === '{{ $section }}'" style="display:none">

                {{-- Passages (for listening/reading) --}}
                @if ($section !== 'structure')
                    <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold uppercase tracking-wider" style="color: var(--dash-text-heading);">
                                Passages / Audio
                                <span class="ml-1 font-normal normal-case" style="color: var(--dash-text-muted);">({{ $sectionPassages->count() }} item)</span>
                            </h3>
                            @if ($toeflTest->status === 'draft')
                                @can('toefl.passage.create')
                                    <button type="button" @click="openPassageModal('{{ $section }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-lg transition-colors"
                                        style="background-color: color-mix(in srgb, var(--dash-primary) 10%, transparent); color: var(--dash-primary); border: 1px solid color-mix(in srgb, var(--dash-primary) 30%, transparent);"
                                        onmouseover="this.style.backgroundColor='color-mix(in srgb, var(--dash-primary) 18%, transparent)'"
                                        onmouseout="this.style.backgroundColor='color-mix(in srgb, var(--dash-primary) 10%, transparent)'">
                                        <span class="material-symbols-outlined text-[16px]">add</span>Tambah Passage
                                    </button>
                                @endcan
                            @endif
                        </div>
                        @if ($sectionPassages->isEmpty())
                            <p class="text-sm italic" style="color: var(--dash-text-faint);">Belum ada passage.</p>
                        @else
                            <div class="space-y-2">
                                @foreach ($sectionPassages as $passage)
                                    <div class="flex items-center justify-between rounded-xl px-4 py-2 text-sm"
                                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                        <div>
                                            <span class="font-medium" style="color: var(--dash-text-heading);">{{ $passage->title }}</span>
                                            @if ($passage->body_text)
                                                <span class="ml-2" style="color: var(--dash-text-muted);">{{ Str::limit($passage->body_text, 60) }}</span>
                                            @endif
                                            @if ($passage->audio_media_id)
                                                <span class="ml-2 px-1.5 py-0.5 text-xs rounded" style="background-color: #fff7ed; color: #c2410c;">Audio</span>
                                            @endif
                                        </div>
                                        @if ($toeflTest->status === 'draft')
                                            <div x-data="{ showPassageDeleteConfirm: false }" class="flex items-center gap-2 ml-4 shrink-0">
                                                <button type="button"
                                                    @click="openPassageModal('{{ $section }}', 'edit', {{ json_encode(['id'=>$passage->id,'section'=>$passage->section,'title'=>$passage->title,'body_text'=>$passage->body_text,'display_order'=>$passage->display_order,'audio_media_id'=>$passage->audio_media_id]) }})"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                    style="color: var(--dash-primary); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                    <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                                </button>
                                                <button type="button" @click="showPassageDeleteConfirm = true"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                                    style="background-color: #dc2626;">
                                                    <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                                </button>
                                                <template x-teleport="body">
                                                    <div x-show="showPassageDeleteConfirm" style="display: none;" class="relative z-50">
                                                        <div x-show="showPassageDeleteConfirm"
                                                             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                                                             @click="showPassageDeleteConfirm = false"></div>
                                                        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                                            <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-0">
                                                                <div x-show="showPassageDeleteConfirm" @click.away="showPassageDeleteConfirm = false"
                                                                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                                                                     class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                                                                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                                                    <div class="rounded-t-2xl px-6 py-5" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                                                        <div class="flex items-start gap-3.5">
                                                                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                                                                <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                                                            </div>
                                                                            <div>
                                                                                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Passage?</h3>
                                                                                <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Passage <strong>{{ $passage->title }}</strong> akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3" style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                                                        <button type="button" @click="showPassageDeleteConfirm = false"
                                                                                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg"
                                                                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</button>
                                                                        <form action="{{ route('toefl.passages.destroy', $passage) }}" method="POST" class="m-0">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit"
                                                                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                                                                    style="background-color: #dc2626;">
                                                                                <span class="material-symbols-outlined text-[16px]">delete</span>Ya, Hapus
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Questions --}}
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wider" style="color: var(--dash-text-heading);">Soal</h3>
                        @if ($toeflTest->status === 'draft')
                            @can('toefl.question.create')
                                <button type="button" @click="openQuestionModal('{{ $section }}')"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">add</span>Tambah Soal
                                </button>
                            @endcan
                        @endif
                    </div>

                    @if ($sectionQuestions->isEmpty())
                        <div class="text-center py-10 text-sm" style="color: var(--dash-text-muted);">
                            Belum ada soal untuk section ini.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <div class="rounded-2xl overflow-hidden" style="border: 1px solid var(--dash-card-border);">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide w-10" style="color: var(--dash-text-muted);">#</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Pertanyaan</th>
                                            @if ($section === 'structure')
                                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide w-32" style="color: var(--dash-text-muted);">Tipe</th>
                                            @endif
                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide w-28" style="color: var(--dash-text-muted);">Jawaban</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide w-28" style="color: var(--dash-text-muted);">Kesulitan</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide w-16" style="color: var(--dash-text-muted);">Status</th>
                                            @if ($toeflTest->status === 'draft')
                                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide w-28" style="color: var(--dash-text-muted);">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sectionQuestions as $q)
                                            <tr class="{{ $q->is_active ? '' : 'opacity-50' }}"
                                                style="border-bottom: 1px solid var(--dash-card-border);"
                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                onmouseout="this.style.backgroundColor='transparent'">
                                                <td class="px-5 py-3.5" style="color: var(--dash-text-muted);">{{ $q->display_order ?: $loop->iteration }}</td>
                                                <td class="px-5 py-3.5" style="color: var(--dash-text-heading);">{{ Str::limit($q->question_text, 80) }}</td>
                                                @if ($section === 'structure')
                                                    <td class="px-5 py-3.5">
                                                        @if (($q->question_type ?? 'multiple_choice') === 'written_expression')
                                                            <span class="px-1.5 py-0.5 text-xs rounded" style="background-color: #f3e8ff; color: #7e22ce;">Written Expr.</span>
                                                        @else
                                                            <span class="px-1.5 py-0.5 text-xs rounded" style="background-color: #dbeafe; color: #1d4ed8;">Structure</span>
                                                        @endif
                                                    </td>
                                                @endif
                                                <td class="px-5 py-3.5">
                                                    <span class="font-mono font-bold" style="color: var(--dash-primary);">{{ $q->correct_option }}</span>
                                                </td>
                                                <td class="px-5 py-3.5">
                                                    @php $diff = ['easy' => 'Mudah', 'intermediate' => 'Sedang', 'advanced' => 'Sulit'][$q->difficulty] ?? $q->difficulty; @endphp
                                                    @php
                                                        $diffStyle = match($q->difficulty) {
                                                            'easy'         => 'background-color:#dcfce7;color:#15803d;',
                                                            'intermediate' => 'background-color:#fef9c3;color:#a16207;',
                                                            default        => 'background-color:#fee2e2;color:#b91c1c;',
                                                        };
                                                    @endphp
                                                    <span class="px-1.5 py-0.5 text-xs rounded" style="{{ $diffStyle }}">{{ $diff }}</span>
                                                </td>
                                                <td class="px-5 py-3.5">
                                                    <span class="text-xs" style="color: {{ $q->is_active ? '#15803d' : 'var(--dash-text-faint)' }};">
                                                        {{ $q->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </span>
                                                </td>
                                                @if ($toeflTest->status === 'draft')
                                                    <td class="px-5 py-3.5 text-right">
                                                        <div x-data="{ showQDeleteConfirm: false }" class="flex items-center justify-end gap-2">
                                                            @can('toefl.question.update')
                                                                <button type="button"
                                                                    @click="openQuestionModal('{{ $section }}', 'edit', {{ json_encode(['id'=>$q->id,'section'=>$q->section,'question_type'=>$q->question_type ?? 'multiple_choice','display_order'=>$q->display_order,'passage_id'=>$q->passage_id,'question_text'=>$q->question_text,'option_a'=>$q->option_a,'option_b'=>$q->option_b,'option_c'=>$q->option_c,'option_d'=>$q->option_d,'correct_option'=>$q->correct_option,'difficulty'=>$q->difficulty,'explanation'=>$q->explanation]) }})"
                                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                                    style="color: var(--dash-primary); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                                    <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                                                </button>
                                                                <form action="{{ route('toefl.questions.toggle-active', $q) }}" method="POST" class="inline m-0">
                                                                    @csrf @method('PATCH')
                                                                    <button type="submit"
                                                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                                                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                                        <span class="material-symbols-outlined text-[14px]">{{ $q->is_active ? 'toggle_off' : 'toggle_on' }}</span>
                                                                        {{ $q->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                            @can('toefl.question.delete')
                                                                <button type="button" @click="showQDeleteConfirm = true"
                                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                                                    style="background-color: #dc2626;">
                                                                    <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                                                </button>
                                                                <template x-teleport="body">
                                                                    <div x-show="showQDeleteConfirm" style="display: none;" class="relative z-50">
                                                                        <div x-show="showQDeleteConfirm"
                                                                             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                                             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                                                                             @click="showQDeleteConfirm = false"></div>
                                                                        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                                                            <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-0">
                                                                                <div x-show="showQDeleteConfirm" @click.away="showQDeleteConfirm = false"
                                                                                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                                                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                                                                                     class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                                                                                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                                                                    <div class="rounded-t-2xl px-6 py-5" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                                                                        <div class="flex items-start gap-3.5">
                                                                                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                                                                                <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                                                                            </div>
                                                                                            <div>
                                                                                                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Soal?</h3>
                                                                                                <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Soal ini akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3" style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                                                                        <button type="button" @click="showQDeleteConfirm = false"
                                                                                                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg"
                                                                                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</button>
                                                                                        <form action="{{ route('toefl.questions.destroy', $q) }}" method="POST" class="m-0">
                                                                                            @csrf @method('DELETE')
                                                                                            <button type="submit"
                                                                                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                                                                                    style="background-color: #dc2626;">
                                                                                                <span class="material-symbols-outlined text-[16px]">delete</span>Ya, Hapus
                                                                                            </button>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Question Modal --}}
    <div x-show="showQuestionModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
        style="display:none; background-color: rgba(15,23,42,0.5);"
        @keydown.escape.window="showQuestionModal = false">
        <div class="rounded-2xl shadow-2xl w-full max-w-2xl my-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             @click.stop>

            {{-- Header --}}
            <div class="rounded-t-2xl flex items-center justify-between px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <div>
                    <h3 class="font-semibold text-base"
                        style="color: var(--dash-text-heading);"
                        x-text="questionMode === 'create' ? 'Tambah Soal' : 'Edit Soal'"></h3>
                    <p class="text-xs mt-0.5 capitalize" style="color: var(--dash-text-muted);" x-text="questionSection + ' section'"></p>
                </div>
                <button type="button" @click="showQuestionModal = false"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg transition-colors"
                    style="color: var(--dash-text-muted);"
                    onmouseover="this.style.backgroundColor='var(--dash-card-border)'"
                    onmouseout="this.style.backgroundColor='transparent'"
                    aria-label="Tutup">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="question-form" method="POST"
                :action="questionMode === 'create'
                    ? '{{ route('toefl.questions.store', $toeflTest) }}'
                    : '{{ url('toefl-questions') }}/' + questionId">
                @csrf
                <input type="hidden" name="_method" :value="questionMode === 'create' ? 'POST' : 'PATCH'">
                <input type="hidden" id="q-section" name="section">

                {{-- Tipe soal (khusus structure) --}}
                <template x-if="questionSection === 'structure'">
                    <div class="px-6 py-4" style="background-color: color-mix(in srgb, var(--dash-primary) 6%, transparent); border-bottom: 1px solid var(--dash-card-border);">
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-2" style="color: var(--dash-primary);">Tipe Soal</label>
                        <div class="flex gap-3">
                            <label class="flex-1 flex items-center gap-3 p-3 rounded-lg border-2 cursor-pointer transition-colors"
                                :style="questionType === 'multiple_choice' ? 'border-color: var(--dash-primary); background-color: var(--dash-card-bg);' : 'border-color: var(--dash-card-border); background-color: color-mix(in srgb, var(--dash-card-bg) 60%, transparent);'">
                                <input type="radio" name="question_type" value="multiple_choice" id="q-question-type"
                                    x-model="questionType" style="accent-color: var(--dash-primary);">
                                <div>
                                    <span class="text-sm font-medium" style="color: var(--dash-text-heading);">Structure</span>
                                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Fill in the Blank</p>
                                </div>
                            </label>
                            <label class="flex-1 flex items-center gap-3 p-3 rounded-lg border-2 cursor-pointer transition-colors"
                                :style="questionType === 'written_expression' ? 'border-color: #7e22ce; background-color: var(--dash-card-bg);' : 'border-color: var(--dash-card-border); background-color: color-mix(in srgb, var(--dash-card-bg) 60%, transparent);'">
                                <input type="radio" name="question_type" value="written_expression"
                                    x-model="questionType" style="accent-color: #7e22ce;">
                                <div>
                                    <span class="text-sm font-medium" style="color: var(--dash-text-heading);">Written Expression</span>
                                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Error Identification</p>
                                </div>
                            </label>
                        </div>
                        <template x-if="questionType === 'written_expression'">
                            <p class="mt-2 text-xs rounded-lg px-3 py-2" style="color: var(--dash-primary); background-color: var(--dash-card-bg); border: 1px solid color-mix(in srgb, var(--dash-primary) 20%, transparent);">
                                Tandai posisi underline dengan <code class="font-mono font-semibold">[A]</code> <code class="font-mono font-semibold">[B]</code> <code class="font-mono font-semibold">[C]</code> <code class="font-mono font-semibold">[D]</code> di dalam teks pertanyaan.
                            </p>
                        </template>
                    </div>
                </template>
                <template x-if="questionSection !== 'structure'">
                    <input type="hidden" id="q-question-type" name="question_type" value="multiple_choice">
                </template>

                {{-- Meta: No. Urut + Passage --}}
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4" style="border-bottom: 1px solid var(--dash-card-border);">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-heading);">No. Urut
                            <span class="font-normal text-xs" style="color: var(--dash-text-muted);">(opsional)</span>
                        </label>
                        <input id="q-display-order" name="display_order" type="number" min="0"
                            class="form-input w-full text-sm min-h-[44px]" placeholder="Otomatis" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-heading);">Passage
                            <span class="font-normal text-xs" style="color: var(--dash-text-muted);">(opsional)</span>
                        </label>
                        <select id="q-passage-id" name="passage_id" class="form-select w-full text-sm min-h-[44px]">
                            <option value="">Tidak ada</option>
                            @foreach ($toeflTest->passages->sortBy('display_order') as $p)
                                <option value="{{ $p->id }}" data-section="{{ $p->section }}"
                                    x-show="questionSection === '{{ $p->section }}' || questionSection === ''">
                                    {{ $p->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Teks Pertanyaan --}}
                <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-heading);">Teks Pertanyaan <span class="text-red-500">*</span></label>
                    <textarea id="q-question-text" name="question_text" rows="4" required
                        class="form-textarea w-full text-sm font-mono"
                        :placeholder="questionPlaceholder"></textarea>
                </div>

                {{-- Pilihan Jawaban --}}
                <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
                    <label class="block text-sm font-medium mb-3" style="color: var(--dash-text-heading);">
                        <span x-text="questionType === 'written_expression' ? 'Frasa Pilihan' : 'Pilihan Jawaban'"></span>
                        <span x-show="questionType !== 'written_expression'" class="text-red-500"> *</span>
                        <span x-show="questionType === 'written_expression'" class="font-normal text-xs ml-1" style="color: var(--dash-text-muted);">(opsional - label referensi)</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach (['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)
                            <div class="flex items-center gap-2">
                                <span class="flex-shrink-0 w-7 h-7 rounded-full text-xs font-bold flex items-center justify-center"
                                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">{{ $label }}</span>
                                <input id="q-option-{{ $key }}" name="option_{{ $key }}" type="text"
                                    :required="questionType !== 'written_expression'"
                                    class="form-input flex-1 text-sm min-h-[44px]"
                                    :placeholder="questionType === 'written_expression' ? 'Frasa {{ $label }}' : 'Pilihan {{ $label }}'" />
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Jawaban & Kesulitan --}}
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-heading);">Jawaban Benar <span class="text-red-500">*</span></label>
                        <select id="q-correct-option" name="correct_option" required class="form-select w-full text-sm min-h-[44px]">
                            @foreach (['A','B','C','D'] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-heading);">Kesulitan <span class="text-red-500">*</span></label>
                        <select id="q-difficulty" name="difficulty" required class="form-select w-full text-sm min-h-[44px]">
                            <option value="easy">Mudah</option>
                            <option value="intermediate">Sedang</option>
                            <option value="advanced">Sulit</option>
                        </select>
                    </div>
                </div>

                {{-- Pembahasan --}}
                <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-heading);">Pembahasan
                        <span class="font-normal text-xs" style="color: var(--dash-text-muted);">(opsional, muncul setelah test selesai)</span>
                    </label>
                    <textarea id="q-explanation" name="explanation" rows="2"
                        class="form-textarea w-full text-sm" placeholder="Penjelasan jawaban..."></textarea>
                </div>

                {{-- Footer --}}
                <div class="rounded-b-2xl px-6 py-4 flex flex-col sm:flex-row sm:justify-end gap-3"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showQuestionModal = false"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 min-h-[44px] rounded-lg text-sm font-semibold transition-colors"
                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                        onmouseover="this.style.backgroundColor='var(--dash-card-border)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 min-h-[44px] rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);"
                        x-text="questionMode === 'create' ? 'Tambah Soal' : 'Simpan Perubahan'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Passage Modal --}}
    <div x-show="showPassageModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
        style="display:none; background-color: rgba(15,23,42,0.5);"
        @keydown.escape.window="showPassageModal = false">
        <div class="rounded-2xl shadow-2xl w-full max-w-xl my-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             @click.stop>

            {{-- Header --}}
            <div class="rounded-t-2xl flex items-center justify-between px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <div>
                    <h3 class="font-semibold text-base"
                        style="color: var(--dash-text-heading);"
                        x-text="passageMode === 'create' ? 'Tambah Passage' : 'Edit Passage'"></h3>
                    <p class="text-xs mt-0.5 capitalize" style="color: var(--dash-text-muted);" x-text="passageSection + ' section'"></p>
                </div>
                <button type="button" @click="showPassageModal = false"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg transition-colors"
                    style="color: var(--dash-text-muted);"
                    onmouseover="this.style.backgroundColor='var(--dash-card-border)'"
                    onmouseout="this.style.backgroundColor='transparent'"
                    aria-label="Tutup">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="passage-form" method="POST"
                :action="passageMode === 'create'
                    ? '{{ route('toefl.passages.store', $toeflTest) }}'
                    : '{{ url('toefl-passages') }}/' + passageId">
                @csrf
                <input type="hidden" name="_method" :value="passageMode === 'create' ? 'POST' : 'PATCH'">
                <input type="hidden" id="p-section" name="section">

                {{-- Judul + No. Urut --}}
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-4" style="border-bottom: 1px solid var(--dash-card-border);">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-heading);">Judul Passage <span class="text-red-500">*</span></label>
                        <input id="p-title" name="title" type="text" required
                            class="form-input w-full text-sm min-h-[44px]" placeholder="Mis: Passage 1 - Astronomy" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-heading);">No. Urut</label>
                        <input id="p-display-order" name="display_order" type="number" min="0"
                            value="0" class="form-input w-full text-sm min-h-[44px]" />
                    </div>
                </div>

                {{-- Audio Media (listening only) --}}
                <template x-if="passageSection === 'listening'">
                    <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-heading);">Audio
                            <span class="font-normal text-xs" style="color: var(--dash-text-muted);">(opsional)</span>
                        </label>
                        <select id="p-audio-media-id" name="audio_media_id" class="form-select w-full text-sm min-h-[44px]">
                            <option value="">Tidak ada</option>
                            @foreach ($audioMedia as $m)
                                <option value="{{ $m->id }}">{{ $m->original_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </template>

                {{-- Teks Bacaan --}}
                <div class="px-6 py-4" x-data="{ preview: false, rawText: '' }" style="border-bottom: 1px solid var(--dash-card-border);">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium" style="color: var(--dash-text-heading);">
                            Teks Bacaan
                            <span class="font-normal text-xs" style="color: var(--dash-text-muted);">(kosongkan jika audio-only)</span>
                        </label>
                        <div class="flex items-center gap-1 rounded-lg p-0.5" style="background-color: var(--dash-nav-hover-bg);">
                            <button type="button" @click="preview = false"
                                class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors"
                                :style="!preview ? 'background-color: var(--dash-card-bg); color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'">Edit</button>
                            <button type="button" @click="preview = true" data-passage-preview
                                class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors"
                                :style="preview ? 'background-color: var(--dash-card-bg); color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'">Preview</button>
                        </div>
                    </div>

                    {{-- Sintaks hint --}}
                    <details class="mb-3 text-xs">
                        <summary class="cursor-pointer select-none" style="color: var(--dash-text-muted);">Panduan sintaks</summary>
                        <div class="mt-2 rounded-lg p-3 space-y-1" style="background-color: #fffbeb; border: 1px solid #fde68a; color: #92400e;">
                            <p>- Setiap baris baru = 1 baris bernomor</p>
                            <p>- <code class="px-1 rounded font-mono" style="background-color: #fef3c7;">[U]kata[/U]</code> = kata bergaris bawah</p>
                            <p class="font-mono" style="color: #b45309;">Halley's Comet was first [U]documented[/U] in 240 B.C.</p>
                        </div>
                    </details>

                    {{-- Edit mode --}}
                    <div x-show="!preview">
                        <textarea id="p-body-text" name="body_text" rows="10"
                            class="form-textarea w-full text-sm font-mono"
                            x-model="rawText"
                            @input="$el.dispatchEvent(new Event('passage-update'))"
                            placeholder="Tulis teks passage di sini...&#10;Setiap baris = 1 baris bernomor.&#10;&#10;Contoh:&#10;Halley's Comet had its first documented sighting in&#10;240 B.C. in China. [U]Since then[/U], it has been seen&#10;from Earth approximately 29 times."></textarea>
                    </div>

                    {{-- Preview mode --}}
                    <div x-show="preview" class="rounded-xl p-4 min-h-[200px]"
                         style="border: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <p class="text-xs uppercase font-semibold tracking-wide mb-3" style="color: var(--dash-text-muted);">Preview</p>
                        <div class="text-sm leading-[1.85] font-serif" style="color: var(--dash-text-body);" id="passage-preview-render"></div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="rounded-b-2xl px-6 py-4 flex flex-col sm:flex-row sm:justify-end gap-3"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showPassageModal = false"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 min-h-[44px] rounded-lg text-sm font-semibold transition-colors"
                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                        onmouseover="this.style.backgroundColor='var(--dash-card-border)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 min-h-[44px] rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);"
                        x-text="passageMode === 'create' ? 'Tambah Passage' : 'Simpan Perubahan'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Publish Confirmation Modal --}}
    @can('toefl.test.publish')
    <div x-show="showPublishModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @keydown.escape.window="showPublishModal = false">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showPublishModal = false"></div>
        <div class="relative rounded-2xl shadow-2xl w-full max-w-md transition-all"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             @click.stop>
            <div class="rounded-t-2xl px-6 py-5" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]" style="color: #059669;">publish</span>
                    </div>
                    <div>
                        <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Publish Test?</h3>
                        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Soal tidak bisa diubah setelah dipublish. Pastikan semua soal sudah benar sebelum melanjutkan.</p>
                    </div>
                </div>
            </div>
            <div class="rounded-b-2xl flex justify-end gap-3 px-6 py-4" style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                <button type="button" @click="showPublishModal = false"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg"
                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</button>
                <form action="{{ route('toefl-tests.publish', $toeflTest) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: #059669;">
                        <span class="material-symbols-outlined text-[16px]">publish</span>Ya, Publish
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endcan


    {{-- Unpublish Confirmation Modal --}}
    @can('toefl.test.publish')
    <div x-show="showUnpublishModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @keydown.escape.window="showUnpublishModal = false">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showUnpublishModal = false"></div>
        <div class="relative rounded-2xl shadow-2xl w-full max-w-md transition-all"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             @click.stop>
            <div class="rounded-t-2xl px-6 py-5" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Kembalikan ke Draft?</h3>
                <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Test tidak bisa dikerjakan peserta sampai dipublish ulang. Hanya bisa dilakukan jika belum ada session pengerjaan.</p>
            </div>
            <div class="rounded-b-2xl flex justify-end gap-3 px-6 py-4" style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                <button type="button" @click="showUnpublishModal = false"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg"
                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</button>
                <form action="{{ route('toefl-tests.unpublish', $toeflTest) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                        Ya, Kembalikan
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endcan

</div>
@endsection
