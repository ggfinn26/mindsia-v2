@extends('layouts.dashboard')

@section('title', 'Detail Evaluasi KPI')
@section('header_title', 'Detail Evaluasi KPI')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ 
    showConfirmModal: false, 
    confirmActionUrl: '', 
    confirmMethod: 'POST', 
    confirmTitle: '', 
    confirmMessage: '', 
    confirmButtonText: 'Konfirmasi', 
    confirmButtonClass: 'bg-[var(--dash-primary)]', 
    confirmIcon: 'warning', 
    confirmIconColor: 'text-red-600' 
}">

    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('kpi.evaluations.index') }}"
               class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <h2 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">{{ $evaluation->employee_name_snapshot }}</h2>
                <p class="mt-0.5 text-sm text-[var(--dash-text-muted)]">{{ $evaluation->position_name_snapshot }} · {{ $evaluation->template?->template_name }}</p>
            </div>
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
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] px-5 py-4">
            <p class="text-xs font-semibold text-[var(--dash-text-muted)]">Periode</p>
            <p class="mt-1 text-sm font-semibold text-[var(--dash-text-heading)]">
                {{ $evaluation->period_start_date?->format('d M Y') }} – {{ $evaluation->period_end_date?->format('d M Y') }}
            </p>
        </div>
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] px-5 py-4">
            <p class="text-xs font-semibold text-[var(--dash-text-muted)]">Evaluator</p>
            <p class="mt-1 text-sm font-semibold text-[var(--dash-text-heading)]">{{ $evaluation->evaluator?->full_name ?? '—' }}</p>
        </div>
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] px-5 py-4">
            <p class="text-xs font-semibold text-[var(--dash-text-muted)]">Total Skor</p>
            <p class="mt-1 font-jakarta text-2xl font-bold text-[var(--dash-text-heading)]">
                {{ $evaluation->total_score !== null ? number_format($evaluation->total_score, 2) : '—' }}
            </p>
        </div>
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] px-5 py-4">
            <p class="text-xs font-semibold text-[var(--dash-text-muted)]">Finalized</p>
            <p class="mt-1 text-sm font-semibold text-[var(--dash-text-heading)]">{{ $evaluation->finalized_at?->format('d M Y H:i') ?? '—' }}</p>
        </div>
    </div>

    {{-- Items --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[var(--dash-card-border)] px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-[var(--dash-text-heading)]">Rincian Indikator</h3>
            @if ($evaluation->isDraft())
                <p class="mt-0.5 text-xs text-[var(--dash-text-muted)]">Isi actual_value untuk indikator manual. Indikator otomatis sudah terisi dari sistem.</p>
            @endif
        </div>
        <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
            <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-semibold text-[var(--dash-text-muted)]">
                <tr>
                    <th class="px-5 py-3">#</th>
                    <th class="px-5 py-3">Indikator</th>
                    <th class="px-5 py-3 text-right">Target</th>
                    <th class="px-5 py-3 text-right">Aktual</th>
                    <th class="px-5 py-3 text-right">Capaian</th>
                    <th class="px-5 py-3 text-right">Bobot</th>
                    <th class="px-5 py-3 text-right">Skor</th>
                    @if ($evaluation->isDraft())
                        <th class="px-5 py-3 text-right">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--dash-card-border)]">
                @foreach ($evaluation->items as $item)
                    <tr class="hover:bg-[var(--dash-page-bg)]">
                        <td class="px-5 py-3 text-[var(--dash-text-faint)]">{{ $item->indicator?->sequence_number }}</td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-[var(--dash-text-heading)]">{{ $item->indicator_name_snapshot }}</p>
                            @if ($item->notes)
                                <p class="mt-0.5 text-xs text-[var(--dash-text-faint)]">{{ $item->notes }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right tabular-nums text-[var(--dash-text-body)]">{{ $item->target_value ?? '—' }}</td>
                        <td class="px-5 py-3 text-right tabular-nums {{ $item->actual_value !== null ? 'font-semibold text-[var(--dash-text-heading)]' : 'text-[var(--dash-text-faint)]' }}">
                            {{ $item->actual_value ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-right tabular-nums {{ $item->actual_value !== null ? 'text-[var(--dash-text-body)]' : 'text-[var(--dash-text-faint)]' }}">
                            {{ $item->achievement_percentage !== null ? number_format($item->achievement_percentage, 1).'%' : '—' }}
                        </td>
                        <td class="px-5 py-3 text-right text-[var(--dash-text-body)]">{{ $item->weight_snapshot }}%</td>
                        <td class="px-5 py-3 text-right tabular-nums font-semibold text-[var(--dash-text-heading)]">
                            {{ $item->score !== null ? number_format($item->score, 2) : '—' }}
                        </td>
                        @if ($evaluation->isDraft())
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('kpi.evaluations.items.update', [$evaluation, $item]) }}" class="inline-flex items-center gap-2">
                                    @csrf @method('PUT')
                                    <input type="number" name="actual_value" value="{{ $item->actual_value }}" step="0.01"
                                           placeholder="Aktual" class="w-20 border-[var(--dash-input-border)] text-xs focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                                    <input type="text" name="notes" value="{{ $item->notes }}"
                                           placeholder="Catatan (opsional)" class="w-32 border-[var(--dash-input-border)] text-xs focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                                    <button type="submit" class="inline-flex min-h-7 items-center bg-[var(--dash-primary)] px-2 text-xs font-semibold text-white hover:opacity-90">Simpan</button>
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
            <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5">
                <h3 class="font-jakarta text-sm font-bold text-[var(--dash-text-heading)] mb-3">Finalize Evaluasi</h3>
                <form method="POST" action="{{ route('kpi.evaluations.finalize', $evaluation) }}" x-ref="finalizeForm">
                    @csrf
                    <div class="mb-3">
                        <label class="mb-1 block text-xs font-semibold text-[var(--dash-text-body)]">Catatan evaluator</label>
                        <textarea name="evaluator_notes" rows="2" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] rounded-lg block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">{{ old('evaluator_notes', $evaluation->evaluator_notes) }}</textarea>
                    </div>
                    <button type="button" 
                            @click="showConfirmModal = true; confirmActionUrl = '{{ route('kpi.evaluations.finalize', $evaluation) }}'; confirmMethod = 'POST'; confirmTitle = 'Finalize Evaluasi'; confirmMessage = 'Finalize evaluasi? Data tidak bisa diubah setelah finalized.'; confirmButtonText = 'Finalize'; confirmButtonClass = 'bg-emerald-600 hover:bg-emerald-700'; confirmIcon = 'check_circle'; confirmIconColor = 'text-emerald-600'"
                            class="inline-flex min-h-10 items-center rounded-lg gap-2 bg-emerald-600 px-5 text-sm font-semibold text-white hover:bg-emerald-700">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span> Finalize Evaluasi
                    </button>
                </form>
            </div>
        @endcan
    @else
        @if ($evaluation->evaluator_notes)
            <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5">
                <p class="text-xs font-semibold text-[var(--dash-text-muted)] mb-1">Catatan evaluator</p>
                <p class="text-sm text-[var(--dash-text-body)]">{{ $evaluation->evaluator_notes }}</p>
            </div>
        @endif
    @endif

    {{-- Documents --}}
    <div class="overflow-hidden rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="flex items-center justify-between border-b border-[var(--dash-card-border)] px-5 py-3">
            <h3 class="font-jakarta text-sm font-bold text-[var(--dash-text-heading)]">Dokumen Evaluasi</h3>
            @can('kpi.document.create')
                <form method="POST" action="{{ route('kpi.documents.store') }}" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="employee_kpi_evaluation_id" value="{{ $evaluation->id }}">
                    <input type="hidden" name="document_type" value="evaluation">
                    <input type="text" name="title" placeholder="Judul dokumen" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] rounded-lg border-[var(--dash-input-border)] text-xs focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                    <input type="file" name="file" required class="text-xs text-[var(--dash-text-body)]">
                    <button type="submit" class="inline-flex min-h-8 items-center gap-1 bg-[var(--dash-primary)] px-3 text-xs font-semibold text-white hover:opacity-90">Upload</button>
                </form>
            @endcan
        </div>
        @forelse ($evaluation->documents as $doc)
            <div class="flex items-center justify-between border-b border-[var(--dash-card-border)] px-5 py-3 last:border-0">
                <div>
                    <p class="text-sm font-medium text-[var(--dash-text-heading)]">{{ $doc->title }}</p>
                    <p class="text-xs text-[var(--dash-text-faint)]">{{ $doc->original_name }}</p>
                </div>
                <button type="button" 
                        @click="showConfirmModal = true; confirmActionUrl = '{{ route('kpi.documents.destroy', $doc) }}'; confirmMethod = 'DELETE'; confirmTitle = 'Hapus Dokumen'; confirmMessage = 'Hapus dokumen evaluasi ini?'; confirmButtonText = 'Hapus'; confirmButtonClass = 'bg-red-600 hover:bg-red-700'; confirmIcon = 'warning'; confirmIconColor = 'text-red-600'"
                        class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
            </div>
        @empty
            <p class="px-5 py-4 text-sm text-[var(--dash-text-faint)]">Belum ada dokumen.</p>
        @endforelse
    </div>

</div>
@endsection

    {{-- Universal Confirm Modal --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>
            <div class="relative z-10 inline-block align-bottom bg-[var(--dash-card-bg)] rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-[var(--dash-card-bg)] px-4 pt-5 pb-4 sm:p-6 sm:pb-4 rounded-t-lg">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                             :class="confirmIconColor === 'text-red-600' ? 'bg-red-100' : 'bg-emerald-100'">
                            <span class="material-symbols-outlined" :class="confirmIconColor" x-text="confirmIcon"></span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-[var(--dash-text-heading)]" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-[var(--dash-text-muted)]" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-[var(--dash-page-bg)] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                    <button type="button" @click="$refs.confirmForm.submit()" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
                            :class="confirmButtonClass" x-text="confirmButtonText"></button>
                    <button type="button" @click="showConfirmModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-[var(--dash-card-border)] shadow-sm px-4 py-2 bg-[var(--dash-card-bg)] text-base font-medium text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>
