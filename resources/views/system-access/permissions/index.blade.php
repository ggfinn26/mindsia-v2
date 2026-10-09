@extends('layouts.dashboard')

@section('title', 'Manajemen Permission - MINDSIA')
@section('header_title', 'Manajemen Permission')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{ tab: '{{ request('tab') === 'users' ? 'users' : 'positions' }}' }">

    {{-- Page header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Sistem & Akses</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Manajemen Permission</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            Atur permission per posisi atau tambah direct permission ke user tertentu.
        </p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex" style="border-bottom: 2px solid var(--dash-card-border);">
        <button @click="tab = 'positions'"
                :style="tab === 'positions' ? 'border-bottom: 2px solid var(--dash-primary); color: var(--dash-primary); margin-bottom: -2px;' : 'color: var(--dash-text-muted);'"
                class="px-5 py-3 text-sm font-semibold transition-colors">
            Permission per Posisi
        </button>
        <button @click="tab = 'users'"
                :style="tab === 'users' ? 'border-bottom: 2px solid var(--dash-primary); color: var(--dash-primary); margin-bottom: -2px;' : 'color: var(--dash-text-muted);'"
                class="px-5 py-3 text-sm font-semibold transition-colors">
            Direct Permission User
            @if($usersWithDirect->isNotEmpty())
                <span class="ml-1.5 inline-flex rounded-full px-1.5 py-0.5 text-xs font-semibold"
                      style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                    {{ $usersWithDirect->count() }}
                </span>
            @endif
        </button>
    </div>

    {{-- Tab: Posisi --}}
    <div x-show="tab === 'positions'">
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-5 py-3"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <p class="text-xs" style="color: var(--dash-text-muted);">
                    Klik "Kelola" untuk atur permission posisi tersebut via role yang dimilikinya.
                </p>
            </div>
            <div>
                @forelse($positions as $position)
                    <div class="flex items-center justify-between gap-4 px-5 py-4"
                         style="border-bottom: 1px solid var(--dash-card-border);"
                         onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                         onmouseout="this.style.backgroundColor=''">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                                {{ $position->position_name }}
                            </p>
                            <div class="mt-0.5 flex items-center gap-2">
                                @if($position->role)
                                    <span class="font-mono text-xs" style="color: var(--dash-text-faint);">
                                        role: {{ $position->role->name }}
                                    </span>
                                    <span style="color: var(--dash-text-faint);">·</span>
                                    <span class="text-xs" style="color: var(--dash-text-muted);">
                                        {{ $position->role->permissions->count() }} permission
                                    </span>
                                @else
                                    <span class="text-xs italic" style="color: var(--dash-text-faint);">Tidak punya role</span>
                                @endif
                            </div>
                        </div>
                        @if($position->role && $position->role->name !== 'IT')
                            <a href="{{ route('system.permissions.position.edit', $position) }}"
                               class="inline-flex shrink-0 items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                               onmouseover="this.style.backgroundColor='var(--dash-primary-bg)'; this.style.color='var(--dash-primary)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-body)'">
                                <span class="material-symbols-outlined text-[14px]">tune</span>
                                Kelola
                            </a>
                        @else
                            <span class="text-xs" style="color: var(--dash-text-faint);">—</span>
                        @endif
                    </div>
                @empty
                    <div class="px-5 py-12 text-center text-sm" style="color: var(--dash-text-faint);">
                        Belum ada posisi.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tab: Direct User --}}
    <div x-show="tab === 'users'" style="display:none">
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-5 py-3"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <p class="text-xs" style="color: var(--dash-text-muted);">
                    Hanya user yang sudah memiliki direct permission yang ditampilkan. Klik "Tambah" untuk assign ke user lain.
                </p>
            </div>

            {{-- Search user --}}
            <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
                <form method="GET" action="{{ route('system.permissions.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                    <input type="hidden" name="tab" value="users">
                    <div class="flex-1">
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">
                            Cari user untuk assign direct permission
                        </label>
                        <input type="text" name="user_search" value="{{ request('user_search') }}"
                               placeholder="Nama atau email...">
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">search</span>
                        Cari
                    </button>
                </form>

                @if(request('user_search'))
                    @php
                        $searchResults = \App\Models\User::where('name', 'like', '%'.request('user_search').'%')
                            ->orWhere('email', 'like', '%'.request('user_search').'%')
                            ->with('permissions')->limit(5)->get();
                    @endphp
                    <div class="mt-3">
                        @if($searchResults->isNotEmpty())
                            <div class="rounded-xl overflow-hidden"
                                 style="border: 1px solid var(--dash-card-border);">
                                @foreach($searchResults as $u)
                                    <div class="flex items-center justify-between gap-3 px-4 py-3"
                                         style="border-bottom: 1px solid var(--dash-card-border);">
                                        <div>
                                            <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $u->name }}</p>
                                            <p class="text-xs" style="color: var(--dash-text-muted);">
                                                {{ $u->email }} · {{ $u->permissions->count() }} direct permission
                                            </p>
                                        </div>
                                        @if(auth()->id() !== $u->id)
                                            <a href="{{ route('system.permissions.user.edit', $u) }}"
                                               class="inline-flex shrink-0 items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">tune</span>Kelola
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs" style="color: var(--dash-text-muted);">Tidak ada user ditemukan.</p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- User list with direct perms --}}
            @if($usersWithDirect->isEmpty())
                <div class="px-5 py-12 text-center">
                    <span class="material-symbols-outlined text-[40px] block mx-auto mb-2"
                          style="color: var(--dash-text-faint);">manage_accounts</span>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
                        Belum ada user dengan direct permission.
                    </p>
                </div>
            @else
                <div>
                    @foreach($usersWithDirect as $u)
                        <div class="flex items-center justify-between gap-4 px-5 py-4"
                             style="border-bottom: 1px solid var(--dash-card-border);"
                             onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                             onmouseout="this.style.backgroundColor=''">
                            <div>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $u->name }}</p>
                                <p class="text-xs" style="color: var(--dash-text-muted);">
                                    {{ $u->email }} · {{ $u->permissions->count() }} direct permission
                                </p>
                            </div>
                            @if(auth()->id() !== $u->id)
                                <a href="{{ route('system.permissions.user.edit', $u) }}"
                                   class="inline-flex shrink-0 items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">tune</span>Kelola
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
