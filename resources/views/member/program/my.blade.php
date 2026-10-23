@extends('layouts.dashboard')

@section('title', 'Program Saya - MINDSIA')
@section('header_title', 'Program Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Portal Siswa</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Program Saya</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Daftar program pembelajaran yang sedang aktif dan pernah Anda ikuti.
            </p>
        </div>
        <a href="{{ route('member.program.browse') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Daftar Program Baru
        </a>
    </div>

    @if($registrations->isEmpty())
        {{-- Empty State Card --}}
        <div class="rounded-2xl px-6 py-16 text-center shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
                  style="color: var(--dash-text-faint);">local_library</span>
            <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Belum Ada Program Aktif</h3>
            <p class="text-sm max-w-md mx-auto mb-6" style="color: var(--dash-text-muted);">
                Anda belum terdaftar di program apapun. Temukan program yang sesuai dengan kebutuhan belajar Anda sekarang!
            </p>
            <a href="{{ route('member.program.browse') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">explore</span>
                Lihat Katalog Program
            </a>
        </div>
    @else
        {{-- Program Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($registrations as $registration)
                @php
                    $program = $registration->program;
                    $statusBg = match ($registration->payment_status) {
                        'paid_full' => 'var(--dash-status-success-bg)',
                        'installments' => 'var(--dash-status-info-bg)',
                        default => 'var(--dash-status-danger-bg)',
                    };
                    $statusText = match ($registration->payment_status) {
                        'paid_full' => 'var(--dash-status-success-text)',
                        'installments' => 'var(--dash-status-info-text)',
                        default => 'var(--dash-status-danger-text)',
                    };
                @endphp
                <div class="rounded-2xl overflow-hidden shadow-sm flex flex-col transition-all duration-200"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                     onmouseover="this.style.borderColor='var(--dash-primary)'"
                     onmouseout="this.style.borderColor='var(--dash-card-border)'">
                    <div class="h-48 w-full relative overflow-hidden" style="background-color: var(--dash-input-bg);">
                        <img src="{{ $program->image_url ?? 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=800&auto=format&fit=crop' }}"
                             alt="{{ $program->program_name ?? $registration->receipt_program_name }}"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <span class="absolute bottom-3 left-4 text-white text-xs font-bold px-2.5 py-0.5 rounded shadow-sm"
                              style="background-color: var(--dash-primary);">
                            {{ $program->program_code ?? '-' }}
                        </span>
                        <div class="absolute top-3 right-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold shadow-sm"
                                  style="background-color: {{ $statusBg }}; color: {{ $statusText }}; border: 1px solid var(--dash-card-border);">
                                {{ $registration->status_label }}
                            </span>
                        </div>
                    </div>
                    <div class="p-5 sm:p-6 flex-grow flex flex-col">
                        <div class="text-xs font-bold uppercase tracking-wider mb-2"
                             style="color: var(--dash-primary-text);">
                            {{ $program->program_code ?? '-' }}
                        </div>
                        <h3 class="text-base sm:text-lg font-bold font-jakarta mb-2"
                            style="color: var(--dash-text-heading);">
                            {{ $program->program_name ?? $registration->receipt_program_name }}
                        </h3>
                        <p class="text-sm mb-6 flex-grow" style="color: var(--dash-text-muted);">
                            {{ $program->tagline ?? 'Program intensif dengan metode pengajaran berstandar internasional.' }}
                        </p>
                        
                        <div class="flex items-center justify-between mt-auto pt-4 border-t"
                             style="border-color: var(--dash-card-border);">
                            <span class="text-base sm:text-lg font-bold"
                                  style="color: var(--dash-text-heading);">
                                Rp {{ number_format($registration->final_price, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('member.registration.index') }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
