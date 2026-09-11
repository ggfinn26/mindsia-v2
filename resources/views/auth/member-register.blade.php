@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Pendaftaran Member</h1>
        <p class="text-gray-600">Isi form berikut untuk mendaftar sebagai member</p>
    </div>

    <form action="{{ route('member.register.post') }}" method="POST" class="space-y-6">
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
                    required
                >
                @error('full_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Kelamin <span class="text-red-600">*</span>
                </label>
                <select
                    id="gender"
                    name="gender"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                >
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('gender')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="birthdate" class="block text-sm font-medium text-gray-700 mb-2">
                Tanggal Lahir <span class="text-red-600">*</span>
            </label>
            <input
                type="date"
                id="birthdate"
                name="birthdate"
                value="{{ old('birthdate') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
            >
            @error('birthdate')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">
                    No. WhatsApp <span class="text-red-600">*</span>
                </label>
                <input
                    type="text"
                    id="whatsapp_number"
                    name="whatsapp_number"
                    value="{{ old('whatsapp_number') }}"
                    placeholder="628xxxxxxxxxx"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                >
                @error('whatsapp_number')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="instagram" class="block text-sm font-medium text-gray-700 mb-2">
                    Instagram
                </label>
                <input
                    type="text"
                    id="instagram"
                    name="instagram"
                    value="{{ old('instagram') }}"
                    placeholder="@username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                @error('instagram')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
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
                required
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                Alamat <span class="text-red-600">*</span>
            </label>
            <textarea
                id="address"
                name="address"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
            >{{ old('address') }}</textarea>
            @error('address')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="institution_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Institusi <span class="text-red-600">*</span>
                </label>
                <select
                    id="institution_id"
                    name="institution_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                >
                    <option value="">Pilih Institusi</option>
                </select>
                @error('institution_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="program_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Program Pilihan
                </label>
                <select
                    id="program_id"
                    name="program_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Pilih Program (Opsional)</option>
                </select>
                @error('program_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="referred_by_code" class="block text-sm font-medium text-gray-700 mb-2">
                Kode Referral (Opsional)
            </label>
            <input
                type="text"
                id="referred_by_code"
                name="referred_by_code"
                value="{{ old('referred_by_code') }}"
                placeholder="Kode karyawan yang mereferensikan Anda"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            @error('referred_by_code')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold mb-4">Data Orang Tua (Opsional)</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="father_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Ayah
                    </label>
                    <input
                        type="text"
                        id="father_name"
                        name="father_name"
                        value="{{ old('father_name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <div>
                    <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Ibu
                    </label>
                    <input
                        type="text"
                        id="mother_name"
                        name="mother_name"
                        value="{{ old('mother_name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <div>
                    <label for="father_occupation" class="block text-sm font-medium text-gray-700 mb-2">
                        Pekerjaan Ayah
                    </label>
                    <input
                        type="text"
                        id="father_occupation"
                        name="father_occupation"
                        value="{{ old('father_occupation') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <div>
                    <label for="mother_occupation" class="block text-sm font-medium text-gray-700 mb-2">
                        Pekerjaan Ibu
                    </label>
                    <input
                        type="text"
                        id="mother_occupation"
                        name="mother_occupation"
                        value="{{ old('mother_occupation') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <div>
                    <label for="father_whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                        No. WhatsApp Ayah
                    </label>
                    <input
                        type="text"
                        id="father_whatsapp"
                        name="father_whatsapp"
                        value="{{ old('father_whatsapp') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <div>
                    <label for="mother_whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                        No. WhatsApp Ibu
                    </label>
                    <input
                        type="text"
                        id="mother_whatsapp"
                        name="mother_whatsapp"
                        value="{{ old('mother_whatsapp') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
            </div>
        </div>

        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold mb-4">Akun</h3>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-600">*</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                >
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">
                    Minimal 8 karakter dengan kombinasi huruf besar, huruf kecil, angka, dan simbol.
                </p>
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
                    required
                >
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
        >
            Daftar
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Sudah memiliki akun?
        <a href="{{ route('member.login') }}" class="text-blue-600 hover:text-blue-800">Masuk di sini</a>
    </p>
</div>
@endsection
