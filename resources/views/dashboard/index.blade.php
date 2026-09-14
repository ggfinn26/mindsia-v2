@extends('layouts.dashboard')

@section('title', 'Overview - MINDSIA')
@section('header_title', 'Overview')

@section('content')
    <div class="mb-6">
        <p class="text-gray-500 font-medium text-sm sm:text-base">Selamat datang kembali, <span class="font-semibold text-gray-800">{{ Auth::user()->name ?? 'Pengguna' }}</span> 👋</p>
    </div>

    <div class="flex flex-col items-center justify-center min-h-[60vh] h-full text-center">
        <div class="h-20 w-20 rounded-full bg-[#EEF4FF] flex items-center justify-center mb-5">
            <span class="material-symbols-outlined text-[#5586DB] text-4xl">dashboard_customize</span>
        </div>
        <h2 class="text-xl font-bold font-jakarta text-gray-800 mb-3">Ruang Kerja Anda</h2>
        <p class="text-sm text-gray-500 leading-relaxed max-w-sm">Widget dan analitik akan ditampilkan di sini berdasarkan peran dan divisi Anda.</p>
    </div>
@endsection
