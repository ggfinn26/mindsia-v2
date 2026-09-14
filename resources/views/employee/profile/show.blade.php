@extends('layouts.dashboard')

@section('title', 'Profile Saya - MINDSIA')
@section('header_title', 'Profile Saya')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover border border-gray-200 shrink-0">
                @else
                    <div class="w-20 h-20 bg-[#EEF4FF] rounded-full flex items-center justify-center text-[#5586DB] text-3xl font-bold shrink-0">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-xl font-bold text-gray-900 font-jakarta">{{ $user->name }}</h2>
                    <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
                    <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700">
                        <span class="material-symbols-outlined text-[14px]">shield</span>
                        {{ $user->roles->pluck('name')->join(', ') ?: 'Tidak ada Role' }}
                    </div>
                </div>
            </div>
            
            <div class="shrink-0">
                <a href="{{ route('employee.profile.edit') }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 active:bg-gray-100 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Edit Profil
                </a>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Informasi Sistem</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Status Akun</p>
                    <p class="font-medium text-gray-900">{{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Telegram Chat ID</p>
                    <p class="font-medium text-gray-900">{{ $user->telegram_chat_id ?: 'Belum diatur' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Terakhir Login</p>
                    <p class="font-medium text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Belum pernah' }}</p>
                </div>
            </div>

            @if($employee)
                <hr class="my-8 border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Data Karyawan</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Kode Karyawan</p>
                        <p class="font-medium text-gray-900">{{ $employee->employee_code }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Nama Lengkap</p>
                        <p class="font-medium text-gray-900">{{ $employee->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Jenis Kelamin</p>
                        <p class="font-medium text-gray-900">{{ $employee->gender == 'M' ? 'Laki-Laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tanggal Lahir</p>
                        <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($employee->birthdate)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">No. WhatsApp</p>
                        <p class="font-medium text-gray-900">{{ $employee->whatsapp_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Penempatan</p>
                        <p class="font-medium text-gray-900">
                            @if($employee->is_hq)
                                Head Quarter (HQ)
                            @else
                                {{ $employee->branch ? $employee->branch->name : 'Belum di-assign' }}
                            @endif
                        </p>
                    </div>
                </div>
            @else
                <hr class="my-8 border-gray-100">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-sm text-gray-600">Akun ini belum dihubungkan dengan profil karyawan (HR Data). Jika Anda merasa ini sebuah kesalahan, silakan hubungi tim HR/IT.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
