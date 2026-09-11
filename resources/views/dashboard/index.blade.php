@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <p class="text-gray-600">Selamat datang, {{ $user->name ?? 'Pengguna' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Widgets akan ditampilkan di sini berdasarkan permission -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Dashboard Kosong</h2>
            <p class="text-gray-600">Widgets akan ditampilkan di sini berdasarkan permission Anda.</p>
        </div>
    </div>
</div>
@endsection
