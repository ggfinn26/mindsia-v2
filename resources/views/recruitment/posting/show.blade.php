@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto" x-data="{
    showPublishModal: false,
    showCloseModal: false
}">
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('recruitment.postings.index') }}" class="text-[color:var(--dash-text-faint)] hover:text-[color:var(--dash-text-body)]">
                <span class="material-symbols-outlined text-xl">arrow_back</span>
            </a>
            <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Detail Job Posting</h1>
        </div>
        <div class="flex gap-2">
            @if($jobPosting->status === 'draft')
                @can('recruitment.job_posting.update')
                    <a href="{{ route('recruitment.postings.edit', $jobPosting) }}"
                        class="btn bg-[var(--dash-card-bg)] border-[color:var(--dash-card-border)] hover:border-[color:var(--dash-input-border)] text-[color:var(--dash-text-body)] text-sm">Edit</a>
                @endcan
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Status badge --}}
    @php
        $statusColors = ['draft'=>'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-heading)]','published'=>'bg-green-100 text-green-800','closed'=>'bg-red-100 text-red-800'];
        $statusLabels = ['draft'=>'Draft','published'=>'Dipublikasikan','closed'=>'Ditutup'];
    @endphp

    {{-- Main info --}}
    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-6 mb-5">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-semibold text-[color:var(--dash-text-body)]">{{ $jobPosting->title }}</h2>
            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$jobPosting->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-heading)]' }}">
                {{ $statusLabels[$jobPosting->status] ?? $jobPosting->status }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Cabang</p>
                <p class="text-sm text-[color:var(--dash-text-heading)]">{{ $jobPosting->branch?->branch_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Posisi</p>
                <p class="text-sm text-[color:var(--dash-text-heading)]">{{ $jobPosting->position?->position_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Tanggal Publish</p>
                <p class="text-sm text-[color:var(--dash-text-heading)]">{{ $jobPosting->publish_date?->format('d M Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Tanggal Tutup</p>
                <p class="text-sm text-[color:var(--dash-text-heading)]">{{ $jobPosting->closing_date?->format('d M Y') ?? 'Tanpa batas' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Dibuat oleh</p>
                <p class="text-sm text-[color:var(--dash-text-heading)]">{{ $jobPosting->createdBy?->full_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-1">Jumlah Pelamar</p>
                <p class="text-sm text-[color:var(--dash-text-heading)]">{{ $jobPosting->applications->count() }} lamaran</p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-2">Deskripsi Pekerjaan</p>
                <div class="text-sm text-[color:var(--dash-text-heading)] bg-[var(--dash-nav-hover-bg)] rounded-md p-4 border border-[color:var(--dash-card-border)] whitespace-pre-wrap">{{ $jobPosting->job_description }}</div>
            </div>
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-2">Tanggung Jawab</p>
                <div class="text-sm text-[color:var(--dash-text-heading)] bg-[var(--dash-nav-hover-bg)] rounded-md p-4 border border-[color:var(--dash-card-border)] whitespace-pre-wrap">{{ $jobPosting->job_responsibilities }}</div>
            </div>
            <div>
                <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-2">Kualifikasi (publik)</p>
                <div class="text-sm text-[color:var(--dash-text-heading)] bg-[var(--dash-nav-hover-bg)] rounded-md p-4 border border-[color:var(--dash-card-border)] whitespace-pre-wrap">{{ $jobPosting->job_requirements_text }}</div>
            </div>
        </div>
    </div>

    {{-- Requirements dari permintaan --}}
    @if($jobPosting->permintaan?->requirementsAuto || $jobPosting->permintaan?->requirementsManual->count())
        <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-5 mb-5">
            <h2 class="text-sm font-semibold text-[color:var(--dash-text-body)] mb-4">Kriteria Seleksi</h2>

            @if($jobPosting->permintaan?->requirementsAuto)
                @php $auto = $jobPosting->permintaan->requirementsAuto; @endphp
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                    <div class="bg-blue-50 rounded-md p-3 text-center">
                        <p class="text-xs text-blue-600 font-medium">Usia</p>
                        <p class="text-sm font-semibold text-blue-800">{{ $auto->minimum_age }}–{{ $auto->maximum_age }} thn</p>
                    </div>
                    <div class="bg-blue-50 rounded-md p-3 text-center">
                        <p class="text-xs text-blue-600 font-medium">Pengalaman</p>
                        <p class="text-sm font-semibold text-blue-800">Min. {{ $auto->minimum_year_experience }} thn</p>
                    </div>
                    <div class="bg-blue-50 rounded-md p-3 text-center">
                        <p class="text-xs text-blue-600 font-medium">Pendidikan</p>
                        <p class="text-sm font-semibold text-blue-800">{{ strtoupper($auto->minimum_education_level ?? '-') }}</p>
                    </div>
                </div>
            @endif

            @if($jobPosting->permintaan?->requirementsManual->count())
                <div>
                    <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-2">Kriteria Manual</p>
                    <ul class="space-y-2">
                        @foreach($jobPosting->permintaan->requirementsManual as $manual)
                            <li class="text-sm">
                                <span class="font-medium text-[color:var(--dash-text-heading)]">{{ $manual->criteria_name }}</span>
                                <span class="text-[color:var(--dash-text-muted)] ml-2">— {{ $manual->criteria_desc }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- Tindakan --}}
    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-5">
        <h2 class="text-sm font-semibold text-[color:var(--dash-text-body)] mb-4">Tindakan</h2>

        @if($jobPosting->status === 'draft')
            @can('recruitment.job_posting.publish')
                <div x-show="!showPublishModal">
                    <button @click="showPublishModal = true"
                        class="btn bg-green-500 hover:bg-green-600 text-white text-sm">
                        <span class="material-symbols-outlined text-base mr-1">publish</span>
                        Publikasikan
                    </button>
                </div>
                <div x-show="showPublishModal" class="space-y-3">
                    <p class="text-sm text-[color:var(--dash-text-body)]">Set tanggal penutupan (opsional) sebelum publish:</p>
                    <form action="{{ route('recruitment.postings.publish', $jobPosting) }}" method="POST">
                        @csrf
                        <div class="flex gap-3 items-end">
                            <div>
                                <label class="block text-xs font-medium text-[color:var(--dash-text-body)] mb-1">Tanggal Penutupan</label>
                                <input type="date" name="closing_date"
                                    value="{{ $jobPosting->closing_date?->format('Y-m-d') }}"
                                    class="form-input text-sm" />
                            </div>
                            <button type="submit" class="btn bg-green-500 hover:bg-green-600 text-white text-sm"
                                onclick="return confirm('Publikasikan posting ini sekarang?')">
                                Konfirmasi Publikasi
                            </button>
                            <button type="button" @click="showPublishModal = false"
                                class="btn bg-[var(--dash-card-bg)] border-[color:var(--dash-card-border)] text-[color:var(--dash-text-body)] text-sm">Batal</button>
                        </div>
                    </form>
                </div>
            @else
                <p class="text-sm text-[color:var(--dash-text-muted)]">Draft — belum dipublikasikan.</p>
            @endcan

        @elseif($jobPosting->status === 'published')
            @can('recruitment.job_posting.update')
                <form action="{{ route('recruitment.postings.close', $jobPosting) }}" method="POST"
                    onsubmit="return confirm('Tutup lowongan ini? Pelamar baru tidak bisa melamar setelah ditutup.')">
                    @csrf
                    <button type="submit" class="btn bg-red-500 hover:bg-red-600 text-white text-sm">
                        <span class="material-symbols-outlined text-base mr-1">close</span>
                        Tutup Lowongan
                    </button>
                </form>
            @else
                <p class="text-sm text-[color:var(--dash-text-muted)]">Aktif — lowongan terbuka untuk pelamar.</p>
            @endcan

        @elseif($jobPosting->status === 'closed')
            <p class="text-sm text-[color:var(--dash-text-muted)]">Lowongan sudah ditutup. Tidak ada tindakan tersedia.</p>
        @endif
    </div>
</div>
@endsection
