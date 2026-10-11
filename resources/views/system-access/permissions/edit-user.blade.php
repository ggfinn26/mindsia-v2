@extends('layouts.dashboard')

@section('title', 'Direct Permission: ' . $user->name)
@section('header_title', 'Direct Permission User')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header & Back Link --}}
    <div>
        <a href="{{ route('system.permissions.index') }}?tab=users" class="dash-back inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-medium mb-3 transition-colors"
           style="border-color: var(--dash-border-subtle); color: var(--dash-text-muted); background-color: var(--dash-card-bg);">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Manajemen Permission
        </a>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $user->name }}</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            {{ $user->email }}
            · {{ count($assigned) }} direct permission aktif
        </p>
        <div class="mt-2.5 rounded-xl border border-amber-200 bg-amber-50/80 px-3.5 py-2.5 text-xs text-amber-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-[16px] text-amber-600 shrink-0">info</span>
            <span>Direct permission bersifat tambahan, tidak mengganti permission dari role/posisi user ini.</span>
        </div>
    </div>

    <form method="POST" action="{{ route('system.permissions.user.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            @foreach ($allPermissions as $domain => $perms)
                <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);"
                     x-data="{ open: {{ $perms->contains(fn($p) => in_array($p->id, $assigned)) ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                        class="flex w-full items-center justify-between border-b px-5 py-3.5 text-left transition-colors"
                        style="border-color: var(--dash-border-subtle); background-color: var(--dash-page-bg);">
                        <div class="flex items-center gap-2">
                            <h2 class="font-jakarta text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">{{ $domain }}</h2>
                            @php $activeCount = $perms->filter(fn($p) => in_array($p->id, $assigned))->count(); @endphp
                            @if ($activeCount > 0)
                                <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $activeCount }}/{{ $perms->count() }}</span>
                            @else
                                <span class="text-xs" style="color: var(--dash-text-muted);">{{ $perms->count() }} tersedia</span>
                            @endif
                        </div>
                        <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-text-muted);" x-text="open ? 'expand_less' : 'expand_more'"></span>
                    </button>
                    <div x-show="open" class="divide-y px-5 py-2" style="border-color: var(--dash-border-subtle);">
                        <div class="flex justify-end gap-3 py-2">
                            <button type="button"
                                @click="$el.closest('[x-data]').querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = true)"
                                class="text-xs font-semibold hover:underline" style="color: var(--dash-primary);">Pilih semua</button>
                            <button type="button"
                                @click="$el.closest('[x-data]').querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false)"
                                class="text-xs font-semibold hover:underline" style="color: var(--dash-text-muted);">Hapus semua</button>
                        </div>
                        @foreach ($perms as $perm)
                            @php $meta = \App\Support\PermissionLabels::get($perm->name); @endphp
                            <label class="flex cursor-pointer items-start gap-3 py-2.5 transition-colors hover:bg-slate-50/70">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                    {{ in_array($perm->id, $assigned) ? 'checked' : '' }}
                                    class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium leading-snug" style="color: var(--dash-text-heading);">{{ $meta['label'] }}</p>
                                    @if($meta['description'])
                                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $meta['description'] }}</p>
                                    @endif
                                    <p class="font-mono text-[10px] mt-0.5" style="color: var(--dash-text-muted);">{{ $perm->name }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="sticky bottom-0 mt-6 flex justify-end gap-3 border-t p-4 rounded-2xl shadow-lg"
             style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);">
            <a href="{{ route('system.permissions.index') }}?tab=users"
                class="inline-flex min-h-10 items-center rounded-lg border px-5 text-sm font-semibold transition-colors hover:bg-slate-100/70"
                style="border-color: var(--dash-border-subtle); color: var(--dash-text-heading);">Batal</a>
            <button type="submit"
                class="inline-flex min-h-10 items-center rounded-lg px-6 text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
                style="background-color: var(--dash-primary);">Simpan Permission</button>
        </div>
    </form>
</div>
@endsection
