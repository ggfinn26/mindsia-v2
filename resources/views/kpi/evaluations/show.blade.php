@extends('layouts.dashboard')

@section('title', 'Detail Evaluasi KPI')
@section('header_title', 'Detail Evaluasi KPI')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{ showFinalize: false, showDeleteDoc: false, deleteDocAction: '' }">

    {{-- Page header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('kpi.evaluations.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Evaluasi
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">KPI</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $evaluation->employee_name_snapshot }}</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">{{ $evaluation->position_name_snapshot }} - {{ $evaluation->template?->template_name }}</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            @if ($evaluation->isDraft())
                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold"
                      style="background-color: #fef3c7; color: #92400e;">Draft</span>
            @else
                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold"
                      style="background-color: #d1fae5; color: #065f46;">Finalized</span>
            @endif
            @if ($evaluation->grade)
                <span class="inline-flex px-3 py-1 rounded-lg text-sm font-bold"
                      style="background-color: #dbeafe; color: #1e40af;">Grade {{ $evaluation->grade }}</span>
            @endif
        </div>
    </div>

    {{-- Flash messages --}}
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
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Summary stat cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl px-5 py-4" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Periode</p>
            <p class="mt-1 text-sm font-semibold" style="color: var(--dash-text-heading);">
                {{ $evaluation->period_start_date?->format('d M Y') }} - {{ $evaluation->period_end_date?->format('d M Y') }}
            </p>
        </div>
        <div class="rounded-2xl px-5 py-4" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Evaluator</p>
            <p class="mt-1 text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $evaluation->evaluator?->full_name ?? '-' }}</p>
        </div>
        <div class="rounded-2xl px-5 py-4" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Total Skor</p>
            <p class="mt-1 font-jakarta text-2xl font-bold" style="color: var(--dash-text-heading);">
                {{ $evaluation->total_score !== null ? number_format($evaluation->total_score, 2) : '-' }}
            </p>
        </div>
        <div class="rounded-2xl px-5 py-4" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Finalized</p>
            <p class="mt-1 text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $evaluation->finalized_at?->format('d M Y H:i') ?? '-' }}</p>
        </div>
    </div>

    {{-- Indicator items table --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-3" style="border-bottom: 1px solid var(--dash-card-border);">
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Rincian Indikator</h2>
            @if ($evaluation->isDraft())
                <p class="mt-0.5 text-xs" style="color: var(--dash-text-muted);">Isi actual_value untuk indikator manual. Indikator otomatis sudah terisi dari sistem.</p>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Indikator</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Target</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Aktual</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Capaian</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Bobot</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Skor</th>
                        @if ($evaluation->isDraft())
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($evaluation->items as $item)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3" style="color: var(--dash-text-faint);">{{ $item->indicator?->sequence_number }}</td>
                            <td class="px-5 py-3">
                                <p class="font-medium" style="color: var(--dash-text-heading);">{{ $item->indicator_name_snapshot }}</p>
                                @if ($item->notes)
                                    <p class="mt-0.5 text-xs" style="color: var(--dash-text-faint);">{{ $item->notes }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right tabular-nums" style="color: var(--dash-text-body);">{{ $item->target_value ?? '-' }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-semibold"
                                style="color: {{ $item->actual_value !== null ? 'var(--dash-text-heading)' : 'var(--dash-text-faint)' }};">
                                {{ $item->actual_value ?? '-' }}
                            </td>
                            <td class="px-5 py-3 text-right tabular-nums"
                                style="color: {{ $item->actual_value !== null ? 'var(--dash-text-body)' : 'var(--dash-text-faint)' }};">
                                {{ $item->achievement_percentage !== null ? number_format($item->achievement_percentage, 1).'%' : '-' }}
                            </td>
                            <td class="px-5 py-3 text-right" style="color: var(--dash-text-body);">{{ $item->weight_snapshot }}%</td>
                            <td class="px-5 py-3 text-right tabular-nums font-semibold" style="color: var(--dash-text-heading);">
                                {{ $item->score !== null ? number_format($item->score, 2) : '-' }}
                            </td>
                            @if ($evaluation->isDraft())
                                <td class="px-5 py-3 text-right">
                                    @if ($item->source_type !== null)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold" style="color: var(--dash-text-muted);">
                                            <span class="material-symbols-outlined text-[14px]">lock</span>Otomatis dari sistem
                                        </span>
                                    @else
                                    <form method="POST" action="{{ route('kpi.evaluations.items.update', [$evaluation, $item]) }}"
                                          class="inline-flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="actual_value" value="{{ $item->actual_value }}" step="0.01"
                                               placeholder="Aktual"
                                               class="w-20 px-2 py-1 text-xs rounded-lg focus:outline-none transition-all"
                                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                                        <input type="text" name="notes" value="{{ $item->notes }}"
                                               placeholder="Catatan (opsional)"
                                               class="w-32 px-2 py-1 text-xs rounded-lg focus:outline-none transition-all"
                                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                                style="background-color: var(--dash-primary);">
                                            <span class="material-symbols-outlined text-[14px]">save</span>Simpan
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Finalize section --}}
    @if ($evaluation->isDraft())
        @can('kpi.evaluation.finalize')
            <div class="rounded-2xl p-5" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <h2 class="font-jakarta text-sm font-bold mb-3" style="color: var(--dash-text-heading);">Finalize Evaluasi</h2>
                <form method="POST" action="{{ route('kpi.evaluations.finalize', $evaluation) }}" id="form-finalize">
                    @csrf
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-semibold" style="color: var(--dash-text-body);">Catatan evaluator</label>
                        <textarea name="evaluator_notes" rows="2"
                                  class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                  style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                  onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                  onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('evaluator_notes', $evaluation->evaluator_notes) }}</textarea>
                    </div>
                    <button type="button"
                            @click="showFinalize = true"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: #059669;">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        Finalize Evaluasi
                    </button>
                </form>
            </div>
        @endcan
    @else
        @if ($evaluation->evaluator_notes)
            <div class="rounded-2xl p-5" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color: var(--dash-text-muted);">Catatan evaluator</p>
                <p class="text-sm" style="color: var(--dash-text-body);">{{ $evaluation->evaluator_notes }}</p>
            </div>
        @endif
    @endif

    {{-- Documents section --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between px-5 py-3" style="border-bottom: 1px solid var(--dash-card-border);">
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Dokumen Evaluasi</h2>
            @can('kpi.document.create')
                <form method="POST" action="{{ route('kpi.documents.store') }}" enctype="multipart/form-data"
                      class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="employee_kpi_evaluation_id" value="{{ $evaluation->id }}">
                    <input type="hidden" name="document_type" value="evaluation">
                    <input type="text" name="title" placeholder="Judul dokumen" required
                           class="px-3 py-1.5 text-xs rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    <input type="file" name="file" required
                           class="text-xs" style="color: var(--dash-text-body);" />
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[14px]">upload</span>Upload
                    </button>
                </form>
            @endcan
        </div>
        @forelse ($evaluation->documents as $doc)
            <div class="flex items-center justify-between px-5 py-3"
                 style="border-bottom: 1px solid var(--dash-card-border);">
                <div>
                    <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $doc->title }}</p>
                    <p class="text-xs" style="color: var(--dash-text-faint);">{{ $doc->original_name }}</p>
                </div>
                @can('kpi.document.delete')
                    <button type="button"
                            @click="deleteDocAction = '{{ route('kpi.documents.destroy', $doc) }}'; showDeleteDoc = true"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg hover:opacity-90 transition-opacity text-white"
                            style="background-color: #dc2626;">
                        <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                    </button>
                @endcan
            </div>
        @empty
            <p class="px-5 py-4 text-sm" style="color: var(--dash-text-faint);">Belum ada dokumen.</p>
        @endforelse
    </div>

    {{-- Modal: Konfirmasi Finalize --}}
    <template x-teleport="body">
        <div x-show="showFinalize" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="background-color: rgba(0,0,0,0.5);">
            <div class="w-full max-w-md rounded-2xl shadow-xl"
                 style="background-color: var(--dash-card-bg);"
                 @click.stop>
                <div class="px-6 py-4 rounded-t-2xl" style="border-bottom: 1px solid var(--dash-card-border);">
                    <h3 class="text-base font-bold" style="color: var(--dash-text-heading);">Konfirmasi Finalize</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm" style="color: var(--dash-text-body);">
                        Evaluasi akan difinalize. Data tidak bisa diubah setelah proses ini. Lanjutkan?
                    </p>
                </div>
                <div class="px-6 py-4 flex justify-end gap-3 rounded-b-2xl" style="border-top: 1px solid var(--dash-card-border);">
                    <button type="button"
                            @click="showFinalize = false"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">close</span>Batal
                    </button>
                    <button type="button"
                            @click="showFinalize = false; document.getElementById('form-finalize').submit()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: #059669;">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>Ya, Finalize
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Modal: Konfirmasi Hapus Dokumen --}}
    <template x-teleport="body">
        <div x-show="showDeleteDoc" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="background-color: rgba(0,0,0,0.5);">
            <div class="w-full max-w-md rounded-2xl shadow-xl"
                 style="background-color: var(--dash-card-bg);"
                 @click.stop>
                <div class="px-6 py-4 rounded-t-2xl" style="border-bottom: 1px solid var(--dash-card-border);">
                    <h3 class="text-base font-bold" style="color: var(--dash-text-heading);">Hapus Dokumen</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm" style="color: var(--dash-text-body);">
                        Dokumen ini akan dihapus permanen. Tindakan tidak bisa dibatalkan. Lanjutkan?
                    </p>
                </div>
                <div class="px-6 py-4 flex justify-end gap-3 rounded-b-2xl" style="border-top: 1px solid var(--dash-card-border);">
                    <button type="button"
                            @click="showDeleteDoc = false; deleteDocAction = ''"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">close</span>Batal
                    </button>
                    <form :action="deleteDocAction" method="POST" x-ref="deleteDocForm">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: #dc2626;">
                            <span class="material-symbols-outlined text-[16px]">delete</span>Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
