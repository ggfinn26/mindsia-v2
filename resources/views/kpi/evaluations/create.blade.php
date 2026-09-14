@extends('layouts.dashboard')

@section('title', 'Buat Evaluasi KPI')
@section('header_title', 'Buat Evaluasi KPI')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('kpi.evaluations.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Evaluasi KPI
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Buat Evaluasi KPI</h2>
        <p class="mt-1 text-sm text-slate-500">Indikator bertipe otomatis akan diisi dari data sistem sesuai periode.</p>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('kpi.evaluations.store') }}" class="border border-slate-200 bg-white p-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Pegawai yang dievaluasi</label>
            <select name="employee_id" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">— Pilih pegawai —</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
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
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Tipe periode</label>
            <select name="period_type" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                @foreach (['monthly' => 'Bulanan', 'quarterly' => 'Kuartalan', 'annual' => 'Tahunan'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('period_type') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Mulai periode</label>
                <input type="date" name="period_start_date" value="{{ old('period_start_date') }}" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Akhir periode</label>
                <input type="date" name="period_end_date" value="{{ old('period_end_date') }}" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Buat Evaluasi</button>
            <a href="{{ route('kpi.evaluations.index') }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</div>
@endsection
