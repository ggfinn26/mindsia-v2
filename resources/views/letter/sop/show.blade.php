@extends('layouts.dashboard')

@section('title', 'Detail Dokumen SOP')
@section('header_title', 'Dokumen SOP')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('sop-documents.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Dokumen SOP
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $sopDocument->title }}</h1>
            <div class="mt-1 flex items-center gap-3">
                <p class="font-mono text-sm text-[color:var(--dash-text-faint)]">{{ $sopDocument->document_code ?? '—' }} v{{ $sopDocument->version }}</p>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $sopDocument->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' }}">
                    {{ $sopDocument->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            @can('letter.sop.update')
                <a href="{{ route('sop-documents.edit', $sopDocument) }}" class="inline-flex min-h-9 items-center border border-[color:var(--dash-input-border)] px-3 text-xs font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Edit</a>
                @if ($sopDocument->is_active)
                    <form method="POST" action="{{ route('sop-documents.deactivate', $sopDocument) }}">
                        @csrf
                        <button type="submit" class="inline-flex min-h-9 items-center border border-amber-300 px-3 text-xs font-semibold text-amber-800 hover:bg-amber-50">Nonaktifkan</button>
                    </form>
                @endif
            @endcan
            @if ($sopDocument->telegram_file_id)
                <a href="{{ route('sop-documents.download', $sopDocument) }}" class="inline-flex min-h-9 items-center gap-1 border border-[color:var(--dash-input-border)] px-3 text-xs font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">
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
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Kategori</p><p class="mt-1 font-medium">{{ $sopDocument->category ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Tanggal efektif</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $sopDocument->effective_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Diupload oleh</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $sopDocument->uploadedBy?->full_name ?? '—' }}</p></div>
            @if ($sopDocument->visible_to)
                <div class="col-span-2 sm:col-span-3">
                    <p class="text-xs text-[color:var(--dash-text-muted)]">Visible untuk role</p>
                    <p class="mt-1 text-[color:var(--dash-text-body)]">{{ implode(', ', $sopDocument->visible_to) }}</p>
                </div>
            @else
                <div><p class="text-xs text-[color:var(--dash-text-muted)]">Visible untuk</p><p class="mt-1 text-[color:var(--dash-text-body)]">Semua role</p></div>
            @endif
        </div>
    </div>
</div>
@endsection
