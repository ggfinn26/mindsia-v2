@extends('layouts.dashboard')

@section('title', 'Detail Template Surat')
@section('header_title', 'Template Surat')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('letter-templates.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Template Surat
            </a>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $letterTemplate->template_name }}</h2>
            <p class="mt-0.5 font-mono text-sm text-slate-400">{{ $letterTemplate->template_code }}</p>
        </div>
        <div class="flex gap-2">
            @can('letter.template.update')
                <a href="{{ route('letter-templates.edit', $letterTemplate) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="border border-slate-200 bg-white divide-y divide-slate-100 text-sm">
        <div class="grid grid-cols-2 gap-4 p-5 sm:grid-cols-3">
            <div><p class="text-xs text-slate-500">Kategori</p><p class="mt-1 font-medium text-slate-900">{{ $letterTemplate->letter_category ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Format nomor</p><p class="mt-1 font-mono text-slate-900">{{ $letterTemplate->letter_number_format ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Status</p>
                <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $letterTemplate->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $letterTemplate->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <div><p class="text-xs text-slate-500">Dibuat oleh</p><p class="mt-1 text-slate-700">{{ $letterTemplate->createdBy?->full_name ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">File template</p>
                @if ($letterTemplate->telegram_file_id)
                    <p class="mt-1 text-emerald-700 text-xs">Ada (disimpan di Telegram)</p>
                @else
                    <p class="mt-1 text-slate-400 text-xs">Belum ada file</p>
                @endif
            </div>
        </div>
    </div>

    @can('letter.generate.create')
    <div class="border border-[#215aac] bg-blue-50 px-5 py-4 flex items-center justify-between">
        <p class="text-sm font-semibold text-[#215aac]">Gunakan template ini untuk membuat surat</p>
        <a href="{{ route('out-letters-generate.create') }}" class="inline-flex min-h-9 items-center gap-2 bg-[#215aac] px-4 text-xs font-semibold text-white hover:bg-[#194a91]">
            <span class="material-symbols-outlined text-[16px]">draw</span> Buat Surat
        </a>
    </div>
    @endcan

    <div class="border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Surat yang pernah dibuat dari template ini</h3>
        </div>
        @forelse ($letterTemplate->generatedLetters as $letter)
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3 last:border-0 text-sm">
                <div>
                    <p class="font-medium text-slate-900">{{ $letter->subject ?? $letter->letter_type }}</p>
                    <p class="text-xs text-slate-400">{{ $letter->letter_number ?? 'Draft' }} · {{ $letter->letter_date?->format('d M Y') }}</p>
                </div>
                <a href="{{ route('out-letters-generate.show', $letter) }}" class="text-xs font-semibold text-[#215aac] hover:underline">Lihat</a>
            </div>
        @empty
            <p class="px-5 py-4 text-sm text-slate-400">Belum ada surat yang dibuat dari template ini.</p>
        @endforelse
    </div>
</div>
@endsection
