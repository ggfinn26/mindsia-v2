@extends('layouts.dashboard')

@section('header_title', 'Tambah Webhook')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Sistem & Akses</p>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Tambah Webhook</h1>
    </div>

    <div class="rounded-xl border border-slate-100 bg-white shadow-sm">
        <form action="{{ route('system.webhooks.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama <span class="text-red-500">*</span></label>
                <input name="name" type="text" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:outline-none focus:ring-1 focus:ring-[#215aac]" required placeholder="Contoh - Notifikasi Telegram HR" />
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Bot (Opsional)</label>
                <select name="bot_id" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:outline-none focus:ring-1 focus:ring-[#215aac]">
                    <option value="">-- Tanpa Bot --</option>
                    @foreach($bots as $bot)
                    <option value="{{ $bot->id }}" {{ old('bot_id') == $bot->id ? 'selected' : '' }}>{{ $bot->name }} ({{ ucfirst($bot->type) }})</option>
                    @endforeach
                </select>
                @error('bot_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Endpoint URL <span class="text-red-500">*</span></label>
                <input name="endpoint_url" type="url" value="{{ old('endpoint_url') }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm font-mono focus:border-[#215aac] focus:outline-none focus:ring-1 focus:ring-[#215aac]" required placeholder="https://api.example.com/webhook/..." />
                <p class="mt-1 text-xs text-slate-400">Hanya http/https. IP lokal & cloud metadata diblokir.</p>
                @error('endpoint_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:outline-none focus:ring-1 focus:ring-[#215aac]">{{ old('notes') }}</textarea>
                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('system.webhooks.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="rounded-lg bg-[#215aac] px-5 py-2 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
