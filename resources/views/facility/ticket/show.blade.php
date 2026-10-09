@extends('layouts.dashboard')

@section('title', 'Detail Tiket Fasilitas')
@section('header_title', 'Tiket Fasilitas')

@section('content')
@php
    $statusBadge = [
        'pending_review' => 'bg-amber-100 text-amber-800',
        'approved'       => 'bg-blue-100 text-blue-800',
        'rejected'       => 'bg-red-100 text-red-800',
        'resolved'       => 'bg-emerald-100 text-emerald-800',
    ];
@endphp

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('facility.tickets.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Tiket Fasilitas
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $facilityTicket->title }}</h1>
            <div class="mt-1 flex items-center gap-3">
                <p class="font-mono text-sm text-[color:var(--dash-text-faint)]">{{ $facilityTicket->ticket_number }}</p>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$facilityTicket->status] ?? 'bg-[var(--dash-nav-hover-bg)]' }}">
                    {{ str_replace('_', ' ', ucfirst($facilityTicket->status)) }}
                </span>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Info --}}
    <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 text-sm">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Cabang</p><p class="mt-1 font-medium">{{ $facilityTicket->branch?->branch_name ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Kategori</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ ucfirst($facilityTicket->category) }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Prioritas</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ ['low'=>'Rendah','medium'=>'Sedang','high'=>'Tinggi'][$facilityTicket->priority] ?? ucfirst($facilityTicket->priority) }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Dibuat oleh</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $facilityTicket->createdBy?->full_name ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Ditugaskan ke</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $facilityTicket->assignedTo?->full_name ?? '—' }}</p></div>
            @if ($facilityTicket->cost_amount)
                <div><p class="text-xs text-[color:var(--dash-text-muted)]">Biaya penanganan</p><p class="mt-1 font-semibold text-[color:var(--dash-text-heading)]">Rp {{ number_format($facilityTicket->cost_amount, 0, ',', '.') }}</p></div>
            @endif
        </div>
        @if ($facilityTicket->description)
            <div class="mt-4 border-t border-[color:var(--dash-card-border)] pt-4">
                <p class="text-xs text-[color:var(--dash-text-muted)]">Deskripsi</p>
                <p class="mt-1 text-[color:var(--dash-text-body)] whitespace-pre-line">{{ $facilityTicket->description }}</p>
            </div>
        @endif
    </div>

    {{-- Actions --}}
    @if ($facilityTicket->status === 'pending_review')
        @can('facility.ticket.review')
        <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 space-y-3">
            <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Review Tiket</h3>
            <form method="POST" action="{{ route('facility.tickets.review', $facilityTicket) }}" class="flex items-end gap-3">
                @csrf
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Catatan</label>
                    <input type="text" name="notes" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
                <button type="submit" name="action" value="approve" class="inline-flex min-h-9 items-center bg-emerald-600 px-3 text-xs font-semibold text-white hover:bg-emerald-700">Setujui</button>
                <button type="submit" name="action" value="reject" class="inline-flex min-h-9 items-center border border-red-200 px-3 text-xs font-semibold text-red-700 hover:bg-red-50">Tolak</button>
            </form>
        </div>
        @endcan
    @elseif ($facilityTicket->status === 'approved')
        @can('facility.ticket.assign')
        <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 space-y-3">
            <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Assign Penanganan</h3>
            <form method="POST" action="{{ route('facility.tickets.assign', $facilityTicket) }}" class="flex items-end gap-3">
                @csrf
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Ditugaskan ke</label>
                    <select name="assigned_to_employee_id" required class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                        <option value="">— Pilih pegawai —</option>
                        @foreach (\App\Models\Employee::where('is_active', true)->get() as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex min-h-9 items-center bg-[var(--dash-primary,#215aac)] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Assign</button>
            </form>
            <form method="POST" action="{{ route('facility.tickets.resolve', $facilityTicket) }}" class="flex items-end gap-3 border-t border-[color:var(--dash-card-border)] pt-3">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Biaya (Rp)</label>
                    <input type="number" name="cost_amount" min="0" step="0.01" class="block border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">Catatan penyelesaian</label>
                    <input type="text" name="notes" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
                <button type="submit" class="inline-flex min-h-9 items-center bg-emerald-600 px-3 text-xs font-semibold text-white hover:bg-emerald-700">Selesaikan</button>
            </form>
        </div>
        @endcan
    @endif

    {{-- Upload Attachment --}}
    @can('facility.ticket.update')
    <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 space-y-3">
        <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Tambah Lampiran</h3>
        <form method="POST" action="{{ route('facility.tickets.attachments.store', $facilityTicket) }}" enctype="multipart/form-data" class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <label class="mb-1 block text-xs font-semibold text-[color:var(--dash-text-body)]">File (foto/dokumen)</label>
                <input type="file" name="file" required class="block w-full text-sm text-[color:var(--dash-text-body)] file:mr-3 file:py-1.5 file:px-3 file:border file:border-[color:var(--dash-input-border)] file:text-xs file:font-medium file:bg-[var(--dash-card-bg)] file:text-[color:var(--dash-text-body)] hover:file:bg-[var(--dash-nav-hover-bg)]">
            </div>
            <button type="submit" class="inline-flex min-h-9 items-center bg-[var(--dash-primary,#215aac)] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Upload</button>
        </form>
    </div>
    @endcan

    {{-- Status History --}}
    @if ($facilityTicket->statusHistories->isNotEmpty())
    <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[color:var(--dash-card-border)] px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Riwayat Status</h3>
        </div>
        <ul class="divide-y divide-[color:var(--dash-card-border)]">
            @foreach ($facilityTicket->statusHistories as $h)
                <li class="px-5 py-3 text-sm flex items-start gap-3">
                    <span class="mt-0.5 text-xs font-mono text-[color:var(--dash-text-faint)] whitespace-nowrap">{{ $h->changed_at?->format('d M H:i') }}</span>
                    <div>
                        <span class="font-semibold text-[color:var(--dash-text-heading)]">{{ str_replace('_', ' ', ucfirst($h->to_status)) }}</span>
                        @if ($h->notes)
                            <span class="text-[color:var(--dash-text-muted)]"> — {{ $h->notes }}</span>
                        @endif
                        <span class="text-[color:var(--dash-text-faint)] text-xs"> oleh {{ $h->changedBy?->full_name }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Attachments --}}
    @if ($facilityTicket->attachments->isNotEmpty())
    <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[color:var(--dash-card-border)] px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Lampiran</h3>
        </div>
        @foreach ($facilityTicket->attachments as $att)
            <div class="flex items-center justify-between border-b border-[color:var(--dash-card-border)] px-5 py-3 last:border-0 text-sm">
                <p class="text-[color:var(--dash-text-body)]">{{ $att->original_name ?? $att->telegram_file_id }}</p>
                <form method="POST" action="{{ route('facility.tickets.attachments.destroy', [$facilityTicket, $att]) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:underline">Hapus</button>
                </form>
            </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
