@extends('layouts.dashboard')

@section('title', 'Detail Tiket - MINDSIA')
@section('header_title', 'Detail Tiket')

@section('content')
@php
    $statusColor = match($ticket->status) {
        'open'        => 'bg-amber-100 text-amber-800',
        'in_progress' => 'bg-blue-100 text-blue-800',
        'resolved'    => 'bg-emerald-100 text-emerald-800',
        'closed'      => 'bg-slate-100 text-slate-700',
        default       => 'bg-slate-100 text-slate-700',
    };
    $statusLabel = match($ticket->status) {
        'open'        => 'Terbuka',
        'in_progress' => 'Diproses',
        'resolved'    => 'Selesai',
        'closed'      => 'Ditutup',
        default       => $ticket->status,
    };
    $categoryLabel = match($ticket->category) {
        'complaint'  => 'Pengaduan',
        'suggestion' => 'Saran',
        'question'   => 'Pertanyaan',
        default      => ucfirst($ticket->category),
    };
    $priorityLabel = match($ticket->priority) {
        'low'    => 'Rendah',
        'medium' => 'Sedang',
        'high'   => 'Tinggi',
        default  => ucfirst($ticket->priority ?? ''),
    };
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('support-tickets.index') }}" class="dash-back mb-3 inline-flex items-center gap-1.5 text-xs font-semibold">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Daftar Tiket
        </a>
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $ticket->subject }}</h1>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColor }}">{{ $statusLabel }}</span>
                </div>
                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                    Tiket <span class="font-mono font-semibold" style="color: var(--dash-text-heading);">{{ $ticket->ticket_number }}</span> dari {{ $ticket->member->full_name ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Ticket Info + Assign + Status --}}
        <div class="space-y-5">
            {{-- Info Card --}}
            <div class="rounded-2xl overflow-hidden shadow-sm"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-5 py-4 border-b flex items-center justify-between"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Informasi Tiket</h2>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusColor }}">{{ $statusLabel }}</span>
                </div>
                <div class="p-5 space-y-3.5 text-sm">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider mb-0.5" style="color: var(--dash-text-muted);">Nomor Tiket</p>
                        <p class="font-mono font-semibold" style="color: var(--dash-text-heading);">{{ $ticket->ticket_number }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider mb-0.5" style="color: var(--dash-text-muted);">Member</p>
                        <p class="font-medium" style="color: var(--dash-text-heading);">{{ $ticket->member->full_name ?? '-' }}</p>
                        @if($ticket->branch)
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $ticket->branch->branch_name }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider mb-0.5" style="color: var(--dash-text-muted);">Kategori</p>
                        <p style="color: var(--dash-text-body);">{{ $categoryLabel }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider mb-0.5" style="color: var(--dash-text-muted);">Prioritas</p>
                        <p style="color: var(--dash-text-body);">{{ $priorityLabel }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider mb-0.5" style="color: var(--dash-text-muted);">Ditugaskan Ke</p>
                        <p style="color: var(--dash-text-body);">{{ $ticket->assignedEmployee->full_name ?? 'Belum ditugaskan' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider mb-0.5" style="color: var(--dash-text-muted);">Waktu Dibuat</p>
                        <p class="text-xs" style="color: var(--dash-text-muted);">{{ $ticket->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    @if($ticket->resolved_at)
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider mb-0.5" style="color: var(--dash-text-muted);">Waktu Selesai</p>
                            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $ticket->resolved_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                    @if($ticket->rating)
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider mb-0.5" style="color: var(--dash-text-muted);">Rating Member</p>
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined text-[16px] {{ $i <= $ticket->rating ? 'text-amber-400' : 'text-slate-300' }}">star</span>
                                @endfor
                                <span class="text-xs ml-1 font-semibold" style="color: var(--dash-text-heading);">{{ $ticket->rating }}/5</span>
                            </div>
                            @if($ticket->rating_notes)
                                <p class="text-xs mt-1 italic" style="color: var(--dash-text-muted);">{{ $ticket->rating_notes }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Assign Form Card --}}
            <div class="rounded-2xl overflow-hidden shadow-sm"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-5 py-3.5 border-b"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h3 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">Tugaskan ke Karyawan</h3>
                </div>
                <form action="{{ route('support-tickets.assign', $ticket) }}" method="POST" class="p-4 flex gap-2">
                    @csrf
                    <input type="number" name="employee_id" placeholder="ID Karyawan"
                           value="{{ $ticket->assigned_employee_id }}"
                           class="flex-1 px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity whitespace-nowrap"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[14px]">assignment_ind</span>
                        Tugaskan
                    </button>
                </form>
            </div>

            {{-- Status Change Card --}}
            @if(!in_array($ticket->status, ['closed']))
            <div class="rounded-2xl overflow-hidden shadow-sm"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-5 py-3.5 border-b"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h3 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">Ubah Status Tiket</h3>
                </div>
                <form action="{{ route('support-tickets.status', $ticket) }}" method="POST" class="p-4 flex gap-2">
                    @csrf
                    <select name="new_status"
                            class="flex-1 px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        @if($ticket->status === 'open')
                            <option value="in_progress">Diproses</option>
                            <option value="resolved">Selesai</option>
                            <option value="closed">Tutup</option>
                        @elseif($ticket->status === 'in_progress')
                            <option value="resolved">Selesai</option>
                            <option value="closed">Tutup</option>
                        @elseif($ticket->status === 'resolved')
                            <option value="closed">Tutup</option>
                        @endif
                    </select>
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors whitespace-nowrap"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[14px]">update</span>
                        Ubah
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Right: Original Subject/Message + Thread + Reply Form --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Original Message Card --}}
            <div class="rounded-2xl overflow-hidden shadow-sm"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-6 py-4 border-b"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="font-jakarta font-bold text-base" style="color: var(--dash-text-heading);">{{ $ticket->subject }}</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm leading-relaxed whitespace-pre-wrap" style="color: var(--dash-text-body);">{{ $ticket->message }}</p>
                </div>
            </div>

            {{-- Thread Balasan --}}
            @if($ticket->replies->isNotEmpty())
            <div class="space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider px-1" style="color: var(--dash-text-muted);">Riwayat Balasan</h3>
                @foreach($ticket->replies as $reply)
                    @php $isEmployee = !is_null($reply->employee_id); @endphp
                    <div class="flex gap-3 {{ $isEmployee ? '' : 'flex-row-reverse' }}">
                        <div class="h-8 w-8 flex-shrink-0 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm"
                             style="background-color: {{ $isEmployee ? 'var(--dash-primary)' : '#059669' }};">
                            {{ $isEmployee ? substr($reply->employee->full_name ?? 'A', 0, 1) : substr($reply->member->full_name ?? 'M', 0, 1) }}
                        </div>
                        <div class="max-w-[80%] {{ $isEmployee ? '' : 'items-end' }} flex flex-col gap-1">
                            <p class="text-xs {{ $isEmployee ? '' : 'text-right' }}" style="color: var(--dash-text-muted);">
                                {{ $isEmployee ? ($reply->employee->full_name ?? 'Staff Admin') : ($reply->member->full_name ?? 'Member') }}
                                · {{ $reply->created_at->format('d M Y, H:i') }}
                            </p>
                            @if($isEmployee)
                                <div class="rounded-2xl px-4 py-3 text-sm text-white shadow-sm"
                                     style="background-color: var(--dash-primary);">
                                    {{ $reply->reply }}
                                </div>
                            @else
                                <div class="rounded-2xl px-4 py-3 text-sm border shadow-sm"
                                     style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border); color: var(--dash-text-heading);">
                                    {{ $reply->reply }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Reply Form --}}
            @if(!in_array($ticket->status, ['closed']))
            <div class="rounded-2xl overflow-hidden shadow-sm"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-6 py-4 border-b"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h3 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Tulis Balasan</h3>
                </div>
                <form action="{{ route('support-tickets.replies.store', $ticket) }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <textarea name="reply" rows="4" required minlength="5"
                        placeholder="Tulis balasan resmi kepada member..."
                        class="block w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none transition-all shadow-sm"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"></textarea>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[18px]">send</span>
                            Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
