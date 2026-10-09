@extends('layouts.dashboard')

@section('title', 'Tambah Indikator KPI')
@section('header_title', 'Tambah Indikator')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('kpi.templates.show', $template) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $template->template_name }}
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Tambah Indikator</h1>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('kpi.templates.indicators.store', $template) }}" class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 space-y-4">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Kode indikator</label>
                <input type="text" name="indicator_code" value="{{ old('indicator_code') }}" required maxlength="100" placeholder="HADIR_RATE" class="block w-full border-[color:var(--dash-input-border)] text-sm uppercase focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <p class="mt-1 text-xs text-[color:var(--dash-text-faint)]">ID unik huruf kapital, dipakai sistem. Contoh: <span class="font-mono">HADIR_RATE</span>, <span class="font-mono">MPI_SCORE</span>.</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Nama indikator</label>
                <input type="text" name="indicator_name" value="{{ old('indicator_name') }}" required maxlength="255" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <p class="mt-1 text-xs text-[color:var(--dash-text-faint)]">Nama yang tampil ke pegawai saat mengisi evaluasi.</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Satuan</label>
                <input type="text" name="unit" value="{{ old('unit') }}" maxlength="50" placeholder="%, hari, sesi..." class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <p class="mt-1 text-xs text-[color:var(--dash-text-faint)]">Label angka hasil evaluasi. Contoh: <span class="font-mono">%</span>, <span class="font-mono">hari</span>, <span class="font-mono">sesi</span>, <span class="font-mono">Rp</span>.</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Target nilai</label>
                <input type="number" name="target_value" value="{{ old('target_value') }}" required step="0.01" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <p class="mt-1 text-xs text-[color:var(--dash-text-faint)]">Nilai ideal yang ingin dicapai (100%, 20 sesi, dst).</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Bobot (%)</label>
                <input type="number" name="weight" value="{{ old('weight') }}" required min="0" max="100" step="0.01" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <p class="mt-1 text-xs text-[color:var(--dash-text-faint)]">Porsi indikator ini dari total KPI. Semua bobot harus berjumlah 100%.</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Urutan</label>
                <input type="number" name="sequence_number" value="{{ old('sequence_number', $nextSequence) }}" required min="1" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <p class="mt-1 text-xs text-[color:var(--dash-text-faint)]">Urutan tampil di form evaluasi. Angka kecil tampil lebih atas.</p>
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">
                Sumber data otomatis <span class="text-[color:var(--dash-text-faint)]">(kosongkan jika diisi manual)</span>
            </label>
            <select name="data_source_type" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <option value="">— Manual —</option>
                @foreach ($allowedSources as $source)
                    <option value="{{ $source }}" @selected(old('data_source_type') === $source)>{{ $sourceLabels[$source] ?? $source }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-[color:var(--dash-text-faint)]">Indikator otomatis diisi dari data sistem saat evaluasi dibuat.</p>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Deskripsi</label>
            <textarea name="description" rows="2" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">{{ old('description') }}</textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', true)) class="border-[color:var(--dash-input-border)] text-[color:var(--dash-primary-text,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            <label for="is_active" class="text-sm font-medium text-[color:var(--dash-text-body)]">Aktif</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Tambah Indikator</button>
            <a href="{{ route('kpi.templates.show', $template) }}" class="inline-flex min-h-10 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Batal</a>
        </div>
    </form>
</div>
@endsection
