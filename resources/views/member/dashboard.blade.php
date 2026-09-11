@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Dashboard Member</h1>
        <p class="text-gray-600">Selamat datang, {{ $memberData->full_name ?? 'Member' }}</p>
    </div>

    @if(session('info'))
        <div class="mb-6 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded">
            {{ session('info') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Profil</h2>
            <p class="text-gray-600">{{ $memberData->full_name }}</p>
            <p class="text-gray-600 text-sm">{{ $member->email }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Status</h2>
            @if($member->is_active)
                <p class="text-green-600">Aktif</p>
            @else
                <p class="text-yellow-600">Menunggu Aktivasi</p>
            @endif
        </div>
    </div>
</div>
@endsection
