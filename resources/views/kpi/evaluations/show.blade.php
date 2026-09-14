@extends('layouts.dashboard')

@section('title', 'Detail Evaluasi KPI')
@section('header_title', 'Detail Evaluasi KPI')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('kpi.evaluations.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Evaluasi KPI
            </a>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $evaluation->employee_name_snapshot }}</h2>
            <p class="mt-0.5 text-sm text-slate-500">{{ $evaluation->position_name_snapshot }} · {{ $evaluation->template?->template_name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $evaluation->isDraft() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                {{ $evaluation->isDraft() ? 'Draft' : 'Finalized' }}
            </span>
            @if ($evaluation->grade)
                <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-sm font-bold text-blue-900">Grade {{ $evaluation->grade }}</span>
            @endif
        </div>
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

    {{-- Summary --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Periode</p>
            <p class="mt-1 text-sm font-semibold text-slate-900">
                {{ $evaluation->period_start_date?->format('d M Y') }} – {{ $evaluation->period_end_date?->format('d M Y') }}
            </p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Evaluator</p>
            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $evaluation->evaluator?->full_name ?? '—' }}</p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Total Skor</p>
            <p class="mt-1 font-jakarta text-2xl font-bold text-slate-900">
                {{ $evaluation->total_score !== null ? number_format($evaluation->total_score, 2) : '—' }}
            </p>
        </div>
        <div class="border border-slate-200 bg-white px-5 py-4">
            <p class="text-xs font-semibold text-slate-500">Finalized</p>
            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $evaluation->finalized_at?->format('d M Y H:i') ?? '—' }}</p>
        </div>
    </div>

    {{-- Items --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Rincian Indikator</h3>
            @if ($evaluation->isDraft())
                <p class="mt-0.5 text-xs text-slate-500">Isi actual_value untuk indikator manual. Indikator otomatis sudah terisi dari sistem.</p>
            @endif
        </div>
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                <tr>
                    <th class="px-5 py-3">#</th>
                    <th class="px-5 py-3">Indikator</th>
                    <th class="px-5 py-3 text-right">Target</th>
                    <th class="px-5 py-3 text-right">Aktual</th>
                    <th class="px-5 py-3 text-right">Bobot</th>
                    <th class="px-5 py-3 text-right">Skor</th>
                    @if ($evaluation->isDraft())
                        <th class="px-5 py-3 text-right">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($evaluation->items as $item)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-5 py-3 text-slate-400">{{ $item->indicator?->sequence_number }}</td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-900">{{ $item->indicator_name_snapshot }}</p>
                            @if ($item->notes)
                                <p class="mt-0.5 text-xs text-slate-400">{{ $item->notes }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right tabular-nums text-slate-700">{{ $item->target_value_snapshot }}</td>
                        <td class="px-5 py-3 text-right tabular-nums {{ $item->actual_value !== null ? 'font-semibold text-slate-900' : 'text-slate-300' }}">
                            {{ $item->actual_value ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-right text-slate-600">{{ $item->weight_snapshot }}%</td>
                        <td class="px-5 py-3 text-right tabular-nums font-semibold text-slate-900">
                            {{ $item->score !== null ? number_format($item->score, 2) : '—' }}
                        </td>
                        @if ($evaluation->isDraft())
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('kpi.evaluations.items.update', [$evaluation, $item]) }}" class="inline-flex items-center gap-2">
                                    @csrf @method('PUT')
                                    <input type="number" name="actual_value" value="{{ $item->actual_value }}" step="0.01"
                                           placeholder="Aktual" class="w-24 border-slate-300 text-xs focus:border-[#215aac] focus:ring-[#215aac]">
                                    <button type="submit" class="inline-flex min-h-7 items-center bg-[#215aac] px-2 text-xs font-semibold text-white hover:bg-[#194a91]">Isi</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Finalize --}}
    @if ($evaluation->isDraft())
        @can('kpi.evaluation.finalize')
            <div class="border border-slate-200 bg-white p-5">
                <h3 class="font-jakarta text-sm font-bold text-slate-900 mb-3">Finalize Evaluasi</h3>
                <form method="POST" action="{{ route('kpi.evaluations.finalize', $evaluation) }}" onsubmit="return confirm('Finalize evaluasi? Data tidak bisa diubah setelah finalized.')">
                    @csrf
                    <div class="mb-3">
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Catatan evaluator</label>
                        <textarea name="evaluator_notes" rows="2" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">{{ old('evaluator_notes', $evaluation->evaluator_notes) }}</textarea>
                    </div>
                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-emerald-600 px-5 text-sm font-semibold text-white hover:bg-emerald-700">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span> Finalize Evaluasi
                    </button>
                </form>
            </div>
        @endcan
    @else
        @if ($evaluation->evaluator_notes)
            <div class="border border-slate-200 bg-white p-5">
                <p class="text-xs font-semibold text-slate-500 mb-1">Catatan evaluator</p>
                <p class="text-sm text-slate-700">{{ $evaluation->evaluator_notes }}</p>
            </div>
        @endif
    @endif

    {{-- Documents --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Dokumen Evaluasi</h3>
            @can('kpi.document.upload')
                <form method="POST" action="{{ route('kpi.documents.store') }}" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="employee_kpi_evaluation_id" value="{{ $evaluation->id }}">
                    <input type="hidden" name="document_type" value="evaluation">
                    <input type="text" name="title" placeholder="Judul dokumen" required class="border-slate-300 text-xs focus:border-[#215aac] focus:ring-[#215aac]">
                    <input type="file" name="file" required class="text-xs text-slate-600">
                    <button type="submit" class="inline-flex min-h-8 items-center gap-1 bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Upload</button>
                </form>
            @endcan
        </div>
        @forelse ($evaluation->documents as $doc)
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3 last:border-0">
                <div>
                    <p class="text-sm font-medium text-slate-900">{{ $doc->title }}</p>
                    <p class="text-xs text-slate-400">{{ $doc->original_name }}</p>
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
