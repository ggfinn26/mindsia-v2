@extends('layouts.dashboard')

@section('title', 'Tiket Bantuan - MINDSIA')
@section('header_title', 'Tiket Bantuan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Portal Siswa</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Tiket Bantuan</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Ajukan pertanyaan atau laporkan kendala kepada tim support MINDSIA.
            </p>
        </div>
        <a href="{{ route('member.tickets.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90 shadow-sm self-start sm:self-auto"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[16px]">add</span>
            Buat Tiket Baru
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-semibold shadow-sm"
             style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-success-text);">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($tickets->isEmpty())
        {{-- Empty State Card --}}
        <div class="rounded-2xl p-12 flex flex-col items-center justify-center text-center shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4"
                 style="background-color: var(--dash-status-info-bg);">
                <span class="material-symbols-outlined text-3xl" style="color: var(--dash-primary);">confirmation_number</span>
            </div>
            <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Belum Ada Tiket</h3>
            <p class="text-sm max-w-md mx-auto mb-6" style="color: var(--dash-text-muted);">Buat tiket jika ada pertanyaan atau kendala yang perlu dibantu tim MINDSIA.</p>
            <a href="{{ route('member.tickets.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Tiket Pertama
            </a>
        </div>
    @else
        <div class="rounded-2xl shadow-sm overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Nomor Tiket</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Subjek</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kategori</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                        @foreach($tickets as $ticket)
                            @php
                                $statusStyle = match($ticket->status) {
                                    'open'        => 'background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text); border: 1px solid var(--dash-card-border);',
                                    'in_progress' => 'background-color: var(--dash-status-info-bg); color: var(--dash-status-info-text); border: 1px solid var(--dash-card-border);',
                                    'resolved'    => 'background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid var(--dash-card-border);',
                                    'closed'      => 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);',
                                    default       => 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);',
                                };
                                $statusLabel = match($ticket->status) {
                                    'open'        => 'Terbuka',
                                    'in_progress' => 'Diproses',
                                    'resolved'    => 'Selesai',
                                    'closed'      => 'Ditutup',
                                    default       => $ticket->status,
                                };
                            @endphp
                            <tr class="transition-colors"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-6 py-4 font-mono text-xs font-semibold" style="color: var(--dash-text-muted);">
                                    {{ $ticket->ticket_number }}
                                </td>
                                <td class="px-6 py-4 font-semibold font-jakarta max-w-xs truncate" style="color: var(--dash-text-heading);">
                                    {{ $ticket->subject }}
                                </td>
                                <td class="px-6 py-4 capitalize" style="color: var(--dash-text-body);">
                                    {{ $ticket->category }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold shadow-xs"
                                          style="{{ $statusStyle }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4" style="color: var(--dash-text-muted);">
                                    {{ $ticket->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('member.tickets.show', $ticket) }}"
                                       class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors cursor-pointer shadow-xs"
                                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
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
