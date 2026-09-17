@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Edit Paket Soal TOEFL</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="#" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium mb-1">Judul Paket Ujian <span class="text-red-500">*</span></label>
                <input name="title" type="text" class="form-input w-full" required />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Deskripsi / Petunjuk Singkat</label>
                <textarea name="description" rows="3" class="form-textarea w-full"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Durasi Listening (menit)</label>
                    <input name="duration_listening" type="number" class="form-input w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Durasi Structure (menit)</label>
                    <input name="duration_structure" type="number" class="form-input w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Durasi Reading (menit)</label>
                    <input name="duration_reading" type="number" class="form-input w-full" />
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('toefl.test.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 mr-3">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
