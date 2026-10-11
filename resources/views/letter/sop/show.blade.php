@extends('layouts.dashboard')

@section('title', 'Detail Dokumen SOP')
@section('header_title', 'Dokumen SOP')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('sop-documents.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Dokumen SOP
            </a>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $sopDocument->title }}</h2>
            <div class="mt-1 flex items-center gap-3">
                <p class="font-mono text-sm text-slate-400">{{ $sopDocument->document_code ?? '—' }} v{{ $sopDocument->version }}</p>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $sopDocument->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $sopDocument->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            @can('letter.sop.update')
                <a href="{{ route('sop-documents.edit', $sopDocument) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
                @if ($sopDocument->is_active)
                    <form method="POST" action="{{ route('sop-documents.deactivate', $sopDocument) }}">
                        @csrf
                        <button type="submit" class="inline-flex min-h-9 items-center border border-amber-300 px-3 text-xs font-semibold text-amber-800 hover:bg-amber-50">Nonaktifkan</button>
                    </form>
                @endif
            @endcan
            @if ($sopDocument->telegram_file_id)
                <a href="{{ route('sop-documents.download', $sopDocument) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
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
            <div><p class="text-xs text-slate-500">Kategori</p><p class="mt-1 font-medium">{{ $sopDocument->category ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Tanggal efektif</p><p class="mt-1 text-slate-700">{{ $sopDocument->effective_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Diupload oleh</p><p class="mt-1 text-slate-700">{{ $sopDocument->uploadedBy?->full_name ?? '—' }}</p></div>
            @if ($sopDocument->visible_to)
                <div class="col-span-2 sm:col-span-3">
                    <p class="text-xs text-slate-500">Visible untuk role</p>
                    <p class="mt-1 text-slate-700">{{ implode(', ', $sopDocument->visible_to) }}</p>
                </div>
            @else
                <div><p class="text-xs text-slate-500">Visible untuk</p><p class="mt-1 text-slate-700">Semua role</p></div>
            @endif
        </div>
    </div>
</div>
@endsection
