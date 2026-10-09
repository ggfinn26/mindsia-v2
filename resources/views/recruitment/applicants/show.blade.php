@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('recruitment.applicants.index') }}" class="text-[color:var(--dash-text-faint)] hover:text-[color:var(--dash-text-body)]">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Profil Pelamar</h1>
        <span class="text-xs px-2 py-0.5 bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-muted)] rounded">Read-only</span>
    </div>

    {{-- Data dasar --}}
    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-6 mb-5">
        <h2 class="text-sm font-semibold text-[color:var(--dash-text-body)] uppercase tracking-wider mb-4">Informasi Dasar</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Nama Lengkap</p>
                <p class="text-sm text-[color:var(--dash-text-heading)] font-medium">{{ $applicant->full_name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Jenis Kelamin</p>
                <p class="text-sm text-[color:var(--dash-text-heading)]">
                    @if($applicant->gender === 'male') Laki-laki
                    @elseif($applicant->gender === 'female') Perempuan
                    @else —
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Email</p>
                <p class="text-sm text-[color:var(--dash-text-heading)]">{{ $applicant->email ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">WhatsApp</p>
                <p class="text-sm text-[color:var(--dash-text-heading)]">{{ $applicant->whatsapp_number }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Tanggal Lahir</p>
                <p class="text-sm text-[color:var(--dash-text-heading)]">{{ $applicant->birth_date?->format('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Kota</p>
                <p class="text-sm text-[color:var(--dash-text-heading)]">{{ $applicant->city ?? '—' }}</p>
            </div>
            @if($applicant->address)
                <div class="sm:col-span-2">
                    <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Alamat</p>
                    <p class="text-sm text-[color:var(--dash-text-heading)]">{{ $applicant->address }}</p>
                </div>
            @endif
        </div>

        @if($applicant->cv_path)
            <div class="mt-4 pt-4 border-t border-[color:var(--dash-card-border)]">
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-2">CV</p>
                <div class="flex items-center gap-2 text-sm text-[color:var(--dash-text-body)] bg-[var(--dash-nav-hover-bg)] rounded-md px-3 py-2 border border-[color:var(--dash-card-border)]">
                    <span class="material-symbols-outlined text-base text-indigo-500">description</span>
                    <span class="font-mono text-xs break-all">{{ $applicant->cv_path }}</span>
                    <span class="text-xs text-[color:var(--dash-text-faint)] ml-auto">(Telegram file_id)</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Pendidikan --}}
    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-6 mb-5">
        <h2 class="text-sm font-semibold text-[color:var(--dash-text-body)] uppercase tracking-wider mb-4">
            Riwayat Pendidikan
            <span class="text-[color:var(--dash-text-faint)] font-normal normal-case ml-1">({{ $applicant->educations->count() }})</span>
        </h2>
        @forelse($applicant->educations->sortByDesc('start_date') as $edu)
            <div class="flex items-start gap-3 {{ !$loop->last ? 'pb-4 mb-4 border-b border-[color:var(--dash-card-border)]' : '' }}">
                <span class="material-symbols-outlined text-blue-400 text-xl mt-0.5">school</span>
                <div>
                    <p class="text-sm font-medium text-[color:var(--dash-text-heading)]">{{ $edu->institution_name }}</p>
                    <p class="text-xs text-[color:var(--dash-text-body)]">{{ strtoupper($edu->education_level) }} — {{ $edu->major }}</p>
                    <p class="text-xs text-[color:var(--dash-text-faint)]">
                        {{ $edu->start_date->format('M Y') }} —
                        {{ $edu->graduation_date ? $edu->graduation_date->format('M Y') : 'Sekarang' }}
                        @if($edu->gpa) · IPK {{ number_format($edu->gpa, 2) }} @endif
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-[color:var(--dash-text-faint)]">Belum ada data pendidikan.</p>
        @endforelse
    </div>

    {{-- Pengalaman Kerja --}}
    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-6 mb-5">
        <h2 class="text-sm font-semibold text-[color:var(--dash-text-body)] uppercase tracking-wider mb-4">
            Pengalaman Kerja
            <span class="text-[color:var(--dash-text-faint)] font-normal normal-case ml-1">({{ $applicant->workExperiences->count() }})</span>
        </h2>
        @forelse($applicant->workExperiences->sortByDesc('start_date') as $work)
            <div class="flex items-start gap-3 {{ !$loop->last ? 'pb-4 mb-4 border-b border-[color:var(--dash-card-border)]' : '' }}">
                <span class="material-symbols-outlined text-green-400 text-xl mt-0.5">work</span>
                <div>
                    <p class="text-sm font-medium text-[color:var(--dash-text-heading)]">{{ $work->position }}</p>
                    <p class="text-xs text-[color:var(--dash-text-body)]">{{ $work->company_name }}</p>
                    <p class="text-xs text-[color:var(--dash-text-faint)]">
                        {{ $work->start_date->format('M Y') }} —
                        {{ $work->end_date ? $work->end_date->format('M Y') : 'Sekarang' }}
                    </p>
                    @if($work->reason_for_leaving)
                        <p class="text-xs text-[color:var(--dash-text-muted)] mt-0.5">Alasan keluar: {{ $work->reason_for_leaving }}</p>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-sm text-[color:var(--dash-text-faint)]">Belum ada data pengalaman kerja.</p>
        @endforelse
    </div>

    {{-- Kursus / Sertifikasi --}}
    @if($applicant->courses->count())
        <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-6 mb-5">
            <h2 class="text-sm font-semibold text-[color:var(--dash-text-body)] uppercase tracking-wider mb-4">
                Kursus / Sertifikasi
                <span class="text-[color:var(--dash-text-faint)] font-normal normal-case ml-1">({{ $applicant->courses->count() }})</span>
            </h2>
            @foreach($applicant->courses as $course)
                <div class="flex items-start gap-3 {{ !$loop->last ? 'pb-4 mb-4 border-b border-[color:var(--dash-card-border)]' : '' }}">
                    <span class="material-symbols-outlined text-yellow-400 text-xl mt-0.5">verified</span>
                    <div>
                        <p class="text-sm font-medium text-[color:var(--dash-text-heading)]">{{ $course->course_name }}</p>
                        <p class="text-xs text-[color:var(--dash-text-body)]">{{ $course->issuer_name }}</p>
                        @if($course->issue_date)
                            <p class="text-xs text-[color:var(--dash-text-faint)]">
                                {{ $course->issue_date->format('M Y') }}
                                @if($course->expiry_date) — {{ $course->expiry_date->format('M Y') }} @endif
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Riwayat Lamaran --}}
    @if($applicant->applications->count())
        <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-6">
            <h2 class="text-sm font-semibold text-[color:var(--dash-text-body)] uppercase tracking-wider mb-4">
                Riwayat Lamaran
                <span class="text-[color:var(--dash-text-faint)] font-normal normal-case ml-1">({{ $applicant->applications->count() }})</span>
            </h2>
            @foreach($applicant->applications->sortByDesc('applied_at') as $app)
                @php
                    $statusColors = [
                        'applied'=>'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]',
                        'screening'=>'bg-yellow-100 text-yellow-700',
                        'interview'=>'bg-blue-100 text-blue-700',
                        'offering'=>'bg-purple-100 text-purple-700',
                        'hired'=>'bg-green-100 text-green-700',
                        'rejected'=>'bg-red-100 text-red-700',
                    ];
                @endphp
                <div class="flex items-center justify-between {{ !$loop->last ? 'pb-3 mb-3 border-b border-[color:var(--dash-card-border)]' : '' }}">
                    <div>
                        <p class="text-sm font-medium text-[color:var(--dash-text-heading)]">{{ $app->posting?->title ?? 'Posting dihapus' }}</p>
                        <p class="text-xs text-[color:var(--dash-text-faint)]">{{ \Carbon\Carbon::parse($app->applied_at)->format('d M Y') }}</p>
                    </div>
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusColors[$app->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' }}">
                        {{ Str::title(str_replace('_', ' ', $app->status)) }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
