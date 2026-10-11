@extends('layouts.dashboard')

@section('title', 'Manajemen Akun Pegawai - MINDSIA')
@section('header_title', 'Manajemen Akun Pegawai')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">SDM & Akses</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Manajemen Akun Pegawai</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola akun, divisi (role), dan kata sandi pegawai sistem.
            </p>
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

    {{-- Filter Card --}}
    <form method="GET" action="{{ route('users.index') }}"
          class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex-1 min-w-[240px]">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Cari Akun</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                      style="color: var(--dash-text-muted);">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..."
                       class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                       style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
            </div>
        </div>

        <div class="w-full sm:w-48">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Divisi (Role)</label>
            <select name="role" class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Role</option>
                @foreach($roles as $roleName)
                    <option value="{{ $roleName }}" @selected(request('role') === $roleName)>{{ $roleName }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-full sm:w-40">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Status Akun</label>
            <select name="status" class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                Filter
            </button>
            @if(request()->hasAny(['search', 'role', 'status']))
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                Reset
            </a>
            @endif
        </div>
    </form>

    {{-- Tabel Container --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Nama & Email</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Divisi (Role)</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status Akun</th>
                        <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="font-semibold text-sm" style="color: var(--dash-text-heading);">{{ $user->name }}</div>
                            <div class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $user->email }}</div>
                            @if($user->employee?->employee_code)
                                <div class="text-[11px] mt-0.5" style="color: var(--dash-text-faint);">{{ $user->employee->employee_code }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($user->roles as $role)
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                          style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-xs" style="color: var(--dash-text-faint);">Tidak ada role</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($user->is_active)
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Aktif</span>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-right">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                <a href="{{ route('users.show', $user->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    Lihat
                                </a>
                                <a href="{{ route('users.edit', $user->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                    Edit
                                </a>
                                @can('auth.user.force_reset_password')
                                <a href="{{ route('users.reset-password.show', $user->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">lock_reset</span>
                                    Reset Sandi
                                </a>
                                @endcan
                                @can('system.permission.manage')
                                <a href="{{ route('users.roles', $user->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">shield</span>
                                    Role
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">group_off</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data akun pegawai.</p>
                            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Tidak ada akun pegawai yang sesuai dengan kriteria filter.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
        <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
