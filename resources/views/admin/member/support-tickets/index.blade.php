@extends('layouts.dashboard')

@section('title', 'Support Ticket Member - MINDSIA')
@section('header_title', 'Support Ticket Member')

@section('content')
<div class="max-w-7xl mx-auto">
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-start gap-3">
            <span class="material-symbols-outlined text-green-500 mt-0.5">check_circle</span>
            <p class="font-medium text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-jakarta">Support Ticket Member</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola tiket bantuan dari member.</p>
        </div>
        <a href="{{ route('ticket-assignment-rules.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
            <span class="material-symbols-outlined text-[18px]">settings</span>
            Pengaturan Assignment
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" class="bg-white border border-gray-100 rounded-xl shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div class="flex flex-col gap-1 min-w-[140px]">
            <label class="text-xs font-medium text-gray-600">Status</label>
            <select name="status" class="rounded-lg border-gray-300 text-sm px-3 py-2 shadow-sm">
                <option value="">Semua Status</option>
                <option value="open" @selected(request('status') === 'open')>Terbuka</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>Diproses</option>
                <option value="resolved" @selected(request('status') === 'resolved')>Selesai</option>
                <option value="closed" @selected(request('status') === 'closed')>Ditutup</option>
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[140px]">
            <label class="text-xs font-medium text-gray-600">Kategori</label>
            <select name="category" class="rounded-lg border-gray-300 text-sm px-3 py-2 shadow-sm">
                <option value="">Semua Kategori</option>
                <option value="complaint" @selected(request('category') === 'complaint')>Pengaduan</option>
                <option value="suggestion" @selected(request('category') === 'suggestion')>Saran</option>
                <option value="question" @selected(request('category') === 'question')>Pertanyaan</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#5586DB] rounded-lg hover:bg-[#4370BC] transition-colors shadow-sm">
            Filter
        </button>
        @if(request()->hasAny(['status', 'category', 'branch_id']))
            <a href="{{ route('support-tickets.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Reset</a>
        @endif
    </form>

    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nomor Tiket</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Member</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Subjek</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kategori</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Ditugaskan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Dibuat</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tickets as $ticket)
                        @php
                            $statusColor = match($ticket->status) {
                                'open'        => 'bg-amber-100 text-amber-700',
                                'in_progress' => 'bg-blue-100 text-blue-700',
                                'resolved'    => 'bg-emerald-100 text-emerald-700',
                                'closed'      => 'bg-gray-100 text-gray-600',
                                default       => 'bg-gray-100 text-gray-600',
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
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs text-gray-600">{{ $ticket->ticket_number }}</td>
                            <td class="px-5 py-3.5 text-gray-900 font-medium">{{ $ticket->member->full_name ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-700 max-w-[240px] truncate">{{ $ticket->subject }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $categoryLabel }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $ticket->assignedEmployee->full_name ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold {{ $statusColor }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $ticket->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('support-tickets.show', $ticket) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-[#5586DB] bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-gray-400 text-sm">
                                <span class="material-symbols-outlined text-4xl text-gray-200 block mb-2">confirmation_number</span>
                                Belum ada support ticket.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
