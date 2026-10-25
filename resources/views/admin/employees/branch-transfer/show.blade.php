@extends('layouts.dashboard')

@section('title', 'Detail Permintaan Pindah Cabang - MINDSIA')
@section('header_title', 'Review Permintaan Pindah Cabang')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('branch-transfers.review.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Permintaan
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Mutasi & Cabang</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Review Pindah Cabang</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Detail permohonan perpindahan cabang pegawai.
        </p>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- Detail Info Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Informasi Permintaan</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Pegawai</p>
                    <p class="mt-1.5 font-semibold text-base" style="color: var(--dash-text-heading);">{{ $transfer->employee?->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Diajukan</p>
                    <p class="mt-1.5" style="color: var(--dash-text-body);">{{ $transfer->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Dari Cabang</p>
                    <p class="mt-1.5 font-medium" style="color: var(--dash-text-heading);">{{ $transfer->fromBranch?->branch_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Ke Cabang</p>
                    <p class="mt-1.5 font-medium" style="color: var(--dash-primary);">{{ $transfer->toBranch?->branch_name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Review Decision Form or Status --}}
    @if ($transfer->status === 'review')
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Keputusan Review</h2>
            </div>
            <form method="POST" action="{{ route('branch-transfers.update', $transfer) }}" class="p-6 space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Catatan (opsional)</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                              placeholder="Tulis catatan persetujuan atau penolakan..."
                              onfocus="this.style.borderColor='var(--dash-primary)'"
                              onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('notes') }}</textarea>
                </div>
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <button type="submit" name="status" value="approved"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: #059669;">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        Setujui
                    </button>
                    <button type="submit" name="status" value="rejected"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-danger, #dc2626);">
                        <span class="material-symbols-outlined text-[18px]">cancel</span>
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="rounded-2xl p-6"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</p>
            <div class="mt-2">
                @php
                    $statusBadgeClass = match($transfer->status) {
                        'approved' => 'bg-emerald-100 text-emerald-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        default     => 'bg-amber-100 text-amber-800',
                    };
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusBadgeClass }}">
                    {{ \App\Support\EnumLabels::get('request_status', $transfer->status) }}
                </span>
            </div>
            @if ($transfer->notes)
            <div class="mt-4 pt-4" style="border-top: 1px solid var(--dash-card-border);">
                <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Catatan</p>
                <div class="mt-1.5 p-4 rounded-xl text-sm leading-relaxed"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    {{ $transfer->notes }}
                </div>
            </div>
            @endif
        </div>
    @endif
</div>
@endsection
