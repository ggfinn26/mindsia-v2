@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Prestasi & Performa Saya</h1>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Ranking -->
        <div class="flex flex-col bg-white shadow-lg rounded-sm border border-gray-200 p-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-2">Ranking (Bulan Ini)</h2>
            <div class="flex items-start">
                <div class="text-3xl font-bold text-gray-800 mr-2">-</div>
            </div>
        </div>
        <!-- MPI Score -->
        <div class="flex flex-col bg-white shadow-lg rounded-sm border border-gray-200 p-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-2">Skor MPI</h2>
            <div class="flex items-start">
                <div class="text-3xl font-bold text-gray-800 mr-2">0</div>
            </div>
        </div>
        <!-- Classes -->
        <div class="flex flex-col bg-white shadow-lg rounded-sm border border-gray-200 p-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-2">Total Kelas</h2>
            <div class="flex items-start">
                <div class="text-3xl font-bold text-gray-800 mr-2">0</div>
            </div>
        </div>
        <!-- Revenue -->
        <div class="flex flex-col bg-white shadow-lg rounded-sm border border-gray-200 p-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-2">Total Omzet</h2>
            <div class="flex items-start">
                <div class="text-3xl font-bold text-gray-800 mr-2">Rp 0</div>
            </div>
        </div>
    </div>
</div>
@endsection
