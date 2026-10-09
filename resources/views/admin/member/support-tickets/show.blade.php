@extends('layouts.dashboard')

@section('title', 'Detail Tiket - MINDSIA')
@section('header_title', 'Detail Tiket')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('support-tickets.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-[color:var(--dash-text-muted)] hover:text-[#5586DB] transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Daftar Tiket
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-start gap-3">
            <span class="material-symbols-outlined text-green-500 mt-0.5">check_circle</span>
            <p class="font-medium text-sm">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-start gap-3">
            <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
            <p class="font-medium text-sm">{{ session('error') }}</p>
        </div>
    @endif

    @php
        $statusColor = match($ticket->status) {
            'open'        => 'bg-amber-100 text-amber-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'resolved'    => 'bg-emerald-100 text-emerald-800',
            'closed'      => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]',
            default       => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]',
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Ticket Info + Actions -->
        <div class="space-y-5">
            <!-- Info Card -->
            <div class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-[color:var(--dash-card-border)] flex items-center justify-between">
                    <h2 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Informasi Tiket</h2>
                    <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-bold {{ $statusColor }}">{{ $statusLabel }}</span>
                </div>
                <div class="p-5 space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-[color:var(--dash-text-faint)] font-medium uppercase tracking-wider mb-0.5">Nomor Tiket</p>
                        <p class="font-mono text-[color:var(--dash-text-heading)]">{{ $ticket->ticket_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[color:var(--dash-text-faint)] font-medium uppercase tracking-wider mb-0.5">Member</p>
                        <p class="text-[color:var(--dash-text-heading)] font-medium">{{ $ticket->member->full_name ?? '-' }}</p>
                        @if($ticket->branch)
                            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $ticket->branch->branch_name }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-[color:var(--dash-text-faint)] font-medium uppercase tracking-wider mb-0.5">Kategori</p>
                        <p class="text-[color:var(--dash-text-body)]">{{ $categoryLabel }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[color:var(--dash-text-faint)] font-medium uppercase tracking-wider mb-0.5">Prioritas</p>
                        <p class="text-[color:var(--dash-text-body)]">{{ $priorityLabel }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[color:var(--dash-text-faint)] font-medium uppercase tracking-wider mb-0.5">Ditugaskan Ke</p>
                        <p class="text-[color:var(--dash-text-body)]">{{ $ticket->assignedEmployee->full_name ?? 'Belum ditugaskan' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[color:var(--dash-text-faint)] font-medium uppercase tracking-wider mb-0.5">Dibuat</p>
                        <p class="text-[color:var(--dash-text-body)] text-xs">{{ $ticket->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    @if($ticket->resolved_at)
                        <div>
                            <p class="text-xs text-[color:var(--dash-text-faint)] font-medium uppercase tracking-wider mb-0.5">Diselesaikan</p>
                            <p class="text-[color:var(--dash-text-body)] text-xs">{{ $ticket->resolved_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                    @if($ticket->rating)
                        <div>
                            <p class="text-xs text-[color:var(--dash-text-faint)] font-medium uppercase tracking-wider mb-0.5">Rating Member</p>
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined text-[16px] {{ $i <= $ticket->rating ? 'text-amber-400' : 'text-gray-200' }}">star</span>
                                @endfor
                                <span class="text-xs text-[color:var(--dash-text-muted)] ml-1">{{ $ticket->rating }}/5</span>
                            </div>
                            @if($ticket->rating_notes)
                                <p class="text-xs text-[color:var(--dash-text-muted)] mt-1">{{ $ticket->rating_notes }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assign Form -->
            <div class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-[color:var(--dash-card-border)]">
                    <h3 class="text-xs font-bold text-[color:var(--dash-text-body)] uppercase tracking-wider">Tugaskan ke Employee</h3>
                </div>
                <form action="{{ route('support-tickets.assign', $ticket) }}" method="POST" class="p-4 flex gap-2">
                    @csrf
                    <input type="number" name="employee_id" placeholder="Employee ID"
                           value="{{ $ticket->assigned_employee_id }}"
                           class="flex-1 rounded-lg border-[color:var(--dash-input-border)] text-sm px-3 py-2 shadow-sm">
                    <button type="submit" class="px-3 py-2 text-sm font-medium text-white bg-[#5586DB] rounded-lg hover:bg-[#4370BC] transition-colors shadow-sm whitespace-nowrap">
                        Tugaskan
                    </button>
                </form>
            </div>

            <!-- Status Change -->
            @if(!in_array($ticket->status, ['closed']))
            <div class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-[color:var(--dash-card-border)]">
                    <h3 class="text-xs font-bold text-[color:var(--dash-text-body)] uppercase tracking-wider">Ubah Status</h3>
                </div>
                <form action="{{ route('support-tickets.status', $ticket) }}" method="POST" class="p-4 flex gap-2">
                    @csrf
                    <select name="new_status" class="flex-1 rounded-lg border-[color:var(--dash-input-border)] text-sm px-3 py-2 shadow-sm">
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
                    <button type="submit" class="px-3 py-2 text-sm font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-800 transition-colors shadow-sm whitespace-nowrap">
                        Ubah
                    </button>
                </form>
            </div>
            @endif
        </div>

        <!-- Right: Thread + Reply -->
        <div class="lg:col-span-2 space-y-5">
            <!-- Original Message -->
            <div class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-[color:var(--dash-card-border)]">
                    <h2 class="font-jakarta font-bold text-[color:var(--dash-text-heading)]">{{ $ticket->subject }}</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-[color:var(--dash-text-body)] leading-relaxed whitespace-pre-wrap">{{ $ticket->message }}</p>
                </div>
            </div>

            <!-- Reply Thread -->
            @if($ticket->replies->isNotEmpty())
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-[color:var(--dash-text-muted)] uppercase tracking-wider px-1">Balasan</h3>
                @foreach($ticket->replies as $reply)
                    @php $isEmployee = !is_null($reply->employee_id); @endphp
                    <div class="flex gap-3 {{ $isEmployee ? '' : 'flex-row-reverse' }}">
                        <div class="h-8 w-8 flex-shrink-0 rounded-full {{ $isEmployee ? 'bg-[#5586DB]' : 'bg-emerald-600' }} flex items-center justify-center text-white text-xs font-bold">
                            {{ $isEmployee ? substr($reply->employee->full_name ?? 'A', 0, 1) : substr($reply->member->full_name ?? 'M', 0, 1) }}
                        </div>
                        <div class="max-w-[80%] {{ $isEmployee ? '' : 'items-end' }} flex flex-col gap-1">
                            <p class="text-xs text-[color:var(--dash-text-faint)] {{ $isEmployee ? '' : 'text-right' }}">
                                {{ $isEmployee ? ($reply->employee->full_name ?? 'Admin') : ($reply->member->full_name ?? 'Member') }}
                                · {{ $reply->created_at->format('d M Y, H:i') }}
                            </p>
                            <div class="rounded-xl px-4 py-3 text-sm {{ $isEmployee ? 'bg-[#5586DB] text-white rounded-tl-none' : 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-heading)] rounded-tr-none' }}">
                                {{ $reply->reply }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Reply Form -->
            @if(!in_array($ticket->status, ['closed']))
            <div class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-[color:var(--dash-card-border)]">
                    <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Tulis Balasan</h3>
                </div>
                <form action="{{ route('support-tickets.replies.store', $ticket) }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <textarea name="reply" rows="4" required minlength="5"
                        placeholder="Tulis balasan kepada member..."
                        class="block w-full rounded-lg border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] px-3 py-2.5 text-sm focus:border-[#5586DB] focus:ring-[#5586DB] shadow-sm"></textarea>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[#5586DB] px-5 text-sm font-bold text-white shadow-sm hover:bg-[#4370BC] transition-colors">
                            <span class="material-symbols-outlined text-[18px]">send</span> Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
