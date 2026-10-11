@extends('layouts.dashboard')

@section('title', 'Tiket Saya')
@section('header_title', 'Tiket Saya')

@section('content')
@php
    $statusConfig = [
        'pending_review' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'Menunggu Review'],
        'approved'       => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'label' => 'Disetujui'],
        'rejected'       => ['bg' => '#fef2f2', 'text' => '#991b1b', 'label' => 'Ditolak'],
        'resolved'       => ['bg' => '#f0fdf4', 'text' => '#166534', 'label' => 'Selesai'],
    ];
    $priorityConfig = [
        'low'    => ['bg' => '#f8fafc', 'text' => '#475569', 'label' => 'Rendah'],
        'medium' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'Sedang'],
        'high'   => ['bg' => '#fef2f2', 'text' => '#991b1b', 'label' => 'Tinggi'],
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Profil Saya</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Tiket Saya</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Tiket fasilitas yang kamu ajukan.</p>
        </div>
        @can('facility.ticket.create')
            <a href="{{ route('facility.tickets.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Tiket
            </a>
        @endcan
    </div>

    {{-- Flash --}}
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
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Nomor / Judul</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Kategori</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Prioritas</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Cabang</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    @php
                        $sc = $statusConfig[$ticket->status] ?? ['bg' => '#f8fafc', 'text' => '#475569', 'label' => ucfirst(str_replace('_', ' ', $ticket->status))];
                        $pc = $priorityConfig[$ticket->priority] ?? ['bg' => '#f8fafc', 'text' => '#475569', 'label' => ucfirst($ticket->priority)];
                    @endphp
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $ticket->title }}</p>
                            <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-faint);">{{ $ticket->ticket_number }}</p>
                        </td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ ucfirst($ticket->category) }}</td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                  style="background-color: {{ $pc['bg'] }}; color: {{ $pc['text'] }};">
                                {{ $pc['label'] }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $ticket->branch?->branch_name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                  style="background-color: {{ $sc['bg'] }}; color: {{ $sc['text'] }};">
                                {{ $sc['label'] }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('facility.tickets.show', $ticket) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">confirmation_number</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada tiket yang diajukan.</p>
                            @can('facility.ticket.create')
                                <a href="{{ route('facility.tickets.create') }}"
                                   class="inline-flex items-center gap-1.5 mt-3 text-sm font-semibold"
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
        <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
