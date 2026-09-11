@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Masuk Sebagai Member</h1>
        <p class="text-gray-600">Akses portal member MINDSIA</p>
    </div>

    <form action="{{ route('member.login.post') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email
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
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Password
            </label>
            <input
                type="password"
                id="password"
                name="password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Password Anda"
                required
            >
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center">
            <input
                type="checkbox"
                id="remember"
                name="remember"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label for="remember" class="ml-2 block text-sm text-gray-700">
                Ingat saya
            </label>
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
        >
            Masuk
        </button>
    </form>

    <div class="mt-6 space-y-4">
        <p class="text-center text-sm text-gray-600">
            Belum punya akun?
            <a href="{{ route('member.register') }}" class="text-blue-600 hover:text-blue-800">Daftar di sini</a>
        </p>

        <p class="text-center text-sm text-gray-600">
            <a href="{{ route('password.request') }}" class="text-blue-600 hover:text-blue-800">Lupa password?</a>
        </p>
    </div>
</div>
@endsection
