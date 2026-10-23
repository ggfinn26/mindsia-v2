@extends('layouts.dashboard')

@section('title', 'Detail Template Surat')
@section('header_title', 'Template Surat')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6 pb-10">
    {{-- Header Section Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('letter-templates.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span> Kembali ke Template Surat
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">
                {{ $letterTemplate->template_name }}
            </h1>
            <p class="mt-2 font-mono text-xs px-2.5 py-1 rounded-md inline-block"
               style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-faint);">
                {{ $letterTemplate->template_code }}
            </p>
        </div>

        <div class="flex gap-2 w-full sm:w-auto shrink-0">
            @can('letter.template.update')
                <a href="{{ route('letter-templates.edit', $letterTemplate) }}"
                   class="w-full sm:w-auto inline-flex justify-center items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">edit</span> Edit Template
                </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Generate Action Banner --}}
    @can('letter.generate.create')
    <div class="rounded-2xl p-5 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
         style="border: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
        <div>
            <h2 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Gunakan Template Ini</h2>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Buat dokumen surat baru dengan format dan placeholder otomatis dari template ini.</p>
        </div>
        <a href="{{ route('out-letters-generate.create') }}"
           class="w-full sm:w-auto inline-flex justify-center items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 whitespace-nowrap shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">draw</span> Buat Surat Baru
        </a>
    </div>
    @endcan

    {{-- Details Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 sm:px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Template</h2>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kategori</p>
                <p class="mt-1.5 text-sm font-medium" style="color: var(--dash-text-heading);">{{ $letterTemplate->letter_category ?? '-' }}</p>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Format Nomor</p>
                <div class="mt-1.5 max-w-full">
                    <p class="text-xs font-mono px-2 py-1 rounded inline-block break-all max-w-full"
                       style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        {{ $letterTemplate->letter_number_format ?? '-' }}
                    </p>
                </div>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</p>
                <span class="mt-1.5 inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $letterTemplate->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $letterTemplate->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Dibuat Oleh</p>
                <p class="mt-1.5 text-sm font-medium" style="color: var(--dash-text-heading);">{{ $letterTemplate->createdBy?->full_name ?? '-' }}</p>
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">File Template</p>
                @if ($letterTemplate->telegram_file_id)
                    <div class="mt-1.5 inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-100">
                        <span class="material-symbols-outlined text-[16px]">task</span>
                        Ada (Tersimpan)
                    </div>
                @else
                    <div class="mt-1.5 inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-md"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-faint);">
                        <span class="material-symbols-outlined text-[16px]">cancel</span>
                        Belum ada file
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Generated Letters Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 sm:px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Riwayat Penggunaan</h2>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                  style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                {{ $letterTemplate->generatedLetters->count() }} Surat
            </span>
        </div>

        <ul class="divide-y" style="border-color: var(--dash-card-border);">
            @forelse ($letterTemplate->generatedLetters as $letter)
                <li class="p-4 sm:px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors"
                    style="border-color: var(--dash-card-border);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate" style="color: var(--dash-text-heading);">
                            {{ $letter->subject ?? $letter->letter_type }}
                        </p>
                        <div class="mt-1 flex items-center gap-2 text-xs flex-wrap" style="color: var(--dash-text-muted);">
                            <span class="font-mono px-1.5 py-0.5 rounded"
                                  style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-faint);">
                                {{ $letter->letter_number ?? 'Draft' }}
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                {{ $letter->letter_date ? $letter->letter_date->format('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('out-letters-generate.show', $letter) }}"
                       class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors w-full sm:w-auto"
                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                        Lihat Detail
                    </a>
                </li>
            @empty
                <li class="px-5 py-12 text-center flex flex-col items-center">
                    <span class="material-symbols-outlined text-[48px] mb-3" style="color: var(--dash-text-faint);">history</span>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada surat yang dibuat.</p>
                    <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Surat yang dibuat dari template ini akan muncul di sini.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
