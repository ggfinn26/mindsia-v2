@extends('layouts.dashboard')

@section('title', 'Daftar Program - MINDSIA')
@section('header_title', 'Katalog Program')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Katalog Program</h1>
            <p class="text-sm text-[color:var(--dash-text-muted)] mt-1">Jelajahi dan daftar program-program unggulan di MINDSIA.</p>
        </div>
    </div>

    @if($programs->isEmpty())
    <!-- Placeholder State -->
    <div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-10 flex flex-col items-center justify-center text-center">
        <div class="h-20 w-20 rounded-full bg-blue-50 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[#5586DB] text-4xl">travel_explore</span>
        </div>
        <h3 class="text-lg font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-2">Katalog Sedang Diperbarui</h3>
        <p class="text-[color:var(--dash-text-muted)] max-w-md mx-auto mb-6">Kami sedang mempersiapkan daftar program terbaru untuk Anda. Silakan kembali lagi nanti atau hubungi tim administrasi kami.</p>
        <a href="{{ route('member.dashboard') }}" class="px-5 py-2.5 bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] text-[color:var(--dash-text-body)] font-medium rounded-lg hover:bg-[var(--dash-nav-hover-bg)] transition-colors shadow-sm inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Dashboard
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($programs as $program)
            <div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                <div class="h-48 w-full bg-[var(--dash-nav-hover-bg)] relative">
                    <img src="{{ $program->image_url }}" alt="{{ $program->program_name }}" class="w-full h-full object-cover">
                </div>
                <div class="p-6 flex-grow flex flex-col">
                    <div class="text-xs font-bold text-[#5586DB] uppercase tracking-wider mb-2">{{ $program->program_code }}</div>
                    <h3 class="text-xl font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-2">{{ $program->program_name }}</h3>
                    <p class="text-sm text-[color:var(--dash-text-muted)] mb-6 flex-grow">{{ $program->tagline ?? 'Program intensif dengan metode pengajaran berstandar internasional.' }}</p>
                    
                    @php
                        $quotaLimit = isset($quotas) && $quotas->has($program->id) ? $quotas->get($program->id)->quota_limit : null;
                        $used = isset($usedQuotas) ? $usedQuotas->get($program->id, 0) : 0;
                        $remaining = $quotaLimit !== null ? max(0, $quotaLimit - $used) : null;
                        $isExhausted = $quotaLimit !== null && $remaining <= 0;
                    @endphp
                    
                    <div class="mb-4">
                        @if($isExhausted)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-700">
                                <span class="material-symbols-outlined text-[14px]">block</span>
                                Kuota Habis
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                @if($quotaLimit !== null)
                                    Tersedia: {{ $remaining }}
                                @else
                                    Tersedia
                                @endif
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-[color:var(--dash-card-border)]">
                        <span class="text-lg font-bold text-[color:var(--dash-text-heading)]">Rp {{ number_format($program->program_price, 0, ',', '.') }}</span>
                        @if($isExhausted)
                            <button disabled class="px-4 py-2 bg-gray-300 text-[color:var(--dash-text-muted)] text-sm font-medium rounded-lg cursor-not-allowed">
                                Penuh
                            </button>
                        @else
                            <a href="{{ route('member.program.register', $program->id) }}" class="px-4 py-2 bg-[#5586DB] text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                                Daftar
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
