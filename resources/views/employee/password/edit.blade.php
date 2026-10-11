@extends('layouts.dashboard')

@section('title', 'Ubah Password - MINDSIA')
@section('header_title', 'Ubah Password')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-2xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('employee.profile.show') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Profile Saya
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Pengaturan Akun</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Ubah Password</h1>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Ubah Password Akun</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                Gunakan password yang panjang, acak, dan aman.
            </p>
        </div>

        <form action="{{ route('employee.password.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="px-6 py-5 space-y-5">
                {{-- Password Saat Ini --}}
                <div>
                    <label for="current_password" class="block text-sm font-medium mb-1"
                           style="color: var(--dash-text-body);">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" required
                               class="w-full px-4 py-2.5 pr-10 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        <button type="button" onclick="toggleAllPasswords(this)"
                                class="pwd-toggle absolute inset-y-0 right-0 pr-3 flex items-center"
                                style="color: var(--dash-text-faint);">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </button>
                    </div>
                </div>

                {{-- Password Baru --}}
                <div>
                    <label for="password" class="block text-sm font-medium mb-1"
                           style="color: var(--dash-text-body);">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-2.5 pr-10 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        <button type="button" onclick="toggleAllPasswords(this)"
                                class="pwd-toggle absolute inset-y-0 right-0 pr-3 flex items-center"
                                style="color: var(--dash-text-faint);">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </button>
                    </div>
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">
                        Minimal 8 karakter, huruf besar, huruf kecil, angka, dan simbol.
                    </p>
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-1"
                           style="color: var(--dash-text-body);">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full px-4 py-2.5 pr-10 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        <button type="button" onclick="toggleAllPasswords(this)"
                                class="pwd-toggle absolute inset-y-0 right-0 pr-3 flex items-center"
                                style="color: var(--dash-text-faint);">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('employee.profile.show') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
