@extends('layouts.dashboard')

@php
    $pageTitle = request('status') == 'interview' ? 'Psikotest & Interview' : 'Aplikasi Lowongan';
@endphp

@section('title', $pageTitle)
@section('header_title', $pageTitle)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
        showRejectModal: false,
        rejectActionUrl: '',
        rejectApplicantName: '',
        openReject(url, name) {
            this.rejectActionUrl = url;
            this.rejectApplicantName = name;
            this.showRejectModal = true;
        }
     }">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Rekrutmen</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">{{ $pageTitle }}</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Pantau seluruh lamaran masuk, tahapan seleksi, dan status pipeline kandidat.
            </p>
        </div>

        @can('recruitment.job_application.create')
            <button type="button" @click="$dispatch('open-manual-application-modal')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shrink-0"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Tambah Lamaran Manual
            </button>
        @endcan
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('recruitment.application.index') }}"
          class="p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            @if(request()->has('status') && request('status') == 'interview')
                <input type="hidden" name="status" value="interview">
            @else
                <div class="w-full sm:w-56">
                    <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Status Lamaran</label>
                    <select name="status"
                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Semua Status</option>
                        <option value="applied" @selected(request('status') === 'applied')>Applied</option>
                        <option value="screening" @selected(request('status') === 'screening')>Screening</option>
                        <option value="interview" @selected(request('status') === 'interview')>Interview / Psikotest</option>
                        <option value="offering" @selected(request('status') === 'offering')>Offering</option>
                        <option value="hired" @selected(request('status') === 'hired')>Hired</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                    </select>
                </div>
            @endif

            <div class="w-full sm:w-56">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Sumber Lamaran</label>
                <select name="source"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Sumber</option>
                    <option value="job_posting" @selected(request('source') === 'job_posting')>Portal Karir</option>
                    <option value="referral" @selected(request('source') === 'referral')>Referral</option>
                    <option value="walk_in" @selected(request('source') === 'walk_in')>Walk-In</option>
                    <option value="archive" @selected(request('source') === 'archive')>Archive (Reserve)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    Filter
                </button>
                @if(request()->filled('status') || request()->filled('source'))
                    <a href="{{ route('recruitment.application.index', request()->has('status') && request('status') == 'interview' ? ['status' => 'interview'] : []) }}"
                       class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table Card Fit Full Desktop --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="w-full">
            <table class="w-full text-sm text-left table-fixed">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="w-12 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                            style="color: var(--dash-text-muted);">No</th>
                        <th class="w-2/5 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Pelamar & Kontak</th>
                        <th class="w-1/4 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Posisi & Tanggal</th>
                        <th class="w-1/6 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="w-1/5 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($applications as $application)
                        @php
                            $statusMap = [
                                'applied'   => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-700 dark:text-slate-300', 'label' => 'Applied'],
                                'screening' => ['bg' => 'bg-blue-100 dark:bg-blue-950/40', 'text' => 'text-blue-700 dark:text-blue-400', 'label' => 'Screening'],
                                'interview' => ['bg' => 'bg-amber-100 dark:bg-amber-950/40', 'text' => 'text-amber-700 dark:text-amber-400', 'label' => 'Interview'],
                                'offering'  => ['bg' => 'bg-purple-100 dark:bg-purple-950/40', 'text' => 'text-purple-700 dark:text-purple-400', 'label' => 'Offering'],
                                'hired'     => ['bg' => 'bg-emerald-100 dark:bg-emerald-950/40', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'Hired'],
                                'rejected'  => ['bg' => 'bg-red-100 dark:bg-red-950/40', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'Rejected'],
                            ];
                            $st = $statusMap[$application->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700'];
                            $st['label'] = \App\Support\EnumLabels::get('recruitment_stage', $application->status);
                            $applicantName = $application->applicant->full_name ?? '-';
                            $postingTitle = $application->posting->title ?? 'Reserve / Manual';
                            $sourceName = \App\Support\EnumLabels::get('application_source', $application->application_source);
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3 text-center text-xs" style="color: var(--dash-text-muted);">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('recruitment.application.show', $application->id) }}"
                                   class="font-semibold block hover:underline transition-colors text-xs sm:text-sm truncate"
                                   style="color: var(--dash-primary-text);">
                                    {{ $applicantName }}
                                </a>
                                <p class="text-[11px] mt-0.5 flex items-center gap-1.5 truncate" style="color: var(--dash-text-muted);">
                                    <span>{{ $application->applicant->email ?? '-' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $sourceName }}</span>
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <div class="font-medium truncate" style="color: var(--dash-text-heading);">
                                    {{ $postingTitle }}
                                </div>
                                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">
                                    Lamar: {{ $application->applied_at ? $application->applied_at->format('d M Y H:i') : '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-semibold rounded-full {{ $st['bg'] }} {{ $st['text'] }}">
                                    {{ $st['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('recruitment.application.show', $application->id) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                                        Lihat
                                    </a>

                                    @can('recruitment.job_application.review')
                                        @if(!in_array($application->status, ['rejected', 'hired']))
                                            <button type="button"
                                                    @click="openReject('{{ route('recruitment.application.reject', $application->id) }}', '{{ addslashes($applicantName) }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90"
                                                    style="background-color: var(--dash-danger, #dc2626);">
                                                <span class="material-symbols-outlined text-[14px]">cancel</span>
                                                Tolak
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">person_search</span>
                                <h3 class="font-jakarta text-base font-semibold" style="color: var(--dash-text-heading);">Tidak ada data lamaran ditemukan</h3>
                                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Coba sesuaikan kriteria filter status atau sumber di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Konfirmasi Tolak Lamaran Kustom (Alpine.js, No confirm(), No overflow-hidden) --}}
    <template x-teleport="body">
        <div x-show="showRejectModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showRejectModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showRejectModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showRejectModal" @click.away="showRejectModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-950/40 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-[22px]">cancel</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Tolak Lamaran Kandidat?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Lamaran untuk <span class="font-semibold" style="color: var(--dash-text-heading);" x-text="rejectApplicantName"></span> akan ditandai ditolak.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form :action="rejectActionUrl" method="POST">
                            @csrf
                            <div class="px-6 py-4 space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                                           style="color: var(--dash-text-faint);">Alasan Penolakan <span class="text-red-500">*</span></label>
                                    <textarea name="reason" rows="3" required
                                              placeholder="Tuliskan alasan penolakan secara jelas..."
                                              class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                              onfocus="this.style.borderColor='var(--dash-danger)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-danger) 20%, transparent)'"
                                              onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"></textarea>
                                </div>
                            </div>

                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showRejectModal = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                    Konfirmasi Tolak
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
