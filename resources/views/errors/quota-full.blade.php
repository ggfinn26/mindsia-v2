@extends('layouts.dashboard')

@section('title', 'Kuota Habis - MINDSIA')
@section('header_title', 'Registrasi Ditutup')

@section('content')
<div class="max-w-3xl mx-auto py-12">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 flex flex-col items-center justify-center text-center">
        <div class="h-24 w-24 rounded-full bg-red-50 flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-red-500 text-5xl">event_busy</span>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 font-jakarta mb-4">Kuota Sudah Habis!</h2>
        
        <p class="text-gray-600 mb-2">
            Waduh, sepertinya Anda kurang cepat. Kuota untuk program ini di cabang Anda sudah terisi penuh.
        </p>
        
        <div class="bg-gray-50 border border-gray-100 rounded-lg p-5 my-6 max-w-lg w-full">
            <p class="text-gray-800 font-medium italic">
                "You're too late! It's like saying <strong>'I am eating'</strong> when you meant to say <strong>'I have eaten'</strong>. The moment is gone!"
            </p>
        </div>
        
        <p class="text-sm text-gray-500 mb-8">
            Jangan khawatir, tunggu pembukaan kuota (batch) selanjutnya ya, atau coba cek program lainnya!
        </p>
        
        <a href="{{ route('member.program.browse') }}" class="px-6 py-3 bg-[#5586DB] text-white font-medium rounded-lg hover:bg-blue-600 transition-colors shadow-sm">
            Kembali ke Katalog
        </a>
    </div>
</div>
@endsection
