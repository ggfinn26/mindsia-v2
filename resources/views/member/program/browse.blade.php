@extends('layouts.dashboard')

@section('title', 'Daftar Program - MINDSIA')
@section('header_title', 'Katalog Program')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold font-jakarta text-gray-900">Katalog Program</h2>
            <p class="text-sm text-gray-500 mt-1">Jelajahi dan daftar program-program unggulan di MINDSIA.</p>
        </div>
    </div>

    @if($programs->isEmpty())
    <!-- Placeholder State -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 flex flex-col items-center justify-center text-center">
        <div class="h-20 w-20 rounded-full bg-blue-50 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[#5586DB] text-4xl">travel_explore</span>
        </div>
        <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-2">Katalog Sedang Diperbarui</h3>
        <p class="text-gray-500 max-w-md mx-auto mb-6">Kami sedang mempersiapkan daftar program terbaru untuk Anda. Silakan kembali lagi nanti atau hubungi tim administrasi kami.</p>
        <a href="{{ route('member.dashboard') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
            Kembali ke Dashboard
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($programs as $program)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                <div class="h-48 w-full bg-gray-100 relative">
                    <img src="{{ $program->image_url }}" alt="{{ $program->program_name }}" class="w-full h-full object-cover">
                </div>
                <div class="p-6 flex-grow flex flex-col">
                    <div class="text-xs font-bold text-[#5586DB] uppercase tracking-wider mb-2">{{ $program->program_code }}</div>
                    <h3 class="text-xl font-bold text-gray-900 font-jakarta mb-2">{{ $program->program_name }}</h3>
                    <p class="text-sm text-gray-500 mb-6 flex-grow">{{ $program->tagline ?? 'Program intensif dengan metode pengajaran berstandar internasional.' }}</p>
                    
                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100">
                        <span class="text-lg font-bold text-gray-900">Rp {{ number_format($program->program_price, 0, ',', '.') }}</span>
                        <a href="{{ route('member.program.register', $program->id) }}" class="px-4 py-2 bg-[#5586DB] text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                            Daftar
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
