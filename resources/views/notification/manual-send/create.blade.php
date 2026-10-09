@extends('layouts.dashboard')

@section('header_title', 'Kirim Notifikasi Manual')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{
    recipientType: '{{ old('recipient_type', 'employee') }}',
    templateKey: '{{ old('template_key') }}'
}">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Notifikasi</p>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Kirim Notifikasi Manual</h1>
        <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Kirim notifikasi langsung ke penerima tertentu tanpa melalui routing otomatis.</p>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 border border-emerald-200 [.mode-dark_&]:border-emerald-500/20 px-4 py-3 text-sm text-emerald-700 [.mode-dark_&]:text-emerald-400">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="rounded-lg bg-red-50 [.mode-dark_&]:bg-red-500/10 border border-red-200 [.mode-dark_&]:border-red-500/20 px-4 py-3 text-sm text-red-700 [.mode-dark_&]:text-red-400">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <form action="{{ route('notification-manual-send.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1">Template <span class="text-red-500 [.mode-dark_&]:text-red-400">*</span></label>
                <select name="template_key" x-model="templateKey"
                    class="w-full rounded-lg border border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)]" required>
                    <option value="">Pilih template...</option>
                    @foreach($templates as $tpl)
                    <option value="{{ $tpl->template_key }}" {{ old('template_key') === $tpl->template_key ? 'selected' : '' }}>
                        {{ $tpl->template_key }} ({{ $tpl->type }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1">Tipe Penerima <span class="text-red-500 [.mode-dark_&]:text-red-400">*</span></label>
                    <select name="recipient_type" x-model="recipientType"
                        class="w-full rounded-lg border border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)]" required>
                        <option value="employee">Pegawai</option>
                        <option value="member">Member</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1">ID Penerima <span class="text-red-500 [.mode-dark_&]:text-red-400">*</span></label>
                    <input name="recipient_id" type="number" value="{{ old('recipient_id') }}" min="1"
                        class="w-full rounded-lg border border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)]"
                        placeholder="Masukkan ID..." required />
                    <p class="mt-1 text-xs text-[var(--dash-text-faint)]" x-text="recipientType === 'employee' ? 'ID dari tabel employees' : 'ID dari tabel members_data'"></p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1">Payload Variabel <span class="text-[var(--dash-text-faint)] text-xs font-normal">(opsional)</span></label>
                <p class="text-xs text-[var(--dash-text-faint)] mb-2">Isi nilai untuk placeholder di template. Format: key=value per baris.</p>
                <div id="payload-container" class="space-y-2">
                    <div class="flex gap-2">
                        <input name="payload[nama_pegawai]" type="text" placeholder="nama_pegawai"
                            class="flex-1 rounded-lg border border-[var(--dash-input-border)] px-3 py-2 text-sm font-mono focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)]" />
                    </div>
                </div>
                <p class="mt-2 text-xs text-[var(--dash-text-faint)]">Variabel yang tidak diisi akan tetap sebagai placeholder di pesan.</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[var(--dash-primary)] px-5 py-2 text-sm font-bold text-white hover:opacity-90 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">send</span> Kirim Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
