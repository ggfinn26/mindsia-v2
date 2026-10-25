@extends('layouts.dashboard')

@section('title', 'Detail Aplikasi Lowongan')
@section('header_title', 'Detail Aplikasi Lowongan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6"
     x-data="{
        showRejectModal: false,
        showPsikotestModal: false,
        showInterviewModal: false,
        showEvalModal: false,
        showOfferingModal: false,
        showNegotiateModal: false,
        showOnboardingModal: false,
        showConfirmActionModal: false,
        confirmActionUrl: '',
        confirmActionTitle: '',
        confirmActionMessage: '',
        confirmActionMethod: 'POST',
        confirmActionIsDanger: false,
        confirmActionBtnText: 'Konfirmasi',
        evalScheduleId: null,
        interviewType: 'offline',
        meetType: '',
        openEval(id) {
            this.evalScheduleId = id;
            this.showEvalModal = true;
        },
        promptAction(url, title, message, method = 'POST', isDanger = false, btnText = 'Konfirmasi') {
            this.confirmActionUrl = url;
            this.confirmActionTitle = title;
            this.confirmActionMessage = message;
            this.confirmActionMethod = method;
            this.confirmActionIsDanger = isDanger;
            this.confirmActionBtnText = btnText;
            this.showConfirmActionModal = true;
        }
     }">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('recruitment.application.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Lamaran
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Rekrutmen</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Detail Aplikasi Lowongan</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Kelola tahapan seleksi, psikotes, wawancara, negosiasi penawaran, dan onboarding.
        </p>
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Sidebar: Profil Pelamar --}}
        <div class="rounded-2xl p-6 shadow-sm md:col-span-1 space-y-5"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
                <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Profil Pelamar</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Data identitas dan latar belakang kandidat</p>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Nama Lengkap</p>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $jobApplication->applicant->full_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Email</p>
                    <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $jobApplication->applicant->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">WhatsApp</p>
                    <p class="text-sm font-medium" style="color: var(--dash-text-heading);">
                        {{ $jobApplication->applicant->whatsapp_number ? '+62'.$jobApplication->applicant->whatsapp_number : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Tanggal Melamar</p>
                    <p class="text-sm font-medium" style="color: var(--dash-text-heading);">
                        {{ $jobApplication->applied_at?->format('d M Y H:i') ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Sumber Lamaran</p>
                    <p class="text-sm font-medium capitalize" style="color: var(--dash-text-heading);">
                        {{ \App\Support\EnumLabels::get('application_source', $jobApplication->application_source) }}
                    </p>
                </div>

                @if($jobApplication->applicant?->educations?->count())
                    <div class="pt-3" style="border-top: 1px solid var(--dash-card-border);">
                        <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--dash-text-faint);">Pendidikan</p>
                        <div class="space-y-2">
                            @foreach($jobApplication->applicant->educations as $edu)
                                <div class="text-xs p-2.5 rounded-lg"
                                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                    <p class="font-bold" style="color: var(--dash-text-heading);">{{ $edu->institution_name }}</p>
                                    <p class="mt-0.5" style="color: var(--dash-text-muted);">{{ $edu->major ?? '-' }} ({{ $edu->graduation_year ?? '-' }})</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($jobApplication->applicant?->workExperiences?->count())
                    <div class="pt-3" style="border-top: 1px solid var(--dash-card-border);">
                        <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--dash-text-faint);">Pengalaman Kerja</p>
                        <div class="space-y-2">
                            @foreach($jobApplication->applicant->workExperiences as $exp)
                                <div class="text-xs p-2.5 rounded-lg"
                                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                    <p class="font-bold" style="color: var(--dash-text-heading);">{{ $exp->company_name }}</p>
                                    <p class="mt-0.5" style="color: var(--dash-text-muted);">{{ $exp->position }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Main: Tahapan Rekrutmen --}}
        <div class="rounded-2xl p-6 shadow-sm md:col-span-2 space-y-6"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            @php
                $statusMap = [
                    'applied'   => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-700 dark:text-slate-300', 'label' => 'Applied'],
                    'screening' => ['bg' => 'bg-blue-100 dark:bg-blue-950/40', 'text' => 'text-blue-700 dark:text-blue-400', 'label' => 'Screening'],
                    'interview' => ['bg' => 'bg-amber-100 dark:bg-amber-950/40', 'text' => 'text-amber-700 dark:text-amber-400', 'label' => 'Interview'],
                    'offering'  => ['bg' => 'bg-purple-100 dark:bg-purple-950/40', 'text' => 'text-purple-700 dark:text-purple-400', 'label' => 'Offering'],
                    'hired'     => ['bg' => 'bg-emerald-100 dark:bg-emerald-950/40', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'Hired'],
                    'rejected'  => ['bg' => 'bg-red-100 dark:bg-red-950/40', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'Rejected'],
                ];
                $st = $statusMap[$jobApplication->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700'];
                $st['label'] = \App\Support\EnumLabels::get('recruitment_stage', $jobApplication->status);
            @endphp

            <div class="flex items-start justify-between pb-4" style="border-bottom: 1px solid var(--dash-card-border);">
                <div>
                    <h2 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Progres Rekrutmen</h2>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                        Posisi: <span class="font-semibold" style="color: var(--dash-primary-text);">{{ $jobApplication->posting->title ?? 'Reserve / Manual' }}</span>
                    </p>
                </div>
                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $st['bg'] }} {{ $st['text'] }}">
                    {{ $st['label'] }}
                </span>
            </div>

            {{-- Timeline Stages --}}
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-3" style="color: var(--dash-text-faint);">Jejak Tahapan Seleksi</p>
                <div class="relative pl-6 space-y-4" style="border-left: 2px solid var(--dash-card-border);">
                    @forelse($jobApplication->stages as $stage)
                        @php
                            $isStageRejected = $stage->result === 'rejected';
                        @endphp
                        <div class="relative">
                            <span class="absolute -left-[31px] top-0.5 flex items-center justify-center w-5 h-5 rounded-full"
                                  style="background-color: {{ $isStageRejected ? 'var(--dash-danger, #dc2626)' : 'var(--dash-primary)' }}; color: white;">
                                <span class="material-symbols-outlined text-[12px]">{{ $isStageRejected ? 'close' : 'check' }}</span>
                            </span>
                            <h3 class="font-semibold text-sm capitalize" style="color: var(--dash-text-heading);">
                                {{ \App\Support\EnumLabels::get('recruitment_stage', $stage->stage) }}
                            </h3>
                            <p class="text-[11px] mt-0.5" style="color: var(--dash-text-faint);">{{ $stage->created_at->format('d M Y H:i') }}</p>
                            @if($stage->notes)
                                <p class="text-xs mt-1 p-2 rounded-lg"
                                   style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body);">
                                    {{ $stage->notes }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="relative">
                            <span class="absolute -left-[31px] top-0.5 flex items-center justify-center w-5 h-5 rounded-full"
                                  style="background-color: var(--dash-card-border); color: var(--dash-text-muted);">
                                <span class="material-symbols-outlined text-[12px]">hourglass_empty</span>
                            </span>
                            <p class="text-xs" style="color: var(--dash-text-muted);">Belum ada tahapan seleksi tercatat.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Psikotest Info --}}
            @if($jobApplication->psikotest)
                <div class="p-4 rounded-xl"
                     style="background-color: color-mix(in srgb, var(--dash-primary) 8%, transparent); border: 1px solid color-mix(in srgb, var(--dash-primary) 20%, transparent);">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">psychology</span>
                        <h3 class="font-bold text-sm" style="color: var(--dash-primary-text);">Psikotest Terjadwal</h3>
                    </div>
                    <p class="text-xs" style="color: var(--dash-text-body);">
                        Tanggal: <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $jobApplication->psikotest->psikotest_date ? \Carbon\Carbon::parse($jobApplication->psikotest->psikotest_date)->format('d M Y') : '-' }}</span>
                        @if($jobApplication->psikotest->psikotest_link)
                            &bull; <a href="{{ $jobApplication->psikotest->psikotest_link }}" target="_blank" class="underline font-semibold" style="color: var(--dash-primary-text);">Buka Tautan Tes</a>
                        @endif
                    </p>
                </div>
            @endif

            {{-- Jadwal Interview --}}
            @if($jobApplication->interviewSchedules->count())
                <div class="space-y-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Jadwal Interview</p>
                    @foreach($jobApplication->interviewSchedules as $schedule)
                        <div class="p-4 rounded-xl space-y-2"
                             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-1.5 font-bold text-sm" style="color: var(--dash-text-heading);">
                                        <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary);">event</span>
                                        <span>{{ \Carbon\Carbon::parse($schedule->scheduled_at)->format('d M Y H:i') }}</span>
                                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                            {{ Str::title($schedule->interview_type) }}
                                        </span>
                                    </div>
                                    @if($schedule->location)
                                        <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Lokasi: {{ $schedule->location }}</p>
                                    @endif
                                    @if($schedule->meeting_link)
                                        <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                                            Tautan: <a href="{{ $schedule->meeting_link }}" target="_blank" class="underline font-medium" style="color: var(--dash-primary-text);">Meeting Link</a>
                                        </p>
                                    @endif
                                    @if($schedule->interviewer)
                                        <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Interviewer: {{ $schedule->interviewer->full_name }}</p>
                                    @endif
                                </div>

                                @if(!$schedule->evaluation)
                                    <div class="flex items-center gap-2 shrink-0">
                                        @can('recruitment.interview.evaluate')
                                            <button type="button" @click="openEval({{ $schedule->id }})"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                                                    style="background-color: var(--dash-primary);">
                                                <span class="material-symbols-outlined text-[14px]">rate_review</span>
                                                Isi Evaluasi
                                            </button>
                                        @endcan
                                        @can('recruitment.interview.delete')
                                            <button type="button"
                                                    @click="promptAction('{{ route('recruitment.interviews.destroy', $schedule) }}', 'Hapus Jadwal Interview?', 'Jadwal interview ini akan dibatalkan dan dihapus.', 'DELETE', true, 'Ya, Hapus')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90"
                                                    style="background-color: var(--dash-danger, #dc2626);">
                                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                                Hapus
                                            </button>
                                        @endcan
                                    </div>
                                @endif
                            </div>

                            @if($schedule->evaluation)
                                <div class="pt-2.5 mt-2.5 text-xs flex items-center justify-between"
                                     style="border-top: 1px solid var(--dash-card-border);">
                                    <span class="font-medium" style="color: var(--dash-text-muted);">
                                        Hasil Evaluasi: Skor {{ $schedule->evaluation->total_score }}/20
                                    </span>
                                    <span class="font-bold uppercase px-2 py-0.5 rounded-full {{ $schedule->evaluation->decision === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($schedule->evaluation->decision === 'reserve' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $schedule->evaluation->decision }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Offering Letter Section --}}
            @if($jobApplication->offeringLetter)
                @php $letter = $jobApplication->offeringLetter; @endphp
                <div class="p-4 rounded-xl space-y-3"
                     style="background-color: color-mix(in srgb, #9333ea 8%, transparent); border: 1px solid color-mix(in srgb, #9333ea 20%, transparent);">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]" style="color: #9333ea;">description</span>
                            <h3 class="font-bold text-sm" style="color: #9333ea;">Offering Letter</h3>
                        </div>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full
                            {{ $letter->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($letter->status === 'declined' ? 'bg-red-100 text-red-800' : 'bg-purple-100 text-purple-800') }}">
                            {{ Str::title($letter->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <span style="color: var(--dash-text-muted);">Gaji Ditawarkan:</span>
                            <span class="font-bold ml-1" style="color: var(--dash-text-heading);">Rp {{ number_format($letter->offered_salary, 0, ',', '.') }}</span>
                        </div>
                        @if($letter->agreed_salary)
                            <div>
                                <span style="color: var(--dash-text-muted);">Gaji Disepakati:</span>
                                <span class="font-bold ml-1" style="color: var(--dash-text-heading);">Rp {{ number_format($letter->agreed_salary, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($letter->start_date)
                            <div>
                                <span style="color: var(--dash-text-muted);">Tanggal Mulai Kerja:</span>
                                <span class="font-bold ml-1" style="color: var(--dash-text-heading);">{{ \Carbon\Carbon::parse($letter->start_date)->format('d M Y') }}</span>
                            </div>
                        @endif
                    </div>

                    @if($letter->notes)
                        <p class="text-xs italic" style="color: var(--dash-text-body);">Catatan: {{ $letter->notes }}</p>
                    @endif

                    @if(!in_array($letter->status, ['accepted', 'declined']))
                        <div class="pt-2 flex flex-wrap gap-2" style="border-top: 1px solid color-mix(in srgb, #9333ea 20%, transparent);">
                            @can('recruitment.offering_letter.negotiate')
                                <button type="button" @click="showNegotiateModal = true"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">currency_exchange</span>
                                    Catat Negosiasi
                                </button>
                            @endcan
                            @can('recruitment.offering_letter.update')
                                <button type="button"
                                        @click="promptAction('{{ route('recruitment.offering-letters.accept', $letter) }}', 'Terima Penawaran?', 'Kandidat menyetujui surat penawaran kerja ini.', 'POST', false, 'Konfirmasi Terima')"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90"
                                        style="background-color: #16a34a;">
                                    <span class="material-symbols-outlined text-[14px]">check</span>
                                    Terima Penawaran
                                </button>
                                <button type="button"
                                        @click="promptAction('{{ route('recruitment.offering-letters.decline', $letter) }}', 'Tolak Penawaran?', 'Kandidat menolak penawaran kerja ini. Status lamaran akan ditutup.', 'POST', true, 'Tolak Penawaran')"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[14px]">close</span>
                                    Tolak Penawaran
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            @endif

            {{-- Onboarding Link --}}
            @if($jobApplication->onboarding)
                <div class="p-3.5 rounded-xl flex items-center justify-between"
                     style="background-color: color-mix(in srgb, #16a34a 10%, transparent); border: 1px solid color-mix(in srgb, #16a34a 25%, transparent);">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]" style="color: #16a34a;">work_history</span>
                        <span class="text-xs font-bold" style="color: #15803d;">
                            Onboarding #{{ $jobApplication->onboarding->id }} - {{ Str::title($jobApplication->onboarding->status) }}
                        </span>
                    </div>
                    <a href="{{ route('recruitment.onboarding.show', $jobApplication->onboarding) }}"
                       class="inline-flex items-center gap-1 text-xs font-bold underline"
                       style="color: #15803d;">
                        Buka Onboarding <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
            @endif

            {{-- Action Buttons Row --}}
            @if(!in_array($jobApplication->status, ['hired', 'rejected']))
                <div class="pt-5 flex flex-wrap items-center justify-end gap-3"
                     style="border-top: 1px solid var(--dash-card-border);">
                    @can('recruitment.job_application.review')
                        <button type="button" @click="showRejectModal = true"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-danger, #dc2626);">
                            <span class="material-symbols-outlined text-[14px]">cancel</span>
                            Tolak Pelamar
                        </button>
                    @endcan

                    @if($jobApplication->status === 'applied')
                        @can('recruitment.psikotest.create')
                            <button type="button" @click="showPsikotestModal = true"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[14px]">psychology</span>
                                Jadwalkan Psikotest
                            </button>
                        @endcan
                    @endif

                    @if($jobApplication->status === 'screening')
                        @can('recruitment.interview.create')
                            <button type="button" @click="showInterviewModal = true"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[14px]">event</span>
                                Jadwalkan Interview
                            </button>
                        @endcan
                    @endif

                    @if($jobApplication->status === 'interview')
                        @can('recruitment.interview.create')
                            <button type="button" @click="showInterviewModal = true"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors"
                                    style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">add</span>
                                + Jadwal Interview
                            </button>
                        @endcan

                        @if($jobApplication->interviewEvaluation?->decision === 'accepted' && !$jobApplication->offeringLetter)
                            @can('recruitment.offering_letter.create')
                                <button type="button" @click="showOfferingModal = true"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90"
                                        style="background-color: #9333ea;">
                                    <span class="material-symbols-outlined text-[14px]">description</span>
                                    Buat Offering Letter
                                </button>
                            @endcan
                        @endif
                    @endif

                    @if($jobApplication->offeringLetter?->status === 'accepted' && !$jobApplication->onboarding)
                        @can('recruitment.onboarding.create')
                            <button type="button" @click="showOnboardingModal = true"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90"
                                    style="background-color: #16a34a;">
                                <span class="material-symbols-outlined text-[14px]">work_history</span>
                                Buat Onboarding
                            </button>
                        @endcan
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ── Modal: Buat Onboarding ── --}}
    <template x-teleport="body">
        <div x-show="showOnboardingModal" style="display:none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showOnboardingModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showOnboardingModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showOnboardingModal" @click.away="showOnboardingModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px]" style="color: #16a34a;">work_history</span>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Buat Onboarding Baru</h3>
                                </div>
                                <button type="button" @click="showOnboardingModal = false" class="text-xs p-1 rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('recruitment.application.onboarding.store', $jobApplication) }}" method="POST">
                            @csrf
                            <div class="px-6 py-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Cabang Penempatan <span class="text-red-500">*</span></label>
                                    <select name="branch_id" required
                                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'"
                                            onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <option value="">Pilih Cabang</option>
                                        @foreach(\App\Models\Branch::orderBy('branch_name')->get() as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Posisi Karyawan <span class="text-red-500">*</span></label>
                                    <select name="position_id" required
                                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'"
                                            onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <option value="">Pilih Posisi</option>
                                        @foreach(\App\Models\Position::orderBy('position_name')->get() as $pos)
                                            <option value="{{ $pos->id }}">{{ $pos->position_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Tipe Karyawan <span class="text-red-500">*</span></label>
                                    <select name="employment_type" required
                                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'"
                                            onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <option value="">Pilih Tipe</option>
                                        <option value="kontrak">Kontrak</option>
                                        <option value="tetap">Tetap</option>
                                        <option value="magang">Magang</option>
                                        <option value="paruh_waktu">Paruh Waktu</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Tanggal Mulai Kerja <span class="text-red-500">*</span></label>
                                    <input type="date" name="start_date" required
                                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'">
                                </div>
                            </div>

                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showOnboardingModal = false"
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
                                    Buat Onboarding
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ── Modal: Buat Offering Letter ── --}}
    <template x-teleport="body">
        <div x-show="showOfferingModal" style="display:none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showOfferingModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showOfferingModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showOfferingModal" @click.away="showOfferingModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px]" style="color: #9333ea;">description</span>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Buat Offering Letter</h3>
                                </div>
                                <button type="button" @click="showOfferingModal = false" class="text-xs p-1 rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('recruitment.application.offering-letter.store', $jobApplication) }}" method="POST">
                            @csrf
                            <div class="px-6 py-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Gaji Ditawarkan (Rp) <span class="text-red-500">*</span></label>
                                    <input type="number" name="offered_salary" min="0" step="1000" required
                                           placeholder="Contoh: 6500000"
                                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Tanggal Mulai Kerja</label>
                                    <input type="date" name="start_date"
                                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Pertemuan Penandatanganan</label>
                                    <select name="meeting_type" x-model="meetType"
                                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all mb-2"
                                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'"
                                            onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <option value="">Tidak ada pertemuan</option>
                                        <option value="offline">Offline (Tatap Muka)</option>
                                        <option value="online">Online (Video Call)</option>
                                    </select>
                                    <div x-show="meetType === 'offline'" class="space-y-2 mt-2">
                                        <input type="datetime-local" name="meeting_at"
                                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                                        <input type="text" name="meeting_location" placeholder="Lokasi pertemuan..."
                                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                                    </div>
                                    <div x-show="meetType === 'online'" class="space-y-2 mt-2">
                                        <input type="datetime-local" name="meeting_at"
                                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                                        <input type="url" name="meeting_link" placeholder="https://meet.google.com/..."
                                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Catatan Penawaran</label>
                                    <textarea name="notes" rows="2"
                                              class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"></textarea>
                                </div>
                            </div>

                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showOfferingModal = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: #9333ea;">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                    Buat Offering Letter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ── Modal: Negosiasi Gaji ── --}}
    @if($jobApplication->offeringLetter)
    <template x-teleport="body">
        <div x-show="showNegotiateModal" style="display:none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showNegotiateModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showNegotiateModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showNegotiateModal" @click.away="showNegotiateModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px]" style="color: #d97706;">currency_exchange</span>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Catat Negosiasi Gaji</h3>
                                </div>
                                <button type="button" @click="showNegotiateModal = false" class="text-xs p-1 rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('recruitment.offering-letters.negotiate', $jobApplication->offeringLetter) }}" method="POST">
                            @csrf
                            <div class="px-6 py-4 space-y-4">
                                <div class="p-3 rounded-xl text-xs"
                                     style="background-color: color-mix(in srgb, #d97706 10%, transparent); border: 1px solid color-mix(in srgb, #d97706 25%, transparent); color: #b45309;">
                                    Gaji awal ditawarkan: <span class="font-bold">Rp {{ number_format($jobApplication->offeringLetter->offered_salary, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Gaji Disepakati (Rp)</label>
                                    <input type="number" name="agreed_salary" min="0" step="1000"
                                           value="{{ $jobApplication->offeringLetter->agreed_salary }}"
                                           placeholder="{{ $jobApplication->offeringLetter->offered_salary }}"
                                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Catatan Negosiasi</label>
                                    <textarea name="notes" rows="3"
                                              class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">{{ $jobApplication->offeringLetter->notes }}</textarea>
                                </div>
                            </div>

                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showNegotiateModal = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: #d97706;">
                                    <span class="material-symbols-outlined text-[16px]">save</span>
                                    Simpan Negosiasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
    @endif

    {{-- ── Modal: Tolak Pelamar ── --}}
    <template x-teleport="body">
        <div x-show="showRejectModal" style="display:none;" class="relative z-50" role="dialog" aria-modal="true">
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
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Tolak Pelamar?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Penolakan bersifat final. Isi alasan penolakan secara jelas.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('recruitment.application.reject', $jobApplication->id) }}" method="POST">
                            @csrf
                            <div class="px-6 py-4 space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Alasan Penolakan <span class="text-red-500">*</span></label>
                                    <textarea name="reason" rows="3" required
                                              placeholder="Tuliskan alasan penolakan..."
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
                                    Tolak Lamaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ── Modal: Jadwalkan Psikotest ── --}}
    <template x-teleport="body">
        <div x-show="showPsikotestModal" style="display:none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showPsikotestModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showPsikotestModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showPsikotestModal" @click.away="showPsikotestModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">psychology</span>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Jadwalkan Psikotest</h3>
                                </div>
                                <button type="button" @click="showPsikotestModal = false" class="text-xs p-1 rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('recruitment.application.psikotest.store', $jobApplication->id) }}" method="POST">
                            @csrf
                            <div class="px-6 py-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Tanggal Psikotest</label>
                                    <input type="date" name="psikotest_date"
                                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Tautan Tes (opsional)</label>
                                    <input type="url" name="psikotest_link" placeholder="https://..."
                                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'">
                                </div>
                            </div>

                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showPsikotestModal = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">save</span>
                                    Simpan Jadwal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ── Modal: Evaluasi Interview ── --}}
    <template x-teleport="body">
        <div x-show="showEvalModal" style="display:none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showEvalModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showEvalModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showEvalModal" @click.away="showEvalModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">rate_review</span>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Evaluasi Hasil Interview</h3>
                                </div>
                                <button type="button" @click="showEvalModal = false" class="text-xs p-1 rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                        </div>

                        <form method="POST" :action="'{{ url('recruitment/interviews') }}/' + evalScheduleId + '/evaluation'">
                            @csrf
                            <div class="px-6 py-4 space-y-4 max-h-[65vh] overflow-y-auto">
                                <p class="text-xs p-2.5 rounded-lg"
                                   style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                                    Skor: 1=Kurang, 2=Cukup, 3=Baik, 4=Sangat Baik &bull; Total: 5-8 Buruk, 9-12 Sedang, 13-17 Bagus, 18-20 Sangat Bagus.
                                </p>

                                @php
                                    $criteria = [
                                        'score_education'       => 'Pendidikan & Kompetensi Teknis',
                                        'score_experience'      => 'Pengalaman Kerja',
                                        'score_personality'     => 'Kepribadian & Tata Krama',
                                        'score_communication'   => 'Komunikasi & Interpersonal',
                                        'score_problem_solving' => 'Problem-solving & Analitis',
                                    ];
                                @endphp
                                @foreach($criteria as $field => $label)
                                    <div>
                                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">{{ $label }} <span class="text-red-500">*</span></label>
                                        <select name="{{ $field }}" required
                                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                                onblur="this.style.borderColor='var(--dash-input-border)'">
                                            <option value="">Pilih Skor</option>
                                            <option value="1">1 - Kurang</option>
                                            <option value="2">2 - Cukup</option>
                                            <option value="3">3 - Baik</option>
                                            <option value="4">4 - Sangat Baik</option>
                                        </select>
                                    </div>
                                @endforeach

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Gaji Sekarang (Rp)</label>
                                        <input type="number" name="current_salary" min="0" step="1000"
                                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Gaji Diinginkan (Rp)</label>
                                        <input type="number" name="desired_salary" min="0" step="1000"
                                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Kesimpulan <span class="text-red-500">*</span></label>
                                    <select name="decision" required
                                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'"
                                            onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <option value="">Pilih Kesimpulan</option>
                                        <option value="accepted">Diterima</option>
                                        <option value="reserve">Cadangan</option>
                                        <option value="rejected">Tidak Diterima</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Catatan Tambahan</label>
                                    <textarea name="notes" rows="2"
                                              class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"></textarea>
                                </div>
                            </div>

                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showEvalModal = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">save</span>
                                    Simpan Evaluasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ── Modal: Jadwalkan Interview ── --}}
    <template x-teleport="body">
        <div x-show="showInterviewModal" style="display:none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showInterviewModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showInterviewModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showInterviewModal" @click.away="showInterviewModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">event</span>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Jadwalkan Interview</h3>
                                </div>
                                <button type="button" @click="showInterviewModal = false" class="text-xs p-1 rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('recruitment.application.interviews.store', $jobApplication->id) }}" method="POST">
                            @csrf
                            <div class="px-6 py-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Tipe Interview <span class="text-red-500">*</span></label>
                                    <select name="interview_type" required x-model="interviewType"
                                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'"
                                            onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <option value="offline">Offline (Tatap Muka)</option>
                                        <option value="online">Online (Video Call)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Waktu Interview <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" name="scheduled_at" required
                                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'">
                                </div>
                                <div x-show="interviewType === 'offline'">
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Lokasi</label>
                                    <input type="text" name="location" placeholder="Ruang Wawancara..."
                                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                                </div>
                                <div x-show="interviewType === 'online'">
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Tautan Meeting</label>
                                    <input type="url" name="meeting_link" placeholder="https://meet.google.com/..."
                                           class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Interviewer <span class="text-red-500">*</span></label>
                                    <select name="interviewer_employee_id" required
                                            class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'"
                                            onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <option value="">Pilih Interviewer</option>
                                        @foreach(\App\Models\Employee::orderBy('full_name')->get() as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-faint);">Catatan</label>
                                    <textarea name="notes" rows="2"
                                              class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"></textarea>
                                </div>
                            </div>

                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showInterviewModal = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">save</span>
                                    Simpan Jadwal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ── Modal Konfirmasi Aksi Generic (Zero Browser Confirm) ── --}}
    <template x-teleport="body">
        <div x-show="showConfirmActionModal" style="display:none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showConfirmActionModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showConfirmActionModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showConfirmActionModal" @click.away="showConfirmActionModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                     :class="confirmActionIsDanger ? 'bg-red-100 dark:bg-red-950/40' : 'bg-blue-100 dark:bg-blue-950/40'">
                                    <span class="material-symbols-outlined text-[22px]"
                                          :class="confirmActionIsDanger ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400'"
                                          x-text="confirmActionIsDanger ? 'warning' : 'help_outline'"></span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);" x-text="confirmActionTitle"></h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);" x-text="confirmActionMessage"></p>
                                </div>
                            </div>
                        </div>

                        <form :action="confirmActionUrl" method="POST">
                            @csrf
                            <input type="hidden" name="_method" :value="confirmActionMethod">
                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showConfirmActionModal = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        :style="confirmActionIsDanger ? 'background-color: var(--dash-danger, #dc2626);' : 'background-color: var(--dash-primary);'">
                                    <span class="material-symbols-outlined text-[16px]" x-text="confirmActionIsDanger ? 'close' : 'check'"></span>
                                    <span x-text="confirmActionBtnText"></span>
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
