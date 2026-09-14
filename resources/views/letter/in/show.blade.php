@extends('layouts.dashboard')

@section('title', 'Detail Surat Masuk')
@section('header_title', 'Surat Masuk')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('in-letters.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Surat Masuk
            </a>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $inLetter->subject }}</h2>
            <p class="font-mono text-sm text-slate-400">{{ $inLetter->letter_number ?? '—' }}</p>
        </div>
        <div class="flex gap-2">
            @can('letter.in.update')
                <a href="{{ route('in-letters.edit', $inLetter) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
            @endcan
            @if ($inLetter->telegram_file_id)
                <a href="{{ route('in-letters.download', $inLetter) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
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
            <div><p class="text-xs text-slate-500">Pengirim</p><p class="mt-1 font-semibold text-slate-900">{{ $inLetter->sender_name }}</p></div>
            <div><p class="text-xs text-slate-500">Tanggal surat</p><p class="mt-1 text-slate-700">{{ $inLetter->letter_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Tanggal diterima</p><p class="mt-1 text-slate-700">{{ $inLetter->receive_date?->format('d M Y') }}</p></div>
            <div><p class="text-xs text-slate-500">PIC</p><p class="mt-1 text-slate-700">{{ $inLetter->pic?->full_name ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">File</p><p class="mt-1 text-slate-700">{{ $inLetter->original_name ?? ($inLetter->telegram_file_id ? 'Ada' : '—') }}</p></div>
            @if ($inLetter->notes)
                <div class="col-span-2 sm:col-span-3"><p class="text-xs text-slate-500">Catatan</p><p class="mt-1 text-slate-700">{{ $inLetter->notes }}</p></div>
            @endif
        </div>
    </div>
</div>
@endsection
