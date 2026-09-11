@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Portal Karir - Dashboard</h1>
        <p class="text-gray-600">Selamat datang, {{ $applicantData->full_name ?? 'Pelamar' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Profil</h2>
            <p class="text-gray-600">{{ $applicantData->full_name ?? '-' }}</p>
            <p class="text-gray-600 text-sm">{{ $applicant->email }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Status Verifikasi</h2>
            @if($applicant->email_verified_at)
                <p class="text-green-600">✓ Email Terverifikasi</p>
            @else
                <p class="text-yellow-600">Menunggu Verifikasi Email</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Akun Aktif</h2>
            @if($applicant->is_active)
                <p class="text-green-600">✓ Aktif</p>
            @else
                <p class="text-yellow-600">Nonaktif</p>
            @endif
        </div>
    </div>

    <div class="mt-8">
        <p class="text-center text-gray-600">
            <a href="{{ route('applicant.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-blue-600 hover:text-blue-800">
                Keluar
            </a>
        </p>
        <form id="logout-form" action="{{ route('applicant.logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>
@endsection
