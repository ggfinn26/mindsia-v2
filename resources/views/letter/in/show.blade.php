@extends('layouts.dashboard')

@section('title', 'Detail Surat Masuk')
@section('header_title', 'Surat Masuk')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('in-letters.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Surat Masuk
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $inLetter->subject }}</h1>
            <p class="font-mono text-sm text-[color:var(--dash-text-faint)]">{{ $inLetter->letter_number ?? '—' }}</p>
        </div>
        <div class="flex gap-2">
            @can('letter.in.update')
                <a href="{{ route('in-letters.edit', $inLetter) }}" class="inline-flex min-h-9 items-center border border-[color:var(--dash-input-border)] px-3 text-xs font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Edit</a>
            @endcan
            @if ($inLetter->telegram_file_id)
                <a href="{{ route('in-letters.download', $inLetter) }}" class="inline-flex min-h-9 items-center gap-1 border border-[color:var(--dash-input-border)] px-3 text-xs font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">
                    <span class="material-symbols-outlined text-[16px]">download</span> Download
                </a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 text-sm">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Pengirim</p><p class="mt-1 font-semibold text-[color:var(--dash-text-heading)]">{{ $inLetter->sender_name }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Tanggal surat</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $inLetter->letter_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Tanggal diterima</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $inLetter->receive_date?->format('d M Y') }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">PIC</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $inLetter->pic?->full_name ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">File</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $inLetter->original_name ?? ($inLetter->telegram_file_id ? 'Ada' : '—') }}</p></div>
            @if ($inLetter->notes)
                <div class="col-span-2 sm:col-span-3"><p class="text-xs text-[color:var(--dash-text-muted)]">Catatan</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $inLetter->notes }}</p></div>
            @endif
        </div>
    </div>
</div>
@endsection
