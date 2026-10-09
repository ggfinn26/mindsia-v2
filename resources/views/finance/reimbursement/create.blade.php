@extends('layouts.dashboard')

@section('title', 'Buat Reimbursement')
@section('header_title', 'Buat Reimbursement')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('reimbursements.index') }}" class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--dash-text-muted)] hover:text-[var(--dash-text-heading)] transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Reimbursement Saya
        </a>
        <h2 class="font-jakarta text-[var(--dash-text-muted)]xl font-bold tracking-tight text-[var(--dash-text-heading)]">Buat Reimbursement</h2>
        <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Isi tujuan dan periode reimbursement. Rincian pengeluaran (item) dapat ditambahkan setelah disimpan.</p>
    </div>

    @if ($errors->any())
        <div class="flex items-start gap-3 rounded-xl border border-red-100 [.mode-dark_&]:border-red-500/20 bg-red-50 [.mode-dark_&]:bg-red-500/10 p-4 shadow-sm">
            <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
            <div>
                <h3 class="text-sm font-semibold text-red-800 [.mode-dark_&]:text-red-400">Terdapat Kesalahan</h3>
                <ul class="mt-1 list-disc pl-4 text-sm text-red-700 [.mode-dark_&]:text-red-400">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('reimbursements.store') }}" class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 shadow-sm space-y-5">
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-bold text-[var(--dash-text-heading)]">Tujuan Bisnis <span class="text-red-500">*</span></label>
            <input type="text" name="business_purpose" value="{{ old('business_purpose') }}" required maxlength="255" placeholder="Contoh: Perjalanan dinas kunjungan sekolah bulan Mei" class="block w-full rounded-lg border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] px-4 py-2.5 text-sm placeholder:text-[var(--dash-text-faint)] focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] shadow-sm">
        </div>
        
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-bold text-[var(--dash-text-heading)]">Periode Mulai <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="date" name="expense_period_start" value="{{ old('expense_period_start') }}" required class="block w-full rounded-lg border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] px-4 py-2.5 text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] shadow-sm">
                </div>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-bold text-[var(--dash-text-heading)]">Periode Selesai <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="date" name="expense_period_end" value="{{ old('expense_period_end') }}" required class="block w-full rounded-lg border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] px-4 py-2.5 text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] shadow-sm">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-[var(--dash-card-border)]">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-[var(--dash-primary)] px-6 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all">
                Simpan & Lanjut Isi Item <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>
            <a href="{{ route('reimbursements.index') }}" class="inline-flex min-h-10 items-center rounded-lg border border-[var(--dash-input-border)] bg-[var(--dash-card-bg)] px-5 text-sm font-bold text-[var(--dash-text-body)] shadow-sm hover:bg-[var(--dash-page-bg)] transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
