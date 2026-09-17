@extends('layouts.app')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('career.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                <span class="material-symbols-outlined mr-1 text-sm">arrow_back</span> Kembali ke daftar lowongan
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-start">
                <div>
                    <h3 class="text-2xl leading-6 font-bold text-gray-900">Tutor Bahasa Inggris</h3>
                    <p class="mt-2 max-w-2xl text-sm text-gray-500">Dipublikasikan pada: -</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Full Time</span>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6 space-y-6">
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Deskripsi Pekerjaan</h4>
                    <div class="text-gray-700 text-sm whitespace-pre-wrap">Deskripsi pekerjaan akan ditampilkan di sini.</div>
                </div>
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Persyaratan</h4>
                    <div class="text-gray-700 text-sm whitespace-pre-wrap">Syarat dan kualifikasi akan ditampilkan di sini.</div>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <button class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Lamar Pekerjaan Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
