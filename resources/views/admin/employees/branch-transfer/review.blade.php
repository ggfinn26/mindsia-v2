@extends('layouts.dashboard')

@section('title', 'Review Pindah Cabang - MINDSIA')
@section('header_title', 'Review Pindah Cabang')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6">

    {{-- Page header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Kelola Karyawan</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Permintaan Pindah Cabang</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            Daftar permintaan perpindahan cabang yang menunggu persetujuan.
        </p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="hidden sm:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Dari Cabang</th>
                        <th class="hidden sm:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Ke Cabang</th>
                        <th class="hidden md:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Diajukan</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4">
                                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                                    {{ $transfer->employee?->full_name }}
                                </p>
                                {{-- From/To inline on mobile --}}
                                <p class="sm:hidden text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                    {{ $transfer->fromBranch?->branch_name }}
                                    <span class="material-symbols-outlined text-[12px] align-middle">arrow_forward</span>
                                    <span style="color: var(--dash-primary);">{{ $transfer->toBranch?->branch_name }}</span>
                                </p>
                            </td>
                            <td class="hidden sm:table-cell px-5 py-4 text-sm"
                                style="color: var(--dash-text-body);">
                                {{ $transfer->fromBranch?->branch_name }}
                            </td>
                            <td class="hidden sm:table-cell px-5 py-4 text-sm font-medium"
                                style="color: var(--dash-primary);">
                                {{ $transfer->toBranch?->branch_name }}
                            </td>
                            <td class="hidden md:table-cell px-5 py-4 text-sm whitespace-nowrap"
                                style="color: var(--dash-text-muted);">
                                {{ $transfer->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('branch-transfers.show', $transfer) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">rate_review</span>
                                    <span class="hidden sm:inline">Review</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">swap_horiz</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
                                    Tidak ada permintaan yang menunggu review.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transfers->hasPages())
            <div class="px-5 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
