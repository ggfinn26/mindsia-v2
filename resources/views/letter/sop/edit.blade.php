@extends('layouts.dashboard')

@section('title', 'Edit Dokumen SOP')
@section('header_title', 'Edit Dokumen SOP')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('sop-documents.show', $sopDocument) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $sopDocument->title }}
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Edit Dokumen SOP</h2>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sop-documents.update', $sopDocument) }}" enctype="multipart/form-data" class="border border-slate-200 bg-white p-6 space-y-4">
        @csrf @method('PUT')
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Judul</label>
                <input type="text" name="title" value="{{ old('title', $sopDocument->title) }}" required maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Kode dokumen</label>
                <input type="text" name="document_code" value="{{ old('document_code', $sopDocument->document_code) }}" maxlength="100" class="block w-full border-slate-300 text-sm uppercase focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Versi</label>
                <input type="text" name="version" value="{{ old('version', $sopDocument->version) }}" maxlength="20" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Kategori</label>
                <input type="text" name="category" value="{{ old('category', $sopDocument->category) }}" maxlength="100" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal efektif</label>
                <input type="date" name="effective_date" value="{{ old('effective_date', $sopDocument->effective_date?->toDateString()) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Ganti file <span class="text-slate-400">(opsional)</span></label>
            <input type="file" name="file" class="block w-full text-sm text-slate-600">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
            <a href="{{ route('sop-documents.show', $sopDocument) }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</div>
@endsection
