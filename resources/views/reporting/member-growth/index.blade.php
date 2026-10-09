@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Laporan & Analisa Pertumbuhan Member</h1>
    </div>

    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-6">
        <p class="text-[color:var(--dash-text-body)]">Grafik tren pendaftaran member baru vs member pasif akan ditampilkan di sini.</p>
    </div>
</div>
@endsection
