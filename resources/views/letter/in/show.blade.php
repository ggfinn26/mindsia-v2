@extends('layouts.dashboard')

@section('title', 'Detail Surat Masuk')
@section('header_title', 'Surat Masuk')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6 pb-10">
    {{-- Header Section Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('in-letters.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span> Kembali ke Surat Masuk
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">
                {{ $inLetter->subject }}
            </h1>
            <p class="font-mono text-xs mt-2 px-2.5 py-1 rounded-md inline-block"
               style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-faint);">
                {{ $inLetter->letter_number ?? '-' }}
            </p>
        </div>
        <div class="flex gap-2 shrink-0">
            @can('letter.in.update')
                <a href="{{ route('in-letters.edit', $inLetter) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                </a>
            @endcan
            @if ($inLetter->telegram_file_id)
                <a href="{{ route('in-letters.download', $inLetter) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">download</span> Download
                </a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 sm:px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Surat Masuk</h2>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-2 gap-5 sm:grid-cols-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Pengirim</p>
                <p class="mt-1 text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $inLetter->sender_name }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Surat</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $inLetter->letter_date?->format('d M Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Diterima</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $inLetter->receive_date?->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">PIC Penerima</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $inLetter->pic?->full_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">File Berkas</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $inLetter->original_name ?? ($inLetter->telegram_file_id ? 'Ada' : '-') }}</p>
            </div>
            @if ($inLetter->notes)
                <div class="col-span-2 sm:col-span-3">
                    <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Catatan Internal</p>
                    <p class="mt-1 text-sm" style="color: var(--dash-text-body);">{{ $inLetter->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
