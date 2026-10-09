@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Detail Materi Pembelajaran</h1>
        <a href="{{ route('classes.learning-material.index') }}" class="btn bg-[var(--dash-card-bg)] border-[color:var(--dash-card-border)] hover:border-[color:var(--dash-input-border)] text-[color:var(--dash-text-body)]">Kembali</a>
    </div>

    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Judul Materi</h3>
            <p class="text-[color:var(--dash-text-heading)] font-medium text-lg">-</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Program / Kelas</h3>
                <p class="text-[color:var(--dash-text-heading)] font-medium">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Diunggah Oleh</h3>
                <p class="text-[color:var(--dash-text-heading)] font-medium">-</p>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Deskripsi</h3>
            <div class="text-[color:var(--dash-text-heading)] p-4 bg-[var(--dash-nav-hover-bg)] rounded-md border border-[color:var(--dash-card-border)] min-h-[100px] whitespace-pre-wrap">-</div>
        </div>

        <div class="mt-6 pt-4 border-t border-[color:var(--dash-card-border)] flex justify-end">
            <a href="#" class="btn bg-indigo-500 hover:bg-indigo-600 text-white flex items-center">
                <span class="material-symbols-outlined mr-2">download</span> Download / Buka File
            </a>
        </div>
    </div>
</div>
@endsection
