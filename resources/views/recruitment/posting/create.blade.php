@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Buat Job Posting</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="#" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium mb-1">Pilih Permintaan Pekerjaan (Opsional)</label>
                <select name="job_permintaan_id" class="form-select w-full">
                    <option value="">Tidak terikat permintaan khusus</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Judul Posting <span class="text-red-500">*</span></label>
                <input name="title" type="text" class="form-input w-full" required />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" class="form-textarea w-full" required></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Syarat / Kualifikasi (Otomatis)</label>
                <textarea name="job_requirements_auto" rows="3" class="form-textarea w-full" placeholder="Kualifikasi yang di-filter otomatis oleh sistem"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Syarat / Kualifikasi (Manual)</label>
                <textarea name="job_requirements_manual" rows="3" class="form-textarea w-full" placeholder="Kualifikasi yang dicek manual oleh HR"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Mulai Publikasi <span class="text-red-500">*</span></label>
                    <input name="publish_date" type="date" class="form-input w-full" required />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Penutupan <span class="text-red-500">*</span></label>
                    <input name="closing_date" type="date" class="form-input w-full" required />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('recruitment.postings.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 mr-3">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Simpan Posting</button>
            </div>
        </form>
    </div>
</div>
@endsection
