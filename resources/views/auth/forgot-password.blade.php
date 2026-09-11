@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Lupa Password</h1>
        <p class="text-gray-600">Masukkan email Anda untuk menerima tautan reset password</p>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
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

        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
        >
            Kirim Tautan Reset
        </button>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
        <p>
            Kembali ke <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">login</a>
        </p>
    </div>
</div>
@endsection
