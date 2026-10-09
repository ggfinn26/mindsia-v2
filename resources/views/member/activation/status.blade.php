@extends('layouts.dashboard')

@section('title', 'Status Aktivasi - MINDSIA')
@section('header_title', 'Status Aktivasi')

@section('content')
<h1 class="sr-only">Status Aktivasi</h1>
<div class="max-w-3xl mx-auto mt-8">
    <div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] overflow-hidden">
        <div class="p-6 sm:p-8 text-center">
            @if($status === 'ditangguhkan')
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                    <span class="material-symbols-outlined text-3xl text-red-600">block</span>
                </div>
                <h3 class="text-xl font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-2">Akun Ditangguhkan</h3>
                <p class="text-[color:var(--dash-text-body)] mb-8 max-w-md mx-auto">
                    {{ $message }}
                </p>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('member.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2 bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] hover:bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)] font-medium rounded-lg text-sm transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            @else
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-100 mb-6">
                    <span class="material-symbols-outlined text-3xl text-amber-600">hourglass_empty</span>
                </div>
                <h3 class="text-xl font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-2">Akun Belum Aktif</h3>
                <p class="text-[color:var(--dash-text-body)] mb-8 max-w-md mx-auto">
                    {{ $message }}
                </p>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('member.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2 bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] hover:bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)] font-medium rounded-lg text-sm transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
