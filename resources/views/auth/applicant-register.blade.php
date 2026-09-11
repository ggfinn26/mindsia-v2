@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Daftar Portal Karir</h1>
        <p class="text-gray-600">Buat akun untuk melamar lowongan pekerjaan</p>
    </div>

    <form action="{{ route('applicant.register.post') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Lengkap <span class="text-red-600">*</span>
                </label>
                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    value="{{ old('full_name') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Nama lengkap"
                    required
                >
                @error('full_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor WhatsApp <span class="text-red-600">*</span>
                </label>
                <input
                    type="text"
                    id="whatsapp_number"
                    name="whatsapp_number"
                    value="{{ old('whatsapp_number') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="628..."
                    required
                >
                @error('whatsapp_number')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-600">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Email Anda"
                    required
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Lahir
                </label>
                <input
                    type="date"
                    id="birth_date"
                    name="birth_date"
                    value="{{ old('birth_date') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                @error('birth_date')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Kelamin
                </label>
                <select
                    id="gender"
                    name="gender"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Pilih jenis kelamin</option>
                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('gender')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                    Kota
                </label>
                <input
                    type="text"
                    id="city"
                    name="city"
                    value="{{ old('city') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Kota Anda"
                >
                @error('city')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                Alamat
            </label>
            <textarea
                id="address"
                name="address"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Alamat lengkap Anda"
            >{{ old('address') }}</textarea>
            @error('address')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-600">*</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Password minimal 8 karakter"
                    required
                >
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Konfirmasi Password <span class="text-red-600">*</span>
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Konfirmasi password"
                    required
                >
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center">
            <input
                type="checkbox"
                id="agree_terms"
                name="agree_terms"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                required
            >
            <label for="agree_terms" class="ml-2 block text-sm text-gray-700">
                Saya setuju dengan <a href="#" class="text-blue-600 hover:text-blue-800">syarat dan ketentuan</a>
            </label>
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
        >
            Daftar Sekarang
        </button>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
        <p>
            Sudah punya akun?
            <a href="{{ route('applicant.login') }}" class="text-blue-600 hover:text-blue-800">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
