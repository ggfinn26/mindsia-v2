@extends('layouts.dashboard')

@section('title', 'Status Lamaran - MINDSIA')
@section('header_title', 'Status Lamaran')

@section('content')
@php
$statusConfig = [
    'applied'   => ['label' => 'Dikirim',    'color' => 'text-blue-700 bg-blue-50 border-blue-200',   'icon' => 'send'],
    'screening' => ['label' => 'Screening',  'color' => 'text-yellow-700 bg-yellow-50 border-yellow-200', 'icon' => 'manage_search'],
    'interview' => ['label' => 'Interview',  'color' => 'text-purple-700 bg-purple-50 border-purple-200', 'icon' => 'record_voice_over'],
    'offering'  => ['label' => 'Penawaran',  'color' => 'text-indigo-700 bg-indigo-50 border-indigo-200', 'icon' => 'description'],
    'hired'     => ['label' => 'Diterima',   'color' => 'text-green-700 bg-green-50 border-green-200',  'icon' => 'check_circle'],
    'rejected'  => ['label' => 'Ditolak',    'color' => 'text-red-700 bg-red-50 border-red-200',      'icon' => 'cancel'],
];
@endphp

<div class="max-w-4xl mx-auto space-y-4">

    @forelse($applications as $app)
        @php $cfg = $statusConfig[$app->status] ?? ['label' => $app->status, 'color' => 'text-[color:var(--dash-text-body)] bg-[var(--dash-nav-hover-bg)] border-[color:var(--dash-card-border)]', 'icon' => 'circle']; @endphp
        <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[color:var(--dash-card-border)] shadow-sm px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <h2 class="font-bold text-[color:var(--dash-text-heading)] font-jakarta text-base">
                        {{ $app->posting->title ?? 'Lowongan' }}
                    </h2>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                        @if($app->posting?->position)
                            <span class="text-xs text-[color:var(--dash-text-muted)] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">badge</span>
                                {{ $app->posting->position->name }}
                            </span>
                        @endif
                        @if($app->posting?->branch)
                            <span class="text-xs text-[color:var(--dash-text-muted)] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">location_on</span>
                                {{ $app->posting->branch->name }}
                            </span>
                        @endif
                        <span class="text-xs text-[color:var(--dash-text-faint)] flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">schedule</span>
                            {{ $app->applied_at->format('d M Y') }}
                        </span>
                    </div>

                    @if($app->notes)
                        <p class="text-xs text-[color:var(--dash-text-muted)] mt-2 italic">{{ $app->notes }}</p>
                    @endif
                </div>

                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-xl shrink-0 {{ $cfg['color'] }}">
                    <span class="material-symbols-outlined text-[13px]">{{ $cfg['icon'] }}</span>
                    {{ $cfg['label'] }}
                </span>
            </div>
        </div>
    @empty
        <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[color:var(--dash-card-border)] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-gray-300 text-[48px]">inbox</span>
            <p class="text-sm text-[color:var(--dash-text-faint)] mt-3">Belum ada lamaran yang dikirim.</p>
            <a href="{{ route('applicant.job-listings.index') }}"
               class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 text-sm font-semibold text-white bg-[#5586DB] hover:bg-[#4470c4] rounded-xl transition-colors">
                Cari Lowongan
            </a>
        </div>
    @endforelse

    <div class="pt-2">
        {{ $applications->links() }}
    </div>

</div>
@endsection
