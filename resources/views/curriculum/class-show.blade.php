@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Ruang Kelas</h1>
        <a href="{{ route('class-curriculum.index', $classroom) }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Content (Syllabus/Materials) -->
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white shadow-lg rounded-sm border border-gray-200">
                <div class="p-5 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800">Materi & Silabus Pembelajaran</h2>
                </div>
                <div class="p-5">
                    <ul class="space-y-4">
                        <li class="p-4 border rounded-md border-gray-200 hover:border-indigo-300 transition-colors">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-800">Pertemuan 1: Introduction to TOEFL</h4>
                                    <p class="text-sm text-gray-500 mt-1">Pengenalan format tes dan strategi dasar.</p>
                                </div>
                                <span class="px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded">Belum Selesai</span>
                            </div>
                            <div class="mt-4 flex gap-2">
                                <a href="#" class="text-sm text-indigo-600 hover:underline flex items-center">
                                    <span class="material-symbols-outlined text-sm mr-1">picture_as_pdf</span> Download Materi
                                </a>
                                <a href="#" class="text-sm text-indigo-600 hover:underline flex items-center ml-4">
                                    <span class="material-symbols-outlined text-sm mr-1">assignment</span> Quiz / Tugas
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Sidebar (Info, Zoom Link, dll) -->
        <div class="space-y-6">
            <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-5">
                <h3 class="font-bold text-gray-800 mb-4">Informasi Kelas</h3>
                <ul class="text-sm space-y-3 text-gray-600">
                    <li class="flex items-center"><span class="material-symbols-outlined text-gray-400 mr-2 text-base">person</span> Tutor: -</li>
                    <li class="flex items-center"><span class="material-symbols-outlined text-gray-400 mr-2 text-base">calendar_month</span> Jadwal: -</li>
                    <li class="flex items-center"><span class="material-symbols-outlined text-gray-400 mr-2 text-base">schedule</span> Sisa Pertemuan: -</li>
                </ul>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="#" class="btn w-full bg-blue-500 hover:bg-blue-600 text-white flex justify-center items-center">
                        <span class="material-symbols-outlined mr-2">videocam</span> Join Zoom Meeting
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
