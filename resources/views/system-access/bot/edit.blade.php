@extends('layouts.dashboard')

@section('header_title', 'Edit Bot')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Sistem & Akses</p>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Edit Bot</h1>
        <p class="mt-1 text-sm text-[var(--dash-text-muted)]">{{ $bot->name }}</p>
    </div>

    <div class="rounded-xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <form action="{{ route('system.bots.update', $bot) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1.5">Nama Bot <span class="text-red-500">*</span></label>
                <input name="name" type="text" value="{{ old('name', $bot->name) }}" class="w-full rounded-lg border bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--dash-primary)]/30" required />
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1.5">Platform <span class="text-red-500">*</span></label>
                <select name="type" class="w-full rounded-lg border bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--dash-primary)]/30" required>
                    <option value="telegram" {{ old('type', $bot->type) === 'telegram' ? 'selected' : '' }}>Telegram</option>
                    <option value="whatsapp" {{ old('type', $bot->type) === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                </select>
                @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1.5">Token / API Key Baru</label>
                <input name="token" type="password" class="w-full rounded-lg border bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] px-3 py-2 text-sm font-mono focus:border-[var(--dash-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--dash-primary)]/30" autocomplete="off" placeholder="Kosongkan jika tidak ingin mengubah token" />
                @error('token') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <input name="is_active" type="checkbox" value="1" id="is_active" class="h-4 w-4 rounded border-[var(--dash-input-border)] text-[var(--dash-primary)]" {{ old('is_active', $bot->is_active) ? 'checked' : '' }} />
                <label for="is_active" class="text-sm text-[var(--dash-text-body)]">Bot aktif</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1.5">Catatan</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--dash-primary)]/30">{{ old('notes', $bot->notes) }}</textarea>
                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('system.bots.index') }}" class="rounded-lg border border-[var(--dash-input-border)] px-4 py-2 text-sm font-medium text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] transition-colors">Batal</a>
                <button type="submit" class="rounded-lg bg-[var(--dash-primary)] px-5 py-2 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
