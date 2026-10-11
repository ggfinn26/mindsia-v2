@extends('layouts.dashboard')

@section('title', 'Manajemen Permission')
@section('header_title', 'Manajemen Permission')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ tab: '{{ request('tab') === 'users' ? 'users' : 'positions' }}' }">

    <div>
        <p class="text-sm font-medium text-[#725c00]">Sistem & Akses</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Manajemen Permission</h2>
        <p class="mt-1 text-sm text-slate-500">Atur permission per posisi atau tambah direct permission ke user tertentu.</p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex border-b border-slate-200">
        <button @click="tab = 'positions'" :class="tab === 'positions' ? 'border-b-2 border-[#215aac] text-[#215aac]' : 'text-slate-500 hover:text-slate-700'"
            class="px-5 py-3 text-sm font-semibold transition-colors">
            Permission per Posisi
        </button>
        <button @click="tab = 'users'" :class="tab === 'users' ? 'border-b-2 border-[#215aac] text-[#215aac]' : 'text-slate-500 hover:text-slate-700'"
            class="px-5 py-3 text-sm font-semibold transition-colors">
            Direct Permission User
            @if ($usersWithDirect->isNotEmpty())
                <span class="ml-1.5 inline-flex rounded-full bg-blue-100 px-1.5 py-0.5 text-xs font-semibold text-blue-700">{{ $usersWithDirect->count() }}</span>
            @endif
        </button>
    </div>

    {{-- Tab: Posisi --}}
    <div x-show="tab === 'positions'">
        <div class="overflow-hidden border border-slate-200 bg-white">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">
                <p class="text-xs font-semibold text-slate-500">Klik "Kelola" untuk atur permission posisi tersebut via role yang dimilikinya.</p>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($positions as $position)
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900">{{ $position->position_name }}</p>
                            <div class="mt-0.5 flex items-center gap-2">
                                @if ($position->role)
                                    <span class="font-mono text-xs text-slate-400">role: {{ $position->role->name }}</span>
                                    <span class="text-xs text-slate-400">·</span>
                                    <span class="text-xs text-slate-500">{{ $position->role->permissions->count() }} permission</span>
                                @else
                                    <span class="text-xs italic text-slate-400">Tidak punya role</span>
                                @endif
                            </div>
                        </div>
                        @if ($position->role && $position->role->name !== 'IT')
                            <a href="{{ route('system.permissions.position.edit', $position) }}"
                                class="inline-flex shrink-0 min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                <span class="material-symbols-outlined text-[14px] mr-1">tune</span> Kelola
                            </a>
                        @else
                            <span class="text-xs italic text-slate-400">—</span>
                        @endif
                    </div>
                @empty
                    <div class="px-5 py-12 text-center text-sm text-slate-400">Belum ada posisi.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tab: Direct User --}}
    <div x-show="tab === 'users'" style="display:none">
        <div class="overflow-hidden border border-slate-200 bg-white">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">
                <p class="text-xs font-semibold text-slate-500">Hanya user yang sudah memiliki direct permission yang ditampilkan. Klik "Tambah" untuk assign ke user lain.</p>
            </div>

            {{-- Cari user untuk tambah direct permission --}}
            <div class="border-b border-slate-100 px-5 py-4">
                <form method="GET" action="{{ route('system.permissions.index') }}" class="flex gap-2 items-end">
                    <input type="hidden" name="tab" value="users">
                    <div class="flex-1">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Cari user untuk assign direct permission</label>
                        <input type="text" name="user_search" value="{{ request('user_search') }}" placeholder="Nama atau email..."
                            class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    </div>
                    <button type="submit" class="inline-flex min-h-9 items-center bg-slate-700 px-4 text-xs font-semibold text-white hover:bg-slate-800">Cari</button>
                </form>
                @if (request('user_search'))
                    @php
                        $searchResults = \App\Models\User::where('name', 'like', '%'.request('user_search').'%')
                            ->orWhere('email', 'like', '%'.request('user_search').'%')
                            ->with('permissions')->limit(5)->get();
                    @endphp
                    @if ($searchResults->isNotEmpty())
                        <div class="mt-3 divide-y divide-slate-100 border border-slate-200 bg-white">
                            @foreach ($searchResults as $u)
                                <div class="flex items-center justify-between px-4 py-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $u->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $u->email }} · {{ $u->permissions->count() }} direct permission</p>
                                    </div>
                                    @if (auth()->id() !== $u->id)
                                        <a href="{{ route('system.permissions.user.edit', $u) }}"
                                            class="inline-flex min-h-8 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Kelola</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-2 text-xs text-slate-500">Tidak ada user ditemukan.</p>
                    @endif
                @endif
            </div>

            {{-- List user dengan direct permission --}}
            @if ($usersWithDirect->isEmpty())
                <div class="px-5 py-12 text-center">
                    <span class="material-symbols-outlined text-3xl text-slate-300">manage_accounts</span>
                    <p class="mt-2 text-sm font-semibold text-slate-600">Belum ada user dengan direct permission.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($usersWithDirect as $u)
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $u->name }}</p>
                                <p class="text-xs text-slate-500">{{ $u->email }} · {{ $u->permissions->count() }} direct permission</p>
                            </div>
                            @if (auth()->id() !== $u->id)
                                <a href="{{ route('system.permissions.user.edit', $u) }}"
                                    class="inline-flex shrink-0 min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    <span class="material-symbols-outlined text-[14px] mr-1">tune</span> Kelola
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
