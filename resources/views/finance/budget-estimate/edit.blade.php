@extends('layouts.dashboard')

@section('title', 'Edit Anggaran')
@section('header_title', 'Edit Anggaran')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('budget-estimates.show', $budgetEstimate) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Detail Anggaran
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Keuangan</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Anggaran</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui data dan periode estimasi anggaran untuk {{ $budgetEstimate->title }}.
        </p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px] shrink-0">error</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <form method="POST" action="{{ route('budget-estimates.update', $budgetEstimate) }}"
          class="rounded-2xl shadow-sm"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @csrf
        @method('PUT')
        <div class="px-6 py-4 rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Informasi Anggaran</h2>
        </div>

        <div class="p-6 space-y-5">
            {{-- Judul Anggaran --}}
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Judul Anggaran <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $budgetEstimate->title) }}" required maxlength="255"
                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
            </div>

            {{-- Periode --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Bulan Periode <span class="text-red-500">*</span>
                    </label>
                    <select name="period_month" required
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        @foreach (array_slice($months, 1, null, true) as $num => $label)
                            <option value="{{ $num }}" @selected(old('period_month', $budgetEstimate->period_month) == $num)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Tahun Periode <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="period_year" value="{{ old('period_year', $budgetEstimate->period_year) }}" required min="2020"
                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Deskripsi / Keperluan
                </label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('description', $budgetEstimate->description) }}</textarea>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="px-6 py-4 rounded-b-2xl flex items-center gap-3"
             style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Simpan Perubahan
            </button>
            <a href="{{ route('budget-estimates.show', $budgetEstimate) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[18px]">close</span>
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
