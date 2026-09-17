@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Isi Survey</h1>
        <a href="{{ route('employee.surveys.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Judul Survey Placeholder</h2>
            <p class="text-gray-600">Deskripsi survey akan ditampilkan di sini.</p>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <form action="#" method="POST">
                @csrf
                <div class="space-y-6">
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded">
                        <p class="font-medium mb-3">1. Pertanyaan contoh placeholder?</p>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="q1" value="a" class="form-radio text-indigo-500">
                                <span class="ml-2">Pilihan A</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="q1" value="b" class="form-radio text-indigo-500">
                                <span class="ml-2">Pilihan B</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Submit Jawaban</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
