@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Target Marketing</h1>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <!-- Actions -->
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">Target Bulan Ini</h2>
            <!-- Config form -->
            <p class="text-sm text-gray-500">Pengaturan target omzet / jumlah kelas per posisi atau pegawai akan ada di sini.</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">Pegawai</th>
                        <th class="px-6 py-3 text-left">Target Omzet</th>
                        <th class="px-6 py-3 text-left">Target Kelas</th>
                        <th class="px-6 py-3 text-left">Target Fixed Member</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Data target belum di-set.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
