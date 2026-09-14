@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Ubah Password</h1>
        <p class="text-gray-600">Masukkan password lama dan password baru Anda</p>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('member.password.change.post') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                Password Saat Ini
            </label>
            <div class="relative w-full">
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                    placeholder="Password saat ini"
                    required
                >
                <button type="button" onclick="const input = this.previousElementSibling; input.type = input.type === 'password' ? 'text' : 'password'; this.innerHTML = input.type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>';" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                </button>
            </div>
            @error('current_password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Password Baru
            </label>
            <div class="relative w-full">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                    placeholder="Password baru"
                    required
                >
                <button type="button" onclick="const input = this.previousElementSibling; input.type = input.type === 'password' ? 'text' : 'password'; this.innerHTML = input.type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>';" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                Konfirmasi Password Baru
            </label>
            <div class="relative w-full">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                    placeholder="Konfirmasi password baru"
                    required
                >
                <button type="button" onclick="const input = this.previousElementSibling; input.type = input.type === 'password' ? 'text' : 'password'; this.innerHTML = input.type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>';" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                </button>
            </div>
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
        >
            Ubah Password
        </button>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
        <p>
            Kembali ke <a href="{{ route('member.dashboard') }}" class="text-blue-600 hover:text-blue-800">dashboard</a>
        </p>
    </div>
</div>
@endsection
