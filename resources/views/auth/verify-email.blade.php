@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-md">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 text-center">
        <div class="mb-4 text-4xl">📧</div>

        <h1 class="text-2xl font-bold text-blue-900 mb-3">
            Verifikasi Email Diperlukan
        </h1>

        <p class="text-gray-700 mb-6">
            Kami telah mengirimkan link verifikasi ke email Anda. Silakan klik link tersebut
            untuk mengaktifkan akun Anda.
        </p>

        <div class="bg-blue-100 border border-blue-300 rounded p-4 mb-6 text-left text-sm text-gray-700">
            <p class="font-medium mb-2">Apa yang harus dilakukan:</p>
            <ol class="list-decimal list-inside space-y-2">
                <li>Buka email Anda</li>
                <li>Cari email dari kami (cek folder spam jika perlu)</li>
                <li>Klik tombol "Verifikasi Email" dalam email</li>
                <li>Kembali ke sini untuk masuk</li>
            </ol>
        </div>

        <form action="{{ route('verification.send') }}" method="POST" class="mb-6">
            @csrf
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
            >
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button
                type="submit"
                class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition"
            >
                Keluar
            </button>
        </form>

        @if (session('status') == 'verification-link-sent')
            <div class="mt-6 bg-green-50 border border-green-200 rounded p-4 text-sm text-green-700">
                ✓ Email verifikasi telah dikirim ke alamat Anda.
            </div>
        @endif
    </div>
</div>
@endsection
