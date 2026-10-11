@extends('layouts.dashboard')

@section('title', 'Upload Bukti Bayar - MINDSIA')
@section('header_title', 'Upload Bukti Bayar')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold font-jakarta text-gray-900">Upload Bukti Bayar</h2>
            <p class="text-sm text-gray-500 mt-1">Unggah bukti transfer untuk pembayaran registrasi atau cicilan Anda.</p>
        </div>
    </div>

    <!-- Placeholder State -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 flex flex-col items-center justify-center text-center">
        <div class="h-20 w-20 rounded-full bg-blue-50 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[#5586DB] text-4xl">cloud_upload</span>
        </div>
        <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-2">Form Upload Belum Tersedia</h3>
        <p class="text-gray-500 max-w-md mx-auto mb-6">Anda belum memiliki tagihan yang membutuhkan konfirmasi pembayaran. Lakukan pendaftaran program terlebih dahulu.</p>
        <a href="{{ route('member.dashboard') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
