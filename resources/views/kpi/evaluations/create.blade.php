@extends('layouts.dashboard')

@section('title', 'Buat Evaluasi KPI')
@section('header_title', 'Buat Evaluasi KPI')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('kpi.evaluations.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Buat Evaluasi KPI</h2>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Indikator bertipe otomatis akan diisi dari data sistem sesuai periode.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #fecaca; background-color: #fef2f2; color: #991b1b;">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('kpi.evaluations.store') }}" class="border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] rounded-2xl p-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Pegawai yang dievaluasi</label>
            <select name="employee_id" required class="block w-full bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] rounded-lg border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                <option value="">Pilih pegawai...</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Template KPI</label>
            <select name="kpi_template_id" required class="block w-full bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] rounded-lg border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                <option value="">Pilih template...</option>
                @foreach ($templates as $t)
                    <option value="{{ $t->id }}" @selected(old('kpi_template_id') == $t->id)>{{ $t->template_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Tipe periode</label>
            <select name="period_type" required class="block w-full bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] rounded-lg border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                @foreach (['monthly' => 'Bulanan', 'quarterly' => 'Kuartalan', 'yearly' => 'Tahunan'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('period_type') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Mulai periode</label>
                <input type="date" name="period_start_date" value="{{ old('period_start_date') }}" required class="block w-full bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] rounded-lg border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Akhir periode</label>
                <input type="date" name="period_end_date" value="{{ old('period_end_date') }}" required class="block w-full bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] rounded-lg border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center rounded-lg gap-2 bg-[var(--dash-primary)] px-5 text-sm font-semibold text-white hover:opacity-90">Buat Evaluasi</button>
            <a href="{{ route('kpi.evaluations.index') }}" class="inline-flex min-h-10 items-center rounded-lg border border-[var(--dash-input-border)] px-4 text-sm font-semibold text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)]">Batal</a>
        </div>
    </form>
</div>
@endsection
