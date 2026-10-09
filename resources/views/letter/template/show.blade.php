@extends('layouts.dashboard')

@section('title', 'Detail Template Surat')
@section('header_title', 'Template Surat')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 sm:space-y-8 pb-10">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('letter-templates.index') }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-3 focus:outline-none focus:ring-2 focus:ring-[color:var(--dash-primary,#215aac)] rounded-sm focus:ring-offset-2">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke daftar
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight leading-tight" style="color: var(--dash-text-heading);">
                {{ $letterTemplate->template_name }}
            </h1>
            <p class="mt-1.5 font-mono text-[14px] text-slate-600 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-md inline-block">
                {{ $letterTemplate->template_code }}
            </p>
        </div>
        
        <div class="flex gap-3 w-full sm:w-auto">
            @can('letter.template.update')
                <a href="{{ route('letter-templates.edit', $letterTemplate) }}" 
                   class="w-full sm:w-auto inline-flex justify-center items-center min-h-[44px] border border-slate-300 bg-white px-5 rounded-lg text-[14px] font-bold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-colors">
                    <span class="material-symbols-outlined text-[18px] mr-2">edit</span> Edit Template
                </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="border border-emerald-200 bg-emerald-50 p-4 rounded-lg" role="alert" aria-live="polite">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px] text-emerald-600 mt-0.5">check_circle</span>
                <div>
                    <h3 class="text-[13px] font-bold text-emerald-800">Berhasil</h3>
                    <p class="mt-0.5 text-[13px] text-emerald-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Generate Action Banner -->
    @can('letter.generate.create')
    <div class="border border-[color:var(--dash-primary,#215aac)] bg-[#F4F7FB] p-5 sm:p-6 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 shadow-sm">
        <div>
            <h3 class="text-[15px] font-bold text-[#1a4687]">Gunakan Template Ini</h3>
            <p class="text-[13px] text-slate-600 mt-1">Buat dokumen surat baru dengan format dan placeholder otomatis dari template ini.</p>
        </div>
        <a href="{{ route('out-letters-generate.create') }}" 
           class="w-full sm:w-auto inline-flex justify-center min-h-[44px] items-center gap-2 bg-[var(--dash-primary,#215aac)] px-6 rounded-lg text-[14px] font-bold text-white hover:bg-[#194a91] focus:outline-none focus:ring-2 focus:ring-[color:var(--dash-primary,#215aac)] focus:ring-offset-2 transition-colors whitespace-nowrap">
            <span class="material-symbols-outlined text-[18px]">draw</span> Buat Surat Baru
        </a>
    </div>
    @endcan

    <!-- Details Card -->
    <div class="bg-white border border-slate-200 shadow-sm rounded-xl overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/50 px-5 sm:px-6 py-4">
            <h3 class="font-jakarta text-[15px] font-bold text-slate-800">Informasi Template</h3>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Kategori</p>
                <p class="mt-1.5 text-[14px] font-medium text-slate-900">{{ $letterTemplate->letter_category ?? '—' }}</p>
            </div>
            <div class="min-w-0">
                <p class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Format Nomor</p>
                <div class="mt-1.5 max-w-full">
                    <p class="text-[13px] font-mono text-slate-900 bg-slate-50 px-2 py-1 rounded inline-block border border-slate-100 break-all max-w-full">
                        {{ $letterTemplate->letter_number_format ?? '—' }}
                    </p>
                </div>
            </div>
            <div class="min-w-0">
                <p class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Status</p>
                <span class="mt-1.5 inline-flex rounded-full px-2.5 py-1 text-[12px] font-bold {{ $letterTemplate->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $letterTemplate->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <div>
                <p class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Dibuat Oleh</p>
                <p class="mt-1.5 text-[14px] font-medium text-slate-900">{{ $letterTemplate->createdBy?->full_name ?? '—' }}</p>
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <p class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">File Template</p>
                @if ($letterTemplate->telegram_file_id)
                    <div class="mt-1.5 flex items-center gap-1.5 text-[13px] font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md inline-flex border border-emerald-100">
                        <span class="material-symbols-outlined text-[16px]">task</span>
                        Ada (Tersimpan)
                    </div>
                @else
                    <div class="mt-1.5 flex items-center gap-1.5 text-[13px] font-medium text-slate-500 bg-slate-50 px-2.5 py-1 rounded-md inline-flex border border-slate-100">
                        <span class="material-symbols-outlined text-[16px]">cancel</span>
                        Belum ada file
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Generated Letters Card -->
    <div class="bg-white border border-slate-200 shadow-sm rounded-xl overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/50 px-5 sm:px-6 py-4 flex items-center justify-between">
            <h3 class="font-jakarta text-[15px] font-bold text-slate-800">Riwayat Penggunaan</h3>
            <span class="bg-slate-200 text-slate-700 text-[11px] font-bold px-2 py-0.5 rounded-full">
                {{ $letterTemplate->generatedLetters->count() }} Surat
            </span>
        </div>
        
        <ul class="divide-y divide-slate-100">
            @forelse ($letterTemplate->generatedLetters as $letter)
                <li class="p-5 sm:px-6 hover:bg-slate-50 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-[14px] font-bold text-slate-900 truncate">
                            {{ $letter->subject ?? $letter->letter_type }}
                        </p>
                        <div class="mt-1.5 flex items-center gap-2 text-[13px] text-slate-500 flex-wrap">
                            <span class="font-mono text-[12px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-600 border border-slate-200">
                                {{ $letter->letter_number ?? 'Draft' }}
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                {{ $letter->letter_date ? $letter->letter_date->format('d M Y') : '—' }}
                            </span>
                        </div>
                    </div>
                    
                    <a href="{{ route('out-letters-generate.show', $letter) }}" 
                       class="inline-flex min-h-[44px] sm:min-h-[36px] items-center justify-center border border-slate-200 bg-white px-4 rounded-lg text-[13px] font-bold text-[color:var(--dash-primary-text,#215aac)] hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-[color:var(--dash-primary,#215aac)] transition-colors w-full sm:w-auto mt-2 sm:mt-0">
                        Lihat Detail
                    </a>
                </li>
            @empty
                <li class="px-5 py-12 text-center flex flex-col items-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-3">history</span>
                    <p class="text-[14px] font-medium text-slate-600">Belum ada surat yang dibuat.</p>
                    <p class="text-[13px] text-slate-400 mt-1">Surat yang di-generate dari template ini akan muncul di sini.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
