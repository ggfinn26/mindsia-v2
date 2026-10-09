@extends('layouts.dashboard')

@section('title', 'Edit Dokumen SOP')
@section('header_title', 'Edit Dokumen SOP')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('sop-documents.show', $sopDocument) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $sopDocument->title }}
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Dokumen SOP</h1>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sop-documents.update', $sopDocument) }}" enctype="multipart/form-data" class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 space-y-4">
        @csrf @method('PUT')
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Judul</label>
                <input type="text" name="title" value="{{ old('title', $sopDocument->title) }}" required maxlength="255" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Kode dokumen</label>
                <input type="text" name="document_code" value="{{ old('document_code', $sopDocument->document_code) }}" maxlength="100" class="block w-full border-[color:var(--dash-input-border)] text-sm uppercase focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Versi</label>
                <input type="text" name="version" value="{{ old('version', $sopDocument->version) }}" maxlength="20" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Kategori</label>
                <input type="text" name="category" value="{{ old('category', $sopDocument->category) }}" maxlength="100" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Tanggal efektif</label>
                <input type="date" name="effective_date" value="{{ old('effective_date', $sopDocument->effective_date?->toDateString()) }}" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Visible untuk posisi <span class="text-[color:var(--dash-text-faint)] font-normal text-xs">(kosong = semua posisi)</span></label>
            <div class="mt-1 grid grid-cols-2 gap-1.5 sm:grid-cols-3">
                @foreach ($roles as $role)
                    @php $checked = in_array($role, old('visible_to', $sopDocument->visible_to ?? [])); @endphp
                    <label class="flex items-center gap-2 text-sm text-[color:var(--dash-text-body)]">
                        <input type="checkbox" name="visible_to[]" value="{{ $role }}"
                            {{ $checked ? 'checked' : '' }}
                            class="rounded border-[color:var(--dash-input-border)] text-[color:var(--dash-primary-text,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                        {{ $role }}
                    </label>
                @endforeach
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Ganti file <span class="text-[color:var(--dash-text-faint)]">(opsional)</span></label>
            <input type="file" name="file" class="block w-full text-sm text-[color:var(--dash-text-body)]">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
            <a href="{{ route('sop-documents.show', $sopDocument) }}" class="inline-flex min-h-10 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Batal</a>
        </div>
    </form>
</div>
@endsection
