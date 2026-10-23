@extends('layouts.dashboard')

@section('title', 'Detail Tiket Fasilitas')
@section('header_title', 'Tiket Fasilitas')

@section('content')
@php
    $statusConfig = [
        'pending_review' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'label' => 'Menunggu Review'],
        'approved'       => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Disetujui'],
        'rejected'       => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Ditolak'],
        'resolved'       => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Selesai'],
    ];

    $priorityConfig = [
        'low'    => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'label' => 'Rendah'],
        'medium' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'label' => 'Sedang'],
        'high'   => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Tinggi'],
    ];

    $categoryConfig = [
        'electrical' => 'Kelistrikan',
        'plumbing'   => 'Pipa / Air',
        'furniture'  => 'Furnitur',
        'equipment'  => 'Peralatan',
        'cleaning'   => 'Kebersihan',
        'AC'         => 'AC',
        'other'      => 'Lainnya',
    ];

    $sc = $statusConfig[$facilityTicket->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'label' => ucfirst(str_replace('_', ' ', $facilityTicket->status))];
    $pc = $priorityConfig[$facilityTicket->priority] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'label' => ucfirst($facilityTicket->priority)];
    $catLabel = $categoryConfig[$facilityTicket->category] ?? ucfirst($facilityTicket->category);
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <a href="{{ route('facility.tickets.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Tiket Fasilitas
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Fasilitas</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">{{ $facilityTicket->title }}</h1>
            <div class="mt-2 flex flex-wrap items-center gap-3">
                <span class="font-mono text-xs px-2.5 py-1 rounded-md"
                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">
                    {{ $facilityTicket->ticket_number }}
                </span>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
                    {{ $sc['label'] }}
                </span>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $pc['bg'] }} {{ $pc['text'] }}">
                    Prioritas: {{ $pc['label'] }}
                </span>
            </div>
        </div>

        @can('facility.ticket.update')
            <div class="shrink-0 pt-2 sm:pt-0">
                <a href="{{ route('facility.tickets.edit', $facilityTicket) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                    Edit Tiket
                </a>
            </div>
        @endcan
    </div>

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card Informasi Tiket --}}
    <div class="rounded-2xl"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="rounded-t-2xl px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">info</span>
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Informasi Tiket</h2>
            </div>
            <span class="text-xs" style="color: var(--dash-text-muted);">
                Dibuat {{ $facilityTicket->created_at?->format('d M Y, H:i') ?? '-' }}
            </span>
        </div>
        <div class="px-6 py-5 space-y-5">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                <div>
                    <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Cabang</p>
                    <p class="mt-1 font-semibold text-sm" style="color: var(--dash-text-heading);">
                        {{ $facilityTicket->branch?->branch_name ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Kategori</p>
                    <p class="mt-1 font-semibold text-sm" style="color: var(--dash-text-heading);">
                        {{ $catLabel }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Prioritas</p>
                    <p class="mt-1 font-semibold text-sm" style="color: var(--dash-text-heading);">
                        {{ $pc['label'] }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Pelapor / Dibuat Oleh</p>
                    <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-heading);">
                        {{ $facilityTicket->createdBy?->full_name ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Ditugaskan Ke</p>
                    <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-heading);">
                        {{ $facilityTicket->assignedTo?->full_name ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Biaya Penanganan</p>
                    <p class="mt-1 text-sm font-semibold" style="color: var(--dash-text-heading);">
                        {{ $facilityTicket->cost_amount ? 'Rp ' . number_format($facilityTicket->cost_amount, 0, ',', '.') : '-' }}
                    </p>
                </div>
            </div>

            @if ($facilityTicket->description)
                <div class="pt-4" style="border-top: 1px solid var(--dash-card-border);">
                    <p class="text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Deskripsi Masalah</p>
                    <p class="text-sm leading-relaxed whitespace-pre-line" style="color: var(--dash-text-body);">
                        {{ $facilityTicket->description }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Review Action Section (jika pending_review) --}}
    @if ($facilityTicket->status === 'pending_review')
        @can('facility.ticket.review')
            <div x-data="{ showRejectModal: false, reviewNotes: '' }"
                 class="rounded-2xl"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="rounded-t-2xl px-6 py-4 flex items-center gap-2"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">rate_review</span>
                    <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Review Pengajuan Tiket</h2>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm mb-4" style="color: var(--dash-text-muted);">
                        Periksa rincian kerusakan dan tentukan apakah pengajuan perbaikan ini disetujui untuk ditangani atau ditolak.
                    </p>
                    <form id="reviewForm" method="POST" action="{{ route('facility.tickets.review', $facilityTicket) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                                Catatan Review (opsional)
                            </label>
                            <input type="text" name="notes" x-model="reviewNotes"
                                   placeholder="Contoh: Disetujui untuk penggantian suku cadang AC"
                                   class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>
                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" name="action" value="approve"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: #059669;">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                Setujui Tiket
                            </button>
                            <button type="button" @click="showRejectModal = true"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-danger, #dc2626);">
                                <span class="material-symbols-outlined text-[16px]">cancel</span>
                                Tolak Tiket
                            </button>
                        </div>
                    </form>

                    {{-- Reject Confirmation Modal (Alpine.js - ZERO NATIVE DIALOGS - NO OVERFLOW-HIDDEN) --}}
                    <template x-teleport="body">
                        <div x-show="showRejectModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
                            <div x-show="showRejectModal"
                                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                                 @click="showRejectModal = false"></div>

                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                    <div x-show="showRejectModal" @click.away="showRejectModal = false"
                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                                        {{-- Header Modal (rounded-t-2xl) --}}
                                        <div class="rounded-t-2xl px-6 py-5"
                                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                            <div class="flex items-start gap-3.5">
                                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                                    <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                                </div>
                                                <div>
                                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Tolak Tiket Fasilitas?</h3>
                                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                                        Pengajuan tiket ini akan ditolak dan statusnya diperbarui menjadi Ditolak.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Footer Modal (rounded-b-2xl) --}}
                                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                            <button type="button" @click="showRejectModal = false"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                Batal
                                            </button>
                                            <button type="submit" form="reviewForm" name="action" value="reject"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                                    style="background-color: var(--dash-danger, #dc2626);">
                                                <span class="material-symbols-outlined text-[16px]">cancel</span>
                                                Ya, Tolak Tiket
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        @endcan
    @elseif ($facilityTicket->status === 'approved')
        {{-- Assign & Resolve Section --}}
        <div class="space-y-6">
            @can('facility.ticket.assign')
                <div class="rounded-2xl"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="rounded-t-2xl px-6 py-4 flex items-center gap-2"
                         style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">person_add</span>
                        <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Tugaskan Petugas Penanganan</h2>
                    </div>
                    <div class="px-6 py-5">
                        <form method="POST" action="{{ route('facility.tickets.assign', $facilityTicket) }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                            @csrf
                            <div class="flex-1">
                                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                                    Pilih Pegawai / Teknisi <span class="text-red-500">*</span>
                                </label>
                                <select name="assigned_to_employee_id" required
                                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <option value="">Pilih pegawai ...</option>
                                    @foreach (\App\Models\Employee::where('is_active', true)->get() as $emp)
                                        <option value="{{ $emp->id }}" @selected($facilityTicket->assigned_to_employee_id == $emp->id)>
                                            {{ $emp->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[16px]">person_check</span>
                                Tugaskan
                            </button>
                        </form>
                    </div>
                </div>
            @endcan

            @can('facility.ticket.resolve')
                <div class="rounded-2xl"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="rounded-t-2xl px-6 py-4 flex items-center gap-2"
                         style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <span class="material-symbols-outlined text-[20px]" style="color: #059669;">task_alt</span>
                        <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Selesaikan Tiket Fasilitas</h2>
                    </div>
                    <div class="px-6 py-5">
                        <form method="POST" action="{{ route('facility.tickets.resolve', $facilityTicket) }}" class="space-y-4">
                            @csrf
                            <div class="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                                        Biaya Penanganan (Rp)
                                    </label>
                                    <input type="number" name="cost_amount" min="0" step="0.01"
                                           value="{{ old('cost_amount', $facilityTicket->cost_amount) }}"
                                           placeholder="0"
                                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                                        Catatan Penyelesaian
                                    </label>
                                    <input type="text" name="notes"
                                           placeholder="Contoh: AC telah dibersihkan dan filter diganti."
                                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                                </div>
                            </div>
                            <div class="pt-2">
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                        style="background-color: #059669;">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                    Selesaikan Tiket
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    @endif

    {{-- Lampiran & Dokumen --}}
    <div class="rounded-2xl"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="rounded-t-2xl px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">attachment</span>
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Lampiran & Foto</h2>
            </div>
            <span class="text-xs" style="color: var(--dash-text-muted);">
                {{ $facilityTicket->attachments->count() }} file
            </span>
        </div>
        <div class="px-6 py-5 space-y-4">
            @if ($facilityTicket->attachments->isNotEmpty())
                <div class="divide-y" style="border-color: var(--dash-card-border);">
                    @foreach ($facilityTicket->attachments as $att)
                        <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0"
                             style="border-color: var(--dash-card-border);"
                             x-data="{ showDeleteAttModal: false }">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">description</span>
                                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">
                                    {{ $att->original_name ?? $att->telegram_file_id }}
                                </p>
                            </div>

                            {{-- Delete Attachment Button & Modal --}}
                            <button type="button" @click="showDeleteAttModal = true"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-danger, #dc2626);">
                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                Hapus
                            </button>

                            {{-- Alpine Modal for Delete Attachment --}}
                            <template x-teleport="body">
                                <div x-show="showDeleteAttModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
                                    <div x-show="showDeleteAttModal"
                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                                         @click="showDeleteAttModal = false"></div>

                                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                            <div x-show="showDeleteAttModal" @click.away="showDeleteAttModal = false"
                                                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                                                <div class="rounded-t-2xl px-6 py-5"
                                                     style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                                    <div class="flex items-start gap-3.5">
                                                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                                            <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                                        </div>
                                                        <div>
                                                            <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Lampiran?</h3>
                                                            <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                                                File "{{ $att->original_name ?? $att->telegram_file_id }}" akan dihapus permanen dari tiket ini.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                                     style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                                    <button type="button" @click="showDeleteAttModal = false"
                                                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                        Batal
                                                    </button>
                                                    <form method="POST" action="{{ route('facility.tickets.attachments.destroy', [$facilityTicket, $att]) }}" class="m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                                                style="background-color: var(--dash-danger, #dc2626);">
                                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                                            Ya, Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm" style="color: var(--dash-text-muted);">Belum ada lampiran pada tiket ini.</p>
            @endif

            {{-- Form Upload Lampiran Baru --}}
            @can('facility.ticket.update')
                <div class="pt-4" style="border-top: 1px solid var(--dash-card-border);">
                    <form method="POST" action="{{ route('facility.tickets.attachments.store', $facilityTicket) }}"
                          enctype="multipart/form-data"
                          class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-xs font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-muted);">
                                Unggah Lampiran Baru (Foto / Dokumen)
                            </label>
                            <input type="file" name="file" required
                                   class="block w-full text-sm rounded-lg cursor-pointer focus:outline-none"
                                   style="color: var(--dash-text-heading); background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);">
                        </div>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity shrink-0"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">upload</span>
                            Unggah File
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </div>

    {{-- Riwayat Status (Audit Trail) --}}
    @if ($facilityTicket->statusHistories->isNotEmpty())
        <div class="rounded-2xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="rounded-t-2xl px-6 py-4 flex items-center gap-2"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">history</span>
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Riwayat Status Tiket</h2>
            </div>
            <div class="px-6 py-4">
                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-[var(--dash-card-border)]">
                    @foreach ($facilityTicket->statusHistories as $h)
                        @php
                            $histSc = $statusConfig[$h->to_status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'label' => ucfirst(str_replace('_', ' ', $h->to_status))];
                        @endphp
                        <div class="relative flex items-start justify-between gap-4">
                            <div class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full border-2"
                                 style="background-color: var(--dash-card-bg); border-color: var(--dash-primary);"></div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $histSc['bg'] }} {{ $histSc['text'] }}">
                                        {{ $histSc['label'] }}
                                    </span>
                                    <span class="text-xs" style="color: var(--dash-text-muted);">
                                        oleh <span class="font-medium" style="color: var(--dash-text-heading);">{{ $h->changedBy?->full_name ?? 'Sistem' }}</span>
                                    </span>
                                </div>
                                @if ($h->notes)
                                    <p class="mt-1.5 text-xs leading-relaxed" style="color: var(--dash-text-body);">
                                        {{ $h->notes }}
                                    </p>
                                @endif
                            </div>
                            <span class="text-xs font-mono shrink-0" style="color: var(--dash-text-faint);">
                                {{ $h->changed_at?->format('d M Y, H:i') ?? '-' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
