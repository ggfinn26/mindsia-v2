@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Daftar Akun Karyawan</h1>
        <p class="text-gray-600">Langkah 1 dari 2: Verifikasi Kode Karyawan</p>
    </div>

    <form action="{{ route('register.verify') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="employee_code" class="block text-sm font-medium text-gray-700 mb-2">
                Kode Karyawan
            </label>
            <input
                type="text"
                id="employee_code"
                name="employee_code"
                value="{{ old('employee_code') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Masukkan kode karyawan Anda"
                required
            >
            @error('employee_code')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
        >
            Lanjut ke Langkah 2
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Masuk di sini</a>
    </p>
</div>
@endsection
