@extends('layouts.dashboard')

@section('title', 'Detail Permintaan Resign - MINDSIA')
@section('header_title', 'Detail Permintaan Resign')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('resign-requests.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Permintaan Resign
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">SDM & Pengunduran Diri</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">{{ $resignRequest->employee?->full_name }}</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Detail pengajuan pengunduran diri pegawai.
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

    {{-- Detail Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Informasi Pengunduran Diri</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Pegawai</p>
                    <p class="mt-1.5 font-semibold text-base" style="color: var(--dash-text-heading);">{{ $resignRequest->employee?->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Efektif</p>
                    <p class="mt-1.5 font-medium" style="color: var(--dash-text-heading);">{{ $resignRequest->resign_date?->format('d M Y') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Diajukan</p>
                    <p class="mt-1.5" style="color: var(--dash-text-body);">{{ $resignRequest->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</p>
                    <div class="mt-1.5">
                        @php
                            $statusClass = match($resignRequest->status) {
                                'approved' => 'bg-emerald-100 text-emerald-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                default     => 'bg-amber-100 text-amber-800',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                            {{ ucfirst($resignRequest->status) }}
                        </span>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Alasan</p>
                    <div class="mt-1.5 p-4 rounded-xl text-sm leading-relaxed"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        {{ $resignRequest->reason ?: 'Tidak ada alasan yang disertakan.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions for Pending Status --}}
    @if ($resignRequest->status === 'pending')
        <div x-data="{ showApprove: false, showReject: false }" class="flex flex-wrap items-center gap-3">
            {{-- Tombol Setujui --}}
            <button type="button" @click="showApprove = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: #059669;">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Setujui & Mulai Offboarding
            </button>

            {{-- Tombol Tolak --}}
            <button type="button" @click="showReject = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-danger, #dc2626);">
                <span class="material-symbols-outlined text-[18px]">cancel</span>
                Tolak
            </button>

            {{-- Modal Setujui --}}
            <template x-teleport="body">
                <div x-show="showApprove" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display:none">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showApprove = false"></div>
                    <div class="relative w-full max-w-md rounded-2xl shadow-xl transition-all"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                        <div class="px-6 py-5 rounded-t-2xl"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-emerald-600 text-[24px]">check_circle</span>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Setujui Pengunduran Diri?</h3>
                                    <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                                        Proses offboarding akan dimulai untuk <strong style="color: var(--dash-text-heading);">{{ $resignRequest->employee?->full_name }}</strong>. Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 px-6 py-4 rounded-b-2xl"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showApprove = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <form method="POST" action="{{ route('resign-requests.approve', $resignRequest) }}" class="m-0">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: #059669;">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                    Ya, Setujui
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Modal Tolak --}}
            <template x-teleport="body">
                <div x-show="showReject" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display:none">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showReject = false"></div>
                    <div class="relative w-full max-w-md rounded-2xl shadow-xl transition-all"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                        <div class="px-6 py-5 rounded-t-2xl"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[24px]" style="color: var(--dash-danger, #dc2626);">cancel</span>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Tolak Pengunduran Diri?</h3>
                                    <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                                        Permintaan dari <strong style="color: var(--dash-text-heading);">{{ $resignRequest->employee?->full_name }}</strong> akan ditolak. Karyawan dapat mengajukan kembali.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 px-6 py-4 rounded-b-2xl"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showReject = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <form method="POST" action="{{ route('resign-requests.reject', $resignRequest) }}" class="m-0">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                    Ya, Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    @endif

    {{-- Offboarding Link if Approved --}}
    @if ($resignRequest->status === 'approved' && $resignRequest->employee)
        @can('update', $resignRequest->employee)
        <div>
            <a href="{{ route('off-boarding.create', $resignRequest->employee) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
               style="background-color: var(--dash-danger, #dc2626);">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Lanjut Proses Offboarding
            </a>
        </div>
        @endcan
    @endif
</div>
@endsection
