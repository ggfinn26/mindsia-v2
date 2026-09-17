@extends('layouts.app')

@section('content')
<div class="bg-indigo-600 pb-12 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white tracking-tight sm:text-5xl">Uji Kemampuan Bahasa Inggris Anda</h1>
        <p class="mt-4 max-w-2xl text-xl text-indigo-100 mx-auto">Ikuti Online TOEFL Test dari MINDSIA secara gratis untuk mengetahui skor awal Anda.</p>
        
        <div class="mt-10">
            <a href="{{ route('toefl.guest.entry') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-indigo-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Mulai Tes Sekarang
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
        <div>
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                <span class="material-symbols-outlined">headphones</span>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Listening Comprehension</h3>
            <p class="mt-2 text-sm text-gray-500">Uji kemampuan mendengarkan percakapan bahasa Inggris.</p>
        </div>
        <div>
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                <span class="material-symbols-outlined">spellcheck</span>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Structure & Written Expression</h3>
            <p class="mt-2 text-sm text-gray-500">Evaluasi pemahaman tata bahasa dan struktur kalimat.</p>
        </div>
        <div>
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                <span class="material-symbols-outlined">menu_book</span>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Reading Comprehension</h3>
            <p class="mt-2 text-sm text-gray-500">Ukur kemampuan membaca dan memahami teks bahasa Inggris.</p>
        </div>
    </div>
</div>
@endsection
