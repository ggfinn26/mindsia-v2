@extends('layouts.dashboard')

@section('title', 'Manajemen Permission')
@section('header_title', 'Manajemen Permission')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ tab: '{{ request('tab') === 'users' ? 'users' : 'positions' }}' }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider font-jakarta" style="color: var(--dash-nav-section);">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight mt-1" style="color: var(--dash-text-heading);">Manajemen Permission</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Atur permission per posisi atau tambah direct permission ke user tertentu.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex border-b" style="border-color: var(--dash-border-subtle);">
        <button @click="tab = 'positions'"
            :class="tab === 'positions' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80'"
            :style="tab === 'positions' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);'"
            class="px-5 py-3 text-sm transition-colors">
            Permission per Posisi
        </button>
        <button @click="tab = 'users'"
            :class="tab === 'users' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80'"
            :style="tab === 'users' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);'"
            class="px-5 py-3 text-sm transition-colors">
            Direct Permission User
            @if ($usersWithDirect->isNotEmpty())
                <span class="ml-1.5 inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $usersWithDirect->count() }}</span>
            @endif
        </button>
    </div>

    {{-- Tab: Posisi --}}
    <div x-show="tab === 'positions'">
        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);">
            <div class="border-b px-5 py-3.5" style="border-color: var(--dash-border-subtle); background-color: var(--dash-page-bg);">
                <p class="text-xs font-semibold" style="color: var(--dash-text-muted);">Klik "Kelola" untuk atur permission posisi tersebut via role yang dimilikinya.</p>
            </div>
            <div class="divide-y" style="border-color: var(--dash-border-subtle);">
                @forelse ($positions as $position)
                    <div class="flex items-center justify-between gap-4 px-5 py-4 transition-colors hover:bg-slate-50/50">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $position->position_name }}</p>
                            <div class="mt-0.5 flex items-center gap-2">
                                @if ($position->role)
                                    <span class="font-mono text-xs" style="color: var(--dash-text-muted);">role: {{ $position->role->name }}</span>
                                    <span class="text-xs text-slate-300">·</span>
                                    <span class="text-xs font-medium text-slate-500">{{ $position->role->permissions->count() }} permission aktif</span>
                                @else
                                    <span class="text-xs italic" style="color: var(--dash-text-muted);">Tidak punya role</span>
                                @endif
                            </div>
                        </div>
                        @if ($position->role && $position->role->name !== 'IT')
                            <a href="{{ route('system.permissions.position.edit', $position) }}"
                                class="inline-flex shrink-0 min-h-9 items-center rounded-lg border px-3 text-xs font-semibold transition-colors hover:bg-slate-100/70"
                                style="border-color: var(--dash-border-subtle); color: var(--dash-text-heading);">
                                <span class="material-symbols-outlined text-[15px] mr-1" style="color: var(--dash-primary);">tune</span> Kelola
                            </a>
                        @else
                            <span class="text-xs italic px-3" style="color: var(--dash-text-muted);">Terkunci</span>
                        @endif
                    </div>
                @empty
                    <div class="px-5 py-12 text-center text-sm" style="color: var(--dash-text-muted);">Belum ada posisi.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tab: Direct User --}}
    <div x-show="tab === 'users'" style="display:none">
        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);">
            <div class="border-b px-5 py-3.5" style="border-color: var(--dash-border-subtle); background-color: var(--dash-page-bg);">
                <p class="text-xs font-semibold" style="color: var(--dash-text-muted);">Hanya user yang sudah memiliki direct permission yang ditampilkan. Cari user di bawah untuk assign direct permission baru.</p>
            </div>

            {{-- Cari user untuk tambah direct permission --}}
            <div class="border-b px-5 py-4" style="border-color: var(--dash-border-subtle);">
                <form method="GET" action="{{ route('system.permissions.index') }}" class="flex gap-2 items-end">
                    <input type="hidden" name="tab" value="users">
                    <div class="flex-1">
                        <label class="mb-1 block text-xs font-medium" style="color: var(--dash-text-heading);">Cari user untuk assign direct permission</label>
                        <input type="text" name="user_search" value="{{ request('user_search') }}" placeholder="Nama atau email..."
                            class="block w-full rounded-lg text-sm transition-colors focus:ring-1"
                            style="border-color: var(--dash-border-subtle); background-color: var(--dash-card-bg); color: var(--dash-text-heading);">
                    </div>
                    <button type="submit" class="inline-flex min-h-9 items-center rounded-lg px-4 text-xs font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">Cari</button>
                </form>
                @if (request('user_search'))
                    @php
                        $searchResults = \App\Models\User::where('name', 'like', '%'.request('user_search').'%')
                            ->orWhere('email', 'like', '%'.request('user_search').'%')
                            ->with('permissions')->limit(5)->get();
                    @endphp
                    @if ($searchResults->isNotEmpty())
                        <div class="mt-3 divide-y rounded-xl border" style="border-color: var(--dash-border-subtle);">
                            @foreach ($searchResults as $u)
                                <div class="flex items-center justify-between px-4 py-3">
                                    <div>
                                        <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $u->name }}</p>
                                        <p class="text-xs" style="color: var(--dash-text-muted);">{{ $u->email }} · {{ $u->permissions->count() }} direct permission</p>
                                    </div>
                                    @if (auth()->id() !== $u->id)
                                        <a href="{{ route('system.permissions.user.edit', $u) }}"
                                            class="inline-flex min-h-8 items-center rounded-lg border px-3 text-xs font-semibold transition-colors hover:bg-slate-100/70"
                                            style="border-color: var(--dash-border-subtle); color: var(--dash-text-heading);">Kelola</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-2 text-xs" style="color: var(--dash-text-muted);">Tidak ada user ditemukan.</p>
                    @endif
                @endif
            </div>

            {{-- List user dengan direct permission --}}
            @if ($usersWithDirect->isEmpty())
                <div class="px-5 py-12 text-center">
                    <span class="material-symbols-outlined text-3xl" style="color: var(--dash-text-muted);">manage_accounts</span>
                    <p class="mt-2 text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada user dengan direct permission.</p>
                </div>
            @else
                <div class="divide-y" style="border-color: var(--dash-border-subtle);">
                    @foreach ($usersWithDirect as $u)
                        <div class="flex items-center justify-between gap-4 px-5 py-4 transition-colors hover:bg-slate-50/50">
                            <div>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $u->name }}</p>
                                <p class="text-xs" style="color: var(--dash-text-muted);">{{ $u->email }} · {{ $u->permissions->count() }} direct permission</p>
                            </div>
                            @if (auth()->id() !== $u->id)
                                <a href="{{ route('system.permissions.user.edit', $u) }}"
                                    class="inline-flex shrink-0 min-h-9 items-center rounded-lg border px-3 text-xs font-semibold transition-colors hover:bg-slate-100/70"
                                    style="border-color: var(--dash-border-subtle); color: var(--dash-text-heading);">
                                    <span class="material-symbols-outlined text-[15px] mr-1" style="color: var(--dash-primary);">tune</span> Kelola
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
