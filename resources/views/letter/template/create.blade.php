@extends('layouts.dashboard')

@section('title', 'Upload Template Surat')
@section('header_title', 'Upload Template Surat')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('letter-templates.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Template Surat
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Upload Template Baru</h2>
        <p class="mt-1 text-sm text-slate-500">Format file: .docx. Placeholder menggunakan alt text pada gambar/tabel di dokumen.</p>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('letter-templates.store') }}" enctype="multipart/form-data" class="border border-slate-200 bg-white p-6 space-y-4">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Kode template</label>
                <input type="text" name="template_code" value="{{ old('template_code') }}" required maxlength="100" placeholder="SK_KARYAWAN" class="block w-full border-slate-300 text-sm uppercase focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama template</label>
                <input type="text" name="template_name" value="{{ old('template_name') }}" required maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Kategori</label>
                <input type="text" name="letter_category" value="{{ old('letter_category') }}" maxlength="100" placeholder="SK, Kontrak, Pemberitahuan..." class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Format nomor surat</label>
                <input type="text" name="letter_number_format" value="{{ old('letter_number_format') }}" placeholder="{NO}/SK/{YYYY}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">File template (.docx)</label>
            <input type="file" name="file" accept=".docx" class="block w-full text-sm text-slate-600">
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', true)) class="border-slate-300 text-[#215aac] focus:ring-[#215aac]">
            <label for="is_active" class="text-sm font-medium text-slate-700">Aktif</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
            <a href="{{ route('letter-templates.index') }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</div>
@endsection
