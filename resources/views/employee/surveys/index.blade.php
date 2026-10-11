@extends('layouts.dashboard')

@section('title', 'Survey Saya - MINDSIA')
@section('header_title', 'Survey Saya')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Survey Saya</h2>
            <p class="text-sm text-slate-500 mt-1">Daftar survei yang ditugaskan kepada Anda.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if($surveys->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-xl border border-slate-100 bg-white py-16 px-6 shadow-sm text-center">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 mb-4">
                <span class="material-symbols-outlined text-slate-400 text-3xl">assignment</span>
            </div>
            <h3 class="font-jakarta text-base font-bold text-slate-900">Belum Ada Survei</h3>
            <p class="mt-1 text-sm text-slate-500 max-w-md mx-auto">Belum ada survei yang ditugaskan kepada Anda saat ini.</p>
            <div class="mt-6">
                <a href="{{ route('employee.dashboard') }}" class="inline-flex min-h-[42px] items-center justify-center rounded-lg border border-slate-200 bg-white px-5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Nama Survey</th>
                            <th class="px-6 py-4">Status Pengisian</th>
                            <th class="px-6 py-4">Waktu Penyelesaian</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-slate-700">
                        @foreach($surveys as $employeeSurvey)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-jakarta font-bold text-slate-900">{{ $employeeSurvey->survey->survey_name }}</p>
                                    @if($employeeSurvey->survey->survey_description)
                                        <p class="mt-0.5 text-xs text-slate-500 line-clamp-1">{{ $employeeSurvey->survey->survey_description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($employeeSurvey->answers_exists)
                                        <span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-wide bg-emerald-100 text-emerald-800">
                                            Sudah Diisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-wide bg-amber-100 text-amber-800">
                                            Belum Diisi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-600">
                                    @if($employeeSurvey->answers_exists)
                                        <span class="inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-slate-400">check_circle</span> {{ $employeeSurvey->updated_at->format('d M Y, H:i') }}</span>
                                    @else
                                        <span class="text-slate-400 italic">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($employeeSurvey->answers_exists)
                                        <a href="{{ route('employee.surveys.show', $employeeSurvey) }}" class="inline-flex min-h-[38px] items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                                            Lihat Jawaban
                                        </a>
                                    @else
                                        <a href="{{ route('employee.surveys.show', $employeeSurvey) }}" class="inline-flex min-h-[38px] items-center justify-center rounded-lg bg-[#215aac] px-4 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
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
