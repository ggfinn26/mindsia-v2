@extends('layouts.dashboard')

@section('title', 'Detail Surat Keluar')
@section('header_title', 'Surat Keluar')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('out-letters-upload.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Arsip Surat Keluar
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $outLetterViaUpload->subject }}</h1>
            <p class="mt-0.5 font-mono text-sm text-[color:var(--dash-text-faint)]">{{ $outLetterViaUpload->letter_number ?? '—' }}</p>
        </div>
        <div class="flex gap-2">
            @can('letter.out.update')
                <a href="{{ route('out-letters-upload.edit', $outLetterViaUpload) }}" class="inline-flex min-h-9 items-center border border-[color:var(--dash-input-border)] px-3 text-xs font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Edit</a>
            @endcan
            @if ($outLetterViaUpload->telegram_file_id)
                <a href="{{ route('out-letters-upload.download', $outLetterViaUpload) }}" class="inline-flex min-h-9 items-center gap-1 border border-[color:var(--dash-input-border)] px-3 text-xs font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">
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
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Tanggal</p><p class="mt-1 font-medium">{{ $outLetterViaUpload->letter_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Penerima</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $outLetterViaUpload->recipient ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Jenis</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $outLetterViaUpload->letter_type ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">File</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $outLetterViaUpload->original_name ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Diupload oleh</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $outLetterViaUpload->uploadedBy?->full_name ?? '—' }}</p></div>
            @if ($outLetterViaUpload->notes)
                <div class="col-span-2 sm:col-span-3"><p class="text-xs text-[color:var(--dash-text-muted)]">Catatan</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $outLetterViaUpload->notes }}</p></div>
            @endif
        </div>
    </div>
</div>
@endsection
