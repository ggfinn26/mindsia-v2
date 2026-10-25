@extends('layouts.dashboard')

@section('title', 'Support Ticket Member - MINDSIA')
@section('header_title', 'Support Ticket Member')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Support Ticket Member</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Kelola tiket pertanyaan, pengaduan, dan saran dari para member bimbingan.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('ticket-assignment-rules.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm shrink-0"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[18px]">settings</span>
                Pengaturan Assignment
            </a>
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

    {{-- Filter Card --}}
    <form method="GET" action="{{ route('support-tickets.index') }}"
          class="flex flex-wrap gap-3 items-end p-4 sm:p-5 rounded-2xl shadow-sm"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex flex-col min-w-[150px]">
            <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Status</label>
            <select name="status"
                    class="h-[38px] px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Status</option>
                <option value="open" @selected(request('status') === 'open')>Terbuka</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>Diproses</option>
                <option value="resolved" @selected(request('status') === 'resolved')>Selesai</option>
                <option value="closed" @selected(request('status') === 'closed')>Ditutup</option>
            </select>
        </div>
        <div class="flex flex-col min-w-[150px]">
            <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Kategori</label>
            <select name="category"
                    class="h-[38px] px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Kategori</option>
                <option value="complaint" @selected(request('category') === 'complaint')>Pengaduan</option>
                <option value="suggestion" @selected(request('category') === 'suggestion')>Saran</option>
                <option value="question" @selected(request('category') === 'question')>Pertanyaan</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 h-[38px] px-4 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                Filter
            </button>
            @if(request()->hasAny(['status', 'category', 'branch_id']))
                <a href="{{ route('support-tickets.index') }}"
                   class="inline-flex items-center gap-1.5 h-[38px] px-3 rounded-lg text-sm font-semibold transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Data Table (Zero horizontal scroll, compact combined columns) --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full text-sm text-left">
            <thead>
                <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Tiket & Subjek</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Member & Kategori</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Status & Penugasan</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Tanggal</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right" style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    @php
                        $statusColor = match($ticket->status) {
                            'open'        => 'bg-amber-100 text-amber-700',
                            'in_progress' => 'bg-blue-100 text-blue-700',
                            'resolved'    => 'bg-emerald-100 text-emerald-700',
                            'closed'      => 'bg-slate-100 text-slate-600',
                            default       => 'bg-slate-100 text-slate-600',
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
                            default      => \App\Support\EnumLabels::get('ticket_category', $ticket->category),
                        };
                    @endphp
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        {{-- Tiket & Subjek Col --}}
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs block" style="color: var(--dash-text-muted);">{{ $ticket->ticket_number }}</span>
                            <p class="font-semibold text-sm line-clamp-1 mt-0.5" style="color: var(--dash-text-heading);" title="{{ $ticket->subject }}">
                                {{ $ticket->subject }}
                            </p>
                        </td>

                        {{-- Member & Kategori Col --}}
                        <td class="px-5 py-4">
                            <p class="font-medium text-xs" style="color: var(--dash-text-heading);">{{ $ticket->member->full_name ?? '-' }}</p>
                            <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">{{ $categoryLabel }}</p>
                        </td>

                        {{-- Status & Penugasan Col --}}
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $statusColor }} mb-1">
                                {{ $statusLabel }}
                            </span>
                            <p class="text-xs" style="color: var(--dash-text-body);">
                                <span class="material-symbols-outlined text-[13px] align-middle" style="color: var(--dash-text-muted);">person</span>
                                {{ $ticket->assignedEmployee->full_name ?? 'Belum ditugaskan' }}
                            </p>
                        </td>

                        {{-- Tanggal Col --}}
                        <td class="px-5 py-4 whitespace-nowrap text-xs" style="color: var(--dash-text-muted);">
                            {{ $ticket->created_at->format('d M Y') }}
                        </td>

                        {{-- Aksi Col --}}
                        <td class="px-5 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('support-tickets.show', $ticket) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">confirmation_number</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada tiket support yang diajukan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($tickets->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
