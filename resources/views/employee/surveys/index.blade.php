@extends('layouts.dashboard')

@section('title', 'Survey Saya - MINDSIA')
@section('header_title', 'Survey Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto space-y-6">

    {{-- Page header --}}
    <div>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Survey Saya</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            Daftar survei yang ditugaskan kepada Anda.
        </p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($surveys->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl py-16 px-6 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full mb-4"
                 style="background-color: var(--dash-nav-hover-bg);">
                <span class="material-symbols-outlined text-[32px]" style="color: var(--dash-text-faint);">assignment</span>
            </div>
            <h3 class="font-jakarta text-base font-bold mb-1" style="color: var(--dash-text-heading);">
                Belum Ada Survei
            </h3>
            <p class="text-sm max-w-md mx-auto mb-6" style="color: var(--dash-text-muted);">
                Belum ada survei yang ditugaskan kepada Anda saat ini.
            </p>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-5 py-2 rounded-lg text-sm font-semibold transition-colors gap-1.5"
               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Kembali ke Dashboard
            </a>
        </div>
    @else
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Nama Survey</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Status Pengisian</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Waktu Penyelesaian</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($surveys as $employeeSurvey)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor=''">
                                <td class="px-6 py-4">
                                    <p class="font-jakarta text-sm font-bold"
                                       style="color: var(--dash-text-heading);">
                                        {{ $employeeSurvey->survey->survey_name }}
                                    </p>
                                    @if($employeeSurvey->survey->survey_description)
                                        <p class="mt-0.5 text-xs line-clamp-1"
                                           style="color: var(--dash-text-muted);">
                                            {{ $employeeSurvey->survey->survey_description }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($employeeSurvey->answers_exists)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide"
                                              style="background-color: #f0fdf4; color: #059669;">
                                            Sudah Diisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide"
                                              style="background-color: #fef3c7; color: #d97706;">
                                            Belum Diisi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($employeeSurvey->answers_exists)
                                        <span class="inline-flex items-center gap-1.5"
                                              style="color: var(--dash-text-body);">
                                            <span class="material-symbols-outlined text-[16px]"
                                                  style="color: var(--dash-text-muted);">check_circle</span>
                                            {{ $employeeSurvey->updated_at->format('d M Y, H:i') }}
                                        </span>
                                    @else
                                        <span style="color: var(--dash-text-faint);">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($employeeSurvey->answers_exists)
                                        <a href="{{ route('employee.surveys.show', $employeeSurvey) }}"
                                           class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold transition-colors gap-1.5"
                                           style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                            Lihat Jawaban
                                        </a>
                                    @else
                                        <a href="{{ route('employee.surveys.show', $employeeSurvey) }}"
                                           class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold text-white hover:opacity-90 transition-opacity gap-1.5 border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]"
                                           style="background-color: var(--dash-primary);">
                                            <span class="material-symbols-outlined text-[16px]">edit_note</span>
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
