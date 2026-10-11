@extends('layouts.dashboard')

@section('title', 'Perpanjang Kontrak - ' . $status->employee->full_name)
@section('header_title', 'Perpanjang Kontrak')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('employment-statuses.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Kelola Kontrak
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">SDM & Karyawan</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Perpanjang Kontrak Kerja</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Pegawai: <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->full_name }}</span> ({{ $status->position?->position_name ?? 'Belum diatur' }})
        </p>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <div class="flex items-center gap-2 font-semibold mb-1">
            <span class="material-symbols-outlined text-[18px]">error</span>
            Ada kesalahan:
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Info kontrak saat ini --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Kontrak Saat Ini</h2>
        </div>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-5 px-6 py-5 sm:grid-cols-4">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Nama Pegawai</dt>
                <dd class="mt-1 text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->full_name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Posisi</dt>
                <dd class="mt-1 text-sm" style="color: var(--dash-text-body);">{{ $status->position?->position_name ?? 'Belum diatur' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Mulai Kontrak</dt>
                <dd class="mt-1 text-sm" style="color: var(--dash-text-body);">{{ $status->contract_start_date?->format('d M Y') ?? 'Belum diatur' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Akhir Kontrak</dt>
                <dd class="mt-1 text-sm font-semibold {{ $status->contract_end_date?->lte(now()->addDays(30)) ? 'text-amber-700' : '' }}"
                    style="{{ $status->contract_end_date?->lte(now()->addDays(30)) ? '' : 'color: var(--dash-text-heading);' }}">
                    {{ $status->contract_end_date?->format('d M Y') ?? 'Tidak ada batas' }}
                </dd>
            </div>
        </dl>
    </div>

    {{-- Form perpanjangan --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Formulir Perpanjangan</h2>
        </div>
        <form action="{{ route('employment-statuses.extend.store', $status) }}" method="POST" class="px-6 py-6 space-y-6" novalidate>
            @csrf
            <div>
                <label for="new_end_date" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Tanggal Akhir Kontrak Baru <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       id="new_end_date"
                       name="new_end_date"
                       value="{{ old('new_end_date') }}"
                       required
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                @error('new_end_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end items-center gap-3 pt-3" style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('employment-statuses.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                    Perpanjang Kontrak
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
