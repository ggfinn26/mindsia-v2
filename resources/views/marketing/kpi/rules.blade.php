@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Aturan & Bobot Ranking (MPI)</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">Form Pengaturan Bobot</h2>
            <!-- Config form -->
            <p class="text-sm text-gray-500">Konfigurasi bobot penilaian (Classes, Revenue, dll) untuk menghitung skor MPI.</p>
        </div>
    </div>
</div>
@endsection
