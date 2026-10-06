@extends('layouts.dashboard')

@section('header_title', 'Edit Template Notifikasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('notification-templates.show', $template) }}" class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Detail
        </a>
        <h1 class="mt-2 font-jakarta text-2xl font-bold tracking-tight text-slate-900">Edit Template Notifikasi</h1>
    </div>

    @if($errors->any())
    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <form action="{{ route('notification-templates.update', $template) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Template Key</label>
                <input type="text" value="{{ $template->template_key }}"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-slate-50 font-mono text-slate-500 cursor-not-allowed"
                    disabled />
                <input type="hidden" name="template_key" value="{{ $template->template_key }}" />
                <p class="mt-1 text-xs text-slate-400">Template key tidak dapat diubah setelah dibuat.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tipe / Channel</label>
                <select name="type" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" required>
                    <option value="in-app" {{ old('type', $template->type) === 'in-app' ? 'selected' : '' }}>In-App</option>
                    <option value="email" {{ old('type', $template->type) === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="telegram" {{ old('type', $template->type) === 'telegram' ? 'selected' : '' }}>Telegram</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Subject <span class="text-slate-400 text-xs font-normal">(wajib untuk Email)</span></label>
                <input name="subject" type="text" value="{{ old('subject', $template->subject) }}"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Body / Isi Pesan <span class="text-red-500">*</span></label>
                <textarea name="body" rows="8"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                    required>{{ old('body', $template->body) }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('notification-templates.show', $template) }}" class="rounded-lg border border-slate-200 px-5 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#215aac] px-5 py-2 text-sm font-bold text-white hover:bg-[#194a91] transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
