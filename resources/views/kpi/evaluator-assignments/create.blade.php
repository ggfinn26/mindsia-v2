@extends('layouts.dashboard')

@section('title', 'Tambah Assignment Evaluator')
@section('header_title', 'Tambah Assignment Evaluator')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('kpi.evaluator-assignments.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Assignment Evaluator
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Tambah Assignment</h2>
        <p class="mt-1 text-sm text-slate-500">Evaluator tidak boleh sama dengan evaluatee.</p>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('kpi.evaluator-assignments.store') }}" class="border border-slate-200 bg-white p-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Evaluator</label>
            <select name="evaluator_employee_id" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">— Pilih evaluator —</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('evaluator_employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Evaluatee (yang dievaluasi)</label>
            <select name="evaluatee_employee_id" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">— Pilih evaluatee —</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('evaluatee_employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Template KPI</label>
            <select name="kpi_template_id" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">— Pilih template —</option>
                @foreach ($templates as $t)
                    <option value="{{ $t->id }}" @selected(old('kpi_template_id') == $t->id)>{{ $t->template_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Berlaku mulai</label>
            <input type="date" name="effective_start_date" value="{{ old('effective_start_date', today()->toDateString()) }}" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', true)) class="border-slate-300 text-[#215aac] focus:ring-[#215aac]">
            <label for="is_active" class="text-sm font-medium text-slate-700">Aktif</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
            <a href="{{ route('kpi.evaluator-assignments.index') }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</div>
@endsection
