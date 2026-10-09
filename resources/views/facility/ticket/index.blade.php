@extends('layouts.dashboard')

@section('title', 'Tiket Fasilitas')
@section('header_title', 'Tiket Fasilitas')

@section('content')
@php
    $statusBadge = [
        'pending_review' => 'bg-amber-100 text-amber-800',
        'approved'       => 'bg-blue-100 text-blue-800',
        'rejected'       => 'bg-red-100 text-red-800',
        'resolved'       => 'bg-emerald-100 text-emerald-800',
    ];
    $priorityBadge = [
        'low'    => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]',
        'medium' => 'bg-amber-100 text-amber-800',
        'high'   => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Fasilitas</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Tiket Fasilitas</h1>
        </div>
        @can('facility.ticket.create')
            <a href="{{ route('facility.tickets.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">add</span> Buat Tiket
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-hidden border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-nav-hover-bg)] text-left text-xs font-semibold text-[color:var(--dash-text-muted)]">
                    <tr>
                        <th class="px-5 py-3">Nomor / Judul</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Prioritas</th>
                        <th class="px-5 py-3">Cabang</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[color:var(--dash-card-border)]">
                    @php $tickets = \App\Models\FacilityTicket::with('branch')->latest()->paginate(20); @endphp
                    @forelse ($tickets as $ticket)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[color:var(--dash-text-heading)]">{{ $ticket->title }}</p>
                                <p class="mt-0.5 font-mono text-xs text-[color:var(--dash-text-faint)]">{{ $ticket->ticket_number }}</p>
                            </td>
                            <td class="px-5 py-4 text-[color:var(--dash-text-body)]">{{ $ticket->category }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $priorityBadge[$ticket->priority] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' }}">{{ ucfirst($ticket->priority) }}</span>
                            </td>
                            <td class="px-5 py-4 text-[color:var(--dash-text-body)]">{{ $ticket->branch?->branch_name ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$ticket->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' }}">
                                    {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('facility.tickets.show', $ticket) }}" class="inline-flex min-h-9 items-center border border-[color:var(--dash-input-border)] px-3 text-xs font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">confirmation_number</span>
                                <p class="mt-2 font-semibold text-[color:var(--dash-text-body)]">Belum ada tiket fasilitas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tickets->hasPages())
            <div class="border-t border-[color:var(--dash-card-border)] px-5 py-4">{{ $tickets->links() }}</div>
        @endif
    </div>
</div>
@endsection
