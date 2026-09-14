@extends('layouts.dashboard')

@section('title', 'Upload Surat Keluar')
@section('header_title', 'Upload Surat Keluar')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('out-letters-upload.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Arsip Surat Keluar
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Upload Surat Keluar</h2>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('out-letters-upload.store') }}" enctype="multipart/form-data" class="border border-slate-200 bg-white p-6 space-y-4">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nomor surat</label>
                <input type="text" name="letter_number" value="{{ old('letter_number') }}" maxlength="100" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal surat</label>
                <input type="date" name="letter_date" value="{{ old('letter_date') }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Perihal</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Penerima</label>
                <input type="text" name="recipient" value="{{ old('recipient') }}" maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Jenis surat</label>
                <input type="text" name="letter_type" value="{{ old('letter_type') }}" maxlength="100" placeholder="SK, SP, Undangan..." class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">File surat <span class="text-red-500">*</span></label>
            <input type="file" name="file" required class="block w-full text-sm text-slate-600">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Catatan</label>
            <textarea name="notes" rows="2" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">{{ old('notes') }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Upload</button>
            <a href="{{ route('out-letters-upload.index') }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</div>
@endsection
