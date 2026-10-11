@extends('layouts.dashboard')

@section('title', 'Reset Password Akun - MINDSIA')
@section('header_title', 'Reset Password Akun')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('users.show', $user->id) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Detail Akun
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">SDM & Akses</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Reset Password: {{ $user->name }}</h1>
        <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">
            Atur ulang kata sandi akun pegawai ini secara langsung oleh Administrator.
        </p>
    </div>

    {{-- Warning Alert --}}
    <div class="flex items-start gap-3 p-4 rounded-xl border text-sm"
         style="background-color: #fefce8; border-color: #fef08a; color: #854d0e;">
        <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5">warning</span>
        <div>
            <p class="font-semibold">Konfirmasi Tindakan Penting</p>
            <p class="mt-0.5 text-xs">
                Anda akan melakukan reset kata sandi untuk akun <span class="font-semibold">{{ $user->name }}</span> ({{ $user->email }}). Pengguna akan diminta mengganti kata sandi ini saat pertama kali login kembali.
            </p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form Card --}}
    <div class="rounded-2xl"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Form Reset Kata Sandi</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Masukkan kata sandi administrator Anda dan tetapkan kata sandi baru untuk pengguna.</p>
        </div>

        <form action="{{ route('users.reset-password.update', $user->id) }}" method="POST" class="p-6 space-y-5">
            @csrf

            {{-- Password Admin Saat Ini --}}
            <div>
                <label for="current_password" class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                    Password Administrator Anda <span class="text-red-500">*</span>
                </label>
                <input type="password"
                       name="current_password"
                       id="current_password"
                       required
                       placeholder="Masukkan kata sandi akun Anda saat ini"
                       class="w-full px-3.5 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                @error('current_password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Baru --}}
            <div>
                <label for="password" class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                    Password Baru untuk {{ $user->name }} <span class="text-red-500">*</span>
                </label>
                <input type="password"
                       name="password"
                       id="password"
                       required
                       placeholder="Minimal 8 karakter"
                       class="w-full px-3.5 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Form Actions --}}
            <div class="pt-4 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('users.show', $user->id) }}"
                   class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: #dc2626;">
                    <span class="material-symbols-outlined text-[16px]">lock_reset</span>
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
