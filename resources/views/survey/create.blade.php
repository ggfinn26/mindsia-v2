@extends('layouts.dashboard')

@section('title', 'Buat Survey')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8"
    x-data="{
        questions: [{ question_text: '', question_type: 'single_choice', scale_min: 1, scale_max: 5, scale_min_label: '', scale_max_label: '', choices: [{ choice_text: '' }, { choice_text: '' }] }],
        addQuestion() {
            this.questions.push({ question_text: '', question_type: 'single_choice', scale_min: 1, scale_max: 5, scale_min_label: '', scale_max_label: '', choices: [{ choice_text: '' }, { choice_text: '' }] });
        },
        removeQuestion(i) {
            if (this.questions.length > 1) this.questions.splice(i, 1);
        },
        addChoice(qi) {
            this.questions[qi].choices.push({ choice_text: '' });
        },
        removeChoice(qi, ci) {
            if (this.questions[qi].choices.length > 1) this.questions[qi].choices.splice(ci, 1);
        },
        typeHasChoices(type) {
            return type === 'single_choice' || type === 'multiple_choice';
        },
        typeHasScale(type) {
            return type === 'scale';
        },
        resetQuestionFields(qi) {
            const q = this.questions[qi];
            if (!this.typeHasChoices(q.question_type)) q.choices = [];
            if (!this.typeHasScale(q.question_type)) { q.scale_min = 1; q.scale_max = 5; q.scale_min_label = ''; q.scale_max_label = ''; }
            if (this.typeHasChoices(q.question_type) && q.choices.length === 0) q.choices = [{ choice_text: '' }, { choice_text: '' }];
        }
    }">

    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('surveys.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Daftar Survey
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Buat Survey Baru</h1>
        </div>
        <a href="{{ route('surveys.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium border border-gray-300">Batal</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('surveys.store') }}" method="POST">
        @csrf

        <!-- Survey Info -->
        <div class="bg-white shadow sm:rounded-lg divide-y divide-gray-200 mb-6">
            <div class="px-6 py-5">
                <h3 class="text-base font-medium text-gray-900 mb-4">Informasi Survey</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Nama Survey <span class="text-red-500">*</span></label>
                        <input type="text" name="survey_name" value="{{ old('survey_name') }}" required maxlength="255" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('survey_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Deskripsi <span class="text-gray-400">(opsional)</span></label>
                        <textarea name="survey_description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('survey_description') }}</textarea>
                        @error('survey_description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Batas Waktu <span class="text-gray-400">(opsional)</span></label>
                        <input type="datetime-local" name="deadline_at" value="{{ old('deadline_at') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('deadline_at')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions -->
        <div class="space-y-4 mb-6">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-medium text-gray-900">Pertanyaan</h3>
                <button type="button" @click="addQuestion()" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    <span class="material-symbols-outlined text-[18px]">add</span> Tambah Pertanyaan
                </button>
            </div>

            <template x-for="(question, qi) in questions" :key="qi">
                <div class="bg-white shadow sm:rounded-lg border border-gray-200">
                    <div class="px-6 py-4">
                        <!-- Question Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-semibold text-gray-800">
                                Pertanyaan <span x-text="qi + 1"></span>
                            </h4>
                            <button type="button" @click="removeQuestion(qi)" x-show="questions.length > 1" class="text-red-500 hover:text-red-700">
                                <span class="material-symbols-outlined text-xl">delete</span>
                            </button>
                        </div>

                        <!-- Question Text -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Teks Pertanyaan <span class="text-red-500">*</span></label>
                            <input type="text" :name="`questions[${qi}][question_text]`" x-model="question.question_text" required maxlength="500" placeholder="Masukkan pertanyaan..." class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Question Type -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Tipe Jawaban <span class="text-red-500">*</span></label>
                            <select :name="`questions[${qi}][question_type]`" x-model="question.question_type" @change="resetQuestionFields(qi)" required class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="single_choice">Pilihan Tunggal</option>
                                <option value="multiple_choice">Pilihan Ganda</option>
                                <option value="scale">Skala</option>
                                <option value="text">Teks Bebas</option>
                            </select>
                        </div>

                        <!-- Scale Options -->
                        <div x-show="typeHasScale(question.question_type)" x-transition class="mb-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nilai Min</label>
                                <input type="number" :name="`questions[${qi}][scale_min]`" x-model.number="question.scale_min" min="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nilai Max</label>
                                <input type="number" :name="`questions[${qi}][scale_max]`" x-model.number="question.scale_max" min="1" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Label Min</label>
                                <input type="text" :name="`questions[${qi}][scale_min_label]`" x-model="question.scale_min_label" maxlength="100" placeholder="Sangat Buruk" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Label Max</label>
                                <input type="text" :name="`questions[${qi}][scale_max_label]`" x-model="question.scale_max_label" maxlength="100" placeholder="Sangat Baik" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- Choices -->
                        <div x-show="typeHasChoices(question.question_type)" x-transition>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-medium text-gray-700">Pilihan Jawaban</label>
                                <button type="button" @click="addChoice(qi)" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">+ Tambah Pilihan</button>
                            </div>
                            <div class="space-y-2">
                                <template x-for="(choice, ci) in question.choices" :key="ci">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-medium text-gray-400 w-5" x-text="String.fromCharCode(65 + ci) + '.'"></span>
                                        <input type="text" :name="`questions[${qi}][choices][${ci}][choice_text]`" x-model="choice.choice_text" required placeholder="Teks pilihan..." class="flex-1 border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <button type="button" @click="removeChoice(qi, ci)" x-show="question.choices.length > 1" class="text-red-500 hover:text-red-700 flex-shrink-0">
                                            <span class="material-symbols-outlined text-lg">close</span>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        @error('questions')
            <p class="text-red-600 text-sm mb-4">{{ $message }}</p>
        @enderror

        <!-- Submit -->
        <div class="flex gap-3">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-5 rounded-md text-sm">
                <span class="material-symbols-outlined text-[18px]">save</span> Simpan Survey
            </button>
            <a href="{{ route('surveys.index') }}" class="inline-flex min-h-10 items-center border border-gray-300 px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50 rounded-md">Batal</a>
        </div>
    </form>
</div>
@endsection
