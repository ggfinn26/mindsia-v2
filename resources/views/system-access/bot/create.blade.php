@extends('layouts.dashboard')

@section('header_title', 'Tambah Bot')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Sistem & Akses</p>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Tambah Bot</h1>
    </div>

    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <form action="{{ route('system.bots.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1.5">Nama Bot <span class="text-red-500">*</span></label>
                <input name="name" type="text" value="{{ old('name') }}" class="w-full rounded-lg border bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--dash-primary)]/30" required />
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1.5">Platform <span class="text-red-500">*</span></label>
                <select name="type" class="w-full rounded-lg border bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--dash-primary)]/30" required>
                    <option value="">Pilih Platform...</option>
                    <option value="telegram" {{ old('type') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                    <option value="whatsapp" {{ old('type') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                </select>
                @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1.5">Token / API Key <span class="text-red-500">*</span></label>
                <input name="token" type="password" class="w-full rounded-lg border bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] px-3 py-2 text-sm font-mono focus:border-[var(--dash-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--dash-primary)]/30" required autocomplete="off" />
                <p class="mt-1 text-xs text-[var(--dash-text-faint)]">Token disimpan terenkripsi. Tidak dapat dilihat kembali setelah disimpan.</p>
                @error('token') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <input name="is_active" type="checkbox" value="1" id="is_active" class="h-4 w-4 rounded border-[var(--dash-input-border)] text-[var(--dash-primary)]" {{ old('is_active', true) ? 'checked' : '' }} />
                <label for="is_active" class="text-sm text-[var(--dash-text-body)]">Aktifkan bot saat disimpan</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1.5">Catatan</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--dash-primary)]/30">{{ old('notes') }}</textarea>
                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('system.bots.index') }}" class="rounded-lg border border-[var(--dash-input-border)] px-4 py-2 text-sm font-medium text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] transition-colors">Batal</a>
                <button type="submit" class="rounded-lg bg-[var(--dash-primary)] px-5 py-2 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
