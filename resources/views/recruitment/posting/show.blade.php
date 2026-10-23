@extends('layouts.dashboard')

@section('title', 'Detail Job Posting')
@section('header_title', 'Detail Job Posting')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{
        showPublishModal: false,
        showCloseModal: false
     }">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('recruitment.postings.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Posting
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Rekrutmen</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Detail Job Posting</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Informasi tayang lowongan, kriteria persyaratan, dan manajemen status.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($jobPosting->status === 'draft')
                @can('recruitment.job_posting.update')
                    <a href="{{ route('recruitment.postings.edit', $jobPosting) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors"
                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[14px]">edit</span>
                        Edit Posting
                    </a>
                @endcan
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Status mapping --}}
    @php
        $statusMap = [
            'draft'     => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-700 dark:text-slate-300', 'label' => 'Draft'],
            'published' => ['bg' => 'bg-emerald-100 dark:bg-emerald-950/40', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'Dipublikasikan'],
            'closed'    => ['bg' => 'bg-red-100 dark:bg-red-950/40', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'Ditutup'],
        ];
        $st = $statusMap[$jobPosting->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'label' => ucfirst($jobPosting->status)];
    @endphp

    {{-- Card 1: Informasi Utama Lowongan --}}
    <div class="rounded-2xl p-6 sm:p-7 shadow-sm space-y-6"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 gap-3"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div>
                <h2 class="font-jakarta text-xl font-bold" style="color: var(--dash-text-heading);">{{ $jobPosting->title }}</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                    ID Posting #{{ $jobPosting->id }} &bull; Dibuat oleh {{ $jobPosting->createdBy?->full_name ?? '-' }}
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full shrink-0 {{ $st['bg'] }} {{ $st['text'] }}">
                {{ $st['label'] }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Cabang</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $jobPosting->branch?->branch_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Posisi Terkait</p>
                <p class="text-sm font-semibold" style="color: var(--dash-primary-text);">{{ $jobPosting->position?->position_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Total Lamaran Masuk</p>
                <a href="{{ route('recruitment.application.index') }}"
                   class="text-sm font-bold hover:underline"
                   style="color: var(--dash-primary);">
                    {{ $jobPosting->applications->count() }} pelamar
                </a>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Tanggal Mulai Tayang</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $jobPosting->publish_date?->format('d M Y') ?? 'Belum dipublikasikan' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Batas Penutupan</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $jobPosting->closing_date?->format('d M Y') ?? 'Tanpa batas waktu' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Asal Job Permintaan</p>
                @if($jobPosting->job_permintaan_id)
                    <a href="{{ route('recruitment.permintaan.show', $jobPosting->job_permintaan_id) }}"
                       class="text-sm font-medium hover:underline inline-flex items-center gap-1"
                       style="color: var(--dash-primary-text);">
                        <span>Permintaan #{{ $jobPosting->job_permintaan_id }}</span>
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </a>
                @else
                    <p class="text-sm font-medium" style="color: var(--dash-text-muted);">-</p>
                @endif
            </div>
        </div>

        <div class="space-y-4 pt-4" style="border-top: 1px solid var(--dash-card-border);">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--dash-text-faint);">Deskripsi Pekerjaan</p>
                <div class="text-sm p-4 rounded-xl whitespace-pre-wrap leading-relaxed"
                     style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    {{ $jobPosting->job_description }}
                </div>
            </div>

            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--dash-text-faint);">Tanggung Jawab</p>
                <div class="text-sm p-4 rounded-xl whitespace-pre-wrap leading-relaxed"
                     style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    {{ $jobPosting->job_responsibilities }}
                </div>
            </div>

            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--dash-text-faint);">Kualifikasi Publik</p>
                <div class="text-sm p-4 rounded-xl whitespace-pre-wrap leading-relaxed"
                     style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    {{ $jobPosting->job_requirements_text }}
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Kriteria Seleksi --}}
    @if($jobPosting->permintaan?->requirementsAuto || $jobPosting->permintaan?->requirementsManual->count())
        <div class="rounded-2xl p-6 sm:p-7 shadow-sm space-y-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
                <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Kriteria Seleksi Terhubung</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Parameter otomatis dan manual yang diset pada job permintaan</p>
            </div>

            @if($jobPosting->permintaan?->requirementsAuto)
                @php $auto = $jobPosting->permintaan->requirementsAuto; @endphp
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-3.5 rounded-xl text-center"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Rentang Usia</p>
                        <p class="text-base font-bold mt-1" style="color: var(--dash-text-heading);">{{ $auto->minimum_age ?? 0 }} - {{ $auto->maximum_age ?? 0 }} tahun</p>
                    </div>
                    <div class="p-3.5 rounded-xl text-center"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Pengalaman</p>
                        <p class="text-base font-bold mt-1" style="color: var(--dash-text-heading);">Min. {{ $auto->minimum_year_experience ?? 0 }} tahun</p>
                    </div>
                    <div class="p-3.5 rounded-xl text-center"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Min. Pendidikan</p>
                        <p class="text-base font-bold mt-1" style="color: var(--dash-text-heading);">{{ strtoupper($auto->minimum_education_level ?? '-') }}</p>
                    </div>
                </div>
            @endif

            @if($jobPosting->permintaan?->requirementsManual->count())
                <div class="pt-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--dash-text-faint);">Kriteria Manual</p>
                    <div class="space-y-2">
                        @foreach($jobPosting->permintaan->requirementsManual as $manual)
                            <div class="p-3 rounded-xl flex items-start gap-2.5 text-xs"
                                 style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                <span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5" style="color: var(--dash-primary);">check_circle</span>
                                <div>
                                    <span class="font-bold" style="color: var(--dash-text-heading);">{{ $manual->criteria_name }}</span>
                                    <span class="ml-1" style="color: var(--dash-text-muted);">- {{ $manual->criteria_desc }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Card 3: Action Panel --}}
    <div class="rounded-2xl p-6 shadow-sm space-y-4"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Tindakan Job Posting</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Ubah status publikasi agar pelamar dapat mengirim lamaran</p>
        </div>

        @if($jobPosting->status === 'draft')
            @can('recruitment.job_posting.publish')
                <div class="flex items-center gap-3">
                    <button type="button" @click="showPublishModal = true"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                            style="background-color: #16a34a;">
                        <span class="material-symbols-outlined text-[16px]">publish</span>
                        Publikasikan Lowongan
                    </button>
                </div>
            @else
                <p class="text-sm" style="color: var(--dash-text-muted);">Draft - lowongan belum dipublikasikan ke portal karir.</p>
            @endcan

        @elseif($jobPosting->status === 'published')
            @can('recruitment.job_posting.update')
                <div class="flex items-center gap-3">
                    <button type="button" @click="showCloseModal = true"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-danger, #dc2626);">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        Tutup Lowongan
                    </button>
                </div>
            @else
                <p class="text-sm" style="color: var(--dash-text-muted);">Aktif - lowongan saat ini terbuka dan menerima lamaran kandidat.</p>
            @endcan

        @elseif($jobPosting->status === 'closed')
            <p class="text-sm" style="color: var(--dash-text-muted);">Lowongan ini sudah ditutup. Tidak menerima lamaran baru.</p>
        @endif
    </div>

    {{-- Modal Publikasi Kustom (Alpine.js, No confirm(), No overflow-hidden) --}}
    <template x-teleport="body">
        <div x-show="showPublishModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showPublishModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showPublishModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showPublishModal" @click.away="showPublishModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950/40 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[22px]">publish</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Publikasikan Lowongan?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Lowongan akan tampil di portal karir publik dan mulai menerima lamaran baru.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('recruitment.postings.publish', $jobPosting) }}" method="POST">
                            @csrf
                            <div class="px-6 py-4 space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                                           style="color: var(--dash-text-faint);">Batas Tanggal Penutupan (opsional)</label>
                                    <input type="date" name="closing_date"
                                           value="{{ $jobPosting->closing_date?->format('Y-m-d') }}"
                                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'" />
                                </div>
                            </div>

                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showPublishModal = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: #16a34a;">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                    Konfirmasi Publikasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Modal Tutup Lowongan Kustom (Alpine.js, No confirm(), No overflow-hidden) --}}
    <template x-teleport="body">
        <div x-show="showCloseModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showCloseModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showCloseModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showCloseModal" @click.away="showCloseModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-950/40 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-[22px]">lock</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Tutup Lowongan Kerja?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Setelah ditutup, kandidat baru tidak dapat lagi mengirimkan lamaran untuk posisi ini.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showCloseModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <form action="{{ route('recruitment.postings.close', $jobPosting) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[16px]">lock</span>
                                    Ya, Tutup Lowongan
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
