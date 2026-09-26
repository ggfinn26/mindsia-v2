@extends('layouts.dashboard')

@section('title', 'Permission Posisi: ' . $position->position_name)
@section('header_title', 'Permission Posisi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <a href="{{ route('system.permissions.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Manajemen Permission
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $position->position_name }}</h2>
        <p class="mt-1 text-sm text-slate-500">
            Role: <span class="font-semibold text-slate-700">{{ $position->role?->name }}</span>
            · {{ count($assigned) }} dari {{ $allPermissions->flatten()->count() }} permission aktif
        </p>
    </div>

    <form method="POST" action="{{ route('system.permissions.position.update', $position) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            @foreach ($allPermissions as $domain => $perms)
                <div class="overflow-hidden border border-slate-200 bg-white" x-data="{ open: {{ $perms->contains(fn($p) => in_array($p->id, $assigned)) ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                        class="flex w-full items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-3 text-left">
                        <div class="flex items-center gap-2">
                            <h3 class="font-jakarta text-xs font-bold uppercase tracking-wider text-slate-700">{{ $domain }}</h3>
                            @php $activeCount = $perms->filter(fn($p) => in_array($p->id, $assigned))->count(); @endphp
                            @if ($activeCount > 0)
                                <span class="inline-flex rounded-full bg-blue-100 px-1.5 py-0.5 text-xs font-semibold text-blue-700">{{ $activeCount }}/{{ $perms->count() }}</span>
                            @else
                                <span class="text-xs text-slate-400">{{ $perms->count() }} tersedia</span>
                            @endif
                        </div>
                        <span class="material-symbols-outlined text-[16px] text-slate-400" x-text="open ? 'expand_less' : 'expand_more'"></span>
                    </button>
                    <div x-show="open" class="divide-y divide-slate-50 px-5 py-2">
                        <div class="flex justify-end gap-3 py-2">
                            <button type="button"
                                @click="$el.closest('[x-data]').querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = true)"
                                class="text-xs font-semibold text-[#215aac] hover:underline">Pilih semua</button>
                            <button type="button"
                                @click="$el.closest('[x-data]').querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false)"
                                class="text-xs font-semibold text-slate-500 hover:underline">Hapus semua</button>
                        </div>
                        @foreach ($perms as $perm)
                            <label class="flex cursor-pointer items-center gap-3 py-2.5 hover:bg-slate-50/70">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                    {{ in_array($perm->id, $assigned) ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-slate-300 text-[#215aac] focus:ring-[#215aac]">
                                <span class="font-mono text-xs text-slate-700">{{ $perm->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="sticky bottom-0 mt-6 flex justify-end gap-3 border-t border-slate-200 bg-white px-0 py-4">
            <a href="{{ route('system.permissions.index') }}"
                class="inline-flex min-h-10 items-center border border-slate-300 px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
            <button type="submit"
                class="inline-flex min-h-10 items-center bg-[#215aac] px-6 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan Permission</button>
        </div>
    </form>
</div>
@endsection
