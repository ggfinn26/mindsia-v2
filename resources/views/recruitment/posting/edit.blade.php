@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('recruitment.postings.show', $jobPosting) }}" class="text-[color:var(--dash-text-faint)] hover:text-[color:var(--dash-text-body)]">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Edit Job Posting</h1>
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

    <form action="{{ route('recruitment.postings.update', $jobPosting) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-6 space-y-5">
            <h2 class="text-sm font-semibold text-[color:var(--dash-text-body)] uppercase tracking-wider">Informasi Posting</h2>

            <div>
                <label class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">
                    Judul Posting <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title"
                    value="{{ old('title', $jobPosting->title) }}"
                    class="form-input w-full" required />
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">
                    Deskripsi Pekerjaan <span class="text-red-500">*</span>
                </label>
                <textarea name="job_description" rows="4" class="form-textarea w-full">{{ old('job_description', $jobPosting->job_description) }}</textarea>
                @error('job_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">
                    Tanggung Jawab <span class="text-red-500">*</span>
                </label>
                <textarea name="job_responsibilities" rows="4" class="form-textarea w-full">{{ old('job_responsibilities', $jobPosting->job_responsibilities) }}</textarea>
                @error('job_responsibilities') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">
                    Kualifikasi (teks publik) <span class="text-red-500">*</span>
                </label>
                <textarea name="job_requirements_text" rows="4" class="form-textarea w-full">{{ old('job_requirements_text', $jobPosting->job_requirements_text) }}</textarea>
                @error('job_requirements_text') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">
                    Tanggal Penutupan (opsional)
                </label>
                <input type="date" name="closing_date"
                    value="{{ old('closing_date', $jobPosting->closing_date?->format('Y-m-d')) }}"
                    class="form-input w-full" />
                @error('closing_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('recruitment.postings.show', $jobPosting) }}"
                class="btn bg-[var(--dash-card-bg)] border-[color:var(--dash-card-border)] hover:border-[color:var(--dash-input-border)] text-[color:var(--dash-text-body)]">Batal</a>
            <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
