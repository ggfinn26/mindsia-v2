@extends('layouts.dashboard')

@section('title', 'Tambah Test — ' . $classroom->class_name)

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Test</h1>
        <a href="{{ route('classrooms.show', $classroom) }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">
            Kembali
        </a>
    </div>

    <p class="text-sm text-gray-500 mb-6">Kelas: <span class="font-medium text-gray-700">{{ $classroom->class_name }}</span></p>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('class-tests.store', $classroom) }}" method="POST" class="bg-white shadow sm:rounded-lg">
        @csrf
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Test <span class="text-red-500">*</span></label>
                <input type="text" name="test_name" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       value="{{ old('test_name') }}" placeholder="Contoh: Pre-Test TOEFL Batch 5" />
                @error('test_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Test <span class="text-red-500">*</span></label>
                    <select name="test_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Tipe</option>
                        <option value="pre_test" {{ old('test_type') === 'pre_test' ? 'selected' : '' }}>Pre-Test</option>
                        <option value="post_test" {{ old('test_type') === 'post_test' ? 'selected' : '' }}>Post-Test</option>
                    </select>
                    @error('test_type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Test <span class="text-red-500">*</span></label>
                    <input type="date" name="date" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           value="{{ old('date') }}" />
                    @error('date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="Deskripsi singkat tentang test ini">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Scoring Criteria -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold text-gray-900">Kriteria Penilaian</h3>
                    <button type="button" x-data x-on:click="addCriterion()"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-sm font-medium">
                        + Tambah Kriteria
                    </button>
                </div>
                <p class="text-sm text-gray-500 mb-4">Tambahkan kriteria penilaian yang relevan untuk test ini.</p>

                <div id="criteria-container" x-data="{ criteria: {{ old('criteria') ? json_encode(old('criteria')) : '[]' }} }">
                    <template x-for="(criterion, index) in criteria" :key="index">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-3">
                            <div class="flex items-start gap-3">
                                <div class="flex-1 space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kriteria <span class="text-red-500">*</span></label>
                                        <input type="text" :name="'criteria[' + index + '][criteria_name]'"
                                               x-model="criterion.criteria_name" required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                               placeholder="Contoh: Grammar, Vocabulary" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                        <textarea :name="'criteria[' + index + '][description]'"
                                                  x-model="criterion.description" rows="2"
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                  placeholder="Penjelasan singkat kriteria ini"></textarea>
                                    </div>
                                </div>
                                <button type="button" x-on:click="criteria.splice(index, 1)"
                                        class="text-red-500 hover:text-red-700 mt-1" title="Hapus kriteria">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="criteria.length === 0" class="text-center text-gray-400 text-sm py-4 border border-dashed border-gray-300 rounded-lg">
                        Belum ada kriteria. Klik "Tambah Kriteria" untuk menambahkan.
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3 justify-end">
            <a href="{{ route('classrooms.show', $classroom) }}" class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Test</button>
        </div>
    </form>
</div>

<script>
    function addCriterion() {
        const container = document.getElementById('criteria-container');
        const alpineData = Alpine.$data(container);
        alpineData.criteria.push({ criteria_name: '', description: '' });
    }
</script>
@endsection
