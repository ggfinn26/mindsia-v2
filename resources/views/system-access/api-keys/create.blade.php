@extends('layouts.dashboard')

@section('header_title', 'Tambah API Key')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Sistem & Akses</p>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Tambah API Key</h1>
    </div>

    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <form action="{{ route('system.api-keys.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1.5">Tipe Service <span class="text-red-500">*</span></label>
                <select name="type" class="w-full rounded-lg border bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--dash-primary)]/30" required>
                    <option value="">Pilih Tipe...</option>
                    <option value="convertapi" {{ old('type') === 'convertapi' ? 'selected' : '' }}>ConvertAPI (docx → PDF)</option>
                    <option value="telegram" {{ old('type') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                    <option value="wa" {{ old('type') === 'wa' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="email" {{ old('type') === 'email' ? 'selected' : '' }}>Email / SMTP</option>
                </select>
                @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1.5">Label <span class="text-red-500">*</span></label>
                <input name="label" type="text" value="{{ old('label') }}" class="w-full rounded-lg border bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--dash-primary)]/30" required placeholder="Contoh - ConvertAPI Key 1" />
                <p class="mt-1 text-xs text-[var(--dash-text-faint)]">Identifikasi key ini, misalnya berdasarkan akun atau kuota.</p>
                @error('label') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1.5">Key Value <span class="text-red-500">*</span></label>
                <input name="key_value" type="password" class="w-full rounded-lg border bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] px-3 py-2 text-sm font-mono focus:border-[var(--dash-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--dash-primary)]/30" required autocomplete="off" />
                <p class="mt-1 text-xs text-[var(--dash-text-faint)]">Disimpan terenkripsi. Tidak dapat dilihat kembali setelah disimpan.</p>
                @error('key_value') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <input name="is_active" type="checkbox" value="1" id="is_active" class="h-4 w-4 rounded border-[var(--dash-input-border)] text-[var(--dash-primary)]" {{ old('is_active', true) ? 'checked' : '' }} />
                <label for="is_active" class="text-sm text-[var(--dash-text-body)]">Aktifkan key saat disimpan</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1.5">Catatan</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--dash-primary)]/30" placeholder="Kuota, tanggal expired, sumber akun, dll.">{{ old('notes') }}</textarea>
                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('system.api-keys.index') }}" class="rounded-lg border border-[var(--dash-input-border)] px-4 py-2 text-sm font-medium text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] transition-colors inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</a>
                <button type="submit" class="rounded-lg bg-[var(--dash-primary)] px-5 py-2 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-colors inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">save</span>Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
