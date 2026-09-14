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
        'low'    => 'bg-slate-100 text-slate-700',
        'medium' => 'bg-amber-100 text-amber-800',
        'high'   => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Fasilitas</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Tiket Fasilitas</h2>
        </div>
        @can('facility.ticket.create')
            <a href="{{ route('facility.tickets.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
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

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nomor / Judul</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Prioritas</th>
                        <th class="px-5 py-3">Cabang</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $tickets = \App\Models\FacilityTicket::with('branch')->latest()->paginate(20); @endphp
                    @forelse ($tickets as $ticket)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $ticket->title }}</p>
                                <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $ticket->ticket_number }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $ticket->category }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $priorityBadge[$ticket->priority] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($ticket->priority) }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $ticket->branch?->branch_name ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$ticket->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('facility.tickets.show', $ticket) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">confirmation_number</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada tiket fasilitas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tickets->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $tickets->links() }}</div>
        @endif
    </div>
</div>
@endsection
