@extends('layouts.dashboard')

@section('title', 'Survey Saya - MINDSIA')
@section('header_title', 'Survey Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Portal Siswa</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Survey Saya</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Daftar survei yang ditugaskan kepada Anda untuk evaluasi program pembelajaran.
        </p>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: var(--dash-status-success-bg); border-color: #bbf7d0; color: var(--dash-status-success-text);">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: var(--dash-status-danger-bg); border-color: #fecaca; color: var(--dash-status-danger-text);">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    @if($surveys->isEmpty())
        {{-- Empty State --}}
        <div class="rounded-2xl px-6 py-16 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
                  style="color: var(--dash-text-faint);">assignment</span>
            <p class="text-base font-bold font-jakarta" style="color: var(--dash-text-heading);">Belum Ada Survei</p>
            <p class="text-xs mt-1 mb-6 max-w-md mx-auto" style="color: var(--dash-text-muted);">
                Belum ada survei yang ditugaskan kepada Anda saat ini. Survei baru akan muncul di halaman ini jika telah tersedia.
            </p>
            <a href="{{ route('member.dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Kembali ke Dashboard
            </a>
        </div>
    @else
        {{-- Tabel Card --}}
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Nama Survey</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Status Pengisian</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide hidden sm:table-cell"
                                style="color: var(--dash-text-muted);">Waktu Penyelesaian</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($surveys as $memberSurvey)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                                        {{ $memberSurvey->survey->survey_name }}
                                    </p>
                                    @if($memberSurvey->survey->survey_description)
                                        <p class="text-xs mt-0.5 line-clamp-1" style="color: var(--dash-text-muted);">
                                            {{ $memberSurvey->survey->survey_description }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                    @if($memberSurvey->answers_exists)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid #bbf7d0;">
                                            Sudah Diisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text); border: 1px solid #fde68a;">
                                            Belum Diisi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-xs hidden sm:table-cell" style="color: var(--dash-text-muted);">
                                    @if($memberSurvey->answers_exists)
                                        {{ $memberSurvey->updated_at->translatedFormat('d M Y, H:i') }}
                                    @else
                                        <span style="color: var(--dash-text-faint);">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    @if($memberSurvey->answers_exists)
                                        <a href="{{ route('member.surveys.show', $memberSurvey) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                                            Lihat Jawaban
                                        </a>
                                    @else
                                        <a href="{{ route('member.surveys.show', $memberSurvey) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90"
                                           style="background-color: var(--dash-primary);">
                                            <span class="material-symbols-outlined text-[14px]">edit_note</span>
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
