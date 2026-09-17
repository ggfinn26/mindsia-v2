@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-3xl">
        <div class="bg-white py-8 px-6 shadow sm:rounded-lg sm:px-10 text-center">
            <div class="mb-6 flex justify-center">
                <span class="material-symbols-outlined text-green-500 text-6xl">check_circle</span>
            </div>
            
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Ujian Selesai!</h2>
            <p class="text-gray-600 mb-8">Terima kasih, Anda telah menyelesaikan tes TOEFL.</p>
            
            <div class="bg-gray-50 rounded-lg p-6 mb-8 border border-gray-200">
                <h3 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">Hasil Skor Anda</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold">Listening</p>
                        <p class="text-3xl font-bold text-indigo-600">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold">Structure</p>
                        <p class="text-3xl font-bold text-indigo-600">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold">Reading</p>
                        <p class="text-3xl font-bold text-indigo-600">-</p>
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500 uppercase font-semibold">Total Score</p>
                    <p class="text-5xl font-extrabold text-gray-900">-</p>
                </div>
            </div>
            
            <div class="flex justify-center gap-4">
                <a href="{{ route('dashboard') }}" class="btn bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-medium">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
