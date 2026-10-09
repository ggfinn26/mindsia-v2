@extends('layouts.dashboard')

@section('title', 'Cari Lowongan - MINDSIA')
@section('header_title', 'Cari Lowongan')

@section('content')
<h1 class="sr-only">Cari Lowongan</h1>
<div class="max-w-4xl mx-auto space-y-4">

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    @forelse($postings as $posting)
        @php $alreadyApplied = $appliedPostingIds->contains($posting->id); @endphp
        <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[color:var(--dash-card-border)] shadow-sm px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <h2 class="font-bold text-[color:var(--dash-text-heading)] font-jakarta text-base">{{ $posting->title }}</h2>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                        @if($posting->position)
                            <span class="text-xs text-[color:var(--dash-text-muted)] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">badge</span>
                                {{ $posting->position->name }}
                            </span>
                        @endif
                        @if($posting->branch)
                            <span class="text-xs text-[color:var(--dash-text-muted)] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">location_on</span>
                                {{ $posting->branch->name }}
                            </span>
                        @endif
                        @if($posting->closing_date)
                            <span class="text-xs text-[color:var(--dash-text-muted)] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">calendar_today</span>
                                Tutup {{ $posting->closing_date->format('d M Y') }}
                            </span>
                        @endif
                    </div>

                    @if($posting->job_description)
                        <p class="text-sm text-[color:var(--dash-text-body)] mt-3 line-clamp-2">{{ $posting->job_description }}</p>
                    @endif
                </div>

                <div class="shrink-0">
                    @if($alreadyApplied)
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 rounded-xl">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                            Sudah Melamar
                        </span>
                    @else
                        <form action="{{ route('applicant.apply', $posting) }}" method="POST"
                              onsubmit="return confirm('Lamar posisi {{ $posting->title }}?')">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-[var(--dash-primary)] hover:opacity-90 rounded-lg transition-colors shadow-sm">
                                <span class="material-symbols-outlined text-[14px]">send</span>
                                Lamar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[color:var(--dash-card-border)] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-gray-300 text-[48px]">work_off</span>
            <p class="text-sm text-[color:var(--dash-text-faint)] mt-3">Belum ada lowongan yang tersedia saat ini.</p>
        </div>
    @endforelse

    <div class="pt-2">
        {{ $postings->links() }}
    </div>

</div>
@endsection
