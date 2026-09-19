@extends('layouts.dashboard')

@section('title', 'Ubah Password - MINDSIA')
@section('header_title', 'Ubah Password')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start gap-3">
            <span class="material-symbols-outlined text-red-600 shrink-0">error</span>
            <div>
                <ul class="list-disc list-inside text-sm font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 font-jakarta">Ubah Password Akun</h2>
            <p class="text-gray-500 text-sm mt-1">Pastikan akun Anda menggunakan password yang panjang, acak, dan aman.</p>
        </div>

        <form action="{{ route('employee.password.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="p-6 sm:p-8 space-y-5">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                    <div class="relative w-full">
                        <input type="password" name="current_password" id="current_password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border pr-10" required>
                        <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                            <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <div class="relative w-full">
                        <input type="password" name="password" id="password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border pr-10" required>
                        <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                            <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan simbol.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <div class="relative w-full">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border pr-10" required>
                        <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                            <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 sm:px-8 border-t border-gray-100 flex items-center justify-end">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#5586DB] border border-transparent rounded-lg shadow-sm hover:bg-[#4370BC] focus:outline-none">
                    Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
