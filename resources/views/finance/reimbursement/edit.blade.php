@extends('layouts.dashboard')

@section('title', 'Edit Reimbursement')
@section('header_title', 'Edit Reimbursement')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('reimbursements.show', $reimbursement) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[var(--dash-text-muted)] hover:text-[var(--dash-text-body)]">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $reimbursement->business_purpose }}
        </a>
        <h2 class="font-jakarta text-[var(--dash-text-muted)]xl font-bold tracking-tight text-[var(--dash-text-heading)]">Edit Reimbursement</h2>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 [.mode-dark_&]:border-red-500/20 bg-red-50 [.mode-dark_&]:bg-red-500/10 px-4 py-3 text-sm text-red-800 [.mode-dark_&]:text-red-400">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('reimbursements.update', $reimbursement) }}" class="border border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Tujuan bisnis</label>
            <input type="text" name="business_purpose" value="{{ old('business_purpose', $reimbursement->business_purpose) }}" required maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Periode mulai</label>
                <input type="date" name="expense_period_start" value="{{ old('expense_period_start', $reimbursement->expense_period_start?->toDateString()) }}" required class="block w-full border-slate-300 text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Periode selesai</label>
                <input type="date" name="expense_period_end" value="{{ old('expense_period_end', $reimbursement->expense_period_end?->toDateString()) }}" required class="block w-full border-slate-300 text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]">
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary)] px-5 text-sm font-semibold text-white hover:opacity-90">Simpan</button>
            <a href="{{ route('reimbursements.show', $reimbursement) }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)]">Batal</a>
        </div>
    </form>
</div>
@endsection
