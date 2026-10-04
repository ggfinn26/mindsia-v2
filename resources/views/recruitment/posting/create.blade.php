@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto"
    x-data="{
        manualItems: {{ json_encode(old('requirements_manual', []) ) }},
        addItem() { this.manualItems.push({criteria_name:'',criteria_desc:''}) },
        removeItem(i) { this.manualItems.splice(i,1) }
    }">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('recruitment.postings.index') }}" class="text-gray-400 hover:text-gray-600">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Buat Job Posting</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Dari permintaan: <span class="font-medium">{{ $permintaan->detail?->position?->position_name ?? '-' }}</span>
                — {{ $permintaan->branch?->branch_name ?? '-' }}
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('recruitment.postings.store') }}" method="POST" class="space-y-5">
        @csrf
        <input type="hidden" name="job_permintaan_id" value="{{ $permintaan->id }}">
        <input type="hidden" name="position_id" value="{{ $permintaan->detail?->position_id }}">

        {{-- Informasi Posting --}}
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 space-y-5">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Informasi Posting</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Judul Posting <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}"
                    class="form-input w-full" placeholder="Contoh: Lowongan Person In Charge Cabang Cilegon" required />
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi Pekerjaan <span class="text-red-500">*</span>
                </label>
                <textarea name="job_description" rows="4" class="form-textarea w-full"
                    placeholder="Gambaran umum peran dan lingkungan kerja...">{{ old('job_description') }}</textarea>
                @error('job_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggung Jawab <span class="text-red-500">*</span>
                </label>
                <textarea name="job_responsibilities" rows="4" class="form-textarea w-full"
                    placeholder="Daftar tanggung jawab utama posisi ini...">{{ old('job_responsibilities') }}</textarea>
                @error('job_responsibilities') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kualifikasi (teks publik) <span class="text-red-500">*</span>
                </label>
                <textarea name="job_requirements_text" rows="4" class="form-textarea w-full"
                    placeholder="Kualifikasi yang ditampilkan di portal karir publik...">{{ old('job_requirements_text') }}</textarea>
                @error('job_requirements_text') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Penutupan (opsional)
                </label>
                <input type="date" name="closing_date" value="{{ old('closing_date') }}"
                    class="form-input w-full" />
                <p class="mt-1 text-xs text-gray-500">Kosongkan untuk lowongan tanpa batas waktu.</p>
                @error('closing_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Requirements Auto (kriteria filter sistem) --}}
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 space-y-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Kriteria Otomatis</h2>
                <p class="text-xs text-gray-500 mt-1">Dipakai sistem untuk menandai label "tidak memenuhi syarat" di portal pelamar.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usia Minimum</label>
                    <input type="number" name="requirements_auto[minimum_age]" min="15" max="99"
                        value="{{ old('requirements_auto.minimum_age', $permintaan->requirementsAuto?->minimum_age) }}"
                        class="form-input w-full" placeholder="e.g. 21" />
                    @error('requirements_auto.minimum_age') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usia Maksimum</label>
                    <input type="number" name="requirements_auto[maximum_age]" min="15" max="99"
                        value="{{ old('requirements_auto.maximum_age', $permintaan->requirementsAuto?->maximum_age) }}"
                        class="form-input w-full" placeholder="e.g. 35" />
                    @error('requirements_auto.maximum_age') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min. Pengalaman (tahun)</label>
                    <input type="number" name="requirements_auto[minimum_year_experience]" min="0"
                        value="{{ old('requirements_auto.minimum_year_experience', $permintaan->requirementsAuto?->minimum_year_experience) }}"
                        class="form-input w-full" placeholder="e.g. 2" />
                    @error('requirements_auto.minimum_year_experience') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min. Pendidikan</label>
                    <select name="requirements_auto[minimum_education_level]" class="form-select w-full">
                        <option value="">Tidak ada minimum</option>
                        @foreach(['sd','smp','sma','d1','d2','d3','d4','s1','s2','s3'] as $level)
                            <option value="{{ $level }}"
                                {{ old('requirements_auto.minimum_education_level', $permintaan->requirementsAuto?->minimum_education_level) === $level ? 'selected' : '' }}>
                                {{ strtoupper($level) }}
                            </option>
                        @endforeach
                    </select>
                    @error('requirements_auto.minimum_education_level') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Requirements Manual (kriteria dicek manual HR) --}}
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Kriteria Manual</h2>
                    <p class="text-xs text-gray-500 mt-1">Dicek manual oleh HR saat screening, bukan filter otomatis.</p>
                </div>
                <button type="button" @click="addItem()"
                    class="btn bg-white border-gray-300 hover:border-gray-400 text-gray-600 text-xs py-1">
                    + Tambah Kriteria
                </button>
            </div>

            @if($permintaan->requirementsManual->count())
                <div class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-md p-2">
                    Permintaan ini sudah punya {{ $permintaan->requirementsManual->count() }} kriteria manual. Tambahan baru akan ditambahkan, bukan menggantikan.
                </div>
            @endif

            <template x-for="(item, i) in manualItems" :key="i">
                <div class="flex gap-3 items-start">
                    <div class="flex-1 space-y-2">
                        <input type="text" :name="`requirements_manual[${i}][criteria_name]`"
                            x-model="item.criteria_name"
                            class="form-input w-full text-sm" placeholder="Nama kriteria (maks 100 karakter)" />
                        <textarea :name="`requirements_manual[${i}][criteria_desc]`"
                            x-model="item.criteria_desc"
                            rows="2" class="form-textarea w-full text-sm" placeholder="Deskripsi kriteria..."></textarea>
                    </div>
                    <button type="button" @click="removeItem(i)" class="mt-1 text-red-400 hover:text-red-600">
                        <span class="material-symbols-outlined text-xl">delete</span>
                    </button>
                </div>
            </template>

            <template x-if="manualItems.length === 0">
                <p class="text-sm text-gray-400 text-center py-3">Belum ada kriteria manual. Klik "+ Tambah Kriteria" untuk menambah.</p>
            </template>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('recruitment.postings.index') }}"
                class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Batal</a>
            <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                Simpan sebagai Draft
            </button>
        </div>
    </form>
</div>
@endsection
