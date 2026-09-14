@extends('layouts.dashboard')

@section('title', 'Applicant Portal - MINDSIA')
@section('header_title', 'Applicant Portal')





@section('content')
    <div class="mb-8">
        <p class="text-gray-500 font-medium text-sm sm:text-base">Selamat datang di Portal Karir, <span class="font-semibold text-gray-800">{{ $applicantData->full_name ?? 'Pelamar' }}</span> 👋</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Profil Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center">
            <div class="h-14 w-14 rounded-full bg-[#EEF4FF] flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[#5586DB] text-3xl">person</span>
            </div>
            <h2 class="text-lg font-bold font-jakarta text-gray-800 mb-2">Profil</h2>
            <p class="text-sm font-medium text-gray-900">{{ $applicantData->full_name ?? '-' }}</p>
            <p class="text-xs text-gray-500">{{ $applicant->email }}</p>
        </div>

        <!-- Status Verifikasi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center">
            <div class="h-14 w-14 rounded-full {{ $applicant->email_verified_at ? 'bg-green-50' : 'bg-yellow-50' }} flex items-center justify-center mb-4">
                <span class="material-symbols-outlined {{ $applicant->email_verified_at ? 'text-green-500' : 'text-yellow-500' }} text-3xl">
                    {{ $applicant->email_verified_at ? 'verified_user' : 'mark_email_unread' }}
                </span>
            </div>
            <h2 class="text-lg font-bold font-jakarta text-gray-800 mb-2">Verifikasi Email</h2>
            @if($applicant->email_verified_at)
                <p class="text-sm font-semibold text-green-600">Terverifikasi</p>
            @else
                <p class="text-sm font-semibold text-yellow-600">Menunggu Verifikasi</p>
            @endif
        </div>

        <!-- Status Akun -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center">
            <div class="h-14 w-14 rounded-full {{ $applicant->is_active ? 'bg-green-50' : 'bg-red-50' }} flex items-center justify-center mb-4">
                <span class="material-symbols-outlined {{ $applicant->is_active ? 'text-green-500' : 'text-red-500' }} text-3xl">
                    {{ $applicant->is_active ? 'check_circle' : 'cancel' }}
                </span>
            </div>
            <h2 class="text-lg font-bold font-jakarta text-gray-800 mb-2">Status Akun</h2>
            @if($applicant->is_active)
                <p class="text-sm font-semibold text-green-600">Aktif</p>
            @else
                <p class="text-sm font-semibold text-red-600">Nonaktif</p>
            @endif
        </div>
    </div>
@endsection
