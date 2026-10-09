@extends('layouts.dashboard')

@section('title', 'Ubah Password - MINDSIA')
@section('header_title', 'Ubah Password')

@section('content')
<h1 class="sr-only">Ubah Password</h1>
<div class="max-w-2xl mx-auto">
    @if(session('status'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-start gap-3">
            <span class="material-symbols-outlined text-green-500 mt-0.5">check_circle</span>
            <div>
                <p class="font-medium">{{ session('status') }}</p>
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

    <div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] overflow-hidden">
        <form action="{{ route('member.password.change.post') }}" method="POST">
            @csrf

            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-4">Ganti Password</h3>
                <p class="text-sm text-[color:var(--dash-text-muted)] mb-6">Pastikan akun Anda menggunakan password panjang acak untuk tetap aman.</p>
                
                <div class="space-y-5">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Password Saat Ini</label>
                        <div class="relative w-full">
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                class="w-full px-4 py-2 bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB] transition-all shadow-sm pr-10"
                                required
                            >
                            <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                                <span class="material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Password Baru</label>
                        <div class="relative w-full">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full px-4 py-2 bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB] transition-all shadow-sm pr-10"
                                required
                            >
                            <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                                <span class="material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]">visibility</span>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-[color:var(--dash-text-muted)]">Minimal 8 karakter, harus mengandung huruf besar, angka, dan simbol khusus.</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Konfirmasi Password Baru</label>
                        <div class="relative w-full">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="w-full px-4 py-2 bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] rounded-lg text-sm focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB] transition-all shadow-sm pr-10"
                                required
                            >
                            <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
                                <span class="material-symbols-outlined text-[18px] text-[color:var(--dash-text-muted)]">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-[var(--dash-nav-hover-bg)] px-6 py-4 sm:px-8 border-t border-[color:var(--dash-card-border)] flex items-center justify-end">
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[#5586DB] border border-transparent rounded-lg shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] hover:bg-[#4370BC] focus:outline-none transition-colors">
                    Perbarui Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
