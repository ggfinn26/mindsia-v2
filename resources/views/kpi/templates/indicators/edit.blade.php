@extends('layouts.dashboard')

@section('title', 'Edit Indikator KPI')
@section('header_title', 'Edit Indikator')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('kpi.templates.show', $template) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $template->template_name }}
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Edit Indikator</h2>
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
                <input type="text" name="indicator_code" value="{{ old('indicator_code', $indicator->indicator_code) }}" required maxlength="100" class="block w-full border-slate-300 text-sm uppercase focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama indikator</label>
                <input type="text" name="indicator_name" value="{{ old('indicator_name', $indicator->indicator_name) }}" required maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Satuan</label>
                <input type="text" name="unit" value="{{ old('unit', $indicator->unit) }}" maxlength="50" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Target nilai</label>
                <input type="number" name="target_value" value="{{ old('target_value', $indicator->target_value) }}" required step="0.01" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Bobot (%)</label>
                <input type="number" name="weight" value="{{ old('weight', $indicator->weight) }}" required min="0" max="100" step="0.01" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Urutan</label>
                <input type="number" name="sequence_number" value="{{ old('sequence_number', $indicator->sequence_number) }}" required min="1" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Sumber data otomatis</label>
            <select name="data_source_type" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">— Manual —</option>
                @foreach ($allowedSources as $source)
                    <option value="{{ $source }}" @selected(old('data_source_type', $indicator->data_source_type) === $source)>{{ $source }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi</label>
            <textarea name="description" rows="2" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">{{ old('description', $indicator->description) }}</textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $indicator->is_active)) class="border-slate-300 text-[#215aac] focus:ring-[#215aac]">
            <label for="is_active" class="text-sm font-medium text-slate-700">Aktif</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
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
