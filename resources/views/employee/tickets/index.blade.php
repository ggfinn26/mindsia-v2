@extends('layouts.dashboard')

@section('title', 'Tiket Saya - MINDSIA')
@section('header_title', 'Tiket Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6">

    {{-- Page header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Profil Saya</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Tiket Saya</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Tiket fasilitas yang kamu ajukan.
            </p>
        </div>
        @can('facility.ticket.create')
            <a href="{{ route('facility.tickets.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity flex-shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Tiket
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Table card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Nomor / Judul</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Kategori</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Prioritas</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Cabang</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4">
                                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                                    {{ $ticket->title }}
                                </p>
                                <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-faint);">
                                    {{ $ticket->ticket_number }}
                                </p>
                            </td>
                            <td class="px-5 py-4 text-sm" style="color: var(--dash-text-body);">
                                {{ ucfirst($ticket->category) }}
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $priorityStyle = match($ticket->priority) {
                                        'high'   => 'background-color:#fee2e2;color:#dc2626;',
                                        'medium' => 'background-color:#fef3c7;color:#d97706;',
                                        default  => 'background-color:var(--dash-nav-hover-bg);color:var(--dash-text-muted);border:1px solid var(--dash-card-border);',
                                    };
                                    $priorityLabel = match($ticket->priority) {
                                        'high'   => 'Tinggi',
                                        'medium' => 'Sedang',
                                        'low'    => 'Rendah',
                                        default  => ucfirst($ticket->priority),
                                    };
                                @endphp
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      style="{{ $priorityStyle }}">
                                    {{ $priorityLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm" style="color: var(--dash-text-body);">
                                {{ $ticket->branch?->branch_name ?? '—' }}
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $statusStyle = match($ticket->status) {
                                        'pending_review' => 'background-color:#fef3c7;color:#d97706;',
                                        'approved'       => 'background-color:var(--dash-primary-bg);color:var(--dash-primary);',
                                        'rejected'       => 'background-color:#fee2e2;color:#dc2626;',
                                        'resolved'       => 'background-color:#f0fdf4;color:#059669;',
                                        default          => 'background-color:var(--dash-nav-hover-bg);color:var(--dash-text-muted);border:1px solid var(--dash-card-border);',
                                    };
                                    $statusLabel = match($ticket->status) {
                                        'pending_review' => 'Menunggu Review',
                                        'approved'       => 'Disetujui',
                                        'rejected'       => 'Ditolak',
                                        'resolved'       => 'Selesai',
                                        default          => ucfirst(str_replace('_', ' ', $ticket->status)),
                                    };
                                @endphp
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      style="{{ $statusStyle }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('facility.tickets.show', $ticket) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">confirmation_number</span>
                                <p class="text-sm font-semibold mb-2" style="color: var(--dash-text-heading);">
                                    Belum ada tiket yang kamu ajukan.
                                </p>
                                @can('facility.ticket.create')
                                    <a href="{{ route('facility.tickets.create') }}"
                                       class="inline-flex items-center gap-1 text-sm font-semibold hover:opacity-75 transition-opacity"
                                       style="color: var(--dash-primary);">
                                        <span class="material-symbols-outlined text-[16px]">add</span>
                                        Buat tiket pertamamu
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="px-5 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
