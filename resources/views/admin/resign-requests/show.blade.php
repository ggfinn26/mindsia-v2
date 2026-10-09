@extends('layouts.dashboard')

@section('title', 'Detail Permintaan Resign - MINDSIA')
@section('header_title', 'Detail Permintaan Resign')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-2xl mx-auto space-y-6"
     x-data="{ showApprove: false, showReject: false }">

    {{-- Back + header --}}
    <div>
        <a href="{{ route('resign-requests.index') }}"
           class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider hover:opacity-75 transition-opacity"
           style="color: var(--dash-text-muted);">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Permintaan Resign
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">
            {{ $resignRequest->employee?->full_name }}
        </h1>
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
        <div class="grid grid-cols-2 gap-4 p-5 text-sm"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1"
                   style="color: var(--dash-text-faint);">Pegawai</p>
                <p class="font-semibold" style="color: var(--dash-text-heading);">
                    {{ $resignRequest->employee?->full_name }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1"
                   style="color: var(--dash-text-faint);">Tanggal Efektif</p>
                <p class="font-medium" style="color: var(--dash-text-heading);">
                    {{ $resignRequest->resign_date?->format('d M Y') ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1"
                   style="color: var(--dash-text-faint);">Diajukan</p>
                <p style="color: var(--dash-text-body);">
                    {{ $resignRequest->created_at->format('d M Y H:i') }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1"
                   style="color: var(--dash-text-faint);">Status</p>
                @php
                    $statusStyle = match($resignRequest->status) {
                        'approved' => 'color:#059669;',
                        'rejected' => 'color:#dc2626;',
                        default    => 'color:#d97706;',
                    };
                @endphp
                <p class="font-semibold" style="{{ $statusStyle }}">
                    {{ ucfirst($resignRequest->status) }}
                </p>
            </div>
        </div>
        <div class="p-5 text-sm">
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-text-faint);">Alasan</p>
            <p style="color: var(--dash-text-body);">{{ $resignRequest->reason }}</p>
        </div>
    </div>

    {{-- Actions: pending --}}
    @if($resignRequest->status === 'pending')
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" @click="showApprove = true"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: #059669;">
                <span class="material-symbols-outlined text-[18px]">check</span>
                Setujui & Mulai Offboarding
            </button>
            <button type="button" @click="showReject = true"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity"
                    style="border: 1px solid #fca5a5; color: #dc2626; background-color: var(--dash-card-bg);">
                <span class="material-symbols-outlined text-[18px]">close</span>
                Tolak
            </button>
        </div>

        {{-- Modal Setujui --}}
        <template x-teleport="body">
            <div x-show="showApprove" x-cloak
                 class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
                 style="display:none">
                <div class="absolute inset-0 bg-black/50"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     @click="showApprove = false"></div>
                <div class="relative rounded-2xl shadow-xl w-full max-w-md"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full"
                                 style="background-color: #f0fdf4;">
                                <span class="material-symbols-outlined text-[20px]" style="color: #059669;">check_circle</span>
                            </div>
                            <div>
                                <h3 class="font-jakarta text-base font-bold mb-1"
                                    style="color: var(--dash-text-heading);">Setujui Pengunduran Diri?</h3>
                                <p class="text-sm" style="color: var(--dash-text-muted);">
                                    Proses offboarding akan dimulai untuk
                                    <strong style="color: var(--dash-text-body);">{{ $resignRequest->employee?->full_name }}</strong>.
                                    Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4"
                         style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <button type="button" @click="showApprove = false"
                                class="px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-1.5"
                                style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                            Batal
                        </button>
                        <form method="POST" action="{{ route('resign-requests.approve', $resignRequest) }}" class="m-0">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity inline-flex items-center gap-1.5"
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
            <div x-show="showReject" x-cloak
                 class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
                 style="display:none">
                <div class="absolute inset-0 bg-black/50"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     @click="showReject = false"></div>
                <div class="relative rounded-2xl shadow-xl w-full max-w-md"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full"
                                 style="background-color: #fee2e2;">
                                <span class="material-symbols-outlined text-[20px]" style="color: #dc2626;">cancel</span>
                            </div>
                            <div>
                                <h3 class="font-jakarta text-base font-bold mb-1"
                                    style="color: var(--dash-text-heading);">Tolak Pengunduran Diri?</h3>
                                <p class="text-sm" style="color: var(--dash-text-muted);">
                                    Permintaan dari
                                    <strong style="color: var(--dash-text-body);">{{ $resignRequest->employee?->full_name }}</strong>
                                    akan ditolak. Karyawan dapat mengajukan kembali.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4"
                         style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <button type="button" @click="showReject = false"
                                class="px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-1.5"
                                style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                            Batal
                        </button>
                        <form method="POST" action="{{ route('resign-requests.reject', $resignRequest) }}" class="m-0">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity inline-flex items-center gap-1.5"
                                    style="background-color: #dc2626;">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                                Ya, Tolak
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    @endif

    {{-- Action: offboarding --}}
    @if($resignRequest->status === 'approved' && $resignRequest->employee)
        @can('update', $resignRequest->employee)
            <a href="{{ route('off-boarding.create', $resignRequest->employee) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
               style="background-color: #dc2626;">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Lanjut Proses Offboarding
            </a>
        @endcan
    @endif

</div>
@endsection
