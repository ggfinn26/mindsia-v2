@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto" x-data="{
    showRejectModal: false,
    showPsikotestModal: false,
    showInterviewModal: false,
    showEvalModal: false,
    showOfferingModal: false,
    showNegotiateModal: false,
    showOnboardingModal: false,
    evalScheduleId: null,
    openEval(id) { this.evalScheduleId = id; this.showEvalModal = true; },
}">

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Detail Aplikasi Lowongan</h1>
        <a href="{{ route('recruitment.application.index') }}" class="btn bg-[var(--dash-card-bg)] border-[color:var(--dash-card-border)] hover:border-[color:var(--dash-input-border)] text-[color:var(--dash-text-body)] inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">arrow_back</span>Kembali</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Sidebar: Profil Pelamar --}}
        <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-6 md:col-span-1">
            <h2 class="text-xl font-bold text-[color:var(--dash-text-heading)] mb-4">Profil Pelamar</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-semibold text-[color:var(--dash-text-muted)] mb-1">Nama Lengkap</h3>
                    <p class="text-[color:var(--dash-text-heading)] font-medium">{{ $jobApplication->applicant->full_name ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-[color:var(--dash-text-muted)] mb-1">Email</h3>
                    <p class="text-[color:var(--dash-text-heading)] font-medium">{{ $jobApplication->applicant->email ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-[color:var(--dash-text-muted)] mb-1">WhatsApp</h3>
                    <p class="text-[color:var(--dash-text-heading)] font-medium">
                        {{ $jobApplication->applicant->whatsapp_number ? '+62'.$jobApplication->applicant->whatsapp_number : '-' }}
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-[color:var(--dash-text-muted)] mb-1">Tanggal Lamar</h3>
                    <p class="text-[color:var(--dash-text-heading)] font-medium">
                        {{ $jobApplication->applied_at?->format('d M Y H:i') ?? '-' }}
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-[color:var(--dash-text-muted)] mb-1">Sumber</h3>
                    <p class="text-[color:var(--dash-text-heading)] font-medium capitalize">{{ str_replace('_', ' ', $jobApplication->application_source) }}</p>
                </div>
                @if($jobApplication->applicant?->educations?->count())
                    <div>
                        <h3 class="text-sm font-semibold text-[color:var(--dash-text-muted)] mb-2">Pendidikan</h3>
                        @foreach($jobApplication->applicant->educations as $edu)
                            <p class="text-sm text-[color:var(--dash-text-body)]">{{ $edu->institution_name }} — {{ $edu->major ?? '-' }} ({{ $edu->graduation_year ?? '-' }})</p>
                        @endforeach
                    </div>
                @endif
                @if($jobApplication->applicant?->workExperiences?->count())
                    <div>
                        <h3 class="text-sm font-semibold text-[color:var(--dash-text-muted)] mb-2">Pengalaman Kerja</h3>
                        @foreach($jobApplication->applicant->workExperiences as $exp)
                            <p class="text-sm text-[color:var(--dash-text-body)]">{{ $exp->company_name }} — {{ $exp->position }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Main: Tahapan --}}
        <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-6 md:col-span-2">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold text-[color:var(--dash-text-heading)] mb-1">Progres Recruitment</h2>
                    <p class="text-sm text-[color:var(--dash-text-muted)]">Posisi: <span class="font-semibold text-[color:var(--dash-text-body)]">{{ $jobApplication->posting->title ?? 'Reserve/Manual' }}</span></p>
                </div>
                @php
                    $statusColors = [
                        'applied'   => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-heading)]',
                        'screening' => 'bg-blue-100 text-blue-800',
                        'interview' => 'bg-yellow-100 text-yellow-800',
                        'offering'  => 'bg-purple-100 text-purple-800',
                        'hired'     => 'bg-green-100 text-green-800',
                        'rejected'  => 'bg-red-100 text-red-800',
                    ];
                    $statusColor = $statusColors[$jobApplication->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-heading)]';
                @endphp
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColor }}">
                    {{ Str::title($jobApplication->status) }}
                </span>
            </div>

            {{-- Timeline stages --}}
            <div class="relative border-l-2 border-indigo-200 ml-3 mb-8">
                @forelse($jobApplication->stages as $stage)
                    <div class="mb-6 ml-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 {{ $stage->result === 'rejected' ? 'bg-red-400' : 'bg-indigo-500' }} rounded-full -left-3 ring-4 ring-white"></span>
                        <h3 class="font-bold text-[color:var(--dash-text-heading)] capitalize">{{ str_replace('_', ' ', $stage->stage) }}</h3>
                        <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5">{{ $stage->created_at->format('d M Y H:i') }}</p>
                        @if($stage->notes)
                            <p class="text-sm text-[color:var(--dash-text-body)] mt-1">{{ $stage->notes }}</p>
                        @endif
                    </div>
                @empty
                    <div class="mb-6 ml-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-gray-200 rounded-full -left-3 ring-4 ring-white"></span>
                        <p class="text-sm text-[color:var(--dash-text-muted)]">Belum ada tahapan tercatat.</p>
                    </div>
                @endforelse
            </div>

            {{-- Psikotest info (jika sudah ada) --}}
            @if($jobApplication->psikotest)
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h3 class="font-semibold text-blue-800 mb-2">Psikotest</h3>
                    <p class="text-sm text-blue-700">
                        Tanggal: {{ $jobApplication->psikotest->psikotest_date ? \Carbon\Carbon::parse($jobApplication->psikotest->psikotest_date)->format('d M Y') : '-' }}
                        @if($jobApplication->psikotest->psikotest_link)
                            | <a href="{{ $jobApplication->psikotest->psikotest_link }}" target="_blank" class="underline">Link Tes</a>
                        @endif
                    </p>
                </div>
            @endif

            {{-- Interview schedules (jika ada) --}}
            @if($jobApplication->interviewSchedules->count())
                <div class="mb-6">
                    <h3 class="font-semibold text-[color:var(--dash-text-body)] mb-3">Jadwal Interview</h3>
                    @foreach($jobApplication->interviewSchedules as $schedule)
                        <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200 mb-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-yellow-800">
                                        {{ \Carbon\Carbon::parse($schedule->scheduled_at)->format('d M Y H:i') }}
                                        ({{ Str::title($schedule->interview_type) }})
                                    </p>
                                    @if($schedule->location)
                                        <p class="text-xs text-yellow-700 mt-1">Lokasi: {{ $schedule->location }}</p>
                                    @endif
                                    @if($schedule->meeting_link)
                                        <p class="text-xs text-yellow-700 mt-1">Link: <a href="{{ $schedule->meeting_link }}" target="_blank" class="underline">Meeting</a></p>
                                    @endif
                                    @if($schedule->interviewer)
                                        <p class="text-xs text-yellow-700 mt-1">Interviewer: {{ $schedule->interviewer->full_name }}</p>
                                    @endif
                                </div>
                                @if(!$schedule->evaluation)
                                    <div class="flex gap-2 ml-2">
                                        @can('recruitment.interview.evaluate')
                                            <button type="button" @click="openEval({{ $schedule->id }})" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">Isi Evaluasi</button>
                                        @endcan
                                        @can('recruitment.interview.delete')
                                            <form action="{{ route('recruitment.interviews.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Hapus jadwal interview ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Hapus</button>
                                            </form>
                                        @endcan
                                    </div>
                                @endif
                            </div>
                            @if($schedule->evaluation)
                                <div class="mt-3 pt-3 border-t border-yellow-300">
                                    <p class="text-xs font-semibold text-yellow-800">Evaluasi: Score {{ $schedule->evaluation->total_score }}/20 — {{ Str::title($schedule->evaluation->decision) }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Action buttons --}}
            @if(!in_array($jobApplication->status, ['hired', 'rejected']))
                <div class="mt-6 pt-6 border-t border-[color:var(--dash-card-border)] flex flex-wrap justify-end gap-3">

                    {{-- Reject --}}
                    @can('recruitment.job_application.review')
                        <button type="button" @click="showRejectModal = true" class="btn bg-[#dc2626] hover:opacity-90 text-white">
                            Reject Pelamar
                        </button>
                    @endcan

                    {{-- Advance: applied → psikotest --}}
                    @if($jobApplication->status === 'applied')
                        @can('recruitment.psikotest.create')
                            <button type="button" @click="showPsikotestModal = true" class="btn bg-[var(--dash-primary)] hover:opacity-90 text-white">
                                Mulai Psikotest
                            </button>
                        @endcan
                    @endif

                    {{-- Advance: screening → interview --}}
                    @if($jobApplication->status === 'screening')
                        @can('recruitment.interview.create')
                            <button type="button" @click="showInterviewModal = true" class="btn bg-[var(--dash-primary)] hover:opacity-90 text-white">
                                Jadwalkan Interview
                            </button>
                        @endcan
                    @endif

                    {{-- Interview: sudah ada jadwal, bisa tambah lagi --}}
                    @if($jobApplication->status === 'interview')
                        @can('recruitment.interview.create')
                            <button type="button" @click="showInterviewModal = true" class="btn bg-yellow-500 hover:bg-yellow-600 text-white">
                                + Jadwal Interview
                            </button>
                        @endcan
                        {{-- Buat offering letter jika evaluasi accepted & belum ada letter --}}
                        @if($jobApplication->interviewEvaluation?->decision === 'accepted' && !$jobApplication->offeringLetter)
                            @can('recruitment.offering_letter.create')
                                <button type="button" @click="showOfferingModal = true" class="btn bg-purple-600 hover:bg-purple-700 text-white inline-flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    Buat Offering Letter
                                </button>
                            @endcan
                        @endif
                    @endif

                </div>
            @endif

            {{-- Onboarding trigger (offering=accepted, belum ada onboarding) --}}
            @if($jobApplication->offeringLetter?->status === 'accepted' && !$jobApplication->onboarding)
                @can('recruitment.onboarding.create')
                    <div class="mt-4 flex justify-end">
                        <button type="button" @click="showOnboardingModal = true" class="btn bg-green-600 hover:bg-green-700 text-white inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            Buat Onboarding
                        </button>
                    </div>
                @endcan
            @endif
            @if($jobApplication->onboarding)
                <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-green-800 font-medium">Onboarding #{{ $jobApplication->onboarding->id }} — {{ Str::title($jobApplication->onboarding->status) }}</span>
                    <a href="{{ route('recruitment.onboarding.show', $jobApplication->onboarding) }}" class="text-sm text-green-700 underline">Buka →</a>
                </div>
            @endif

            {{-- Offering Letter section --}}
            @if($jobApplication->offeringLetter)
                @php $letter = $jobApplication->offeringLetter; @endphp
                <div class="mt-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <h3 class="font-semibold text-purple-800 mb-3">Offering Letter</h3>
                    <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                        <div>
                            <span class="text-purple-600 font-medium">Gaji Ditawarkan:</span>
                            <span class="text-[color:var(--dash-text-heading)] ml-1">Rp {{ number_format($letter->offered_salary, 0, ',', '.') }}</span>
                        </div>
                        @if($letter->agreed_salary)
                            <div>
                                <span class="text-purple-600 font-medium">Gaji Disepakati:</span>
                                <span class="text-[color:var(--dash-text-heading)] ml-1">Rp {{ number_format($letter->agreed_salary, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($letter->start_date)
                            <div>
                                <span class="text-purple-600 font-medium">Tanggal Mulai:</span>
                                <span class="text-[color:var(--dash-text-heading)] ml-1">{{ \Carbon\Carbon::parse($letter->start_date)->format('d M Y') }}</span>
                            </div>
                        @endif
                        <div>
                            <span class="text-purple-600 font-medium">Status:</span>
                            <span class="ml-1 px-2 py-0.5 text-xs font-semibold rounded-full
                                {{ $letter->status === 'accepted' ? 'bg-green-100 text-green-800' : ($letter->status === 'declined' ? 'bg-red-100 text-red-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ Str::title($letter->status) }}
                            </span>
                        </div>
                    </div>
                    @if($letter->notes)
                        <p class="text-xs text-purple-700 mb-3">Catatan: {{ $letter->notes }}</p>
                    @endif
                    @if(!in_array($letter->status, ['accepted', 'declined']))
                        <div class="flex flex-wrap gap-2">
                            @can('recruitment.offering_letter.negotiate')
                                <button type="button" @click="showNegotiateModal = true" class="btn btn-sm bg-yellow-500 hover:bg-yellow-600 text-white text-xs">
                                    Catat Negosiasi
                                </button>
                            @endcan
                            @can('recruitment.offering_letter.update')
                                <form action="{{ route('recruitment.offering-letters.accept', $letter) }}" method="POST" onsubmit="return confirm('Konfirmasi penawaran diterima?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-green-600 hover:bg-green-700 text-white text-xs">Terima Penawaran</button>
                                </form>
                                <form action="{{ route('recruitment.offering-letters.decline', $letter) }}" method="POST" onsubmit="return confirm('Penawaran ditolak? Lamaran akan ditutup.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-[#dc2626] hover:opacity-90 text-white text-xs inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">close</span>Tolak Penawaran</button>
                                </form>
                            @endcan
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ── MACRO: modal shell ── color = Tailwind color class prefix (e.g. green, purple) ──────── --}}
    @php
    // input class helper reused across all modals
    $inp = 'w-full border border-[color:var(--dash-card-border)] rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 bg-[var(--dash-nav-hover-bg)]';
    @endphp

    {{-- ── Modal: Buat Onboarding (green) ─────────────────────────────── --}}
    <div x-show="showOnboardingModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div @click="showOnboardingModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div class="relative bg-[var(--dash-card-bg)] rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-green-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2 text-white">
                    <span class="material-symbols-outlined text-xl">work_history</span>
                    <h3 class="text-base font-semibold">Buat Onboarding</h3>
                </div>
                <button type="button" @click="showOnboardingModal = false" class="text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('recruitment.application.onboarding.store', $jobApplication) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Cabang <span class="text-red-500">*</span></label>
                    <select name="branch_id" required class="{{ $inp }}">
                        <option value="">Pilih cabang...</option>
                        @foreach(\App\Models\Branch::orderBy('branch_name')->get() as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Posisi <span class="text-red-500">*</span></label>
                    <select name="position_id" required class="{{ $inp }}">
                        <option value="">Pilih posisi...</option>
                        @foreach(\App\Models\Position::orderBy('position_name')->get() as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->position_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Tipe Karyawan <span class="text-red-500">*</span></label>
                    <select name="employment_type" required class="{{ $inp }}">
                        <option value="">Pilih tipe...</option>
                        <option value="kontrak">Kontrak</option>
                        <option value="tetap">Tetap</option>
                        <option value="magang">Magang</option>
                        <option value="paruh_waktu">Paruh Waktu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" required class="{{ $inp }}">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-[color:var(--dash-card-border)]">
                    <button type="button" @click="showOnboardingModal = false" class="btn border border-[color:var(--dash-card-border)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                    <button type="submit" class="btn bg-green-600 hover:bg-green-700 text-white inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">add</span>Buat Onboarding</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal: Buat Offering Letter (purple) ────────────────────────── --}}
    <div x-show="showOfferingModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div @click="showOfferingModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div class="relative bg-[var(--dash-card-bg)] rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="bg-purple-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2 text-white">
                    <span class="material-symbols-outlined text-xl">description</span>
                    <h3 class="text-base font-semibold">Buat Offering Letter</h3>
                </div>
                <button type="button" @click="showOfferingModal = false" class="text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('recruitment.application.offering-letter.store', $jobApplication) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Gaji Ditawarkan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="offered_salary" min="0" step="1000" required class="{{ $inp }}" placeholder="6.500.000">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Tanggal Mulai Kerja</label>
                    <input type="date" name="start_date" class="{{ $inp }}">
                </div>
                <div x-data="{ meetType: '' }">
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Pertemuan Penandatanganan</label>
                    <select name="meeting_type" x-model="meetType" class="{{ $inp }} mb-2">
                        <option value="">Tidak ada pertemuan</option>
                        <option value="offline">Offline</option>
                        <option value="online">Online</option>
                    </select>
                    <div x-show="meetType === 'offline'" class="space-y-2">
                        <input type="datetime-local" name="meeting_at" class="{{ $inp }}">
                        <input type="text" name="meeting_location" placeholder="Lokasi pertemuan" class="{{ $inp }}">
                    </div>
                    <div x-show="meetType === 'online'" class="space-y-2">
                        <input type="datetime-local" name="meeting_at" class="{{ $inp }}">
                        <input type="url" name="meeting_link" placeholder="https://meet.google.com/..." class="{{ $inp }}">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Catatan</label>
                    <textarea name="notes" rows="2" class="{{ $inp }}"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-[color:var(--dash-card-border)]">
                    <button type="button" @click="showOfferingModal = false" class="btn border border-[color:var(--dash-card-border)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                    <button type="submit" class="btn bg-purple-600 hover:bg-purple-700 text-white inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">add</span>Buat Offering Letter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal: Negosiasi (amber) ─────────────────────────────────────── --}}
    @if($jobApplication->offeringLetter)
    <div x-show="showNegotiateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div @click="showNegotiateModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div class="relative bg-[var(--dash-card-bg)] rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-amber-500 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2 text-white">
                    <span class="material-symbols-outlined text-xl">currency_exchange</span>
                    <h3 class="text-base font-semibold">Catat Negosiasi Gaji</h3>
                </div>
                <button type="button" @click="showNegotiateModal = false" class="text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('recruitment.offering-letters.negotiate', $jobApplication->offeringLetter) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="bg-amber-50 rounded-lg px-4 py-3 text-sm text-amber-800">
                    Gaji ditawarkan: <strong>Rp {{ number_format($jobApplication->offeringLetter->offered_salary, 0, ',', '.') }}</strong>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Gaji Disepakati (Rp)</label>
                    <input type="number" name="agreed_salary" min="0" step="1000"
                        value="{{ $jobApplication->offeringLetter->agreed_salary }}"
                        class="{{ $inp }}" placeholder="{{ $jobApplication->offeringLetter->offered_salary }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Catatan Negosiasi</label>
                    <textarea name="notes" rows="3" class="{{ $inp }}">{{ $jobApplication->offeringLetter->notes }}</textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-[color:var(--dash-card-border)]">
                    <button type="button" @click="showNegotiateModal = false" class="btn border border-[color:var(--dash-card-border)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                    <button type="submit" class="btn bg-amber-500 hover:bg-amber-600 text-white inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">save</span>Simpan Negosiasi</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ── Modal: Reject Pelamar (red) ─────────────────────────────────── --}}
    <div x-show="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div @click="showRejectModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div class="relative bg-[var(--dash-card-bg)] rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-red-500 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2 text-white">
                    <span class="material-symbols-outlined text-xl">cancel</span>
                    <h3 class="text-base font-semibold">Tolak Pelamar</h3>
                </div>
                <button type="button" @click="showRejectModal = false" class="text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('recruitment.application.reject', $jobApplication->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <p class="text-sm text-[color:var(--dash-text-body)]">Penolakan bersifat final. Isi alasan dengan jelas agar bisa dikomunikasikan ke pelamar.</p>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" required class="{{ $inp }} focus:ring-red-300 focus:border-red-400"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-[color:var(--dash-card-border)]">
                    <button type="button" @click="showRejectModal = false" class="btn border border-[color:var(--dash-card-border)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                    <button type="submit" class="btn bg-[#dc2626] hover:opacity-90 text-white inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Tolak Lamaran</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal: Jadwalkan Psikotest (blue) ───────────────────────────── --}}
    <div x-show="showPsikotestModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div @click="showPsikotestModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div class="relative bg-[var(--dash-card-bg)] rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-blue-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2 text-white">
                    <span class="material-symbols-outlined text-xl">psychology</span>
                    <h3 class="text-base font-semibold">Jadwalkan Psikotest</h3>
                </div>
                <button type="button" @click="showPsikotestModal = false" class="text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('recruitment.application.psikotest.store', $jobApplication->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Tanggal Psikotest</label>
                    <input type="date" name="psikotest_date" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Link Tes <span class="text-[color:var(--dash-text-faint)] font-normal normal-case">(opsional)</span></label>
                    <input type="url" name="psikotest_link" placeholder="https://psikotes.link/..." class="{{ $inp }}">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-[color:var(--dash-card-border)]">
                    <button type="button" @click="showPsikotestModal = false" class="btn border border-[color:var(--dash-card-border)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                    <button type="submit" class="btn bg-[var(--dash-primary)] hover:opacity-90 text-white inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">save</span>Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal: Evaluasi Interview (indigo) ──────────────────────────── --}}
    <div x-show="showEvalModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" style="display:none;">
        <div @click="showEvalModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div class="relative bg-[var(--dash-card-bg)] rounded-2xl shadow-2xl w-full max-w-lg my-auto overflow-hidden">
            <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2 text-white">
                    <span class="material-symbols-outlined text-xl">rate_review</span>
                    <h3 class="text-base font-semibold">Evaluasi Interview</h3>
                </div>
                <button type="button" @click="showEvalModal = false" class="text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form method="POST" :action="'{{ url('recruitment/interviews') }}/' + evalScheduleId + '/evaluation'" class="p-6 space-y-4">
                @csrf
                <p class="text-xs text-[color:var(--dash-text-muted)]">Skor 1=Kurang · 2=Cukup · 3=Baik · 4=Sangat Baik &nbsp;|&nbsp; Total: 5–8 Buruk · 9–12 Sedang · 13–17 Bagus · 18–20 Sangat Bagus</p>
                @php
                    $criteria = [
                        'score_education'     => 'Pendidikan & Kompetensi Teknis',
                        'score_experience'    => 'Pengalaman Kerja',
                        'score_personality'   => 'Kepribadian, Penampilan & Tata Krama',
                        'score_communication' => 'Komunikasi & Interpersonal',
                        'score_problem_solving' => 'Problem-solving, Logika & Analitis',
                    ];
                @endphp
                @foreach($criteria as $field => $label)
                    <div>
                        <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">{{ $label }} <span class="text-red-500">*</span></label>
                        <select name="{{ $field }}" required class="{{ $inp }}">
                            <option value="">Pilih skor...</option>
                            <option value="1">1 — Kurang</option>
                            <option value="2">2 — Cukup</option>
                            <option value="3">3 — Baik</option>
                            <option value="4">4 — Sangat Baik</option>
                        </select>
                    </div>
                @endforeach
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Gaji Sekarang</label>
                        <input type="number" name="current_salary" min="0" step="1000" class="{{ $inp }}" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Gaji Diinginkan</label>
                        <input type="number" name="desired_salary" min="0" step="1000" class="{{ $inp }}" placeholder="0">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Kesimpulan <span class="text-red-500">*</span></label>
                    <select name="decision" required class="{{ $inp }}">
                        <option value="">Pilih kesimpulan...</option>
                        <option value="accepted">✅ Diterima</option>
                        <option value="reserve">⏳ Cadangan</option>
                        <option value="rejected">❌ Tidak Diterima</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" class="{{ $inp }}"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-[color:var(--dash-card-border)]">
                    <button type="button" @click="showEvalModal = false" class="btn border border-[color:var(--dash-card-border)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                    <button type="submit" class="btn bg-[var(--dash-primary)] hover:opacity-90 text-white inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">save</span>Simpan Evaluasi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal: Jadwalkan Interview (yellow) ─────────────────────────── --}}
    <div x-show="showInterviewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" style="display:none;">
        <div @click="showInterviewModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div class="relative bg-[var(--dash-card-bg)] rounded-2xl shadow-2xl w-full max-w-md my-auto overflow-hidden">
            <div class="bg-yellow-500 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2 text-white">
                    <span class="material-symbols-outlined text-xl">event_available</span>
                    <h3 class="text-base font-semibold">Jadwalkan Interview</h3>
                </div>
                <button type="button" @click="showInterviewModal = false" class="text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('recruitment.application.interviews.store', $jobApplication->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Tipe Interview <span class="text-red-500">*</span></label>
                    <select name="interview_type" required class="{{ $inp }}" x-model="interviewType" x-init="interviewType = 'offline'">
                        <option value="offline">Offline</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Waktu Interview <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="scheduled_at" required class="{{ $inp }}">
                </div>
                <div x-show="interviewType === 'offline'">
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Lokasi</label>
                    <input type="text" name="location" class="{{ $inp }}" placeholder="Ruang Wawancara, Lt. 2">
                </div>
                <div x-show="interviewType === 'online'">
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Link Meeting</label>
                    <input type="url" name="meeting_link" class="{{ $inp }}" placeholder="https://meet.google.com/...">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Interviewer <span class="text-red-500">*</span></label>
                    <select name="interviewer_employee_id" required class="{{ $inp }}">
                        <option value="">Pilih interviewer...</option>
                        @foreach(\App\Models\Employee::orderBy('full_name')->get() as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1.5">Catatan</label>
                    <textarea name="notes" rows="2" class="{{ $inp }}"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-[color:var(--dash-card-border)]">
                    <button type="button" @click="showInterviewModal = false" class="btn border border-[color:var(--dash-card-border)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                    <button type="submit" class="btn bg-yellow-500 hover:bg-yellow-600 text-white">Jadwalkan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
