@extends('layouts.dashboard')

@section('title', 'Tambah Test — ' . $classroom->class_name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('classrooms.show', $classroom) }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Tambah Test</h1>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Kelas: <span class="font-medium text-[var(--dash-text-heading)]">{{ $classroom->class_name }}</span></p>
        </div>
    </div>

    @if ($errors->any())
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #fecaca; background-color: #fef2f2; color: #991b1b;">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('class-tests.store', $classroom) }}" method="POST" class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-2xl overflow-hidden">
        @csrf
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Nama Test <span class="text-red-500">*</span></label>
                <input type="text" name="test_name" required
                       class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                       value="{{ old('test_name') }}" placeholder="Contoh: Pre-Test TOEFL Batch 5" />
                @error('test_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Tipe Test <span class="text-red-500">*</span></label>
                    <select name="test_type" required
                            class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]">
                        <option value="">Pilih tipe...</option>
                        <option value="pre_test" {{ old('test_type') === 'pre_test' ? 'selected' : '' }}>Pre-Test</option>
                        <option value="post_test" {{ old('test_type') === 'post_test' ? 'selected' : '' }}>Post-Test</option>
                    </select>
                    @error('test_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Tanggal Test <span class="text-red-500">*</span></label>
                    <input type="date" name="date" required
                           class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                           value="{{ old('date') }}" />
                    @error('date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                          placeholder="Deskripsi singkat tentang test ini">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Scoring Criteria -->
            <div class="border-t border-[var(--dash-card-border)] pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold text-[var(--dash-text-heading)]">Kriteria Penilaian</h3>
                    <button type="button" x-data x-on:click="addCriterion()"
                            class="bg-[var(--dash-input-bg)] hover:opacity-90 text-[var(--dash-text-body)] px-3 py-1.5 rounded text-sm font-medium inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        Tambah Kriteria
                    </button>
                </div>
                <p class="text-sm text-[var(--dash-text-muted)] mb-4">Tambahkan kriteria penilaian yang relevan untuk test ini.</p>

                <div id="criteria-container" x-data="{ criteria: {{ old('criteria') ? json_encode(old('criteria')) : '[]' }} }">
                    <template x-for="(criterion, index) in criteria" :key="index">
                        <div class="bg-[var(--dash-page-bg)] border border-[var(--dash-card-border)] rounded-lg p-4 mb-3">
                            <div class="flex items-start gap-3">
                                <div class="flex-1 space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Nama Kriteria <span class="text-red-500">*</span></label>
                                        <input type="text" :name="'criteria[' + index + '][criteria_name]'"
                                               x-model="criterion.criteria_name" required
                                               class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                                               placeholder="Contoh: Grammar, Vocabulary" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Deskripsi</label>
                                        <textarea :name="'criteria[' + index + '][description]'"
                                                  x-model="criterion.description" rows="2"
                                                  class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
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

                    <div x-show="criteria.length === 0" class="text-center text-[var(--dash-text-faint)] text-sm py-4 border border-dashed border-[var(--dash-input-border)] rounded-lg">
                        Belum ada kriteria. Klik "Tambah Kriteria" untuk menambahkan.
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-[var(--dash-page-bg)] border-t border-[var(--dash-card-border)] flex gap-3 justify-end">
            <a href="{{ route('classrooms.show', $classroom) }}" class="px-6 py-2 text-[var(--dash-text-body)] border border-[var(--dash-input-border)] rounded-lg hover:bg-[var(--dash-page-bg)] inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</a>
            <button type="submit" class="px-6 py-2 bg-[var(--dash-primary)] text-white rounded-lg hover:opacity-90 inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">save</span>Simpan Test</button>
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
