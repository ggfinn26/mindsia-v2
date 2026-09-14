@extends('layouts.dashboard')

@section('title', 'Detail Template KPI')
@section('header_title', 'Template KPI')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('kpi.templates.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Template KPI
            </a>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $template->template_name }}</h2>
            <div class="mt-1 flex items-center gap-3">
                <p class="font-mono text-sm text-slate-400">{{ $template->template_code }}</p>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $template->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>
        @can('kpi.template.update')
            <a href="{{ route('kpi.templates.edit', $template) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                <span class="material-symbols-outlined text-[16px]">edit</span> Edit
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Info --}}
    <div class="border border-slate-200 bg-white">
        <div class="grid grid-cols-2 gap-4 p-5 text-sm sm:grid-cols-3">
            <div>
                <p class="text-xs text-slate-500">Posisi</p>
                <p class="font-medium text-slate-900">{{ $template->position?->position_name ?? 'Semua posisi' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Dibuat oleh</p>
                <p class="font-medium text-slate-900">{{ $template->createdBy?->full_name ?? '—' }}</p>
            </div>
            @if ($template->description)
                <div class="col-span-2 sm:col-span-3">
                    <p class="text-xs text-slate-500">Deskripsi</p>
                    <p class="text-slate-700">{{ $template->description }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Indicators --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
            <div>
                <h3 class="font-jakarta text-sm font-bold text-slate-900">Indikator KPI</h3>
                <p class="mt-0.5 text-xs text-slate-500">Total bobot harus 100%. Indikator dengan data source diisi otomatis saat evaluasi dibuat.</p>
            </div>
            @can('kpi.template.update')
                <a href="{{ route('kpi.templates.indicators.create', $template) }}" class="inline-flex min-h-8 items-center gap-1 bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">
                    <span class="material-symbols-outlined text-[14px]">add</span> Tambah
                </a>
            @endcan
        </div>
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                <tr>
                    <th class="px-5 py-3">#</th>
                    <th class="px-5 py-3">Indikator</th>
                    <th class="px-5 py-3">Satuan</th>
                    <th class="px-5 py-3 text-right">Target</th>
                    <th class="px-5 py-3 text-right">Bobot</th>
                    <th class="px-5 py-3">Sumber Data</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php $totalWeight = 0; @endphp
                @forelse ($template->indicators as $indicator)
                    @php $totalWeight += $indicator->weight; @endphp
                    <tr class="{{ $indicator->is_active ? '' : 'opacity-50' }} hover:bg-slate-50/70">
                        <td class="px-5 py-3 text-slate-400">{{ $indicator->sequence_number }}</td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-900">{{ $indicator->indicator_name }}</p>
                            <p class="font-mono text-xs text-slate-400">{{ $indicator->indicator_code }}</p>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $indicator->unit ?? '—' }}</td>
                        <td class="px-5 py-3 text-right text-slate-700">{{ $indicator->target_value }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-900">{{ $indicator->weight }}%</td>
                        <td class="px-5 py-3">
                            @if ($indicator->data_source_type)
                                <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 font-mono text-xs text-blue-800">{{ $indicator->data_source_type }}</span>
                            @else
                                <span class="text-xs text-slate-400">Manual</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $indicator->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $indicator->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @can('kpi.template.update')
                                <a href="{{ route('kpi.templates.indicators.edit', [$template, $indicator]) }}" class="inline-flex min-h-8 items-center border border-slate-300 px-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-8 text-center text-sm text-slate-400">Belum ada indikator. Tambah indikator di atas.</td>
                    </tr>
                @endforelse
            </tbody>
            @if ($template->indicators->count())
                <tfoot class="bg-slate-50 text-xs font-semibold text-slate-500">
                    <tr>
                        <td colspan="4" class="px-5 py-3 text-right">Total bobot:</td>
                        <td class="px-5 py-3 text-right {{ abs($totalWeight - 100) < 0.01 ? 'text-emerald-700' : 'text-red-700' }}">{{ $totalWeight }}%</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    {{-- Documents --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Dokumen Template</h3>
            @can('kpi.document.upload')
                <form method="POST" action="{{ route('kpi.documents.store') }}" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="kpi_template_id" value="{{ $template->id }}">
                    <input type="hidden" name="document_type" value="template">
                    <input type="text" name="title" placeholder="Judul dokumen" required class="border-slate-300 text-xs focus:border-[#215aac] focus:ring-[#215aac]">
                    <input type="file" name="file" required class="text-xs text-slate-600">
                    <button type="submit" class="inline-flex min-h-8 items-center gap-1 bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Upload</button>
                </form>
            @endcan
        </div>
        @forelse ($template->documents as $doc)
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3 last:border-0">
                <div>
                    <p class="text-sm font-medium text-slate-900">{{ $doc->title }}</p>
                    <p class="text-xs text-slate-400">{{ $doc->original_name }} · {{ $doc->uploadedAt?->format('d M Y') }}</p>
                </div>
                <form method="POST" action="{{ route('kpi.documents.destroy', $doc) }}" onsubmit="return confirm('Hapus dokumen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:underline">Hapus</button>
                </form>
            </div>
        @empty
            <p class="px-5 py-4 text-sm text-slate-400">Belum ada dokumen.</p>
        @endforelse
    </div>

</div>
@endsection
