@extends('layouts.dashboard')

@section('title', 'Kelola Role User - MINDSIA')
@section('header_title', 'Kelola Role User')

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
            style="color: var(--dash-text-heading);">Kelola Role & Akses: {{ $user->name }}</h1>
        <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">
            Atur penugasan divisi dan role sistem untuk pengguna {{ $user->name }} ({{ $user->email }}).
        </p>
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

    {{-- Form Card --}}
    <div class="rounded-2xl"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Daftar Divisi / Role Tersedia</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Centang role yang akan diberikan kepada akun ini</p>
        </div>

        <form action="{{ route('users.assign-role', $user->id) }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @forelse($allRoles as $role)
                <label class="flex items-center gap-3 p-3.5 rounded-xl border transition-colors cursor-pointer select-none"
                       style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border);"
                       onmouseover="this.style.borderColor='var(--dash-primary)'"
                       onmouseout="this.style.borderColor='var(--dash-input-border)'">
                    <input type="checkbox"
                           name="roles[]"
                           value="{{ $role->id }}"
                           {{ $user->hasRole($role->name) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-blue-600 focus:ring-0 cursor-pointer"
                           style="accent-color: var(--dash-primary);">
                    <div>
                        <span class="text-sm font-semibold block" style="color: var(--dash-text-heading);">
                            {{ $role->name }}
                        </span>
                        <span class="text-[11px] block mt-0.5" style="color: var(--dash-text-muted);">
                            Guard: {{ $role->guard_name }}
                        </span>
                    </div>
                </label>
                @empty
                <div class="col-span-2 py-8 text-center">
                    <span class="material-symbols-outlined text-[36px] block mb-2" style="color: var(--dash-text-faint);">manage_accounts</span>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data role di sistem.</p>
                </div>
                @endforelse
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
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
