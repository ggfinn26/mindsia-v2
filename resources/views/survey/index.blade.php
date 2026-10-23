@extends('layouts.dashboard')

@section('title', 'Kelola Survey - MINDSIA')
@section('header_title', 'Kelola Survey')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6" x-data="{
    showConfirmModal: false,
    confirmTitle: '',
    confirmMessage: '',
    confirmActionUrl: '',
    confirmMethod: 'POST',
    confirmActionText: 'Konfirmasi'
}">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Page header (Pola A) --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">
                @if(request('tab') === 'assign')
                    Manajemen Survey
                @elseif(request('tab') === 'results')
                    Laporan & Analitik
                @else
                    Konfigurasi Survey
                @endif
            </p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">
                @if(request('tab') === 'assign')
                    Assign Survey
                @elseif(request('tab') === 'results')
                    Hasil Survey
                @else
                    Template Survey
                @endif
            </h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                @if(request('tab') === 'assign')
                    Tugaskan kuesioner survey ke member maupun pegawai secara terjadwal.
                @elseif(request('tab') === 'results')
                    Pantau ringkasan statistik dan detail jawaban dari seluruh responden survey.
                @else
                    Kelola template kuesioner, pertanyaan, dan batas waktu pengisian survey.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if(!request()->has('tab') || request('tab') === 'template')
                @can('survey.form.create')
                <a href="{{ route('surveys.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Survey
                </a>
                @endcan
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex border-b" style="border-color: var(--dash-card-border);">
        <a href="{{ route('surveys.index') }}"
           class="px-5 py-3 text-sm transition-colors {{ !request()->has('tab') || request('tab') === 'template' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80' }}"
           style="{{ !request()->has('tab') || request('tab') === 'template' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);' }}">
            <span class="inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">quiz</span>
                Template Survey
            </span>
        </a>
        <a href="{{ route('surveys.index', ['tab' => 'assign']) }}"
           class="px-5 py-3 text-sm transition-colors {{ request('tab') === 'assign' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80' }}"
           style="{{ request('tab') === 'assign' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);' }}">
            <span class="inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">assignment_ind</span>
                Assign Survey
            </span>
        </a>
        <a href="{{ route('surveys.index', ['tab' => 'results']) }}"
           class="px-5 py-3 text-sm transition-colors {{ request('tab') === 'results' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80' }}"
           style="{{ request('tab') === 'results' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);' }}">
            <span class="inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">analytics</span>
                Hasil Survey
            </span>
        </a>
    </div>

    {{-- Table Container --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Nama Survey</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Batas Waktu</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($surveys as $survey)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-4 font-semibold" style="color: var(--dash-text-heading);">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-text-faint);">description</span>
                                    <span>{{ $survey->survey_name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm" style="color: var(--dash-text-body);">
                                @if ($survey->deadline_at)
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-faint);">calendar_month</span>
                                        {{ $survey->deadline_at->format('d M Y H:i') }}
                                    </span>
                                @else
                                    <span class="italic text-xs" style="color: var(--dash-text-faint);">Tanpa Batas</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($survey->isExpired())
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-700">
                                        Berakhir
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center justify-end gap-2 flex-wrap">
                                    @if(request('tab') === 'assign')
                                        <a href="{{ route('surveys.show', $survey->id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors text-white hover:opacity-90"
                                           style="background-color: var(--dash-primary);">
                                            <span class="material-symbols-outlined text-[14px]">person_add</span>
                                            Assign Member / Pegawai
                                        </a>
                                    @elseif(request('tab') === 'results')
                                        @can('survey.result.view')
                                            <a href="{{ route('surveys.results', $survey->id) }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">analytics</span>
                                                Lihat Hasil
                                            </a>
                                        @endcan
                                    @else
                                        <a href="{{ route('surveys.show', $survey->id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                                            Detail
                                        </a>
                                        @can('survey.form.update')
                                            <a href="{{ route('surveys.edit', $survey->id) }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">edit</span>
                                                Edit
                                            </a>
                                        @endcan
                                        @can('survey.form.delete')
                                            <button type="button"
                                                    @click="showConfirmModal = true; confirmTitle = 'Hapus Survey'; confirmMessage = 'Apakah Anda yakin ingin menghapus survey {{ addslashes($survey->survey_name) }}? Tindakan ini tidak dapat dibatalkan.'; confirmActionUrl = '{{ route('surveys.destroy', $survey->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                                    style="background-color: var(--dash-danger, #dc2626);">
                                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                                Hapus
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">fact_check</span>
                                <h3 class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data survey</h3>
                                <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">
                                    @if(request('tab') === 'assign')
                                        Belum ada survey yang dapat ditugaskan.
                                    @elseif(request('tab') === 'results')
                                        Belum ada hasil survey yang tersedia.
                                    @else
                                        Silakan buat template survey baru untuk memulai kuesioner.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus (Custom Alpine.js, Card TANPA overflow-hidden) --}}
    <template x-teleport="body">
        <div x-show="showConfirmModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="showConfirmModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showConfirmModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showConfirmModal" @click.away="showConfirmModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        {{-- Header modal (rounded-t-2xl) --}}
                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer modal (rounded-b-2xl) --}}
                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showConfirmModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <form :action="confirmActionUrl" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="_method" :value="confirmMethod">
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);"
                                        x-text="confirmActionText">
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
