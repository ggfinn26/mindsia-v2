@extends('layouts.dashboard')

@section('title', 'Ubah Password - MINDSIA')
@section('header_title', 'Ubah Password')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-2xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('member.profile.show') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Profil Saya
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Pengaturan Akun</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Ubah Password</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui kata sandi akun Anda secara berkala untuk menjaga keamanan data.
        </p>
    </div>

    {{-- Flash & Error Messages --}}
    @if(session('status') || session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
         style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-success-text);">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        <p>{{ session('status') ?? session('success') }}</p>
    </div>
    @endif

    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm font-medium space-y-1"
         style="background-color: var(--dash-status-danger-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
        <div class="flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <span class="font-bold">Terjadi kesalahan pada input:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 pl-6">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Card Form --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Ganti Password Akun</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                Pastikan akun Anda menggunakan kombinasi password yang kuat dan aman.
            </p>
        </div>

        <form action="{{ route('member.password.change.post') }}" method="POST">
            @csrf

            <div class="px-6 py-6 space-y-5">
                {{-- Password Saat Ini --}}
                <div>
                    <label for="current_password" class="block text-sm font-semibold mb-1"
                           style="color: var(--dash-text-body);">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="w-full px-4 py-2.5 pr-10 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                            required
                        >
                        <button type="button" onclick="toggleAllPasswords(this)"
                                class="pwd-toggle absolute inset-y-0 right-0 pr-3 flex items-center transition-colors"
                                style="color: var(--dash-text-faint);">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </button>
                    </div>
                </div>

                {{-- Password Baru --}}
                <div>
                    <label for="password" class="block text-sm font-semibold mb-1"
                           style="color: var(--dash-text-body);">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full px-4 py-2.5 pr-10 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                            required
                        >
                        <button type="button" onclick="toggleAllPasswords(this)"
                                class="pwd-toggle absolute inset-y-0 right-0 pr-3 flex items-center transition-colors"
                                style="color: var(--dash-text-faint);">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </button>
                    </div>
                    <p class="mt-1.5 text-xs" style="color: var(--dash-text-faint);">
                        Minimal 8 karakter, harus mengandung huruf besar, angka, dan simbol khusus.
                    </p>
                </div>

                {{-- Konfirmasi Password Baru --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold mb-1"
                           style="color: var(--dash-text-body);">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="w-full px-4 py-2.5 pr-10 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                            required
                        >
                        <button type="button" onclick="toggleAllPasswords(this)"
                                class="pwd-toggle absolute inset-y-0 right-0 pr-3 flex items-center transition-colors"
                                style="color: var(--dash-text-faint);">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('member.profile.show') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
