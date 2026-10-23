@extends('layouts.dashboard')

@section('title', 'Profil Pelamar')
@section('header_title', 'Profil Pelamar')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('recruitment.applicants.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Pelamar
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Rekrutmen</p>
            <div class="flex items-center gap-3">
                <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                    style="color: var(--dash-text-heading);">Profil Pelamar</h1>
                <span class="text-[11px] px-2.5 py-0.5 rounded-full font-semibold"
                      style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                    Read-only
                </span>
            </div>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Data profil lengkap, rekam jejak pendidikan, riwayat karir, dan aktivitas lamaran.
            </p>
        </div>
    </div>

    {{-- Card 1: Data Dasar --}}
    <div class="rounded-2xl p-6 sm:p-7 shadow-sm space-y-5"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Informasi Pribadi</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Biodata dan identitas kontak pelamar</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Nama Lengkap</p>
                <p class="text-sm font-bold" style="color: var(--dash-text-heading);">{{ $applicant->full_name }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Jenis Kelamin</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">
                    @if($applicant->gender === 'male') Laki-laki
                    @elseif($applicant->gender === 'female') Perempuan
                    @else -
                    @endif
                </p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Email</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $applicant->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Nomor WhatsApp</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $applicant->whatsapp_number ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Tanggal Lahir</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $applicant->birth_date?->format('d M Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Kota Domisili</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $applicant->city ?? '-' }}</p>
            </div>
            @if($applicant->address)
                <div class="sm:col-span-2 lg:col-span-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Alamat Tempat Tinggal</p>
                    <p class="text-sm font-medium leading-relaxed" style="color: var(--dash-text-body);">{{ $applicant->address }}</p>
                </div>
            @endif
        </div>

        @if($applicant->cv_path)
            <div class="pt-4" style="border-top: 1px solid var(--dash-card-border);">
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--dash-text-faint);">Berkas Curriculum Vitae (CV)</p>
                <div class="flex items-center gap-2.5 p-3 rounded-xl text-xs font-mono break-all"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <span class="material-symbols-outlined text-[18px] shrink-0" style="color: var(--dash-primary);">description</span>
                    <span class="flex-1">{{ $applicant->cv_path }}</span>
                    <span class="text-[10px] uppercase font-sans font-semibold px-2 py-0.5 rounded shrink-0"
                          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                        Telegram Storage
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- Card 2: Riwayat Pendidikan --}}
    <div class="rounded-2xl p-6 sm:p-7 shadow-sm space-y-4"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
            <div>
                <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Riwayat Pendidikan</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Latar belakang akademis formal kandidat</p>
            </div>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                  style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                {{ $applicant->educations->count() }} Data
            </span>
        </div>

        <div class="space-y-3">
            @forelse($applicant->educations->sortByDesc('start_date') as $edu)
                <div class="flex items-start gap-3 p-3.5 rounded-xl"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5" style="color: var(--dash-primary);">school</span>
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <h3 class="font-bold text-sm" style="color: var(--dash-text-heading);">{{ $edu->institution_name }}</h3>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full w-fit"
                                  style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-primary-text);">
                                {{ strtoupper($edu->education_level) }}
                            </span>
                        </div>
                        <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Jurusan: {{ $edu->major ?? '-' }}</p>
                        <p class="text-[11px] mt-0.5" style="color: var(--dash-text-faint);">
                            {{ $edu->start_date->format('M Y') }} -
                            {{ $edu->graduation_date ? $edu->graduation_date->format('M Y') : 'Sekarang' }}
                            @if($edu->gpa) &bull; IPK {{ number_format($edu->gpa, 2) }} @endif
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-xs text-center py-4" style="color: var(--dash-text-muted);">Belum ada riwayat pendidikan yang dicantumkan.</p>
            @endforelse
        </div>
    </div>

    {{-- Card 3: Pengalaman Kerja --}}
    <div class="rounded-2xl p-6 sm:p-7 shadow-sm space-y-4"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
            <div>
                <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Pengalaman Kerja</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Riwayat pekerjaan profesional dan organisasi sebelumnya</p>
            </div>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                  style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                {{ $applicant->workExperiences->count() }} Data
            </span>
        </div>

        <div class="space-y-3">
            @forelse($applicant->workExperiences->sortByDesc('start_date') as $work)
                <div class="flex items-start gap-3 p-3.5 rounded-xl"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5" style="color: #16a34a;">work</span>
                    <div class="flex-1">
                        <h3 class="font-bold text-sm" style="color: var(--dash-text-heading);">{{ $work->position }}</h3>
                        <p class="text-xs mt-0.5 font-medium" style="color: var(--dash-primary-text);">{{ $work->company_name }}</p>
                        <p class="text-[11px] mt-0.5" style="color: var(--dash-text-faint);">
                            {{ $work->start_date->format('M Y') }} -
                            {{ $work->end_date ? $work->end_date->format('M Y') : 'Sekarang' }}
                        </p>
                        @if($work->reason_for_leaving)
                            <p class="text-xs mt-1.5 italic" style="color: var(--dash-text-body);">
                                Alasan keluar: {{ $work->reason_for_leaving }}
                            </p>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-xs text-center py-4" style="color: var(--dash-text-muted);">Belum ada pengalaman kerja yang dicantumkan.</p>
            @endforelse
        </div>
    </div>

    {{-- Card 4: Kursus / Sertifikasi --}}
    @if($applicant->courses->count())
        <div class="rounded-2xl p-6 sm:p-7 shadow-sm space-y-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center justify-between pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
                <div>
                    <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Kursus & Sertifikasi</h2>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Pelatihan teknis dan sertifikasi kompetensi</p>
                </div>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                      style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                    {{ $applicant->courses->count() }} Data
                </span>
            </div>

            <div class="space-y-3">
                @foreach($applicant->courses as $course)
                    <div class="flex items-start gap-3 p-3.5 rounded-xl"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5" style="color: #d97706;">verified</span>
                        <div class="flex-1">
                            <h3 class="font-bold text-sm" style="color: var(--dash-text-heading);">{{ $course->course_name }}</h3>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Penerbit: {{ $course->issuer_name }}</p>
                            @if($course->issue_date)
                                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-faint);">
                                    Terbit: {{ $course->issue_date->format('M Y') }}
                                    @if($course->expiry_date) - Berlaku s/d {{ $course->expiry_date->format('M Y') }} @endif
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Card 5: Riwayat Lamaran --}}
    @if($applicant->applications->count())
        <div class="rounded-2xl p-6 sm:p-7 shadow-sm space-y-4"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center justify-between pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
                <div>
                    <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Riwayat Lamaran di MINDSIA</h2>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Seluruh permohonan kerja yang pernah diajukan kandidat ini</p>
                </div>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                      style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                    {{ $applicant->applications->count() }} Lamaran
                </span>
            </div>

            <div class="space-y-3">
                @foreach($applicant->applications->sortByDesc('applied_at') as $app)
                    @php
                        $statusMap = [
                            'applied'   => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-700 dark:text-slate-300', 'label' => 'Applied'],
                            'screening' => ['bg' => 'bg-blue-100 dark:bg-blue-950/40', 'text' => 'text-blue-700 dark:text-blue-400', 'label' => 'Screening'],
                            'interview' => ['bg' => 'bg-amber-100 dark:bg-amber-950/40', 'text' => 'text-amber-700 dark:text-amber-400', 'label' => 'Interview'],
                            'offering'  => ['bg' => 'bg-purple-100 dark:bg-purple-950/40', 'text' => 'text-purple-700 dark:text-purple-400', 'label' => 'Offering'],
                            'hired'     => ['bg' => 'bg-emerald-100 dark:bg-emerald-950/40', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'Hired'],
                            'rejected'  => ['bg' => 'bg-red-100 dark:bg-red-950/40', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'Rejected'],
                        ];
                        $st = $statusMap[$app->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'label' => ucfirst($app->status)];
                    @endphp
                    <div class="flex items-center justify-between p-3.5 rounded-xl"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <div>
                            <p class="font-bold text-sm" style="color: var(--dash-text-heading);">
                                {{ $app->posting?->title ?? 'Posting Lowongan Dihapus' }}
                            </p>
                            <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">
                                Diajukan pada {{ \Carbon\Carbon::parse($app->applied_at)->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $st['bg'] }} {{ $st['text'] }}">
                                {{ $st['label'] }}
                            </span>
                            <a href="{{ route('recruitment.application.show', $app->id) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg transition-colors"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                Lihat
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
