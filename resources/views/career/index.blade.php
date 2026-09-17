@extends('layouts.app')

@section('content')
<div class="bg-indigo-600 pb-12 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white tracking-tight sm:text-5xl">Karir Bersama Kami</h1>
        <p class="mt-4 max-w-2xl text-xl text-indigo-100 mx-auto">Mari bergabung dan kembangkan potensi Anda bersama MINDSIA.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Job Card Placeholder -->
        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100 hover:shadow-md transition">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Tutor Bahasa Inggris</h3>
                <p class="text-sm text-gray-500 mb-4 line-clamp-3">Membantu member memahami materi TOEFL dengan metode interaktif...</p>
                <div class="flex justify-between items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Full Time</span>
                    <a href="{{ route('career.show') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Lihat Detail &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
