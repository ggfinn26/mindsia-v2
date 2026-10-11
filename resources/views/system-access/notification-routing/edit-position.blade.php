@extends('layouts.dashboard')

@section('title', 'Routing Notifikasi: ' . $position->position_name)
@section('header_title', 'Routing Notifikasi')

@section('content')
@php
    $totalAvailable = collect($systemEvents)->flatten(1)->count();
    $activeCount = $assignedRoutings->count();
@endphp

<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header & Back Link --}}
    <div>
        <a href="{{ route('system.notification-routings.index') }}" class="dash-back inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-medium mb-3 transition-colors"
           style="border-color: var(--dash-border-subtle); color: var(--dash-text-muted); background-color: var(--dash-card-bg);">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Routing Notifikasi
        </a>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $position->position_name }}</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            @if ($position->role)
                Role: <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $position->role->name }}</span> ·
            @endif
            {{ $activeCount }} dari {{ $totalAvailable }} event notifikasi aktif untuk posisi ini
        </p>
    </div>

    <form method="POST" action="{{ route('system.notification-routings.position.update', $position) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            @foreach ($systemEvents as $domain => $events)
                @php
                    $domainActiveCount = collect($events)->filter(fn($meta, $key) => $assignedRoutings->has($key))->count();
                @endphp
                <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);"
                     x-data="{ open: {{ $domainActiveCount > 0 ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                        class="flex w-full items-center justify-between border-b px-5 py-3.5 text-left transition-colors"
                        style="border-color: var(--dash-border-subtle); background-color: var(--dash-page-bg);">
                        <div class="flex items-center gap-2">
                            <h2 class="font-jakarta text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">{{ $domain }}</h2>
                            @if ($domainActiveCount > 0)
                                <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $domainActiveCount }}/{{ count($events) }}</span>
                            @else
                                <span class="text-xs" style="color: var(--dash-text-muted);">{{ count($events) }} tersedia</span>
                            @endif
                        </div>
                        <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-text-muted);" x-text="open ? 'expand_less' : 'expand_more'"></span>
                    </button>
                    <div x-show="open" class="divide-y px-5 py-2" style="border-color: var(--dash-border-subtle);">
                        <div class="flex justify-end gap-3 py-2">
                            <button type="button"
                                @click="$el.closest('[x-data]').querySelectorAll('.event-toggle').forEach(cb => { cb.checked = true; cb.dispatchEvent(new Event('change')); })"
                                class="text-xs font-semibold hover:underline" style="color: var(--dash-primary);">Pilih semua</button>
                            <button type="button"
                                @click="$el.closest('[x-data]').querySelectorAll('.event-toggle').forEach(cb => { cb.checked = false; cb.dispatchEvent(new Event('change')); })"
                                class="text-xs font-semibold hover:underline" style="color: var(--dash-text-muted);">Hapus semua</button>
                        </div>
                        @foreach ($events as $eventKey => $meta)
                            @php
                                $existingRows = $assignedRoutings->get($eventKey, collect());
                                $isEventActive = $existingRows->isNotEmpty();
                                $activeChannels = $existingRows->pluck('channel')->toArray();
                                if (empty($activeChannels)) {
                                    $activeChannels = [$meta['default_channel'] ?? 'in-app'];
                                }
                                $activeScope = $existingRows->first()?->scope ?? ($meta['default_scope'] ?? 'branch');
                            @endphp
                            <div class="py-3.5 transition-colors hover:bg-slate-50/70" x-data="{
                                enabled: {{ $isEventActive ? 'true' : 'false' }},
                                scope: '{{ $activeScope }}'
                            }">
                                <div class="flex items-start justify-between gap-4">
                                    <label class="flex cursor-pointer items-start gap-3 min-w-0 flex-1">
                                        <input type="checkbox" name="events[{{ $eventKey }}][enabled]" value="1"
                                            x-model="enabled"
                                            class="event-toggle mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold leading-snug" style="color: var(--dash-text-heading);">{{ $meta['label'] }}</p>
                                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $meta['desc'] }}</p>
                                            <p class="font-mono text-[10px] mt-0.5" style="color: var(--dash-text-muted);">{{ $eventKey }}</p>
                                        </div>
                                    </label>
                                </div>

                                {{-- Channels and Scope config (shown when enabled) --}}
                                <div x-show="enabled" class="mt-3 ml-7 pt-3 border-t flex flex-wrap items-center gap-6" style="border-color: var(--dash-border-subtle);">
                                    <div class="flex items-center gap-4">
                                        <span class="text-xs font-semibold" style="color: var(--dash-text-heading);">Channel:</span>
                                        <label class="inline-flex items-center gap-1.5 text-xs cursor-pointer" style="color: var(--dash-text-body);">
                                            <input type="checkbox" name="events[{{ $eventKey }}][channels][]" value="in-app"
                                                {{ in_array('in-app', $activeChannels) ? 'checked' : '' }}
                                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                            <span>In-App</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 text-xs cursor-pointer" style="color: var(--dash-text-body);">
                                            <input type="checkbox" name="events[{{ $eventKey }}][channels][]" value="email"
                                                {{ in_array('email', $activeChannels) ? 'checked' : '' }}
                                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                            <span>Email</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 text-xs cursor-pointer" style="color: var(--dash-text-body);">
                                            <input type="checkbox" name="events[{{ $eventKey }}][channels][]" value="telegram"
                                                {{ in_array('telegram', $activeChannels) ? 'checked' : '' }}
                                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                            <span>Telegram</span>
                                        </label>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold" style="color: var(--dash-text-heading);">Scope:</span>
                                        <select name="events[{{ $eventKey }}][scope]" x-model="scope"
                                            class="rounded-lg text-xs py-1 px-2.5 transition-colors focus:ring-1"
                                            style="border-color: var(--dash-border-subtle); background-color: var(--dash-card-bg); color: var(--dash-text-heading);">
                                            <option value="branch">Branch (Cabang yang sama)</option>
                                            <option value="area">Area (Wilayah yang sama)</option>
                                            <option value="global">Global (Semua Cabang)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="sticky bottom-0 mt-6 flex justify-end gap-3 border-t p-4 rounded-2xl shadow-lg"
             style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);">
            <a href="{{ route('system.notification-routings.index') }}"
                class="inline-flex min-h-10 items-center rounded-lg border px-5 text-sm font-semibold transition-colors hover:bg-slate-100/70"
                style="border-color: var(--dash-border-subtle); color: var(--dash-text-heading);">Batal</a>
            <button type="submit"
                class="inline-flex min-h-10 items-center rounded-lg px-6 text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
                style="background-color: var(--dash-primary);">Simpan Routing</button>
        </div>
    </form>
</div>
@endsection
