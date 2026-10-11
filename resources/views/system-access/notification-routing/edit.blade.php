@extends('layouts.dashboard')

@section('title', 'Edit Routing Notifikasi')
@section('header_title', 'Routing Notifikasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('system.notification-routings.index') }}" class="dash-back inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-medium mb-3 transition-colors"
           style="border-color: var(--dash-border-subtle); color: var(--dash-text-muted); background-color: var(--dash-card-bg);">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Routing Notifikasi
        </a>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Aturan Routing</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Perbarui konfigurasi event kustom, channel, dan scope.</p>
    </div>

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-700 shadow-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);">
        <form action="{{ route('system.notification-routings.update', $routing) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-heading);">Event Key <span class="text-red-500">*</span></label>
                <input name="event_key" type="text" value="{{ old('event_key', $routing->event_key) }}"
                    class="w-full rounded-lg text-sm font-mono transition-colors focus:ring-1"
                    style="border-color: var(--dash-border-subtle); background-color: var(--dash-card-bg); color: var(--dash-text-heading);" required />
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-heading);">Target Posisi</label>
                <select name="position_id" class="w-full rounded-lg text-sm transition-colors focus:ring-1"
                    style="border-color: var(--dash-border-subtle); background-color: var(--dash-card-bg); color: var(--dash-text-heading);">
                    <option value="">Semua Posisi</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ old('position_id', $routing->position_id) == $pos->id ? 'selected' : '' }}>{{ $pos->position_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-heading);">Channel <span class="text-red-500">*</span></label>
                    <select name="channel" class="w-full rounded-lg text-sm transition-colors focus:ring-1"
                        style="border-color: var(--dash-border-subtle); background-color: var(--dash-card-bg); color: var(--dash-text-heading);" required>
                        <option value="in-app" {{ old('channel', $routing->channel) === 'in-app' ? 'selected' : '' }}>In-App</option>
                        <option value="email" {{ old('channel', $routing->channel) === 'email' ? 'selected' : '' }}>Email</option>
                        <option value="telegram" {{ old('channel', $routing->channel) === 'telegram' ? 'selected' : '' }}>Telegram</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-heading);">Scope <span class="text-red-500">*</span></label>
                    <select name="scope" class="w-full rounded-lg text-sm transition-colors focus:ring-1"
                        style="border-color: var(--dash-border-subtle); background-color: var(--dash-card-bg); color: var(--dash-text-heading);" required>
                        <option value="global" {{ old('scope', $routing->scope) === 'global' ? 'selected' : '' }}>Global</option>
                        <option value="branch" {{ old('scope', $routing->scope) === 'branch' ? 'selected' : '' }}>Branch</option>
                        <option value="area" {{ old('scope', $routing->scope) === 'area' ? 'selected' : '' }}>Area</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-heading);">Deskripsi</label>
                <input name="description" type="text" value="{{ old('description', $routing->description) }}"
                    class="w-full rounded-lg text-sm transition-colors focus:ring-1"
                    style="border-color: var(--dash-border-subtle); background-color: var(--dash-card-bg); color: var(--dash-text-heading);" />
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                    {{ old('is_active', $routing->is_active) ? 'checked' : '' }} />
                <label for="is_active" class="text-sm font-semibold cursor-pointer" style="color: var(--dash-text-heading);">Aktifkan aturan ini</label>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t" style="border-color: var(--dash-border-subtle);">
                <a href="{{ route('system.notification-routings.index') }}"
                    class="rounded-lg border px-5 py-2 text-sm font-semibold transition-colors hover:bg-slate-100/70"
                    style="border-color: var(--dash-border-subtle); color: var(--dash-text-heading);">Batal</a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg px-5 py-2 text-sm font-bold text-white shadow-sm transition-opacity hover:opacity-90"
                    style="background-color: var(--dash-primary);">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
