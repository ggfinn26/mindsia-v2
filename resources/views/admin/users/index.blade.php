@extends('layouts.dashboard')

@section('title', 'Manajemen Akun Pegawai')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-end mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl text-[var(--dash-text-heading)] font-bold tracking-tight">Manajemen Akun</h1>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Kelola akun, divisi (role), dan kata sandi pegawai sistem.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-md bg-emerald-50 border border-emerald-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-md bg-rose-50 border border-rose-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-rose-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-rose-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter & Table -->
    <div class="bg-[var(--dash-card-bg)] shadow-sm border border-[var(--dash-card-border)] rounded-lg overflow-hidden">
        <!-- Filter Form -->
        <div class="px-6 py-4 border-b border-[var(--dash-card-border)] bg-[var(--dash-page-bg)]">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1 w-full sm:w-auto">
                    <label for="search" class="block text-xs font-medium text-[var(--dash-text-muted)] mb-1">Cari Nama/Email/NIK</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full rounded-lg border border-[var(--dash-input-border)] px-3 py-2 text-sm bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)]">
                </div>
                <div class="w-full sm:w-48">
                    <label for="role" class="block text-xs font-medium text-[var(--dash-text-muted)] mb-1">Filter Divisi (Role)</label>
                    <select name="role" id="role" class="w-full rounded-lg border border-[var(--dash-input-border)] px-3 py-2 text-sm bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)]">
                        <option value="">Semua Divisi</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ request('role') == $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <label for="status" class="block text-xs font-medium text-[var(--dash-text-muted)] mb-1">Status Akun</label>
                    <select name="status" id="status" class="w-full rounded-lg border border-[var(--dash-input-border)] px-3 py-2 text-sm bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)]">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <!-- Retain sorting params -->
                <input type="hidden" name="sort" value="{{ request('sort', 'created_at') }}">
                <input type="hidden" name="dir" value="{{ request('dir', 'desc') }}">
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[var(--dash-primary)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-colors">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'role', 'status']))
                        <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center rounded-lg border border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] px-4 py-2 text-sm font-semibold text-[var(--dash-text-body)] shadow-sm hover:bg-[var(--dash-page-bg)] transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)]">
                <thead class="bg-[var(--dash-page-bg)]">
                    <tr>
                        @php
                            $sortCol = request('sort', 'created_at');
                            $sortDir = request('dir', 'desc');
                            $nextDir = $sortDir === 'asc' ? 'desc' : 'asc';
                        @endphp
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'dir' => $sortCol === 'name' ? $nextDir : 'asc']) }}" class="group inline-flex items-center gap-1 hover:text-[var(--dash-text-heading)]">
                                Nama & Email
                                @if($sortCol === 'name')
                                    <span class="material-symbols-outlined text-[14px]">{{ $sortDir === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-[14px] opacity-0 group-hover:opacity-50">sort</span>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider whitespace-nowrap">Divisi (Role)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'is_active', 'dir' => $sortCol === 'is_active' ? $nextDir : 'asc']) }}" class="group inline-flex items-center gap-1 hover:text-[var(--dash-text-heading)]">
                                Status Akun
                                @if($sortCol === 'is_active')
                                    <span class="material-symbols-outlined text-[14px]">{{ $sortDir === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-[14px] opacity-0 group-hover:opacity-50">sort</span>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'last_login_at', 'dir' => $sortCol === 'last_login_at' ? $nextDir : 'desc']) }}" class="group inline-flex items-center gap-1 hover:text-[var(--dash-text-heading)]">
                                Login Terakhir
                                @if($sortCol === 'last_login_at')
                                    <span class="material-symbols-outlined text-[14px]">{{ $sortDir === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-[14px] opacity-0 group-hover:opacity-50">sort</span>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="relative px-6 py-3 text-right">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)] bg-[var(--dash-card-bg)]">
                    @forelse($users as $user)
                        <tr class="hover:bg-[var(--dash-page-bg)] transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-[var(--dash-text-heading)]">{{ $user->name }}</div>
                                <div class="text-xs text-[var(--dash-text-muted)] mt-0.5">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($user->roles as $role)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 [.mode-dark_&]:bg-blue-500/10 [.mode-dark_&]:text-blue-400 [.mode-dark_&]:border-blue-500/20">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-[var(--dash-text-faint)] italic">Belum ada role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center w-max px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 [.mode-dark_&]:bg-emerald-500/10 [.mode-dark_&]:text-emerald-400 [.mode-dark_&]:border-emerald-500/20">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center w-max px-2 py-0.5 rounded text-[11px] font-bold bg-red-50 text-red-700 border border-red-200 [.mode-dark_&]:bg-red-500/10 [.mode-dark_&]:text-red-400 [.mode-dark_&]:border-red-500/20">Nonaktif</span>
                                    @endif
                                    
                                    @if($user->email_verified_at)
                                        <span class="text-[10px] text-[var(--dash-text-muted)] flex items-center gap-1"><span class="material-symbols-outlined text-[12px] text-emerald-500">check_circle</span> Verified</span>
                                    @else
                                        <span class="text-[10px] text-[var(--dash-text-muted)] flex items-center gap-1"><span class="material-symbols-outlined text-[12px] text-amber-500">pending</span> Unverified</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-[var(--dash-text-body)]">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('users.show', $user->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors mr-2"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>Lihat
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors mr-2"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                    </a>
                                    <a href="{{ route('users.reset-password.show', $user->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                       style="background-color: #dc2626;">
                                        <span class="material-symbols-outlined text-[14px]">lock_reset</span>Reset
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-4xl text-[var(--dash-text-faint)] mb-2">person_off</span>
                                <h3 class="text-sm font-bold text-[var(--dash-text-heading)]">Belum ada data</h3>
                                <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Tidak ada akun pegawai yang ditemukan sesuai filter Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-[var(--dash-card-border)] bg-[var(--dash-page-bg)]">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
