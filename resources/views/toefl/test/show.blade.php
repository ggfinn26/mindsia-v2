@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Paket Soal TOEFL</h1>
        <a href="{{ route('toefl.test.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Judul Ujian</h3>
                <p class="text-gray-900 font-medium text-lg">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Durasi</h3>
                <p class="text-gray-900 font-medium">- Menit</p>
            </div>
        </div>
    </div>

    <!-- Management Soal (Listening, Structure, Reading) Placeholder -->
    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="border-b border-gray-200 p-4 flex justify-between items-center">
            <h2 class="font-bold text-gray-800">Daftar Pertanyaan (Sections)</h2>
            <button class="btn btn-sm bg-indigo-500 hover:bg-indigo-600 text-white">Tambah Pertanyaan</button>
        </div>
        <div class="p-6">
            <p class="text-gray-500 text-sm text-center py-4">Belum ada soal ditambahkan.</p>
        </div>
    </div>
</div>
@endsection
