@extends('layouts.dashboard')

@section('title', 'Upload Dokumen SOP')
@section('header_title', 'Upload Dokumen SOP')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('sop-documents.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Dokumen SOP
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Upload Dokumen SOP</h1>
        <p class="mt-1 text-sm text-slate-500">Dokumen langsung aktif setelah diupload. <code class="text-xs bg-slate-100 px-1">visible_to</code> kosong = semua role bisa lihat.</p>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sop-documents.store') }}" enctype="multipart/form-data" class="border border-slate-200 bg-white p-6 space-y-4">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Judul dokumen</label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Kode dokumen</label>
                <input type="text" name="document_code" value="{{ old('document_code') }}" maxlength="100" placeholder="SOP-HR-001" class="block w-full border-slate-300 text-sm uppercase focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Versi</label>
                <input type="text" name="version" value="{{ old('version', '1.0') }}" maxlength="20" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Kategori</label>
                <input type="text" name="category" value="{{ old('category') }}" maxlength="100" placeholder="HR, Operasional, Keuangan..." class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal efektif</label>
                <input type="date" name="effective_date" value="{{ old('effective_date', today()->toDateString()) }}" class="block w-full border-slate-300 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            </div>
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Visible untuk posisi <span class="text-slate-400 font-normal text-xs">(kosong = semua posisi)</span></label>
            <div class="mt-1 grid grid-cols-2 gap-1.5 sm:grid-cols-3">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="visible_to[]" value="{{ $role }}"
                            {{ in_array($role, old('visible_to', [])) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-[color:var(--dash-primary-text,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                        {{ $role }}
                    </label>
                @endforeach
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">File SOP <span class="text-red-500">*</span></label>
            <input type="file" name="file" required class="block w-full text-sm text-slate-600">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Upload</button>
            <a href="{{ route('sop-documents.index') }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</div>
@endsection
