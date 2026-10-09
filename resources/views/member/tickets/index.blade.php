@extends('layouts.dashboard')

@section('title', 'Tiket Bantuan - MINDSIA')
@section('header_title', 'Tiket Bantuan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Tiket Bantuan</h1>
            <p class="text-sm text-slate-500 mt-1">Ajukan pertanyaan atau masalah kepada tim MINDSIA.</p>
        </div>
        <a href="{{ route('member.tickets.create') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[var(--dash-primary,#215aac)] px-5 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
            <span class="material-symbols-outlined text-[18px]">add</span> Buat Tiket
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if($tickets->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-xl border border-slate-100 bg-white py-16 px-6 shadow-sm text-center">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 mb-4">
                <span class="material-symbols-outlined text-slate-400 text-3xl">confirmation_number</span>
            </div>
            <h3 class="font-jakarta text-base font-bold text-slate-900">Belum Ada Tiket</h3>
            <p class="mt-1 text-sm text-slate-500 max-w-md mx-auto">Buat tiket jika ada pertanyaan atau kendala yang perlu dibantu tim MINDSIA.</p>
            <div class="mt-6">
                <a href="{{ route('member.tickets.create') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[var(--dash-primary,#215aac)] px-5 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">add</span> Buat Tiket Pertama
                </a>
            </div>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Nomor Tiket</th>
                            <th class="px-6 py-4">Subjek</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-slate-700">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $ticket->ticket_number }}</td>
                                <td class="px-6 py-4 font-jakarta font-bold text-slate-900 max-w-[200px] truncate">{{ $ticket->subject }}</td>
                                <td class="px-6 py-4 capitalize text-slate-600">{{ $ticket->category }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColor = match($ticket->status) {
                                            'open'     => 'bg-amber-100 text-amber-800',
                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                            'resolved' => 'bg-emerald-100 text-emerald-800',
                                            'closed'   => 'bg-slate-100 text-slate-700',
                                            default    => 'bg-slate-100 text-slate-700',
                                        };
                                        $statusLabel = match($ticket->status) {
                                            'open'        => 'Terbuka',
                                            'in_progress' => 'Diproses',
                                            'resolved'    => 'Selesai',
                                            'closed'      => 'Ditutup',
                                            default       => $ticket->status,
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">{{ $ticket->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('member.tickets.show', $ticket) }}" class="inline-flex min-h-[34px] items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
