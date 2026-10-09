@extends('layouts.dashboard')

@section('title', 'Permission Posisi: ' . $position->position_name . ' - MINDSIA')
@section('header_title', 'Permission Posisi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Back + header --}}
    <div>
        <a href="{{ route('system.permissions.index') }}"
           class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider hover:opacity-75 transition-opacity"
           style="color: var(--dash-text-muted);">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Manajemen Permission
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">{{ $position->position_name }}</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            Role: <span class="font-semibold" style="color: var(--dash-text-body);">{{ $position->role?->name }}</span>
            · {{ count($assigned) }} dari {{ $allPermissions->flatten()->count() }} permission aktif
        </p>
    </div>

    <form method="POST" action="{{ route('system.permissions.position.update', $position) }}">
        @csrf
        @method('PUT')

        <div class="space-y-3">
            @foreach($allPermissions as $domain => $perms)
                @php $activeCount = $perms->filter(fn($p) => in_array($p->id, $assigned))->count(); @endphp
                <div class="rounded-2xl overflow-hidden"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                     x-data="{ open: {{ $activeCount > 0 ? 'true' : 'false' }} }">

                    {{-- Accordion header --}}
                    <button type="button" @click="open = !open"
                            class="flex w-full items-center justify-between px-5 py-3 text-left transition-colors"
                            style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);"
                            onmouseover="this.style.opacity='0.85'"
                            onmouseout="this.style.opacity='1'">
                        <div class="flex items-center gap-2">
                            <h3 class="font-jakarta text-xs font-bold uppercase tracking-wider"
                                style="color: var(--dash-text-heading);">{{ $domain }}</h3>
                            @if($activeCount > 0)
                                <span class="inline-flex rounded-full px-1.5 py-0.5 text-xs font-semibold"
                                      style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                                    {{ $activeCount }}/{{ $perms->count() }}
                                </span>
                            @else
                                <span class="text-xs" style="color: var(--dash-text-faint);">
                                    {{ $perms->count() }} tersedia
                                </span>
                            @endif
                        </div>
                        <span class="material-symbols-outlined text-[16px]"
                              style="color: var(--dash-text-muted);"
                              x-text="open ? 'expand_less' : 'expand_more'"></span>
                    </button>

                    {{-- Accordion body --}}
                    <div x-show="open" class="px-5 py-2">
                        <div class="flex justify-end gap-4 py-2" style="border-bottom: 1px solid var(--dash-card-border);">
                            <button type="button"
                                    @click="$el.closest('[x-data]').querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = true)"
                                    class="text-xs font-semibold hover:opacity-75 transition-opacity"
                                    style="color: var(--dash-primary);">Pilih semua</button>
                            <button type="button"
                                    @click="$el.closest('[x-data]').querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false)"
                                    class="text-xs font-semibold hover:opacity-75 transition-opacity"
                                    style="color: var(--dash-text-muted);">Hapus semua</button>
                        </div>
                        @foreach($perms as $perm)
                            @php $meta = \App\Support\PermissionLabels::get($perm->name); @endphp
                            <label class="flex cursor-pointer items-start gap-3 py-2.5"
                                   style="border-bottom: 1px solid var(--dash-card-border);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor=''">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                       {{ in_array($perm->id, $assigned) ? 'checked' : '' }}
                                       class="mt-0.5 h-4 w-4 shrink-0 rounded"
                                       style="accent-color: var(--dash-primary);">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium leading-snug"
                                       style="color: var(--dash-text-body);">{{ $meta['label'] }}</p>
                                    @if($meta['description'])
                                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $meta['description'] }}</p>
                                    @endif
                                    <p class="font-mono text-[10px] mt-0.5" style="color: var(--dash-text-faint);">{{ $perm->name }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Sticky footer --}}
        <div class="sticky bottom-0 mt-6 flex flex-col sm:flex-row justify-end gap-3 py-4"
             style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);">
            <a href="{{ route('system.permissions.index') }}"
               class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Simpan Permission
            </button>
        </div>
    </form>
</div>
@endsection
