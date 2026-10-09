@extends('layouts.dashboard')

@section('title', 'Detail Pindah Cabang - MINDSIA')
@section('header_title', 'Review Pindah Cabang')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-2xl mx-auto space-y-6">

    {{-- Back + header --}}
    <div>
        <a href="{{ route('branch-transfers.review.index') }}"
           class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider hover:opacity-75 transition-opacity"
           style="color: var(--dash-text-muted);">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Daftar Permintaan
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Permintaan Pindah Cabang</h1>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Detail card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="grid grid-cols-2 gap-4 p-5 text-sm">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1"
                   style="color: var(--dash-text-faint);">Pegawai</p>
                <p class="font-semibold" style="color: var(--dash-text-heading);">
                    {{ $transfer->employee?->full_name }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1"
                   style="color: var(--dash-text-faint);">Diajukan</p>
                <p style="color: var(--dash-text-body);">
                    {{ $transfer->created_at->format('d M Y H:i') }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1"
                   style="color: var(--dash-text-faint);">Dari Cabang</p>
                <p class="font-medium" style="color: var(--dash-text-heading);">
                    {{ $transfer->fromBranch?->branch_name }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1"
                   style="color: var(--dash-text-faint);">Ke Cabang</p>
                <p class="font-medium" style="color: var(--dash-primary);">
                    {{ $transfer->toBranch?->branch_name }}
                </p>
            </div>
        </div>
    </div>

    {{-- Review form --}}
    @if($transfer->status === 'review')
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <form method="POST" action="{{ route('branch-transfers.update', $transfer) }}">
                @csrf
                @method('PUT')
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5"
                               style="color: var(--dash-text-body);">
                            Catatan <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                        </label>
                        <textarea name="notes" rows="3"
                                  placeholder="Catatan untuk keputusan ini...">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="px-5 py-4 flex flex-col sm:flex-row gap-3"
                     style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <button type="submit" name="status" value="approved"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: #059669;">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Setujui
                    </button>
                    <button type="submit" name="status" value="rejected"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity"
                            style="border: 1px solid #fca5a5; color: #dc2626; background-color: var(--dash-card-bg);">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="rounded-2xl px-5 py-4 text-sm"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-text-faint);">Status</p>
            <p class="font-semibold"
               style="color: {{ $transfer->status === 'approved' ? '#059669' : '#dc2626' }};">
                {{ ucfirst($transfer->status) }}
            </p>
        </div>
    @endif

</div>
@endsection
