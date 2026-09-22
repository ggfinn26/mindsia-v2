@extends('layouts.dashboard')

@section('title', $toeflTest->test_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto"
    x-data="{
        activeTab: 'listening',
        showQuestionModal: false,
        questionMode: 'create',
        questionSection: 'listening',
        questionId: null,
        showPassageModal: false,
        passageMode: 'create',
        passageId: null,
        passageSection: 'listening',
        openQuestionModal(section, mode = 'create', q = null) {
            this.questionSection = section;
            this.questionMode = mode;
            this.questionId = q ? q.id : null;
            if (mode === 'edit' && q) {
                document.getElementById('q-section').value = q.section;
                document.getElementById('q-display-order').value = q.display_order;
                document.getElementById('q-passage-id').value = q.passage_id ?? '';
                document.getElementById('q-question-text').value = q.question_text;
                document.getElementById('q-option-a').value = q.option_a;
                document.getElementById('q-option-b').value = q.option_b;
                document.getElementById('q-option-c').value = q.option_c;
                document.getElementById('q-option-d').value = q.option_d;
                document.getElementById('q-correct-option').value = q.correct_option;
                document.getElementById('q-difficulty').value = q.difficulty;
                document.getElementById('q-explanation').value = q.explanation ?? '';
            } else {
                document.getElementById('question-form').reset();
                document.getElementById('q-section').value = section;
            }
            this.showQuestionModal = true;
        },
        openPassageModal(section, mode = 'create', p = null) {
            this.passageSection = section;
            this.passageMode = mode;
            this.passageId = p ? p.id : null;
            if (mode === 'edit' && p) {
                document.getElementById('p-section').value = p.section;
                document.getElementById('p-title').value = p.title;
                document.getElementById('p-body-text').value = p.body_text ?? '';
                document.getElementById('p-display-order').value = p.display_order;
            } else {
                document.getElementById('passage-form').reset();
                document.getElementById('p-section').value = section;
            }
            this.showPassageModal = true;
        }
    }">

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
                    <a href="{{ route('toefl-tests.edit', $toeflTest) }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-700 text-sm">Edit</a>
                @endcan
                @can('toefl.test.publish')
                    <form action="{{ route('toefl-tests.publish', $toeflTest) }}" method="POST" class="inline"
                        onsubmit="return confirm('Publish test ini? Soal tidak bisa diubah setelah dipublish.')">
                        @csrf
                        <button type="submit" class="btn bg-green-500 hover:bg-green-600 text-white text-sm">Publish</button>
                    </form>
                @endcan
            @endif
            @can('toefl.test.delete')
                @if (!$toeflTest->sessions()->exists())
                    <form action="{{ route('toefl-tests.destroy', $toeflTest) }}" method="POST" class="inline"
                        onsubmit="return confirm('Hapus test ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn bg-white border-red-200 hover:border-red-400 text-red-600 text-sm">Hapus</button>
                    </form>
                @endif
            @endcan
            <a href="{{ route('toefl-tests.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 text-sm">Kembali</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
    @endif

    {{-- Section Tabs --}}
    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="border-b border-gray-200 flex">
            @foreach (['listening' => 'Listening', 'structure' => 'Structure', 'reading' => 'Reading'] as $key => $label)
                @php $count = $toeflTest->questions->where('section', $key)->count(); @endphp
                <button type="button" @click="activeTab = '{{ $key }}'"
                    :class="activeTab === '{{ $key }}' ? 'border-b-2 border-indigo-500 text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="px-6 py-3 text-sm focus:outline-none">
                    {{ $label }}
                    <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full"
                        :class="activeTab === '{{ $key }}' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600'">
                        {{ $count }}
                    </span>
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
                                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">+ Tambah Passage</button>
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
                                                    @click="openPassageModal('{{ $section }}', 'edit', {{ json_encode(['id'=>$passage->id,'section'=>$passage->section,'title'=>$passage->title,'body_text'=>$passage->body_text,'display_order'=>$passage->display_order]) }})"
                                                    class="text-blue-600 hover:text-blue-800">Edit</button>
                                                <form action="{{ route('toefl.passages.destroy', $passage) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('Hapus passage ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
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
                                    class="btn btn-sm bg-indigo-500 hover:bg-indigo-600 text-white text-sm">+ Tambah Soal</button>
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
                                                                @click="openQuestionModal('{{ $section }}', 'edit', {{ json_encode(['id'=>$q->id,'section'=>$q->section,'display_order'=>$q->display_order,'passage_id'=>$q->passage_id,'question_text'=>$q->question_text,'option_a'=>$q->option_a,'option_b'=>$q->option_b,'option_c'=>$q->option_c,'option_d'=>$q->option_d,'correct_option'=>$q->correct_option,'difficulty'=>$q->difficulty,'explanation'=>$q->explanation]) }})"
                                                                class="text-blue-600 hover:text-blue-800 text-xs">Edit</button>
                                                            <form action="{{ route('toefl.questions.toggle-active', $q) }}" method="POST" class="inline">
                                                                @csrf @method('PATCH')
                                                                <button type="submit" class="text-gray-500 hover:text-gray-700 text-xs">
                                                                    {{ $q->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                                </button>
                                                            </form>
                                                        @endcan
                                                        @can('toefl.question.delete')
                                                            <form action="{{ route('toefl.questions.destroy', $q) }}" method="POST" class="inline"
                                                                onsubmit="return confirm('Hapus soal ini?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Hapus</button>
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
    <div x-show="showQuestionModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-start justify-center py-8 px-4"
        style="display:none" @keydown.escape.window="showQuestionModal = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
            <div class="flex items-center justify-between p-5 border-b">
                <h3 class="font-bold text-gray-900" x-text="questionMode === 'create' ? 'Tambah Soal — ' + questionSection.charAt(0).toUpperCase() + questionSection.slice(1) : 'Edit Soal'"></h3>
                <button type="button" @click="showQuestionModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form id="question-form" method="POST" class="p-5 space-y-4"
                :action="questionMode === 'create'
                    ? '{{ route('toefl.questions.store', $toeflTest) }}'
                    : '{{ url('toefl-questions') }}/' + questionId">
                @csrf
                <input type="hidden" name="_method" :value="questionMode === 'create' ? 'POST' : 'PATCH'">
                <input type="hidden" id="q-section" name="section">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">No. Urut</label>
                        <input id="q-display-order" name="display_order" type="number" min="0"
                            class="form-input w-full text-sm" placeholder="Opsional" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Passage</label>
                        <select id="q-passage-id" name="passage_id" class="form-select w-full text-sm">
                            <option value="">— Tidak ada passage —</option>
                            @foreach ($toeflTest->passages->sortBy('display_order') as $p)
                                <option value="{{ $p->id }}">[{{ ucfirst($p->section) }}] {{ $p->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Teks Pertanyaan <span class="text-red-500">*</span></label>
                    <textarea id="q-question-text" name="question_text" rows="3" required
                        class="form-textarea w-full text-sm"
                        placeholder="Written Expression: gunakan [A] [B] [C] [D] untuk posisi underline"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    @foreach (['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Pilihan {{ $label }} <span class="text-red-500">*</span></label>
                            <input id="q-option-{{ $key }}" name="option_{{ $key }}" type="text" required
                                class="form-input w-full text-sm" />
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jawaban Benar <span class="text-red-500">*</span></label>
                        <select id="q-correct-option" name="correct_option" required class="form-select w-full text-sm">
                            @foreach (['A','B','C','D'] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kesulitan <span class="text-red-500">*</span></label>
                        <select id="q-difficulty" name="difficulty" required class="form-select w-full text-sm">
                            <option value="easy">Mudah</option>
                            <option value="intermediate">Sedang</option>
                            <option value="advanced">Sulit</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Pembahasan <span class="text-gray-400">(opsional, muncul setelah test selesai)</span></label>
                    <textarea id="q-explanation" name="explanation" rows="2" class="form-textarea w-full text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t">
                    <button type="button" @click="showQuestionModal = false"
                        class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 text-sm">Batal</button>
                    <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white text-sm"
                        x-text="questionMode === 'create' ? 'Tambah Soal' : 'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Passage Modal --}}
    <div x-show="showPassageModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-start justify-center py-8 px-4"
        style="display:none" @keydown.escape.window="showPassageModal = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
            <div class="flex items-center justify-between p-5 border-b">
                <h3 class="font-bold text-gray-900" x-text="passageMode === 'create' ? 'Tambah Passage' : 'Edit Passage'"></h3>
                <button type="button" @click="showPassageModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form id="passage-form" method="POST" class="p-5 space-y-4"
                :action="passageMode === 'create'
                    ? '{{ route('toefl.passages.store', $toeflTest) }}'
                    : '{{ url('toefl-passages') }}/' + passageId">
                @csrf
                <input type="hidden" name="_method" :value="passageMode === 'create' ? 'POST' : 'PATCH'">
                <input type="hidden" id="p-section" name="section">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Judul Passage <span class="text-red-500">*</span></label>
                        <input id="p-title" name="title" type="text" required class="form-input w-full text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">No. Urut</label>
                        <input id="p-display-order" name="display_order" type="number" min="0"
                            value="0" class="form-input w-full text-sm" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Teks Bacaan <span class="text-gray-400">(kosongkan jika audio-only)</span></label>
                    <textarea id="p-body-text" name="body_text" rows="5" class="form-textarea w-full text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t">
                    <button type="button" @click="showPassageModal = false"
                        class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 text-sm">Batal</button>
                    <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white text-sm"
                        x-text="passageMode === 'create' ? 'Tambah Passage' : 'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
