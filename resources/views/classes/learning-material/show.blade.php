@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Materi Pembelajaran</h1>
        <a href="{{ route('classes.learning-material.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Judul Materi</h3>
            <p class="text-gray-900 font-medium text-lg">-</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Program / Kelas</h3>
                <p class="text-gray-900 font-medium">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Diunggah Oleh</h3>
                <p class="text-gray-900 font-medium">-</p>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Deskripsi</h3>
            <div class="text-gray-900 p-4 bg-gray-50 rounded-md border border-gray-100 min-h-[100px] whitespace-pre-wrap">-</div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end">
            <a href="#" class="btn bg-indigo-500 hover:bg-indigo-600 text-white flex items-center">
                <span class="material-symbols-outlined mr-2">download</span> Download / Buka File
            </a>
        </div>
    </div>
</div>
@endsection
