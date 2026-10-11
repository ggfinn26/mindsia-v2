@extends('layouts.dashboard')

@section('header_title', 'Edit Routing Notifikasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('system.notification-routings.index') }}" class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Routing
        </a>
        <h1 class="mt-2 font-jakarta text-2xl font-bold tracking-tight text-slate-900">Edit Aturan Routing</h1>
    </div>

    @if($errors->any())
    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <form action="{{ route('system.notification-routings.update', $routing) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Event Key <span class="text-red-500">*</span></label>
                <input name="event_key" type="text" value="{{ old('event_key', $routing->event_key) }}"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm font-mono focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" required />
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Target Posisi</label>
                <select name="position_id" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                    <option value="">Semua Posisi</option>
                    @foreach($positions as $pos)
                    <option value="{{ $pos->id }}" {{ old('position_id', $routing->position_id) == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Channel <span class="text-red-500">*</span></label>
                    <select name="channel" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" required>
                        <option value="in-app" {{ old('channel', $routing->channel) === 'in-app' ? 'selected' : '' }}>In-App</option>
                        <option value="email" {{ old('channel', $routing->channel) === 'email' ? 'selected' : '' }}>Email</option>
                        <option value="telegram" {{ old('channel', $routing->channel) === 'telegram' ? 'selected' : '' }}>Telegram</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Scope <span class="text-red-500">*</span></label>
                    <select name="scope" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" required>
                        <option value="global" {{ old('scope', $routing->scope) === 'global' ? 'selected' : '' }}>Global</option>
                        <option value="branch" {{ old('scope', $routing->scope) === 'branch' ? 'selected' : '' }}>Branch</option>
                        <option value="area" {{ old('scope', $routing->scope) === 'area' ? 'selected' : '' }}>Area</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi</label>
                <input name="description" type="text" value="{{ old('description', $routing->description) }}"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-slate-300 text-[#215aac]"
                    {{ old('is_active', $routing->is_active) ? 'checked' : '' }} />
                <label for="is_active" class="text-sm font-semibold text-slate-700">Aktifkan aturan ini</label>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('system.notification-routings.index') }}" class="rounded-lg border border-slate-200 px-5 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#215aac] px-5 py-2 text-sm font-bold text-white hover:bg-[#194a91] transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
