@extends('layouts.dashboard')

@section('title', 'Buat Template KPI - MINDSIA')
@section('header_title', 'Buat Template KPI')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-2xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('kpi.templates.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Buat Template Baru</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">KPI → Template</p>
        </div>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl p-6 space-y-5"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="POST" action="{{ route('kpi.templates.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Kode Template <span style="color:#dc2626;">*</span>
                </label>
                <input type="text" name="template_code" value="{{ old('template_code') }}"
                       required maxlength="100" placeholder="KPI_TUTOR_2026"
                       class="block w-full rounded-lg px-3 py-2 text-sm uppercase focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Nama Template <span style="color:#dc2626;">*</span>
                </label>
                <input type="text" name="template_name" value="{{ old('template_name') }}"
                       required maxlength="255"
                       class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Posisi <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                </label>
                <select name="position_id"
                        class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Pilih posisi</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}" @selected(old('position_id') == $position->id)>
                            {{ $position->position_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">{{ old('description') }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" id="is_active"
                       @checked(old('is_active', true)) class="h-4 w-4 rounded">
                <label for="is_active" class="text-sm font-medium" style="color: var(--dash-text-body);">Aktif</label>
            </div>
            <div class="flex items-center gap-3 pt-2" style="border-top: 1px solid var(--dash-card-border);">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>Simpan Template
                </button>
                <a href="{{ route('kpi.templates.index') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
