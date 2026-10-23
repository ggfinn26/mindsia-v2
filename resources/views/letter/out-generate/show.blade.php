@extends('layouts.dashboard')

@section('title', 'Detail Surat Keluar')
@section('header_title', 'Surat Keluar')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6" x-data="{ confirmPublish: false }">
    {{-- Header Section Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('out-letters-generate.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span> Kembali ke Surat Keluar
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat Keluar</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">
                {{ $outLetterViaGenerate->subject ?? $outLetterViaGenerate->letter_type }}
            </h1>
            <div class="mt-2 flex items-center gap-3">
                <p class="font-mono text-xs px-2.5 py-1 rounded-md"
                   style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-faint);">
                    {{ $outLetterViaGenerate->letter_number ?? 'Draft' }}
                </p>
                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $outLetterViaGenerate->isDraft() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ $outLetterViaGenerate->isDraft() ? 'Draft' : 'Terbit' }}
                </span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            @if ($outLetterViaGenerate->isDraft())
                @can('letter.generate.create')
                    <a href="{{ route('out-letters-generate.edit', $outLetterViaGenerate) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                    </a>
                @endcan
                @can('letter.generate.publish')
                    <button type="button" @click="confirmPublish = true"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">publish</span> Terbitkan
                    </button>
                @endcan
            @endif
            @if ($outLetterViaGenerate->telegram_file_id)
                <a href="{{ route('out-letters-generate.download', $outLetterViaGenerate) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
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

    <div class="rounded-2xl p-5 sm:p-6 text-sm" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="grid grid-cols-2 gap-5 sm:grid-cols-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Template</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-heading);">{{ $outLetterViaGenerate->template?->template_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Surat</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $outLetterViaGenerate->letter_date?->format('d M Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Cabang</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $outLetterViaGenerate->branch?->branch_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Penerima</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $outLetterViaGenerate->recipient ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Penandatangan</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $outLetterViaGenerate->signer_name_snapshot ?? $outLetterViaGenerate->signer?->full_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Dibuat Oleh</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $outLetterViaGenerate->createdBy?->full_name ?? '-' }}</p>
            </div>
            @if (!$outLetterViaGenerate->isDraft())
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Diterbitkan Oleh</p>
                    <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $outLetterViaGenerate->publishedBy?->full_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Terbit</p>
                    <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $outLetterViaGenerate->published_at?->format('d M Y H:i') ?? '-' }}</p>
                </div>
            @endif
            @if ($outLetterViaGenerate->notes)
                <div class="col-span-2 sm:col-span-3">
                    <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Catatan</p>
                    <p class="mt-1 text-sm" style="color: var(--dash-text-body);">{{ $outLetterViaGenerate->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    @if ($outLetterViaGenerate->isDraft())
        @can('letter.generate.publish')
            {{-- Modal Konfirmasi Terbitkan (Card TANPA overflow-hidden) --}}
            <div x-show="confirmPublish" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                 @keydown.escape.window="confirmPublish = false">
                <div class="w-full max-w-md rounded-2xl"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                     @click.outside="confirmPublish = false">
                    <div class="flex items-center gap-3 px-6 py-4 rounded-t-2xl"
                         style="border-bottom: 1px solid var(--dash-card-border);">
                        <span class="material-symbols-outlined text-[22px]" style="color: var(--dash-primary);">publish</span>
                        <h2 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Terbitkan surat?</h2>
                    </div>
                    <div class="px-6 py-4 text-sm" style="color: var(--dash-text-body);">
                        Nomor surat akan dikunci secara permanen dan dokumen tidak dapat diedit lagi setelah diterbitkan.
                    </div>
                    <form method="POST" action="{{ route('out-letters-generate.publish', $outLetterViaGenerate) }}"
                          class="flex justify-end gap-2 px-6 py-4 rounded-b-2xl"
                          style="border-top: 1px solid var(--dash-card-border);">
                        @csrf
                        <button type="button" @click="confirmPublish = false"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            <span class="material-symbols-outlined text-[18px]">close</span> Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[18px]">publish</span> Terbitkan
                        </button>
                    </form>
                </div>
            </div>
        @endcan
    @endif
</div>
@endsection
