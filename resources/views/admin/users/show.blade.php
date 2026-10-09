@extends('layouts.dashboard')

@section('title', 'Profil Akun Pegawai')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[color:var(--dash-text-heading)]">Profil Akun Pegawai</h1>
        <a href="{{ route('users.index') }}" class="bg-[var(--dash-nav-hover-bg)] hover:bg-gray-200 text-[color:var(--dash-text-body)] px-4 py-2 rounded-lg font-medium border border-[color:var(--dash-input-border)] inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-[var(--dash-card-bg)] shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-5 border-b border-[color:var(--dash-card-border)] flex justify-between items-center">
            <h3 class="text-base font-semibold text-[color:var(--dash-text-heading)]">Informasi Akun</h3>
            <div class="flex gap-2">
                <a href="{{ route('users.edit', $user->id) }}" class="bg-[var(--dash-primary)] hover:opacity-90 text-white px-3 py-1.5 rounded-lg text-sm font-medium inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                    Edit
                </a>
                @can('auth.user.force_reset_password')
                    <a href="{{ route('users.reset-password.show', $user->id) }}" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">lock_reset</span>
                        Reset Password
                    </a>
                @endcan
            </div>
        </div>
        <dl class="divide-y divide-[color:var(--dash-card-border)]">
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-[color:var(--dash-text-muted)]">Nama Lengkap</dt>
                <dd class="text-sm text-[color:var(--dash-text-heading)] col-span-2">{{ $user->name }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-[color:var(--dash-text-muted)]">Email</dt>
                <dd class="text-sm text-[color:var(--dash-text-heading)] col-span-2">{{ $user->email }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-[color:var(--dash-text-muted)]">Divisi (Role)</dt>
                <dd class="text-sm text-[color:var(--dash-text-heading)] col-span-2">
                    @forelse($user->roles as $role)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-1">{{ $role->name }}</span>
                    @empty
                        <span class="text-[color:var(--dash-text-faint)] italic">Tidak ada divisi</span>
                    @endforelse
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-[color:var(--dash-text-muted)]">Status Email</dt>
                <dd class="text-sm col-span-2">
                    @if($user->email_verified_at)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Terverifikasi</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Verifikasi</span>
                    @endif
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-[color:var(--dash-text-muted)]">Login Terakhir</dt>
                <dd class="text-sm text-[color:var(--dash-text-heading)] col-span-2">
                    {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : '-' }}
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-[color:var(--dash-text-muted)]">Akun Dibuat</dt>
                <dd class="text-sm text-[color:var(--dash-text-heading)] col-span-2">{{ $user->created_at->format('d M Y') }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
