@extends('layouts.dashboard')

@section('header_title', 'Tambah Bot')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Sistem & Akses</p>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Tambah Bot</h1>
    </div>

    <div class="rounded-xl border border-slate-100 bg-white shadow-sm">
        <form action="{{ route('system.bots.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Bot <span class="text-red-500">*</span></label>
                <input name="name" type="text" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:outline-none focus:ring-1 focus:ring-[#215aac]" required />
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Platform <span class="text-red-500">*</span></label>
                <select name="type" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:outline-none focus:ring-1 focus:ring-[#215aac]" required>
                    <option value="">-- Pilih Platform --</option>
                    <option value="telegram" {{ old('type') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                    <option value="whatsapp" {{ old('type') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                </select>
                @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Token / API Key <span class="text-red-500">*</span></label>
                <input name="token" type="password" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm font-mono focus:border-[#215aac] focus:outline-none focus:ring-1 focus:ring-[#215aac]" required autocomplete="off" />
                <p class="mt-1 text-xs text-slate-400">Token disimpan terenkripsi. Tidak dapat dilihat kembali setelah disimpan.</p>
                @error('token') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <input name="is_active" type="checkbox" value="1" id="is_active" class="h-4 w-4 rounded border-slate-300 text-[#215aac]" {{ old('is_active', true) ? 'checked' : '' }} />
                <label for="is_active" class="text-sm text-slate-700">Aktifkan bot saat disimpan</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:outline-none focus:ring-1 focus:ring-[#215aac]">{{ old('notes') }}</textarea>
                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('system.bots.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="rounded-lg bg-[#215aac] px-5 py-2 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
