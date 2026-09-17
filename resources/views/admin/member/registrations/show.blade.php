@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Registrasi Member</h1>
        <a href="#" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap</h3>
                <p class="text-gray-900 font-medium">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Email</h3>
                <p class="text-gray-900 font-medium">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Telepon / WhatsApp</h3>
                <p class="text-gray-900 font-medium">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Program Dipilih</h3>
                <p class="text-gray-900 font-medium">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Status Pembayaran</h3>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu Pembayaran</span>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end gap-3">
            <button class="btn bg-red-500 hover:bg-red-600 text-white">Batalkan Pendaftaran</button>
            <button class="btn bg-green-500 hover:bg-green-600 text-white">Konfirmasi & Aktifkan Member</button>
        </div>
    </div>
</div>
@endsection
