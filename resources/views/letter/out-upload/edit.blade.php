@extends('layouts.dashboard')

@section('title', 'Edit Surat Keluar')
@section('header_title', 'Edit Surat Keluar')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('out-letters-upload.show', $outLetterViaUpload) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $outLetterViaUpload->subject }}
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Surat Keluar</h1>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('out-letters-upload.update', $outLetterViaUpload) }}" enctype="multipart/form-data" class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 space-y-4">
        @csrf @method('PUT')
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Nomor surat</label>
                <input type="text" name="letter_number" value="{{ old('letter_number', $outLetterViaUpload->letter_number) }}" maxlength="100" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Tanggal</label>
                <input type="date" name="letter_date" value="{{ old('letter_date', $outLetterViaUpload->letter_date?->toDateString()) }}" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Perihal</label>
                <input type="text" name="subject" value="{{ old('subject', $outLetterViaUpload->subject) }}" required maxlength="255" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Penerima</label>
                <input type="text" name="recipient" value="{{ old('recipient', $outLetterViaUpload->recipient) }}" maxlength="255" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Ganti file <span class="text-[color:var(--dash-text-faint)]">(opsional)</span></label>
            <input type="file" name="file" class="block w-full text-sm text-[color:var(--dash-text-body)]">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
            <a href="{{ route('out-letters-upload.show', $outLetterViaUpload) }}" class="inline-flex min-h-10 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Batal</a>
        </div>
    </form>
</div>
@endsection
