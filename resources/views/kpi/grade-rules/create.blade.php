@extends('layouts.dashboard')

@section('title', 'Tambah Grade KPI')
@section('header_title', 'Tambah Grade KPI')

@section('content')
<div class="max-w-lg mx-auto space-y-6">
    <div>
        <a href="{{ route('kpi.grade-rules.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Grade KPI
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Tambah Grade</h2>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('kpi.grade-rules.store') }}" class="border border-slate-200 bg-white p-6 space-y-4">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Grade <span class="text-slate-400">(A, B, C...)</span></label>
                <input type="text" name="grade" value="{{ old('grade') }}" required maxlength="10" placeholder="A" class="block w-full border-slate-300 text-sm uppercase focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div class="sm:col-span-2 grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Skor minimum</label>
                    <input type="number" name="minimum_score" value="{{ old('minimum_score', 0) }}" required min="0" step="0.01" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Skor maksimum <span class="text-slate-400">(kosong=∞)</span></label>
                    <input type="number" name="maximum_score" value="{{ old('maximum_score') }}" min="0" step="0.01" placeholder="kosong=tidak ada batas" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi</label>
            <input type="text" name="description" value="{{ old('description') }}" maxlength="255" placeholder="Sangat Baik" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', true)) class="border-slate-300 text-[#215aac] focus:ring-[#215aac]">
            <label for="is_active" class="text-sm font-medium text-slate-700">Aktif</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
            <a href="{{ route('kpi.grade-rules.index') }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</div>
@endsection
