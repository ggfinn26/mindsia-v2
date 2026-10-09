@extends('layouts.dashboard')

@section('title', 'Edit Indikator KPI')
@section('header_title', 'Edit Indikator')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('kpi.templates.show', $template) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $template->template_name }}
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Indikator</h1>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('kpi.templates.indicators.update', [$template, $indicator]) }}" class="border border-slate-200 bg-white p-6 space-y-4">
        @csrf @method('PUT')
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Kode indikator</label>
                <input type="text" name="indicator_code" value="{{ old('indicator_code', $indicator->indicator_code) }}" required maxlength="100" class="block w-full border-slate-300 text-sm uppercase focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <p class="mt-1 text-xs text-slate-400">ID unik huruf kapital, dipakai sistem. Contoh: <span class="font-mono">HADIR_RATE</span>, <span class="font-mono">MPI_SCORE</span>.</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama indikator</label>
                <input type="text" name="indicator_name" value="{{ old('indicator_name', $indicator->indicator_name) }}" required maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <p class="mt-1 text-xs text-slate-400">Nama yang tampil ke pegawai saat mengisi evaluasi.</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Satuan</label>
                <input type="text" name="unit" value="{{ old('unit', $indicator->unit) }}" maxlength="50" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <p class="mt-1 text-xs text-slate-400">Label angka hasil evaluasi. Contoh: <span class="font-mono">%</span>, <span class="font-mono">hari</span>, <span class="font-mono">sesi</span>, <span class="font-mono">Rp</span>.</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Target nilai</label>
                <input type="number" name="target_value" value="{{ old('target_value', $indicator->target_value) }}" required step="0.01" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <p class="mt-1 text-xs text-slate-400">Nilai ideal yang ingin dicapai (100%, 20 sesi, dst).</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Bobot (%)</label>
                <input type="number" name="weight" value="{{ old('weight', $indicator->weight) }}" required min="0" max="100" step="0.01" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <p class="mt-1 text-xs text-slate-400">Porsi indikator ini dari total KPI. Semua bobot harus berjumlah 100%.</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Urutan</label>
                <input type="number" name="sequence_number" value="{{ old('sequence_number', $indicator->sequence_number) }}" required min="1" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <p class="mt-1 text-xs text-slate-400">Urutan tampil di form evaluasi. Angka kecil tampil lebih atas.</p>
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Sumber data otomatis</label>
            <select name="data_source_type" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <option value="">— Manual —</option>
                @foreach ($allowedSources as $source)
                    <option value="{{ $source }}" @selected(old('data_source_type', $indicator->data_source_type) === $source)>{{ $sourceLabels[$source] ?? $source }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi</label>
            <textarea name="description" rows="2" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">{{ old('description', $indicator->description) }}</textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $indicator->is_active)) class="border-slate-300 text-[color:var(--dash-primary-text,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            <label for="is_active" class="text-sm font-medium text-slate-700">Aktif</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
            <a href="{{ route('kpi.templates.show', $template) }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>

    <div class="border border-red-100 bg-white p-4">
        <form method="POST" action="{{ route('kpi.templates.indicators.destroy', [$template, $indicator]) }}" onsubmit="return confirm('Nonaktifkan indikator ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Nonaktifkan indikator</button>
        </form>
    </div>
</div>
@endsection
