@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Materi Pembelajaran</h1>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <a href="{{ route('classes.learning-material.create') }}" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Upload Materi</a>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">Judul Materi</th>
                        <th class="px-6 py-3 text-left">Untuk Program / Kelas</th>
                        <th class="px-6 py-3 text-left">Tipe File</th>
                        <th class="px-6 py-3 text-left">Diunggah Oleh</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada materi pembelajaran.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
