@extends('layouts.dashboard')

@section('title', $toeflTest->test_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto" x-data="toeflTestShow">

    {{-- Header --}}
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold text-gray-800">{{ $toeflTest->test_name }}</h1>
                @if ($toeflTest->status === 'published')
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                @else
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                @endif
                @if ($toeflTest->is_trial)
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Trial</span>
                @endif
                @if ($toeflTest->level)
                    @php $levelColors = ['beginner'=>'bg-emerald-100 text-emerald-800','intermediate'=>'bg-amber-100 text-amber-800','advanced'=>'bg-red-100 text-red-800']; @endphp
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $levelColors[$toeflTest->level] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($toeflTest->level) }}
                    </span>
                @endif
            </div>
            <p class="text-sm text-gray-500">
                Listening {{ $toeflTest->listening_time_limit }}m &bull;
                Structure {{ $toeflTest->structure_time_limit }}m &bull;
                Reading {{ $toeflTest->reading_time_limit }}m
                &mdash; Total {{ $toeflTest->listening_time_limit + $toeflTest->structure_time_limit + $toeflTest->reading_time_limit }} menit
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if ($toeflTest->status === 'draft')
                @can('toefl.test.update')
                    <a href="{{ route('toefl-tests.edit', $toeflTest) }}" class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium transition-colors">Edit</a>
                @endcan
                @can('toefl.test.publish')
                    <button type="button" @click="showPublishModal = true"
                        class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-green-500 hover:bg-green-600 text-white text-sm font-medium transition-colors">Publish</button>
                @endcan
            @endif
            @if ($toeflTest->status === 'published' && $toeflTest->is_trial)
                <button type="button"
                    x-data
                    @click="navigator.clipboard.writeText('{{ route('toefl.guest.entry', $toeflTest) }}'); $el.textContent = 'Tersalin!'; setTimeout(() => $el.textContent = 'Copy Link Trial', 2000)"
                    class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-blue-50 border border-blue-300 hover:bg-blue-100 text-blue-700 text-sm font-medium transition-colors">Copy Link Trial</button>
            @endif
            @can('toefl.test.delete')
                @if (!$toeflTest->sessions()->exists())
                    <form action="{{ route('toefl-tests.destroy', $toeflTest) }}" method="POST" class="inline"
                        onsubmit="return confirm('Hapus test ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-white border border-gray-300 hover:bg-red-50 hover:border-red-400 text-red-600 text-sm font-medium transition-colors">Hapus</button>
                    </form>
                @endif
            @endcan
            <a href="{{ route('toefl-tests.index') }}" class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium transition-colors">Kembali</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
    @endif

    {{-- Section Tabs --}}
    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="border-b border-gray-200 flex flex-col sm:flex-row">
            @foreach (['listening' => 'Listening', 'structure' => 'Structure', 'reading' => 'Reading'] as $key => $label)
                @php
                    $count = $toeflTest->questions->where('section', $key)->count();
                    $mcCount = $toeflTest->questions->where('section', $key)->where('question_type', 'multiple_choice')->count();
                    $weCount = $toeflTest->questions->where('section', $key)->where('question_type', 'written_expression')->count();
                @endphp
                <button type="button" @click="activeTab = '{{ $key }}'"
                    :class="activeTab === '{{ $key }}' ? 'border-b-2 border-indigo-500 text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-3 min-h-[44px] min-w-[44px] text-sm focus:outline-none transition-colors">
                    {{ $label }}
                    <span class="px-1.5 py-0.5 text-xs rounded-full"
                        :class="activeTab === '{{ $key }}' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600'">
                        {{ $count }}
                    </span>
                    @if ($key === 'structure' && $count > 0)
                        <span class="text-xs text-gray-400 font-normal">({{ $mcCount }}S + {{ $weCount }}WE)</span>
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
                    <div class="border-b border-gray-100 px-6 py-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">
                                Passages / Audio
                                <span class="ml-1 text-gray-400 font-normal normal-case">({{ $sectionPassages->count() }} item)</span>
                            </h3>
                            @if ($toeflTest->status === 'draft')
                                @can('toefl.passage.create')
                                    <button type="button" @click="openPassageModal('{{ $section }}')"
                                        class="inline-flex items-center justify-center px-3 py-2 min-h-[44px] min-w-[44px] rounded-md text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-900 transition-colors text-sm font-medium">+ Tambah Passage</button>
                                @endcan
                            @endif
                        </div>
                        @if ($sectionPassages->isEmpty())
                            <p class="text-sm text-gray-400 italic">Belum ada passage.</p>
                        @else
                            <div class="space-y-2">
                                @foreach ($sectionPassages as $passage)
                                    <div class="flex items-center justify-between bg-gray-50 rounded px-4 py-2 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-800">{{ $passage->title }}</span>
                                            @if ($passage->body_text)
                                                <span class="text-gray-400 ml-2">{{ Str::limit($passage->body_text, 60) }}</span>
                                            @endif
                                            @if ($passage->audio_media_id)
                                                <span class="ml-2 px-1.5 py-0.5 text-xs bg-orange-100 text-orange-700 rounded">Audio</span>
                                            @endif
                                        </div>
                                        @if ($toeflTest->status === 'draft')
                                            <div class="flex items-center gap-3 ml-4 shrink-0">
                                                <button type="button"
                                                    @click="openPassageModal('{{ $section }}', 'edit', {{ json_encode(['id'=>$passage->id,'section'=>$passage->section,'title'=>$passage->title,'body_text'=>$passage->body_text,'display_order'=>$passage->display_order,'audio_media_id'=>$passage->audio_media_id]) }})"
                                                    class="inline-flex items-center justify-center px-3 py-2 min-h-[44px] min-w-[44px] rounded-md text-blue-600 hover:bg-blue-50 hover:text-blue-800 transition-colors">Edit</button>
                                                <form action="{{ route('toefl.passages.destroy', $passage) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('Hapus passage ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center px-3 py-2 min-h-[44px] min-w-[44px] rounded-md text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors">Hapus</button>
                                                </form>
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
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Soal</h3>
                        @if ($toeflTest->status === 'draft')
                            @can('toefl.question.create')
                                <button type="button" @click="openQuestionModal('{{ $section }}')"
                                    class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-indigo-500 hover:bg-indigo-600 text-white transition-colors text-sm font-medium">+ Tambah Soal</button>
                            @endcan
                        @endif
                    </div>

                    @if ($sectionQuestions->isEmpty())
                        <div class="text-center py-10 text-gray-400 text-sm">
                            Belum ada soal untuk section ini.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-xs uppercase text-gray-500 border-b border-gray-200">
                                    <tr>
                                        <th class="pb-2 pr-3 text-left w-10">#</th>
                                        <th class="pb-2 pr-3 text-left">Pertanyaan</th>
                                        @if ($section === 'structure')
                                            <th class="pb-2 pr-3 text-left w-32">Tipe</th>
                                        @endif
                                        <th class="pb-2 pr-3 text-left w-28">Jawaban</th>
                                        <th class="pb-2 pr-3 text-left w-28">Kesulitan</th>
                                        <th class="pb-2 pr-3 text-left w-16">Status</th>
                                        @if ($toeflTest->status === 'draft')
                                            <th class="pb-2 text-right w-28">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($sectionQuestions as $q)
                                        <tr class="{{ $q->is_active ? '' : 'opacity-50' }}">
                                            <td class="py-3 pr-3 text-gray-400">{{ $q->display_order ?: $loop->iteration }}</td>
                                            <td class="py-3 pr-3 text-gray-800">{{ Str::limit($q->question_text, 80) }}</td>
                                            @if ($section === 'structure')
                                                <td class="py-3 pr-3">
                                                    @if (($q->question_type ?? 'multiple_choice') === 'written_expression')
                                                        <span class="px-1.5 py-0.5 text-xs rounded bg-purple-100 text-purple-700">Written Expr.</span>
                                                    @else
                                                        <span class="px-1.5 py-0.5 text-xs rounded bg-blue-100 text-blue-700">Structure</span>
                                                    @endif
                                                </td>
                                            @endif
                                            <td class="py-3 pr-3">
                                                <span class="font-mono font-bold text-indigo-600">{{ $q->correct_option }}</span>
                                            </td>
                                            <td class="py-3 pr-3">
                                                @php $diff = ['easy'=>'Mudah','intermediate'=>'Sedang','advanced'=>'Sulit'][$q->difficulty] ?? $q->difficulty; @endphp
                                                <span class="px-1.5 py-0.5 text-xs rounded
                                                    {{ $q->difficulty === 'easy' ? 'bg-green-100 text-green-700' : ($q->difficulty === 'intermediate' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                    {{ $diff }}
                                                </span>
                                            </td>
                                            <td class="py-3 pr-3">
                                                <span class="text-xs {{ $q->is_active ? 'text-green-600' : 'text-gray-400' }}">
                                                    {{ $q->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                            @if ($toeflTest->status === 'draft')
                                                <td class="py-3 text-right">
                                                    <div class="flex items-center justify-end gap-2">
                                                        @can('toefl.question.update')
                                                            <button type="button"
                                                                @click="openQuestionModal('{{ $section }}', 'edit', {{ json_encode(['id'=>$q->id,'section'=>$q->section,'question_type'=>$q->question_type ?? 'multiple_choice','display_order'=>$q->display_order,'passage_id'=>$q->passage_id,'question_text'=>$q->question_text,'option_a'=>$q->option_a,'option_b'=>$q->option_b,'option_c'=>$q->option_c,'option_d'=>$q->option_d,'correct_option'=>$q->correct_option,'difficulty'=>$q->difficulty,'explanation'=>$q->explanation]) }})"
                                                                class="inline-flex items-center justify-center px-3 py-2 min-h-[44px] min-w-[44px] rounded-md text-blue-600 hover:bg-blue-50 hover:text-blue-800 transition-colors text-sm">Edit</button>
                                                            <form action="{{ route('toefl.questions.toggle-active', $q) }}" method="POST" class="inline">
                                                                @csrf @method('PATCH')
                                                                <button type="submit" class="inline-flex items-center justify-center px-3 py-2 min-h-[44px] min-w-[44px] rounded-md text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors text-sm">
                                                                    {{ $q->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                                </button>
                                                            </form>
                                                        @endcan
                                                        @can('toefl.question.delete')
                                                            <form action="{{ route('toefl.questions.destroy', $q) }}" method="POST" class="inline"
                                                                onsubmit="return confirm('Hapus soal ini?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="inline-flex items-center justify-center px-3 py-2 min-h-[44px] min-w-[44px] rounded-md text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors text-sm">Hapus</button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Question Modal --}}
    <div x-show="showQuestionModal"
        class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 overflow-y-auto"
        style="display:none" @keydown.escape.window="showQuestionModal = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl my-4" @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="font-semibold text-gray-900 text-base"
                        x-text="questionMode === 'create' ? 'Tambah Soal' : 'Edit Soal'"></h3>
                    <p class="text-xs text-gray-400 mt-0.5 capitalize" x-text="questionSection + ' section'"></p>
                </div>
                <button type="button" @click="showQuestionModal = false"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors text-xl leading-none"
                    aria-label="Tutup">&times;</button>
            </div>

            <form id="question-form" method="POST" class="divide-y divide-gray-100"
                :action="questionMode === 'create'
                    ? '{{ route('toefl.questions.store', $toeflTest) }}'
                    : '{{ url('toefl-questions') }}/' + questionId">
                @csrf
                <input type="hidden" name="_method" :value="questionMode === 'create' ? 'POST' : 'PATCH'">
                <input type="hidden" id="q-section" name="section">

                {{-- Tipe soal (khusus structure) --}}
                <template x-if="questionSection === 'structure'">
                    <div class="px-6 py-4 bg-indigo-50/60">
                        <label class="block text-xs font-semibold text-indigo-700 uppercase tracking-wider mb-2">Tipe Soal</label>
                        <div class="flex gap-3">
                            <label class="flex-1 flex items-center gap-3 p-3 rounded-lg border-2 cursor-pointer transition-colors"
                                :class="questionType === 'multiple_choice' ? 'border-indigo-500 bg-white' : 'border-gray-200 bg-white/60 hover:border-gray-300'">
                                <input type="radio" name="question_type" value="multiple_choice" id="q-question-type"
                                    x-model="questionType" class="text-indigo-600">
                                <div>
                                    <span class="text-sm font-medium text-gray-800">Structure</span>
                                    <p class="text-xs text-gray-400 mt-0.5">Fill in the Blank</p>
                                </div>
                            </label>
                            <label class="flex-1 flex items-center gap-3 p-3 rounded-lg border-2 cursor-pointer transition-colors"
                                :class="questionType === 'written_expression' ? 'border-purple-500 bg-white' : 'border-gray-200 bg-white/60 hover:border-gray-300'">
                                <input type="radio" name="question_type" value="written_expression"
                                    x-model="questionType" class="text-purple-600">
                                <div>
                                    <span class="text-sm font-medium text-gray-800">Written Expression</span>
                                    <p class="text-xs text-gray-400 mt-0.5">Error Identification</p>
                                </div>
                            </label>
                        </div>
                        <template x-if="questionType === 'written_expression'">
                            <p class="mt-2 text-xs text-indigo-600 bg-white rounded-md px-3 py-2 border border-indigo-100">
                                Tandai posisi underline dengan <code class="font-mono font-semibold">[A]</code> <code class="font-mono font-semibold">[B]</code> <code class="font-mono font-semibold">[C]</code> <code class="font-mono font-semibold">[D]</code> di dalam teks pertanyaan.
                            </p>
                        </template>
                    </div>
                </template>
                <template x-if="questionSection !== 'structure'">
                    <input type="hidden" id="q-question-type" name="question_type" value="multiple_choice">
                </template>

                {{-- Meta: No. Urut + Passage --}}
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Urut
                            <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                        </label>
                        <input id="q-display-order" name="display_order" type="number" min="0"
                            class="form-input w-full text-sm min-h-[44px]" placeholder="Otomatis" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Passage
                            <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                        </label>
                        <select id="q-passage-id" name="passage_id" class="form-select w-full text-sm min-h-[44px]">
                            <option value="">— Tidak ada —</option>
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
                <div class="px-6 py-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Teks Pertanyaan <span class="text-red-500">*</span></label>
                    <textarea id="q-question-text" name="question_text" rows="4" required
                        class="form-textarea w-full text-sm font-mono"
                        :placeholder="questionPlaceholder"></textarea>
                </div>

                {{-- Pilihan Jawaban --}}
                <div class="px-6 py-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        <span x-text="questionType === 'written_expression' ? 'Frasa Pilihan' : 'Pilihan Jawaban'"></span>
                        <span x-show="questionType !== 'written_expression'" class="text-red-500"> *</span>
                        <span x-show="questionType === 'written_expression'" class="text-gray-400 font-normal text-xs ml-1">(opsional — label referensi)</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach (['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)
                            <div class="flex items-center gap-2">
                                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-gray-100 text-gray-600 text-xs font-bold flex items-center justify-center">{{ $label }}</span>
                                <input id="q-option-{{ $key }}" name="option_{{ $key }}" type="text"
                                    :required="questionType !== 'written_expression'"
                                    class="form-input flex-1 text-sm min-h-[44px]"
                                    :placeholder="questionType === 'written_expression' ? 'Frasa {{ $label }}' : 'Pilihan {{ $label }}'" />
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Jawaban & Kesulitan --}}
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/50">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jawaban Benar <span class="text-red-500">*</span></label>
                        <select id="q-correct-option" name="correct_option" required class="form-select w-full text-sm min-h-[44px]">
                            @foreach (['A','B','C','D'] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kesulitan <span class="text-red-500">*</span></label>
                        <select id="q-difficulty" name="difficulty" required class="form-select w-full text-sm min-h-[44px]">
                            <option value="easy">Mudah</option>
                            <option value="intermediate">Sedang</option>
                            <option value="advanced">Sulit</option>
                        </select>
                    </div>
                </div>

                {{-- Pembahasan --}}
                <div class="px-6 py-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pembahasan
                        <span class="text-gray-400 font-normal text-xs">(opsional, muncul setelah test selesai)</span>
                    </label>
                    <textarea id="q-explanation" name="explanation" rows="2"
                        class="form-textarea w-full text-sm" placeholder="Penjelasan jawaban..."></textarea>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:justify-end gap-3">
                    <button type="button" @click="showQuestionModal = false"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 min-h-[44px] rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium transition-colors">Batal</button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 min-h-[44px] rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition-colors"
                        x-text="questionMode === 'create' ? 'Tambah Soal' : 'Simpan Perubahan'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Passage Modal --}}
    <div x-show="showPassageModal"
        class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 overflow-y-auto"
        style="display:none" @keydown.escape.window="showPassageModal = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl my-4" @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="font-semibold text-gray-900 text-base"
                        x-text="passageMode === 'create' ? 'Tambah Passage' : 'Edit Passage'"></h3>
                    <p class="text-xs text-gray-400 mt-0.5 capitalize" x-text="passageSection + ' section'"></p>
                </div>
                <button type="button" @click="showPassageModal = false"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors text-xl leading-none"
                    aria-label="Tutup">&times;</button>
            </div>

            <form id="passage-form" method="POST" class="divide-y divide-gray-100"
                :action="passageMode === 'create'
                    ? '{{ route('toefl.passages.store', $toeflTest) }}'
                    : '{{ url('toefl-passages') }}/' + passageId">
                @csrf
                <input type="hidden" name="_method" :value="passageMode === 'create' ? 'POST' : 'PATCH'">
                <input type="hidden" id="p-section" name="section">

                {{-- Judul + No. Urut --}}
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Passage <span class="text-red-500">*</span></label>
                        <input id="p-title" name="title" type="text" required
                            class="form-input w-full text-sm min-h-[44px]" placeholder="Mis: Passage 1 - Astronomy" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Urut</label>
                        <input id="p-display-order" name="display_order" type="number" min="0"
                            value="0" class="form-input w-full text-sm min-h-[44px]" />
                    </div>
                </div>

                {{-- Audio Media (listening only) --}}
                <template x-if="passageSection === 'listening'">
                    <div class="px-6 py-4 border-t border-gray-100">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Audio
                            <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                        </label>
                        <select id="p-audio-media-id" name="audio_media_id" class="form-select w-full text-sm min-h-[44px]">
                            <option value="">— Tidak ada —</option>
                            @foreach ($audioMedia as $m)
                                <option value="{{ $m->id }}">{{ $m->original_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </template>

                {{-- Teks Bacaan --}}
                <div class="px-6 py-4" x-data="{ preview: false, rawText: '' }">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Teks Bacaan
                            <span class="text-gray-400 font-normal text-xs">(kosongkan jika audio-only)</span>
                        </label>
                        <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5">
                            <button type="button" @click="preview = false"
                                class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors"
                                :class="!preview ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'">Edit</button>
                            <button type="button" @click="preview = true" data-passage-preview
                                class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors"
                                :class="preview ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'">Preview</button>
                        </div>
                    </div>

                    {{-- Sintaks hint — collapsible dan lebih minimalis --}}
                    <details class="mb-3 text-xs">
                        <summary class="cursor-pointer text-gray-400 hover:text-gray-600 select-none">Panduan sintaks</summary>
                        <div class="mt-2 bg-amber-50 border border-amber-200 rounded-lg p-3 space-y-1 text-amber-800">
                            <p>• Setiap baris baru = 1 baris bernomor</p>
                            <p>• <code class="bg-amber-100 px-1 rounded font-mono">[U]kata[/U]</code> → kata bergaris bawah</p>
                            <p class="text-amber-600 font-mono">Halley's Comet was first [U]documented[/U] in 240 B.C.</p>
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
                    <div x-show="preview" class="border border-gray-200 rounded-lg p-4 bg-gray-50 min-h-[200px]">
                        <p class="text-xs text-gray-400 mb-3 uppercase font-semibold tracking-wide">Preview</p>
                        <div class="text-sm text-gray-700 leading-[1.85] font-serif" id="passage-preview-render"></div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:justify-end gap-3">
                    <button type="button" @click="showPassageModal = false"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 min-h-[44px] rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium transition-colors">Batal</button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 min-h-[44px] rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition-colors"
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
    <div class="absolute inset-0 bg-black/40" @click="showPublishModal = false"></div>
    <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900">Publish Test?</h3>
                <p class="mt-1 text-sm text-gray-500">Soal tidak bisa diubah setelah dipublish. Pastikan semua soal sudah benar sebelum melanjutkan.</p>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" @click="showPublishModal = false"
                class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium transition-colors">Batal</button>
            <form action="{{ route('toefl-tests.publish', $toeflTest) }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] rounded-lg bg-green-500 hover:bg-green-600 text-white text-sm font-medium transition-colors">Ya, Publish</button>
            </form>
        </div>
    </div>
    </div>
    @endcan

</div>
@endsection
