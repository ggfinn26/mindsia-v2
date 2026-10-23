@extends('layouts.dashboard')

@section('title', 'Detail Tiket - MINDSIA')
@section('header_title', 'Detail Tiket')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('member.tickets.index') }}"
               class="dash-back flex items-center justify-center w-9 h-9 rounded-lg transition-colors cursor-pointer shrink-0"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
               title="Kembali ke Daftar Tiket">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5"
                   style="color: var(--dash-primary-text);">{{ $ticket->ticket_number }}</p>
                <h1 class="font-jakarta text-xl sm:text-2xl font-bold tracking-tight"
                    style="color: var(--dash-text-heading);">{{ $ticket->subject }}</h1>
            </div>
        </div>
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
        <span class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-bold uppercase tracking-wide self-start sm:self-auto shadow-xs"
              style="{{ $statusStyle }}">
            {{ $statusLabel }}
        </span>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-semibold shadow-sm"
             style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-success-text);">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-semibold shadow-sm"
             style="background-color: var(--dash-status-danger-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Ticket Info -->
    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex flex-wrap gap-4 text-xs font-medium"
             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
            @php
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
                    default  => ucfirst($ticket->priority),
                };
            @endphp
            <span><strong style="color: var(--dash-text-heading);">Kategori:</strong> {{ $categoryLabel }}</span>
            <span><strong style="color: var(--dash-text-heading);">Prioritas:</strong> {{ $priorityLabel }}</span>
            <span><strong style="color: var(--dash-text-heading);">Dibuat:</strong> {{ $ticket->created_at->format('d M Y, H:i') }}</span>
            @if($ticket->assignedEmployee)
                <span><strong style="color: var(--dash-text-heading);">Ditangani:</strong> {{ $ticket->assignedEmployee->full_name }}</span>
            @endif
        </div>
        <div class="p-6">
            <p class="text-sm leading-relaxed whitespace-pre-wrap" style="color: var(--dash-text-heading);">{{ $ticket->message }}</p>
        </div>
    </div>

    <!-- Reply Thread -->
    @if($ticket->replies->isNotEmpty())
    <div class="space-y-4">
        <h3 class="font-jakarta text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Riwayat Percakapan</h3>
        @foreach($ticket->replies as $reply)
            @php $isEmployee = !is_null($reply->employee_id); @endphp
            <div class="flex gap-3 {{ $isEmployee ? '' : 'flex-row-reverse' }}">
                <div class="h-8 w-8 flex-shrink-0 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-xs"
                     style="{{ $isEmployee ? 'background-color: var(--dash-primary);' : 'background-color: var(--dash-status-success-text);' }}">
                    {{ $isEmployee ? substr($reply->employee->full_name ?? 'A', 0, 1) : 'S' }}
                </div>
                <div class="max-w-[75%] {{ $isEmployee ? '' : 'items-end' }} flex flex-col gap-1">
                    <p class="text-xs {{ $isEmployee ? '' : 'text-right' }}" style="color: var(--dash-text-muted);">
                        {{ $isEmployee ? ($reply->employee->full_name ?? 'Admin') : 'Saya' }}
                        · {{ $reply->created_at->format('d M Y, H:i') }}
                    </p>
                    <div class="rounded-2xl px-4 py-3 text-sm shadow-xs"
                         style="{{ $isEmployee ? 'background-color: var(--dash-card-bg); color: var(--dash-text-heading); border: 1px solid var(--dash-card-border); border-top-left-radius: 4px;' : 'background-color: var(--dash-primary); color: #ffffff; border-top-right-radius: 4px;' }}">
                        {{ $reply->reply }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Reply Form -->
    @if(!in_array($ticket->status, ['closed']))
    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Tulis Balasan</h3>
        </div>
        <form action="{{ route('member.tickets.replies.store', $ticket) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <textarea name="reply" rows="4" required minlength="5"
                placeholder="Tulis balasan Anda..."
                class="block w-full rounded-xl px-3.5 py-2.5 text-sm resize-none transition-colors focus:outline-none"
                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"></textarea>
            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-xs font-semibold text-white transition-opacity hover:opacity-90 shadow-sm cursor-pointer"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">send</span> Kirim Balasan
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Rating Form (after resolved/closed) -->
    @if(in_array($ticket->status, ['resolved', 'closed']) && !$ticket->rating)
    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="background-color: var(--dash-status-success-bg); border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-status-success-text);">Beri Penilaian Layanan</h3>
        </div>
        <form action="{{ route('member.tickets.rate', $ticket) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div x-data="{ rating: 0 }">
                <div class="flex items-center gap-1 mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" @click="rating = {{ $i }}"
                            class="focus:outline-none transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[28px]"
                                :class="rating >= {{ $i }} ? 'text-amber-400' : ''"
                                :style="rating < {{ $i }} ? 'color: var(--dash-text-faint);' : ''">star</span>
                        </button>
                    @endfor
                    <span class="text-xs font-semibold ml-2" style="color: var(--dash-text-muted);" x-text="rating ? rating + ' / 5 Bintang' : 'Pilih rating'"></span>
                    <input type="hidden" name="rating" :value="rating">
                </div>
                <textarea name="rating_notes" rows="2"
                    placeholder="Komentar tambahan (opsional)..."
                    class="block w-full rounded-xl px-3.5 py-2.5 text-sm resize-none transition-colors focus:outline-none"
                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"></textarea>
                <div class="flex justify-end mt-3">
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-xs font-semibold text-white transition-opacity hover:opacity-90 shadow-sm cursor-pointer"
                            style="background-color: var(--dash-status-success-text);">
                        Kirim Penilaian
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endif

    @if($ticket->rating)
    <div class="flex items-center gap-2 rounded-2xl p-4 shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Penilaian Anda:</span>
        <div class="flex items-center gap-0.5">
            @for($i = 1; $i <= 5; $i++)
                <span class="material-symbols-outlined text-[18px] {{ $i <= $ticket->rating ? 'text-amber-400' : '' }}"
                      style="{{ $i > $ticket->rating ? 'color: var(--dash-text-faint);' : '' }}">star</span>
            @endfor
        </div>
        <span class="text-xs font-bold" style="color: var(--dash-text-heading);">{{ $ticket->rating }}/5</span>
        @if($ticket->rating_notes)
            <span class="text-xs ml-2" style="color: var(--dash-text-muted);"> - {{ $ticket->rating_notes }}</span>
        @endif
    </div>
    @endif
</div>
@endsection
