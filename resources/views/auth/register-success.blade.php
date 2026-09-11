@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-md">
    <div class="bg-green-50 border border-green-200 rounded-lg p-8 text-center">
        <div class="mb-4 text-4xl">✓</div>

        <h1 class="text-2xl font-bold text-green-900 mb-3">
            Akun Berhasil Dibuat
        </h1>

        <p class="text-gray-700 mb-6">
            Silakan cek email Anda untuk verifikasi. Kami telah mengirimkan link verifikasi
            ke email yang Anda daftarkan.
        </p>

        <div class="bg-green-100 border border-green-300 rounded p-4 mb-6 text-left text-sm text-gray-700">
            <p class="font-medium mb-2">Langkah berikutnya:</p>
            <ol class="list-decimal list-inside space-y-2">
                <li>Buka email Anda</li>
                <li>Klik link verifikasi dalam email</li>
                <li>Kembali ke halaman login untuk masuk</li>
            </ol>
        </div>

        <p class="text-sm text-gray-600 mb-6">
            Tidak menerima email? Periksa folder spam atau hubungi tim support.
        </p>

        <a
            href="{{ route('login') }}"
            class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition"
        >
            Kembali ke Login
        </a>
    </div>
</div>
@endsection
