@extends('layouts.dashboard')

@section('title', 'Ubah Password - MINDSIA')
@section('header_title', 'Ubah Password')

@section('content')
<div class="max-w-md mx-auto">
    <div class="mb-6">
        <p class="text-sm text-[color:var(--dash-text-muted)]">Masukkan password lama dan password baru Anda.</p>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('applicant.password.change.post') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="current_password" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-2">
                Password Saat Ini
            </label>
            <div class="relative w-full">
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    class="w-full px-4 py-2 border border-[color:var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                    placeholder="Password saat ini"
                    required
                >
                <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                            <span class="material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]">visibility</span>
                        </button>
            </div>
            @error('current_password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-2">
                Password Baru
            </label>
            <div class="relative w-full">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-4 py-2 border border-[color:var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                    placeholder="Password baru"
                    required
                >
                <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                            <span class="material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]">visibility</span>
                        </button>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-2">
                Konfirmasi Password Baru
            </label>
            <div class="relative w-full">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="w-full px-4 py-2 border border-[color:var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                    placeholder="Konfirmasi password baru"
                    required
                >
                <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                            <span class="material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]">visibility</span>
                        </button>
            </div>
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
        >
            Ubah Password
        </button>
    </form>

    <div class="mt-6 text-center text-sm text-[color:var(--dash-text-body)]">
        <p>
            Kembali ke <a href="{{ route('applicant.dashboard') }}" class="text-blue-600 hover:text-blue-800">dashboard</a>
        </p>
    </div>
</div>
@endsection
