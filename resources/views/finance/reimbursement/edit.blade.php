@extends('layouts.dashboard')

@section('title', 'Edit Reimbursement')
@section('header_title', 'Edit Reimbursement')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('reimbursements.show', $reimbursement) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Detail Reimbursement
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Keuangan</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Reimbursement</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui data pengajuan klaim untuk {{ $reimbursement->business_purpose }}.
        </p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px] shrink-0">error</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card (TANPA overflow-hidden) --}}
    <form method="POST" action="{{ route('reimbursements.update', $reimbursement) }}"
          class="rounded-2xl shadow-sm"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @csrf
        @method('PUT')
        <div class="px-6 py-4 rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Informasi Pengajuan</h2>
        </div>

        <div class="p-6 space-y-5">
            {{-- Tujuan Bisnis --}}
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Tujuan Bisnis <span class="text-red-500">*</span>
                </label>
                <input type="text" name="business_purpose" value="{{ old('business_purpose', $reimbursement->business_purpose) }}"
                       required maxlength="255"
                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
            </div>

            {{-- Periode --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Periode Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="expense_period_start" value="{{ old('expense_period_start', $reimbursement->expense_period_start?->toDateString()) }}"
                           required
                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Periode Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="expense_period_end" value="{{ old('expense_period_end', $reimbursement->expense_period_end?->toDateString()) }}"
                           required
                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'" />
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="px-6 py-4 rounded-b-2xl flex items-center gap-3"
             style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Simpan Perubahan
            </button>
            <a href="{{ route('reimbursements.show', $reimbursement) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[18px]">close</span>
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
