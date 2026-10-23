@extends('layouts.dashboard')

@section('title', 'Tiket Fasilitas')
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
        'other'      => 'Lainnya',
        'AC'         => 'AC',
    ];

    $tickets = \App\Models\FacilityTicket::with('branch')->latest()->paginate(20);
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header (Pola A) --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Fasilitas</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Tiket Fasilitas</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Daftar pelaporan kerusakan dan permintaan penanganan fasilitas cabang dan kantor pusat.</p>
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

    {{-- Flash Message --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
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
                    @forelse ($tickets as $ticket)
                        @php
                            $sc = $statusConfig[$ticket->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'label' => ucfirst(str_replace('_', ' ', $ticket->status))];
                            $pc = $priorityConfig[$ticket->priority] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'label' => ucfirst($ticket->priority)];
                            $catLabel = $categoryConfig[$ticket->category] ?? ucfirst($ticket->category);
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $ticket->title }}</p>
                                <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-faint);">{{ $ticket->ticket_number }}</p>
                            </td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $catLabel }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $pc['bg'] }} {{ $pc['text'] }}">
                                    {{ $pc['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $ticket->branch?->branch_name ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
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
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada tiket fasilitas.</p>
                                @can('facility.ticket.create')
                                    <a href="{{ route('facility.tickets.create') }}"
                                       class="inline-flex items-center gap-1.5 mt-3 text-sm font-semibold"
                                       style="color: var(--dash-primary);">
                                        <span class="material-symbols-outlined text-[16px]">add</span>
                                        Buat tiket pertama
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tickets->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
