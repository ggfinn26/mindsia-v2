@extends('layouts.dashboard')

@section('title', 'Detail Surat Keluar')
@section('header_title', 'Surat Keluar')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('out-letters-upload.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Arsip Surat Keluar
            </a>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $outLetterViaUpload->subject }}</h2>
            <p class="mt-0.5 font-mono text-sm text-slate-400">{{ $outLetterViaUpload->letter_number ?? '—' }}</p>
        </div>
        <div class="flex gap-2">
            @can('letter.out.update')
                <a href="{{ route('out-letters-upload.edit', $outLetterViaUpload) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
            @endcan
            @if ($outLetterViaUpload->telegram_file_id)
                <a href="{{ route('out-letters-upload.download', $outLetterViaUpload) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
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

    <div class="border border-slate-200 bg-white p-5 text-sm">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            <div><p class="text-xs text-slate-500">Tanggal</p><p class="mt-1 font-medium">{{ $outLetterViaUpload->letter_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Penerima</p><p class="mt-1 text-slate-700">{{ $outLetterViaUpload->recipient ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Jenis</p><p class="mt-1 text-slate-700">{{ $outLetterViaUpload->letter_type ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">File</p><p class="mt-1 text-slate-700">{{ $outLetterViaUpload->original_name ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Diupload oleh</p><p class="mt-1 text-slate-700">{{ $outLetterViaUpload->uploadedBy?->full_name ?? '—' }}</p></div>
            @if ($outLetterViaUpload->notes)
                <div class="col-span-2 sm:col-span-3"><p class="text-xs text-slate-500">Catatan</p><p class="mt-1 text-slate-700">{{ $outLetterViaUpload->notes }}</p></div>
            @endif
        </div>
    </div>
</div>
@endsection
