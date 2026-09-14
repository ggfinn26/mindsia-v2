@extends('layouts.dashboard')

@section('title', 'Edit Profile - MINDSIA')
@section('header_title', 'Edit Profile')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Informasi Akun</h3>
                
                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Panggilan (Tampil di Dashboard)</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border" required>
                    </div>
                    
                    <div>
                        <label for="telegram_chat_id" class="block text-sm font-medium text-gray-700 mb-1">Telegram Chat ID</label>
                        <input type="text" name="telegram_chat_id" id="telegram_chat_id" value="{{ old('telegram_chat_id', $user->telegram_chat_id) }}" placeholder="Misal: 123456789" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                        <p class="mt-1 text-xs text-gray-500">Gunakan ID ini untuk menerima notifikasi dari bot Telegram kami.</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed">
                        <p class="mt-1 text-xs text-gray-500">Email tidak dapat diubah. Hubungi IT jika perlu mengubah email.</p>
                    </div>
                </div>

                @if($employee)
                    <hr class="my-8 border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Data Karyawan</h3>
                    
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (HR)</label>
                                <input type="text" value="{{ $employee->full_name }}" disabled class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Karyawan</label>
                                <input type="text" value="{{ $employee->employee_code }}" disabled class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed">
                            </div>
                        </div>

                        <div>
                            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">+62</span>
                                <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', preg_replace('/^\+?62/', '', $employee->whatsapp_number)) }}" placeholder="81234567890" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-lg border border-gray-300 focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Gunakan nomor yang aktif untuk keperluan notifikasi sistem.</p>
                        </div>
                        
                        <div>
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                            <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg shadow-sm px-3 py-2 bg-white">
                            <p class="mt-1 text-xs text-gray-500">Maksimal ukuran 2MB. Format JPG, JPEG, PNG.</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-gray-50 px-6 py-4 sm:px-8 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('employee.profile.show') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#5586DB] border border-transparent rounded-lg shadow-sm hover:bg-[#4370BC] focus:outline-none">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
