@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Aplikasi Lowongan</h1>
        <a href="{{ route('recruitment.application.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Detail Pelamar -->
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 md:col-span-1">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Profil Pelamar</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Nama Lengkap</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Email</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Telepon/WA</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Dokumen CV</h3>
                    <p class="text-gray-900 font-medium"><a href="#" class="text-indigo-600 hover:underline">Download CV</a></p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 md:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Progres & Tahapan Recruitment</h2>
            
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-500 mb-1">Posisi yang Dilamar</h3>
                <p class="text-gray-900 font-medium text-lg">-</p>
            </div>

            <!-- Stages Placeholder -->
            <div class="relative border-l-2 border-indigo-200 ml-3">
                <!-- Administrative / CV Review -->
                <div class="mb-8 ml-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-indigo-500 rounded-full -left-3 ring-4 ring-white"></span>
                    <h3 class="font-bold text-gray-800">Seleksi Administrasi (CV)</h3>
                    <p class="text-sm text-gray-500 mt-1">Lulus administrasi / Belum dinilai.</p>
                </div>
                
                <!-- Psikotest -->
                <div class="mb-8 ml-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-gray-200 rounded-full -left-3 ring-4 ring-white"></span>
                    <h3 class="font-bold text-gray-800">Psikotest</h3>
                    <p class="text-sm text-gray-500 mt-1">Menunggu tahapan ini.</p>
                </div>
                
                <!-- Interview -->
                <div class="mb-8 ml-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-gray-200 rounded-full -left-3 ring-4 ring-white"></span>
                    <h3 class="font-bold text-gray-800">Interview & Evaluasi</h3>
                    <p class="text-sm text-gray-500 mt-1">Menunggu tahapan ini.</p>
                </div>
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end gap-3">
                <button class="btn bg-red-500 hover:bg-red-600 text-white">Reject Pelamar</button>
                <button class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Loloskan ke Tahap Berikutnya</button>
            </div>
        </div>
    </div>
</div>
@endsection
