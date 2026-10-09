@extends('layouts.dashboard')

@section('title', 'Program Saya - MINDSIA')
@section('header_title', 'Program Saya')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold font-jakarta text-[color:var(--dash-text-heading)]">Program Saya</h2>
            <p class="text-sm text-[color:var(--dash-text-muted)] mt-1">Daftar program yang sedang aktif dan pernah Anda ikuti.</p>
        </div>
        <a href="{{ route('member.program.browse') }}" class="px-5 py-2 bg-[#5586DB] text-white font-medium rounded-lg hover:bg-[#4370BC] transition-colors shadow-sm inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span>
            Daftar Program Baru
        </a>
    </div>

    @if($registrations->isEmpty())
        <!-- Placeholder State -->
        <div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] p-10 flex flex-col items-center justify-center text-center">
            <div class="h-20 w-20 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[#5586DB] text-4xl">local_library</span>
            </div>
            <h3 class="text-lg font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-2">Belum Ada Program Aktif</h3>
            <p class="text-[color:var(--dash-text-muted)] max-w-md mx-auto mb-6">Anda belum mendaftar di program apapun. Temukan program yang sesuai dengan kebutuhan belajar Anda sekarang!</p>
            <a href="{{ route('member.program.browse') }}" class="px-5 py-2.5 bg-[#5586DB] text-white font-medium rounded-lg hover:bg-[#4370BC] transition-colors shadow-sm">
                Lihat Katalog Program
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($registrations as $registration)
                @php
                    $program = $registration->program;
                @endphp
                <div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                    <div class="h-48 w-full bg-[var(--dash-nav-hover-bg)] relative">
                        <img src="{{ $program->image_url ?? 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=800&auto=format&fit=crop' }}" alt="{{ $program->program_name ?? $registration->receipt_program_name }}" class="w-full h-full object-cover">
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $registration->status_color }} shadow-sm bg-opacity-90 backdrop-blur-sm">
                                {{ $registration->status_label }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <div class="text-xs font-bold text-[#5586DB] uppercase tracking-wider mb-2">{{ $program->program_code ?? '-' }}</div>
                        <h3 class="text-xl font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-2">{{ $program->program_name ?? $registration->receipt_program_name }}</h3>
                        <p class="text-sm text-[color:var(--dash-text-muted)] mb-6 flex-grow">{{ $program->tagline ?? 'Program intensif dengan metode pengajaran berstandar internasional.' }}</p>
                        
                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-[color:var(--dash-card-border)]">
                            <span class="text-lg font-bold text-[color:var(--dash-text-heading)]">Rp {{ number_format($registration->final_price, 0, ',', '.') }}</span>
                            <a href="#" class="px-4 py-2 bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)] text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
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
