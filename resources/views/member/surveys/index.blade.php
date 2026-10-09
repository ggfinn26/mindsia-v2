@extends('layouts.dashboard')

@section('title', 'Survey Saya - MINDSIA')
@section('header_title', 'Survey Saya')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Survey Saya</h1>
            <p class="text-sm text-[color:var(--dash-text-muted)] mt-1">Daftar survei yang ditugaskan kepada Anda untuk evaluasi program.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if($surveys->isEmpty())
        <!-- Placeholder State -->
        <div class="flex flex-col items-center justify-center rounded-2xl border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] py-16 px-6 shadow-sm text-center">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-[var(--dash-nav-hover-bg)] mb-4">
                <span class="material-symbols-outlined text-[color:var(--dash-text-faint)] text-3xl">assignment</span>
            </div>
            <h3 class="font-jakarta text-base font-bold text-[color:var(--dash-text-heading)]">Belum Ada Survei</h3>
            <p class="mt-1 text-sm text-[color:var(--dash-text-muted)] max-w-md mx-auto">Belum ada survei yang ditugaskan kepada Anda saat ini. Kami akan memberi tahu Anda jika ada survei baru.</p>
            <div class="mt-6">
                <a href="{{ route('member.dashboard') }}" class="inline-flex min-h-[42px] items-center justify-center rounded-lg border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] px-5 text-sm font-bold text-[color:var(--dash-text-body)] shadow-sm hover:bg-[var(--dash-nav-hover-bg)] transition-colors">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                    <thead class="bg-[var(--dash-nav-hover-bg)] text-left text-xs font-bold uppercase tracking-wider text-[color:var(--dash-text-muted)]">
                        <tr>
                            <th class="px-6 py-4">Nama Survey</th>
                            <th class="px-6 py-4">Status Pengisian</th>
                            <th class="px-6 py-4">Waktu Penyelesaian</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-[color:var(--dash-text-body)]">
                        @foreach($surveys as $memberSurvey)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-jakarta font-bold text-[color:var(--dash-text-heading)]">{{ $memberSurvey->survey->survey_name }}</p>
                                    @if($memberSurvey->survey->survey_description)
                                        <p class="mt-0.5 text-xs text-[color:var(--dash-text-muted)] line-clamp-1">{{ $memberSurvey->survey->survey_description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($memberSurvey->answers_exists)
                                        <span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-wide bg-emerald-100 text-emerald-800">
                                            Sudah Diisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-wide bg-amber-100 text-amber-800">
                                            Belum Diisi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-[color:var(--dash-text-body)]">
                                    @if($memberSurvey->answers_exists)
                                        <span class="inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-[color:var(--dash-text-faint)]">check_circle</span> {{ $memberSurvey->updated_at->format('d M Y, H:i') }}</span>
                                    @else
                                        <span class="text-[color:var(--dash-text-faint)] italic">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($memberSurvey->answers_exists)
                                        <a href="{{ route('member.surveys.show', $memberSurvey) }}" class="inline-flex min-h-[38px] items-center justify-center rounded-lg border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] px-4 text-sm font-bold text-[color:var(--dash-text-body)] shadow-sm hover:bg-[var(--dash-nav-hover-bg)] transition-colors">
                                            Lihat Jawaban
                                        </a>
                                    @else
                                        <a href="{{ route('member.surveys.show', $memberSurvey) }}" class="inline-flex min-h-[38px] items-center justify-center rounded-lg bg-[var(--dash-primary,#215aac)] px-4 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
                                            Mulai Isi
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
