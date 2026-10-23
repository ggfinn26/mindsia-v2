@extends('layouts.dashboard')

@section('title', 'Katalog Program - MINDSIA')
@section('header_title', 'Katalog Program')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Portal Siswa</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Katalog Program</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Jelajahi dan daftar program-program unggulan di MINDSIA.
            </p>
        </div>
        <a href="{{ route('member.program.my') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors shrink-0"
           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[18px]">school</span>
            Program Saya
        </a>
    </div>

    @if($programs->isEmpty())
        {{-- Empty State Card --}}
        <div class="rounded-2xl px-6 py-16 text-center shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
                  style="color: var(--dash-text-faint);">travel_explore</span>
            <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Katalog Sedang Diperbarui</h3>
            <p class="text-sm max-w-md mx-auto mb-6" style="color: var(--dash-text-muted);">
                Kami sedang mempersiapkan daftar program terbaru untuk Anda. Silakan kembali lagi nanti atau hubungi tim administrasi kami.
            </p>
            <a href="{{ route('member.dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Kembali ke Dashboard
            </a>
        </div>
    @else
        {{-- Program Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($programs as $program)
                @php
                    $quotaLimit = isset($quotas) && $quotas->has($program->id) ? $quotas->get($program->id)->quota_limit : null;
                    $used = isset($usedQuotas) ? $usedQuotas->get($program->id, 0) : 0;
                    $remaining = $quotaLimit !== null ? max(0, $quotaLimit - $used) : null;
                    $isExhausted = $quotaLimit !== null && $remaining <= 0;
                @endphp
                <div class="rounded-2xl overflow-hidden shadow-sm flex flex-col transition-all duration-200"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                     onmouseover="this.style.borderColor='var(--dash-primary)'"
                     onmouseout="this.style.borderColor='var(--dash-card-border)'">
                    <div class="h-48 w-full relative overflow-hidden" style="background-color: var(--dash-input-bg);">
                        <img src="{{ $program->image_url }}" alt="{{ $program->program_name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <span class="absolute bottom-3 left-4 text-white text-xs font-bold px-2.5 py-0.5 rounded shadow-sm"
                              style="background-color: var(--dash-primary);">
                            {{ $program->program_code }}
                        </span>
                    </div>
                    <div class="p-5 sm:p-6 flex-grow flex flex-col">
                        <div class="text-xs font-bold uppercase tracking-wider mb-2"
                             style="color: var(--dash-primary-text);">
                            {{ $program->program_code }}
                        </div>
                        <h3 class="text-base sm:text-lg font-bold font-jakarta mb-2"
                            style="color: var(--dash-text-heading);">
                            {{ $program->program_name }}
                        </h3>
                        <p class="text-sm mb-4 flex-grow" style="color: var(--dash-text-muted);">
                            {{ $program->tagline ?? 'Program intensif dengan metode pengajaran berstandar internasional.' }}
                        </p>
                        
                        <div class="mb-4">
                            @if($isExhausted)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold shadow-sm"
                                      style="background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text); border: 1px solid var(--dash-card-border);">
                                    <span class="material-symbols-outlined text-[14px]">block</span>
                                    Kuota Habis
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold shadow-sm"
                                      style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid var(--dash-card-border);">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    @if($quotaLimit !== null)
                                        Tersedia: {{ $remaining }}
                                    @else
                                        Tersedia
                                    @endif
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between mt-auto pt-4 border-t"
                             style="border-color: var(--dash-card-border);">
                            <span class="text-base sm:text-lg font-bold"
                                  style="color: var(--dash-text-heading);">
                                Rp {{ number_format($program->program_price, 0, ',', '.') }}
                            </span>
                            @if($isExhausted)
                                <button disabled
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold opacity-50 cursor-not-allowed"
                                        style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[16px]">block</span>
                                    Penuh
                                </button>
                            @else
                                <a href="{{ route('member.program.register', $program->id) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
                                   style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
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
