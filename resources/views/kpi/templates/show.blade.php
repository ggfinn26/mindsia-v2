@extends('layouts.dashboard')

@section('title', 'Detail Template KPI - MINDSIA')
@section('header_title', 'Template KPI')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('kpi.templates.index') }}"
               class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                        style="color: var(--dash-text-heading);">{{ $template->template_name }}</h1>
                    @if($template->is_active)
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                              style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                    @else
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Nonaktif</span>
                    @endif
                </div>
                <p class="mt-0.5 font-mono text-sm" style="color: var(--dash-text-muted);">{{ $template->template_code }}</p>
            </div>
        </div>
        @can('kpi.template.update')
            <a href="{{ route('kpi.templates.edit', $template) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors shrink-0"
               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[14px]">edit</span>Edit
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
    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Info card --}}
    <div class="rounded-2xl p-5"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Posisi</p>
                <p class="font-medium" style="color: var(--dash-text-heading);">
                    {{ $template->position?->position_name ?? 'Semua posisi' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Dibuat oleh</p>
                <p class="font-medium" style="color: var(--dash-text-heading);">
                    {{ $template->createdBy?->full_name ?? '—' }}
                </p>
            </div>
            @if($template->description)
                <div class="col-span-2 sm:col-span-3">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Deskripsi</p>
                    <p style="color: var(--dash-text-body);">{{ $template->description }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Indikator --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between px-5 py-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div>
                <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Indikator KPI</h3>
                <p class="mt-0.5 text-xs" style="color: var(--dash-text-muted);">
                    Total bobot harus 100%. Indikator dengan data source diisi otomatis saat evaluasi dibuat.
                </p>
            </div>
            @can('kpi.template.update')
                <a href="{{ route('kpi.templates.indicators.create', $template) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[14px]">add</span>Tambah
                </a>
            @endcan
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[5%]">
                    <col class="w-[22%]">
                    <col class="w-[10%]">
                    <col class="w-[10%]">
                    <col class="w-[9%]">
                    <col class="w-[15%]">
                    <col class="w-[12%]">
                    <col class="w-[17%]">
                </colgroup>
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Indikator</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Satuan</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Target</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Bobot</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Sumber Data</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalWeight = 0; @endphp
                    @forelse($template->indicators as $indicator)
                        @php if($indicator->is_active) $totalWeight += $indicator->weight; @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border); {{ !$indicator->is_active ? 'opacity: 0.5;' : '' }}"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-3 text-sm" style="color: var(--dash-text-muted);">
                                {{ $indicator->sequence_number }}
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium truncate" style="color: var(--dash-text-heading);">
                                    {{ $indicator->indicator_name }}
                                </p>
                                <p class="font-mono text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                    {{ $indicator->indicator_code }}
                                </p>
                            </td>
                            <td class="px-5 py-3 text-sm" style="color: var(--dash-text-body);">
                                {{ $indicator->unit ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-right text-sm" style="color: var(--dash-text-body);">
                                {{ $indicator->target_value }}
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-sm" style="color: var(--dash-text-heading);">
                                {{ $indicator->weight }}%
                            </td>
                            <td class="px-5 py-3">
                                @if($indicator->data_source_type)
                                    <span class="inline-flex rounded-full px-2 py-0.5 font-mono text-xs"
                                          style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                                        {{ $indicator->data_source_type }}
                                    </span>
                                @else
                                    <span class="text-xs" style="color: var(--dash-text-muted);">Manual</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if($indicator->is_active)
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                          style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-right">
                                @can('kpi.template.update')
                                    <div class="inline-flex gap-1.5">
                                        <a href="{{ route('kpi.templates.indicators.edit', [$template, $indicator]) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                           style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[13px]">edit</span>
                                        </a>
                                        <form method="POST"
                                              action="{{ route('kpi.templates.indicators.destroy', [$template, $indicator]) }}"
                                              onsubmit="return confirm('Hapus indikator ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg transition-colors"
                                                    style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                                                    onmouseover="this.style.backgroundColor='#fecaca'"
                                                    onmouseout="this.style.backgroundColor='#fee2e2'">
                                                <span class="material-symbols-outlined text-[13px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-sm" style="color: var(--dash-text-faint);">
                                Belum ada indikator. Tambah indikator di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($template->indicators->count())
                    <tfoot>
                        <tr style="border-top: 2px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <td colspan="4" class="px-5 py-3 text-right text-xs font-semibold"
                                style="color: var(--dash-text-muted);">Total bobot:</td>
                            <td class="px-5 py-3 text-right text-sm font-bold"
                                style="color: {{ abs($totalWeight - 100) < 0.01 ? '#059669' : '#dc2626' }};">
                                {{ $totalWeight }}%
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Dokumen --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between px-5 py-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Dokumen Template</h3>
            @can('kpi.document.create')
                <form method="POST" action="{{ route('kpi.documents.store') }}" enctype="multipart/form-data"
                      class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="kpi_template_id" value="{{ $template->id }}">
                    <input type="hidden" name="document_type" value="template">
                    <input type="text" name="title" placeholder="Judul dokumen" required
                           class="rounded-lg px-3 py-1.5 text-xs focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                           style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[13px]">attach_file</span>
                        <span id="doc-file-name">Pilih file</span>
                        <input type="file" name="file" required class="hidden"
                               onchange="document.getElementById('doc-file-name').textContent = this.files[0]?.name || 'Pilih file'">
                    </label>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[13px]">upload</span>Upload
                    </button>
                </form>
            @endcan
        </div>
        @forelse($template->documents as $doc)
            <div class="flex items-center justify-between px-5 py-3"
                 style="border-bottom: 1px solid var(--dash-card-border);">
                <div>
                    <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $doc->title }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                        {{ $doc->original_name }} · {{ $doc->uploadedAt?->format('d M Y') }}
                    </p>
                </div>
                @can('kpi.document.create')
                <form method="POST" action="{{ route('kpi.documents.destroy', $doc) }}"
                      onsubmit="return confirm('Hapus dokumen?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg transition-colors"
                            style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                            onmouseover="this.style.backgroundColor='#fecaca'"
                            onmouseout="this.style.backgroundColor='#fee2e2'">
                        <span class="material-symbols-outlined text-[13px]">delete</span>
                    </button>
                </form>
                @endcan
            </div>
        @empty
            <p class="px-5 py-6 text-sm" style="color: var(--dash-text-faint);">Belum ada dokumen.</p>
        @endforelse
    </div>

</div>
@endsection
