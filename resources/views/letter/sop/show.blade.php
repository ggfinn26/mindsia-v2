@extends('layouts.dashboard')

@section('title', 'Detail Dokumen SOP')
@section('header_title', 'Dokumen SOP')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6 pb-10" x-data="{ confirmDeactivate: false }">
    {{-- Header Section Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('sop-documents.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span> Kembali ke Dokumen SOP
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">
                {{ $sopDocument->title }}
            </h1>
            <div class="mt-2 flex items-center gap-3">
                <p class="font-mono text-xs px-2.5 py-1 rounded-md"
                   style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-faint);">
                    {{ $sopDocument->document_code ?? '-' }} v{{ $sopDocument->version }}
                </p>
                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sopDocument->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $sopDocument->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 shrink-0">
            @can('letter.sop.update')
                <a href="{{ route('sop-documents.edit', $sopDocument) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                </a>
                @if ($sopDocument->is_active)
                    <button type="button" @click="confirmDeactivate = true"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
                            style="border: 1px solid #fcd34d; background-color: #fef3c7; color: #92400e;">
                        <span class="material-symbols-outlined text-[18px]">toggle_off</span> Nonaktifkan
                    </button>
                @endif
            @endcan
            @if ($sopDocument->telegram_file_id)
                <a href="{{ route('sop-documents.download', $sopDocument) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">download</span> Download
                </a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Details Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 sm:px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Dokumen SOP</h2>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-2 gap-5 sm:grid-cols-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kategori</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-heading);">{{ $sopDocument->category ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Efektif</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $sopDocument->effective_date?->format('d M Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Diupload Oleh</p>
                <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $sopDocument->uploadedBy?->full_name ?? '-' }}</p>
            </div>
            @if ($sopDocument->visible_to)
                <div class="col-span-2 sm:col-span-3">
                    <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Visible Untuk Role</p>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @foreach ($sopDocument->visible_to as $targetRole)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                {{ $targetRole }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="col-span-2 sm:col-span-3">
                    <p class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Akses Dokumen</p>
                    <p class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">Semua role dapat mengakses dokumen ini</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Konfirmasi Nonaktifkan --}}
    @can('letter.sop.update')
        @if ($sopDocument->is_active)
            <div x-show="confirmDeactivate" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                 @keydown.escape.window="confirmDeactivate = false">
                <div class="w-full max-w-md rounded-2xl"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                     @click.outside="confirmDeactivate = false">
                    <div class="flex items-center gap-3 px-6 py-4 rounded-t-2xl"
                         style="border-bottom: 1px solid var(--dash-card-border);">
                        <span class="material-symbols-outlined text-[22px]" style="color: #d97706;">warning</span>
                        <h2 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Nonaktifkan Dokumen SOP?</h2>
                    </div>
                    <div class="px-6 py-4 text-sm" style="color: var(--dash-text-body);">
                        Dokumen SOP ini tidak akan ditampilkan lagi ke daftar dokumen aktif bagi karyawan. Anda yakin ingin melanjutkan?
                    </div>
                    <form method="POST" action="{{ route('sop-documents.deactivate', $sopDocument) }}"
                          class="flex justify-end gap-2 px-6 py-4 rounded-b-2xl"
                          style="border-top: 1px solid var(--dash-card-border);">
                        @csrf
                        <button type="button" @click="confirmDeactivate = false"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            <span class="material-symbols-outlined text-[18px]">close</span> Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-danger);">
                            <span class="material-symbols-outlined text-[18px]">toggle_off</span> Nonaktifkan
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @endcan
</div>
@endsection
