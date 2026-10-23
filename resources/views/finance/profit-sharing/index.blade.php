@extends('layouts.dashboard')

@section('title', 'Bagi Hasil')
@section('header_title', 'Bagi Hasil')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Keuangan</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Bagi Hasil (Profit Sharing)</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Perhitungan dan laporan pembagian dividen bagi hasil operasional cabang.
            </p>
        </div>
    </div>

    {{-- Content Card --}}
    <div class="p-16 rounded-2xl text-center shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
              style="color: var(--dash-text-faint);">pie_chart</span>
        <h2 class="text-base font-bold" style="color: var(--dash-text-heading);">Perhitungan Profit Sharing</h2>
        <p class="mt-1 text-sm max-w-md mx-auto" style="color: var(--dash-text-muted);">
            Perhitungan pembagian keuntungan untuk pihak terkait dan investor cabang akan otomatis terakumulasi di sini berdasarkan tutup buku periode.
        </p>
    </div>
</div>
@endsection
