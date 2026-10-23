@extends('layouts.dashboard')

@section('title', 'Buat Survey - MINDSIA')
@section('header_title', 'Buat Survey')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
    x-data="{
        questions: [{ question_text: '', question_type: 'single_choice', scale_min: 1, scale_max: 5, scale_min_label: '', scale_max_label: '', choices: [{ choice_text: '' }, { choice_text: '' }] }],
        addQuestion() {
            this.questions.push({ question_text: '', question_type: 'single_choice', scale_min: 1, scale_max: 5, scale_min_label: '', scale_max_label: '', choices: [{ choice_text: '' }, { choice_text: '' }] });
        },
        removeQuestion(i) {
            if (this.questions.length > 1) {
                this.questions.splice(i, 1);
            }
        },
        addChoice(qi) {
            this.questions[qi].choices.push({ choice_text: '' });
        },
        removeChoice(qi, ci) {
            if (this.questions[qi].choices.length > 1) {
                this.questions[qi].choices.splice(ci, 1);
            }
        },
        typeHasChoices(type) {
            return type === 'single_choice' || type === 'multiple_choice';
        },
        typeHasScale(type) {
            return type === 'scale';
        },
        resetQuestionFields(qi) {
            const q = this.questions[qi];
            if (!this.typeHasChoices(q.question_type)) {
                q.choices = [];
            }
            if (!this.typeHasScale(q.question_type)) {
                q.scale_min = 1;
                q.scale_max = 5;
                q.scale_min_label = '';
                q.scale_max_label = '';
            }
            if (this.typeHasChoices(q.question_type) && q.choices.length === 0) {
                q.choices = [{ choice_text: '' }, { choice_text: '' }];
            }
        }
    }">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('surveys.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke Daftar Survey
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Konfigurasi Survey</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Buat Survey Baru</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Susun kuesioner baru beserta konfigurasi tipe pertanyaan dan pilihan jawaban.
        </p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">error</span>
            <div class="space-y-1">
                <p class="font-semibold">Terdapat kesalahan input:</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('surveys.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Card: Informasi Survey --}}
        <div class="rounded-2xl shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="rounded-t-2xl px-6 py-4"
                 style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Informasi Survey</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Judul dan penjelasan umum mengenai survei.</p>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Nama Survey <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="survey_name" value="{{ old('survey_name') }}" required maxlength="255"
                           placeholder="Contoh: Survey Kepuasan Layanan Q1"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('survey_name')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Deskripsi <span class="text-xs" style="color: var(--dash-text-faint);">(opsional)</span>
                    </label>
                    <textarea name="survey_description" rows="3"
                              placeholder="Jelaskan tujuan atau petunjuk pengisian survei ini..."
                              class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                              onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                              onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('survey_description') }}</textarea>
                    @error('survey_description')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Batas Waktu Pengisian <span class="text-xs" style="color: var(--dash-text-faint);">(opsional)</span>
                    </label>
                    <input type="datetime-local" name="deadline_at" value="{{ old('deadline_at') }}"
                           class="w-full sm:max-w-xs px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('deadline_at')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section: Question Builder --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Daftar Pertanyaan</h2>
                    <p class="text-xs" style="color: var(--dash-text-muted);">Kelola butir soal dan format respon peserta.</p>
                </div>
                <button type="button" @click="addQuestion()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Pertanyaan
                </button>
            </div>

            <template x-for="(question, qi) in questions" :key="qi">
                <div class="rounded-2xl p-5 space-y-4 shadow-sm"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    {{-- Question Header --}}
                    <div class="flex items-center justify-between pb-3"
                         style="border-bottom: 1px solid var(--dash-card-border);">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold text-white"
                                  style="background-color: var(--dash-primary);"
                                  x-text="qi + 1"></span>
                            <span class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">
                                Pertanyaan <span x-text="qi + 1"></span>
                            </span>
                        </div>
                        <button type="button" @click="removeQuestion(qi)" x-show="questions.length > 1"
                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-danger, #dc2626);">
                            <span class="material-symbols-outlined text-[14px]">delete</span>
                            Hapus Soal
                        </button>
                    </div>

                    {{-- Question Text --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Teks Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" :name="`questions[${qi}][question_text]`" x-model="question.question_text" required maxlength="500"
                               placeholder="Tuliskan pertanyaan survei..."
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>

                    {{-- Question Type --}}
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Tipe Jawaban <span class="text-red-500">*</span>
                        </label>
                        <select :name="`questions[${qi}][question_type]`" x-model="question.question_type" @change="resetQuestionFields(qi)" required
                                class="w-full sm:max-w-xs px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="single_choice">Pilihan Tunggal</option>
                            <option value="multiple_choice">Pilihan Ganda</option>
                            <option value="scale">Skala</option>
                            <option value="text">Teks Bebas</option>
                        </select>
                    </div>

                    {{-- Scale Options --}}
                    <div x-show="typeHasScale(question.question_type)" x-transition
                         class="p-4 rounded-xl space-y-3"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Konfigurasi Skala</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Nilai Min</label>
                                <input type="number" :name="`questions[${qi}][scale_min]`" x-model.number="question.scale_min" min="0"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Nilai Max</label>
                                <input type="number" :name="`questions[${qi}][scale_max]`" x-model.number="question.scale_max" min="1"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Label Min</label>
                                <input type="text" :name="`questions[${qi}][scale_min_label]`" x-model="question.scale_min_label" maxlength="100"
                                       placeholder="Sangat Buruk"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Label Max</label>
                                <input type="text" :name="`questions[${qi}][scale_max_label]`" x-model="question.scale_max_label" maxlength="100"
                                       placeholder="Sangat Baik"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'">
                            </div>
                        </div>
                    </div>

                    {{-- Choices Options --}}
                    <div x-show="typeHasChoices(question.question_type)" x-transition class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium" style="color: var(--dash-text-body);">Pilihan Jawaban</label>
                            <button type="button" @click="addChoice(qi)"
                                    class="inline-flex items-center gap-1 text-xs font-semibold transition-colors"
                                    style="color: var(--dash-primary-text);">
                                <span class="material-symbols-outlined text-[16px]">add_circle</span>
                                Tambah Pilihan
                            </button>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(choice, ci) in question.choices" :key="ci">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md text-xs font-bold shrink-0"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);"
                                          x-text="String.fromCharCode(65 + ci) + '.'"></span>
                                    <input type="text" :name="`questions[${qi}][choices][${ci}][choice_text]`" x-model="choice.choice_text" required
                                           placeholder="Teks opsi pilihan..."
                                           class="flex-1 px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                                    <button type="button" @click="removeChoice(qi, ci)" x-show="question.choices.length > 1"
                                            class="p-1 rounded-lg text-red-500 hover:text-red-700 transition-colors shrink-0">
                                        <span class="material-symbols-outlined text-[18px]">close</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        @error('questions')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        {{-- Form Actions --}}
        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--dash-card-border);">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Simpan Survey
            </button>
            <a href="{{ route('surveys.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">close</span>
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
