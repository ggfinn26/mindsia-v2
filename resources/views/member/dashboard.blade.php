@extends('layouts.dashboard')

@section('title', 'Member Portal - MINDSIA')
@section('header_title', 'Member Portal')





@section('content')
    <div class="mb-8">
        <p class="text-gray-500 font-medium text-sm sm:text-base">Selamat datang, <span class="font-semibold text-gray-800">{{ $memberData->full_name ?? 'Member' }}</span> 👋</p>
    </div>

    @if(session('info'))
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl flex items-start gap-3">
            <span class="material-symbols-outlined text-blue-500 mt-0.5">info</span>
            <div>
                <p class="font-medium">{{ session('info') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Profil Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center">
            <div class="h-14 w-14 rounded-full bg-[#EEF4FF] flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[#5586DB] text-3xl">account_circle</span>
            </div>
            <h2 class="text-lg font-bold font-jakarta text-gray-800 mb-2">Profil</h2>
            <p class="text-sm font-medium text-gray-900">{{ $memberData->full_name }}</p>
            <p class="text-xs text-gray-500">{{ $member->email }}</p>
        </div>

        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center">
            <div class="h-14 w-14 rounded-full {{ $member->is_active ? 'bg-green-50' : 'bg-yellow-50' }} flex items-center justify-center mb-4">
                <span class="material-symbols-outlined {{ $member->is_active ? 'text-green-500' : 'text-yellow-500' }} text-3xl">
                    {{ $member->is_active ? 'how_to_reg' : 'pending_actions' }}
                </span>
            </div>
            <h2 class="text-lg font-bold font-jakarta text-gray-800 mb-2">Status Akun</h2>
            @if($member->is_active)
                <p class="text-sm font-semibold text-green-600">Aktif</p>
            @else
                <p class="text-sm font-semibold text-yellow-600">Menunggu Aktivasi</p>
            @endif
        </div>
    </div>
@endsection
