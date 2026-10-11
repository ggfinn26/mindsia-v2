@extends('layouts.dashboard')

@section('title', 'Detail Template KPI')
@section('header_title', 'Template KPI')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{ showDeleteIndicator: false, deleteIndicatorId: null, deleteIndicatorName: '', deleteDocId: null, deleteDocName: '', showDeleteDoc: false }">

    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('kpi.templates.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Template
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">KPI</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $template->template_name }}</h1>
            <div class="mt-2 flex items-center gap-3">
                <p class="font-mono text-sm" style="color: var(--dash-text-faint);">{{ $template->template_code }}</p>
                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold {{ $template->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>
        @can('kpi.template.update')
            <a href="{{ route('kpi.templates.edit', $template) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors shrink-0"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[14px]">edit</span>Edit
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Info --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Informasi Template</h2>
        </div>
        <div class="grid grid-cols-2 gap-4 px-6 py-5 text-sm sm:grid-cols-3">
            <div>
                <p class="text-xs mb-1" style="color: var(--dash-text-muted);">Posisi</p>
                <p class="font-medium" style="color: var(--dash-text-heading);">{{ $template->position?->position_name ?? 'Semua posisi' }}</p>
            </div>
            <div>
                <p class="text-xs mb-1" style="color: var(--dash-text-muted);">Dibuat oleh</p>
                <p class="font-medium" style="color: var(--dash-text-heading);">{{ $template->createdBy?->full_name ?? '-' }}</p>
            </div>
            @if ($template->description)
                <div class="col-span-2 sm:col-span-3">
                    <p class="text-xs mb-1" style="color: var(--dash-text-muted);">Deskripsi</p>
                    <p style="color: var(--dash-text-body);">{{ $template->description }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Indicators --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div>
                <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Indikator KPI</h2>
                <p class="mt-0.5 text-xs" style="color: var(--dash-text-muted);">Total bobot aktif harus 100%. Indikator dengan sumber data diisi otomatis saat evaluasi dibuat.</p>
            </div>
            @can('kpi.template.update')
                <a href="{{ route('kpi.templates.indicators.create', $template) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity shrink-0"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[14px]">add</span>Tambah
                </a>
            @endcan
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border);">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Indikator</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Satuan</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Target</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Bobot</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Sumber Data</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $totalWeight = 0; @endphp
                @forelse ($template->indicators as $indicator)
                    @php if ($indicator->is_active) $totalWeight += $indicator->weight; @endphp
                    <tr class="{{ $indicator->is_active ? '' : 'opacity-50' }}"
                        style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3" style="color: var(--dash-text-faint);">{{ $indicator->sequence_number }}</td>
                        <td class="px-5 py-3">
                            <p class="font-medium" style="color: var(--dash-text-heading);">{{ $indicator->indicator_name }}</p>
                            <p class="font-mono text-xs" style="color: var(--dash-text-faint);">{{ $indicator->indicator_code }}</p>
                        </td>
                        <td class="px-5 py-3" style="color: var(--dash-text-body);">{{ $indicator->unit ?? '-' }}</td>
                        <td class="px-5 py-3 text-right" style="color: var(--dash-text-body);">{{ $indicator->target_value }}</td>
                        <td class="px-5 py-3 text-right font-semibold" style="color: var(--dash-text-heading);">{{ $indicator->weight }}%</td>
                        <td class="px-5 py-3">
                            @if ($indicator->data_source_type)
                                <span class="inline-flex px-2 py-0.5 rounded-lg font-mono text-xs bg-blue-100 text-blue-800">{{ $indicator->data_source_type }}</span>
                            @else
                                <span class="text-xs" style="color: var(--dash-text-faint);">Manual</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold {{ $indicator->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $indicator->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @can('kpi.template.update')
                                <div class="inline-flex gap-1">
                                    <a href="{{ route('kpi.templates.indicators.edit', [$template, $indicator]) }}"
                                       class="inline-flex items-center gap-1 px-2 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[13px]">edit</span>Edit
                                    </a>
                                    <button type="button"
                                            @click="showDeleteIndicator = true; deleteIndicatorId = {{ $indicator->id }}; deleteIndicatorName = '{{ addslashes($indicator->indicator_name) }}'"
                                            class="inline-flex items-center gap-1 px-2 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                            style="background-color: #dc2626;">
                                        <span class="material-symbols-outlined text-[13px]">delete</span>Hapus
                                    </button>
                                </div>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">assessment</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada indikator. Tambah indikator di atas.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($template->indicators->count())
                <tfoot>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                        <td colspan="4" class="px-5 py-3 text-right text-xs font-semibold" style="color: var(--dash-text-muted);">Total bobot:</td>
                        <td class="px-5 py-3 text-right text-xs font-semibold {{ abs($totalWeight - 100) < 0.01 ? 'text-emerald-700' : 'text-red-700' }}">{{ $totalWeight }}%</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    {{-- Documents --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Dokumen Template</h2>
            @can('kpi.document.create')
                <form method="POST" action="{{ route('kpi.documents.store') }}" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="kpi_template_id" value="{{ $template->id }}">
                    <input type="hidden" name="document_type" value="template">
                    <input type="text" name="title" placeholder="Judul dokumen" required
                           class="px-3 py-1.5 text-xs rounded-lg focus:outline-none"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'">
                    <input type="file" name="file" required class="text-xs" style="color: var(--dash-text-muted);">
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[14px]">upload</span>Upload
                    </button>
                </form>
            @endcan
        </div>
        @forelse ($template->documents as $doc)
            <div class="flex items-center justify-between px-5 py-3" style="border-bottom: 1px solid var(--dash-card-border);">
                <div>
                    <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $doc->title }}</p>
                    <p class="text-xs" style="color: var(--dash-text-faint);">{{ $doc->original_name }} - {{ $doc->uploadedAt?->format('d M Y') }}</p>
                </div>
                <button type="button"
                        @click="showDeleteDoc = true; deleteDocId = {{ $doc->id }}; deleteDocName = '{{ addslashes($doc->title) }}'"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                        style="background-color: #dc2626;">
                    <span class="material-symbols-outlined text-[13px]">delete</span>Hapus
                </button>
            </div>
        @empty
            <div class="px-5 py-8 text-center">
                <span class="material-symbols-outlined text-[36px] block mx-auto mb-2" style="color: var(--dash-text-faint);">folder_open</span>
                <p class="text-sm" style="color: var(--dash-text-muted);">Belum ada dokumen.</p>
            </div>
        @endforelse
    </div>

    {{-- Modal hapus indikator --}}
    <div x-show="showDeleteIndicator" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background-color: rgba(0,0,0,0.5);">
        <div class="w-full max-w-md rounded-2xl shadow-xl"
             style="background-color: var(--dash-card-bg);">
            <div class="px-6 py-4 rounded-t-2xl" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h3 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Hapus Indikator</h3>
            </div>
            <div class="px-6 py-5">
                <p class="text-sm" style="color: var(--dash-text-body);">Yakin ingin menghapus indikator <strong x-text="deleteIndicatorName"></strong>? Tindakan ini tidak bisa dibatalkan.</p>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 rounded-b-2xl" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <button type="button" @click="showDeleteIndicator = false"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </button>
                <form method="POST" :action="`/kpi/templates/{{ $template->id }}/indicators/${deleteIndicatorId}`">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: #dc2626;">
                        <span class="material-symbols-outlined text-[16px]">delete</span>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal hapus dokumen --}}
    <div x-show="showDeleteDoc" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background-color: rgba(0,0,0,0.5);">
        <div class="w-full max-w-md rounded-2xl shadow-xl"
             style="background-color: var(--dash-card-bg);">
            <div class="px-6 py-4 rounded-t-2xl" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h3 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Hapus Dokumen</h3>
            </div>
            <div class="px-6 py-5">
                <p class="text-sm" style="color: var(--dash-text-body);">Yakin ingin menghapus dokumen <strong x-text="deleteDocName"></strong>?</p>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 rounded-b-2xl" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <button type="button" @click="showDeleteDoc = false"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </button>
                <form method="POST" :action="`/kpi/documents/${deleteDocId}`">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: #dc2626;">
                        <span class="material-symbols-outlined text-[16px]">delete</span>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
