@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Edit Profil Pelamar</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="#" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input name="name" type="text" class="form-input w-full" value="{{ auth()->user()->name }}" required />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Telepon / WhatsApp</label>
                <input name="phone" type="text" class="form-input w-full" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Upload CV (PDF)</label>
                <input type="file" name="cv_file" accept=".pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('applicant.profile.show') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 mr-3">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Simpan Profil</button>
            </div>
        </form>
    </div>
</div>
@endsection
