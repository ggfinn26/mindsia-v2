@extends('layouts.dashboard')

@section('title', 'Detail Tiket - MINDSIA')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('member.tickets.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-500 hover:text-[#215aac] transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Daftar Tiket
        </a>
        <div class="mt-2 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $ticket->subject }}</h1>
                <p class="text-xs text-slate-400 mt-1 font-mono">{{ $ticket->ticket_number }}</p>
            </div>
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
            @endphp
            <span class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-bold uppercase tracking-wide {{ $statusColor }}">
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-red-600">error</span>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Ticket Info -->
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap gap-4 text-xs text-slate-500">
            <span><strong class="text-slate-700">Kategori:</strong> {{ $ticket->category }}</span>
            <span><strong class="text-slate-700">Prioritas:</strong> {{ ucfirst($ticket->priority) }}</span>
            <span><strong class="text-slate-700">Dibuat:</strong> {{ $ticket->created_at->format('d M Y, H:i') }}</span>
            @if($ticket->assignedEmployee)
                <span><strong class="text-slate-700">Ditangani:</strong> {{ $ticket->assignedEmployee->full_name }}</span>
            @endif
        </div>
        <div class="p-6">
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $ticket->message }}</p>
        </div>
    </div>

    <!-- Reply Thread -->
    @if($ticket->replies->isNotEmpty())
    <div class="space-y-4">
        <h3 class="font-jakarta text-sm font-bold text-slate-700 uppercase tracking-wider">Balasan</h3>
        @foreach($ticket->replies as $reply)
            @php $isEmployee = !is_null($reply->employee_id); @endphp
            <div class="flex gap-3 {{ $isEmployee ? '' : 'flex-row-reverse' }}">
                <div class="h-8 w-8 flex-shrink-0 rounded-full {{ $isEmployee ? 'bg-[#215aac]' : 'bg-emerald-600' }} flex items-center justify-center text-white text-xs font-bold">
                    {{ $isEmployee ? substr($reply->employee->full_name ?? 'A', 0, 1) : 'S' }}
                </div>
                <div class="max-w-[75%] {{ $isEmployee ? '' : 'items-end' }} flex flex-col gap-1">
                    <p class="text-xs text-slate-400 {{ $isEmployee ? '' : 'text-right' }}">
                        {{ $isEmployee ? ($reply->employee->full_name ?? 'Admin') : 'Saya' }}
                        · {{ $reply->created_at->format('d M Y, H:i') }}
                    </p>
                    <div class="rounded-xl px-4 py-3 text-sm {{ $isEmployee ? 'bg-slate-100 text-slate-800 rounded-tl-none' : 'bg-[#215aac] text-white rounded-tr-none' }}">
                        {{ $reply->reply }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Reply Form -->
    @if(!in_array($ticket->status, ['closed']))
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-jakarta text-sm font-bold text-slate-800">Tulis Balasan</h3>
        </div>
        <form action="{{ route('member.tickets.replies.store', $ticket) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <textarea name="reply" rows="4" required minlength="5"
                placeholder="Tulis balasan Anda..."
                class="block w-full rounded-lg border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[#215aac] focus:ring-[#215aac] shadow-sm"></textarea>
            <div class="flex justify-end">
                <button type="submit" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[#215aac] px-5 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">send</span> Kirim Balasan
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Rating Form (after resolved/closed) -->
    @if(in_array($ticket->status, ['resolved', 'closed']) && !$ticket->rating)
    <div class="overflow-hidden rounded-xl border border-emerald-100 bg-emerald-50 shadow-sm">
        <div class="px-6 py-4 border-b border-emerald-100">
            <h3 class="font-jakarta text-sm font-bold text-emerald-800">Beri Penilaian Layanan</h3>
        </div>
        <form action="{{ route('member.tickets.rate', $ticket) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div x-data="{ rating: 0 }">
                <div class="flex items-center gap-1 mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" @click="rating = {{ $i }}"
                            class="focus:outline-none transition-colors">
                            <span class="material-symbols-outlined text-3xl"
                                :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-slate-200'">star</span>
                        </button>
                    @endfor
                    <span class="text-sm text-slate-500 ml-2" x-text="rating ? rating + '/5' : 'Pilih rating'"></span>
                    <input type="hidden" name="rating" :value="rating">
                </div>
                <textarea name="rating_notes" rows="2"
                    placeholder="Komentar tambahan (opsional)..."
                    class="block w-full rounded-lg border-emerald-200 bg-white px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"></textarea>
                <div class="flex justify-end mt-3">
                    <button type="submit" class="inline-flex min-h-[38px] items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 text-sm font-bold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                        Kirim Penilaian
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endif

    @if($ticket->rating)
    <div class="flex items-center gap-2 rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
        <span class="text-sm text-slate-500">Penilaian Anda:</span>
        @for($i = 1; $i <= 5; $i++)
            <span class="material-symbols-outlined text-[20px] {{ $i <= $ticket->rating ? 'text-amber-400' : 'text-slate-200' }}">star</span>
        @endfor
        <span class="text-sm font-bold text-slate-700">{{ $ticket->rating }}/5</span>
        @if($ticket->rating_notes)
            <span class="text-sm text-slate-500 ml-2">— {{ $ticket->rating_notes }}</span>
        @endif
    </div>
    @endif
</div>
@endsection
