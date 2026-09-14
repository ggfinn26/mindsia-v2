@extends('layouts.dashboard')

@section('title', 'Detail Surat Keluar')
@section('header_title', 'Surat Keluar')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('out-letters-generate.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Surat Keluar
            </a>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $outLetterViaGenerate->subject ?? $outLetterViaGenerate->letter_type }}</h2>
            <div class="mt-1 flex items-center gap-3">
                <p class="font-mono text-sm text-slate-400">{{ $outLetterViaGenerate->letter_number ?? 'Draft' }}</p>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $outLetterViaGenerate->isDraft() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ $outLetterViaGenerate->isDraft() ? 'Draft' : 'Terbit' }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            @if ($outLetterViaGenerate->isDraft())
                @can('letter.generate.create')
                    <a href="{{ route('out-letters-generate.edit', $outLetterViaGenerate) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
                @endcan
                @can('letter.generate.publish')
                    <form method="POST" action="{{ route('out-letters-generate.publish', $outLetterViaGenerate) }}" onsubmit="return confirm('Terbitkan surat? Nomor surat akan dikunci.')">
                        @csrf
                        <button type="submit" class="inline-flex min-h-9 items-center bg-emerald-600 px-3 text-xs font-semibold text-white hover:bg-emerald-700">
                            <span class="material-symbols-outlined text-[16px] mr-1">publish</span> Terbitkan
                        </button>
                    </form>
                @endcan
            @endif
            @if ($outLetterViaGenerate->telegram_file_id)
                <a href="{{ route('out-letters-generate.download', $outLetterViaGenerate) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
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

    <div class="border border-slate-200 bg-white divide-y divide-slate-100 text-sm">
        <div class="grid grid-cols-2 gap-4 p-5 sm:grid-cols-3">
            <div><p class="text-xs text-slate-500">Template</p><p class="mt-1 font-medium text-slate-900">{{ $outLetterViaGenerate->template?->template_name ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Tanggal surat</p><p class="mt-1 text-slate-700">{{ $outLetterViaGenerate->letter_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Cabang</p><p class="mt-1 text-slate-700">{{ $outLetterViaGenerate->branch?->branch_name ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Penerima</p><p class="mt-1 text-slate-700">{{ $outLetterViaGenerate->recipient ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Penandatangan</p><p class="mt-1 text-slate-700">{{ $outLetterViaGenerate->signer_name_snapshot ?? $outLetterViaGenerate->signer?->full_name ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Dibuat oleh</p><p class="mt-1 text-slate-700">{{ $outLetterViaGenerate->createdBy?->full_name ?? '—' }}</p></div>
            @if (!$outLetterViaGenerate->isDraft())
                <div><p class="text-xs text-slate-500">Diterbitkan oleh</p><p class="mt-1 text-slate-700">{{ $outLetterViaGenerate->publishedBy?->full_name ?? '—' }}</p></div>
                <div><p class="text-xs text-slate-500">Tanggal terbit</p><p class="mt-1 text-slate-700">{{ $outLetterViaGenerate->published_at?->format('d M Y H:i') ?? '—' }}</p></div>
            @endif
            @if ($outLetterViaGenerate->notes)
                <div class="col-span-2 sm:col-span-3"><p class="text-xs text-slate-500">Catatan</p><p class="mt-1 text-slate-700">{{ $outLetterViaGenerate->notes }}</p></div>
            @endif
        </div>
    </div>
</div>
@endsection
