@extends('layouts.dashboard')

@section('title', 'Edit Grade KPI')
@section('header_title', 'Edit Grade KPI')

@section('content')
<div class="max-w-lg mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('kpi.grade-rules.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Grade — {{ $rule->grade }}</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('kpi.grade-rules.update', $rule) }}" class="border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 space-y-4">
        @csrf @method('PUT')
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Grade</label>
                <input type="text" name="grade" value="{{ old('grade', $rule->grade) }}" required maxlength="10" class="block w-full border-[var(--dash-input-border)] text-sm uppercase focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
            </div>
            <div class="sm:col-span-2 grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Skor minimum</label>
                    <input type="number" name="minimum_score" value="{{ old('minimum_score', $rule->minimum_score) }}" required min="0" step="0.01" class="block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Skor maksimum</label>
                    <input type="number" name="maximum_score" value="{{ old('maximum_score', $rule->maximum_score) }}" min="0" step="0.01" class="block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                </div>
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Deskripsi</label>
            <input type="text" name="description" value="{{ old('description', $rule->description) }}" maxlength="255" class="block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $rule->is_active)) class="bg-[var(--dash-card-bg)] border-[var(--dash-input-border)] text-[var(--dash-primary)] rounded focus:ring-[var(--dash-primary)]/30">
            <label for="is_active" class="text-sm font-medium text-[var(--dash-text-body)]">Aktif</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center rounded-lg gap-2 bg-[var(--dash-primary)] px-5 text-sm font-semibold text-white hover:opacity-90">Simpan</button>
            <a href="{{ route('kpi.grade-rules.index') }}" class="inline-flex min-h-10 items-center rounded-lg border border-[var(--dash-input-border)] px-4 text-sm font-semibold text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)]">Batal</a>
        </div>
    </form>
</div>
@endsection
