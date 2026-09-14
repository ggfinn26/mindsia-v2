@extends('layouts.dashboard')

@section('title', 'Buat Surat Keluar')
@section('header_title', 'Buat Surat Keluar')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('out-letters-generate.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Surat Keluar
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Buat Surat Keluar</h2>
        <p class="mt-1 text-sm text-slate-500">Pilih template lalu isi variabel manual. Surat dibuat sebagai draft.</p>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('out-letters-generate.store') }}" class="border border-slate-200 bg-white p-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Template surat</label>
            <select name="letter_template_id" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">— Pilih template —</option>
                @foreach (\App\Models\LetterTemplate::where('is_active', true)->get() as $tpl)
                    <option value="{{ $tpl->id }}" @selected(old('letter_template_id') == $tpl->id)>{{ $tpl->template_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Cabang</label>
            <select name="branch_id" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">— Pilih cabang —</option>
                @foreach (\App\Models\Branch::where('is_active', true)->get() as $branch)
                    <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Penandatangan <span class="text-slate-400">(opsional)</span></label>
            <select name="signer_employee_id" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">— Default dari pengaturan TTD —</option>
                @foreach (\App\Models\Employee::where('is_active', true)->get() as $emp)
                    <option value="{{ $emp->id }}" @selected(old('signer_employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Penerima</label>
                <input type="text" name="recipient" value="{{ old('recipient') }}" maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Perihal / Subjek</label>
                <input type="text" name="subject" value="{{ old('subject') }}" maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Catatan internal</label>
            <textarea name="notes" rows="2" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">{{ old('notes') }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Buat Surat (Draft)</button>
            <a href="{{ route('out-letters-generate.index') }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</div>
@endsection
