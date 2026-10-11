@extends('layouts.dashboard')

@section('title', 'Detail Akun Pegawai - MINDSIA')
@section('header_title', 'Detail Akun Pegawai')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <a href="{{ route('users.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Manajemen Akun Pegawai
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">SDM & Akses</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">{{ $user->name }}</h1>
            <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">
                Informasi detail akun pegawai dan hak akses sistem.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('users.edit', $user->id) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                Edit
            </a>
            @can('auth.user.force_reset_password')
            <a href="{{ route('users.reset-password.show', $user->id) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">lock_reset</span>
                Reset Password
            </a>
            @endcan
            @can('system.permission.manage')
            <a href="{{ route('users.roles', $user->id) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">shield</span>
                Kelola Role
            </a>
            @endcan
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- Detail Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        {{-- Profile Header Summary --}}
        <div class="px-6 py-5 flex items-center gap-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold font-jakarta shrink-0"
                 style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-bold font-jakarta truncate" style="color: var(--dash-text-heading);">{{ $user->name }}</h2>
                <p class="text-sm truncate" style="color: var(--dash-text-muted);">{{ $user->email }}</p>
                <div class="mt-1.5 flex flex-wrap gap-1.5">
                    @forelse($user->roles as $role)
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                              style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                            {{ $role->name }}
                        </span>
                    @empty
                        <span class="text-xs" style="color: var(--dash-text-faint);">Tidak ada role</span>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Section: Informasi Akun --}}
        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Informasi Kredensial Akun</h3>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Detail status autentikasi dan informasi akun sistem</p>
        </div>

        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Nama Lengkap</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Alamat Email</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Status Akun</p>
                <div>
                    @if($user->is_active)
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Aktif</span>
                    @else
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-700">Nonaktif</span>
                    @endif
                </div>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Verifikasi Email</p>
                <div>
                    @if($user->email_verified_at)
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Terverifikasi</span>
                    @else
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Belum Verifikasi</span>
                    @endif
                </div>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Login Terakhir</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                    {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : '-' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Akun Dibuat</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                    {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                </p>
            </div>
        </div>

        @if($user->employee)
        {{-- Section: Data Pegawai Terkait --}}
        <div class="px-6 py-4"
             style="border-top: 1px solid var(--dash-card-border); border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Data Profil Pegawai</h3>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Informasi biodata pegawai yang terhubung dengan akun ini</p>
        </div>

        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Kode Pegawai</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $user->employee->employee_code ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Nama Lengkap Pegawai</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $user->employee->full_name ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">No. Telepon / WhatsApp</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $user->employee->phone_number ?: '-' }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
