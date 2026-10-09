@extends('layouts.dashboard')

@section('title', 'Upload Surat Keluar')
@section('header_title', 'Upload Surat Keluar')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('out-letters-upload.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Arsip Surat Keluar
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Upload Surat Keluar</h1>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('out-letters-upload.store') }}" enctype="multipart/form-data" class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 space-y-4">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Cabang <span class="text-red-500">*</span></label>
                <select name="branch_id" required class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    <option value="">— Pilih cabang —</option>
                    @foreach (App\Models\Branch::where('is_active', true)->orderBy('branch_name')->get() as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Nomor surat</label>
                <input type="text" name="letter_number" value="{{ old('letter_number') }}" maxlength="100" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Tanggal surat</label>
                <input type="date" name="letter_date" value="{{ old('letter_date') }}" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Perihal</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Penerima</label>
                <input type="text" name="recipient" value="{{ old('recipient') }}" maxlength="255" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Jenis surat</label>
                <input type="text" name="letter_type" value="{{ old('letter_type') }}" maxlength="100" placeholder="SK, SP, Undangan..." class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">File surat <span class="text-red-500">*</span></label>
            <input type="file" name="file" required class="block w-full text-sm text-[color:var(--dash-text-body)]">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Catatan</label>
            <textarea name="notes" rows="2" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">{{ old('notes') }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Upload</button>
            <a href="{{ route('out-letters-upload.index') }}" class="inline-flex min-h-10 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Batal</a>
        </div>
    </form>
</div>
@endsection
