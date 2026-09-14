@extends('layouts.dashboard')

@section('title', 'Detail Rekap Absensi')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Detail Rekap Absensi #{{ $id }}</h1>
        <a href="{{ route('attendance-recaps.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium border border-gray-300">Kembali</a>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <span class="material-symbols-outlined text-blue-500 mr-3">info</span>
            <p class="text-sm text-blue-700">Detail rekap absensi sedang dalam pengembangan.</p>
        </div>
    </div>
</div>
@endsection
