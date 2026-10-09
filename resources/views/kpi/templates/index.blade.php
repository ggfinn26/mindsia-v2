@extends('layouts.dashboard')

@section('title', 'Template KPI - MINDSIA')
@section('header_title', 'Template KPI')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">KPI</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Template KPI</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Kelola template evaluasi KPI per posisi. Template menentukan indikator dan bobot penilaian.
            </p>
        </div>
        @can('kpi.template.create')
            <a href="{{ route('kpi.templates.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Template
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="rounded-2xl px-5 py-4"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" action="{{ route('kpi.templates.index') }}"
              class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Posisi</label>
                <select name="position_id"
                        class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Semua posisi</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}" @selected(request('position_id') == $position->id)>
                            {{ $position->position_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Status</label>
                <select name="is_active"
                        class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Semua</option>
                    <option value="1" @selected(request('is_active') === '1')>Aktif</option>
                    <option value="0" @selected(request('is_active') === '0')>Nonaktif</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>Filter
                </button>
                @if(request()->hasAny(['position_id', 'is_active']))
                    <a href="{{ route('kpi.templates.index') }}"
                       class="inline-flex items-center gap-1 text-xs font-medium transition-opacity hover:opacity-75"
                       style="color: var(--dash-text-muted);">
                        <span class="material-symbols-outlined text-[14px]">close</span>Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[30%]">
                    <col class="w-[28%]">
                    <col class="w-[12%]">
                    <col class="w-[14%]">
                    <col class="w-[16%]">
                </colgroup>
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Template</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Posisi</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Indikator</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4">
                                <p class="font-semibold truncate" style="color: var(--dash-text-heading);">
                                    {{ $template->template_name }}
                                </p>
                                <p class="mt-0.5 font-mono text-xs" style="color: var(--dash-text-muted);">
                                    {{ $template->template_code }}
                                </p>
                            </td>
                            <td class="px-5 py-4 text-sm truncate" style="color: var(--dash-text-body);">
                                {{ $template->position?->position_name ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-right text-sm" style="color: var(--dash-text-body);">
                                {{ $template->indicators->count() }}
                            </td>
                            <td class="px-5 py-4">
                                @if($template->is_active)
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('kpi.templates.show', $template) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">assignment</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
                                    Belum ada template KPI.
                                </p>
                                @can('kpi.template.create')
                                    <a href="{{ route('kpi.templates.create') }}"
                                       class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                       style="background-color: var(--dash-primary);">
                                        <span class="material-symbols-outlined text-[16px]">add</span>Buat Template
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
            <div class="px-5 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $templates->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
