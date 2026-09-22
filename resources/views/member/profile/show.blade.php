@extends('layouts.dashboard')

@section('title', 'Profile Saya - MINDSIA')
@section('header_title', 'Profile Saya')

@section('content')
<div class="max-w-4xl mx-auto">
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-start gap-3">
            <span class="material-symbols-outlined text-green-500 mt-0.5">check_circle</span>
            <div>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <form action="{{ route('member.profile.avatar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-center gap-6">
                <div class="h-24 w-24 rounded-full bg-[#EEF4FF] flex items-center justify-center overflow-hidden border-2 border-gray-100 shrink-0">
                    @if($member->telegram_photo_id)
                        <img src="{{ $member->telegram_photo_id }}" alt="Avatar" class="h-full w-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-[#5586DB] text-5xl">account_circle</span>
                    @endif
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-1">Foto Profil</h3>
                    <p class="text-xs text-gray-500 mb-3">Maksimal 2MB. Format: JPG, PNG.</p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center sm:justify-start">
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="w-full sm:w-auto text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg px-3 py-2 bg-white" required>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 shadow-sm whitespace-nowrap">
                            Upload Foto
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 sm:p-8 space-y-8">
            <!-- Informasi Pribadi -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Informasi Pribadi</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" value="{{ $member->full_name }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" value="{{ $member->email }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
                        <input type="text" value="{{ $member->whatsapp_number }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                        <input type="text" value="{{ $member->instagram ?: '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" value="{{ $member->birth_place ?: '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="text" value="{{ $member->birthdate ? $member->birthdate->format('d/m/Y') : '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea rows="3" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>{{ $member->address ?: '-' }}</textarea>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Data Akademik & Institusi -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Data Akademik & Cabang</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cabang MINDSIA</label>
                        <input type="text" value="{{ $member->branch->branch_name ?? '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Institusi Asal</label>
                        <input type="text" value="{{ $member->institution->institution_name ?? $member->institution_name_custom ?? '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang Pendidikan</label>
                        <input type="text" value="{{ $member->education_level ?: '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan atau Kelas</label>
                        <input type="text" value="{{ $member->class_or_faculty ?: '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Data Orang Tua -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Data Orang Tua</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
                        <input type="text" value="{{ $member->father_name ?: '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
                        <input type="text" value="{{ $member->mother_name ?: '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Ayah</label>
                        <input type="text" value="{{ $member->father_whatsapp ?: '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Ibu</label>
                        <input type="text" value="{{ $member->mother_whatsapp ?: '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Ayah</label>
                        <input type="text" value="{{ $member->father_occupation ?: '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Ibu</label>
                        <input type="text" value="{{ $member->mother_occupation ?: '-' }}" class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed" readonly>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-3">
                <span class="material-symbols-outlined text-blue-500 mt-0.5">info</span>
                <p class="text-sm text-blue-800">
                    Data profil Anda saat ini bersifat <strong>hanya-baca (read-only)</strong>. Jika ada perubahan data, silakan hubungi tim administrasi MINDSIA.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection