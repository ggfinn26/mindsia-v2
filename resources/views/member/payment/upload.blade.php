@extends('layouts.dashboard')

@section('title', 'Upload Bukti Bayar - MINDSIA')
@section('header_title', 'Upload Bukti Bayar')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold font-jakarta text-[color:var(--dash-text-heading)]">Upload Bukti Bayar</h2>
            <p class="text-sm text-[color:var(--dash-text-muted)] mt-1">Unggah bukti transfer untuk pembayaran registrasi atau cicilan Anda.</p>
        </div>
    </div>

    <!-- Placeholder State -->
    <div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-10 flex flex-col items-center justify-center text-center">
        <div class="h-20 w-20 rounded-full bg-blue-50 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[#5586DB] text-4xl">cloud_upload</span>
        </div>
        <h3 class="text-lg font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-2">Form Upload Belum Tersedia</h3>
        <p class="text-[color:var(--dash-text-muted)] max-w-md mx-auto mb-6">Anda belum memiliki tagihan yang membutuhkan konfirmasi pembayaran. Lakukan pendaftaran program terlebih dahulu.</p>
        <a href="{{ route('member.dashboard') }}" class="px-5 py-2.5 bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] text-[color:var(--dash-text-body)] font-medium rounded-lg hover:bg-[var(--dash-nav-hover-bg)] transition-colors shadow-sm">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
