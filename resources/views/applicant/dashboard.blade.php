@extends('layouts.dashboard')

@section('title', 'Applicant Portal - MINDSIA')
@section('header_title', 'Applicant Portal')

@php
$statusSteps = ['applied', 'screening', 'interview', 'offering', 'hired'];
$statusLabel = [
    'applied'   => 'Lamaran Dikirim',
    'screening' => 'Seleksi Berkas',
    'interview' => 'Wawancara',
    'offering'  => 'Penawaran',
    'hired'     => 'Diterima',
    'rejected'  => 'Tidak Lolos',
];
$statusColor = [
    'applied'   => 'text-blue-600 bg-blue-50 border-blue-200',
    'screening' => 'text-yellow-600 bg-yellow-50 border-yellow-200',
    'interview' => 'text-purple-600 bg-purple-50 border-purple-200',
    'offering'  => 'text-orange-600 bg-orange-50 border-orange-200',
    'hired'     => 'text-green-600 bg-green-50 border-green-200',
    'rejected'  => 'text-red-600 bg-red-50 border-red-200',
];
@endphp

@section('content')
    <div class="mb-8">
        <p class="text-gray-500 font-medium text-sm sm:text-base">Selamat datang, <span class="font-semibold text-gray-800">{{ $applicantData->full_name ?? 'Pelamar' }}</span> 👋</p>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- Profil --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-[#EEF4FF] flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[#5586DB] text-2xl">person</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Profil</p>
                <p class="text-sm font-bold text-gray-900">{{ $applicantData->full_name ?? '-' }}</p>
                <p class="text-xs text-gray-500">{{ $applicant->email }}</p>
            </div>
        </div>

        {{-- Verifikasi Email --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="h-12 w-12 rounded-full {{ $applicant->email_verified_at ? 'bg-green-50' : 'bg-yellow-50' }} flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined {{ $applicant->email_verified_at ? 'text-green-500' : 'text-yellow-500' }} text-2xl">
                    {{ $applicant->email_verified_at ? 'verified_user' : 'mark_email_unread' }}
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Verifikasi Email</p>
                @if($applicant->email_verified_at)
                    <p class="text-sm font-bold text-green-600">Terverifikasi</p>
                @else
                    <p class="text-sm font-bold text-yellow-600">Menunggu Verifikasi</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Progress Lamaran --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-800 font-jakarta">Progress Lamaran Saya</h2>
            <span class="text-xs font-semibold text-gray-400">{{ $applications->count() }} lamaran</span>
        </div>

        @if($applications->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="h-16 w-16 rounded-full bg-gray-50 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-gray-300 text-4xl">work_outline</span>
                </div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Belum ada lamaran</p>
                <p class="text-xs text-gray-400">Cari lowongan dan mulai lamar posisi yang sesuai.</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($applications as $app)
                @php
                    $isRejected = $app->status === 'rejected';
                    $currentStep = $isRejected ? -1 : array_search($app->status, $statusSteps);
                @endphp
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $app->posting->job_title ?? 'Posisi tidak tersedia' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Dilamar {{ $app->applied_at?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $statusColor[$app->status] ?? 'text-gray-600 bg-gray-50 border-gray-200' }}">
                            {{ $statusLabel[$app->status] ?? $app->status }}
                        </span>
                    </div>

                    @if(!$isRejected)
                    {{-- Progress steps --}}
                    <div class="flex items-center gap-0">
                        @foreach($statusSteps as $i => $step)
                            @php $done = $i <= $currentStep; $active = $i === $currentStep; @endphp
                            <div class="flex items-center {{ $i < count($statusSteps) - 1 ? 'flex-1' : '' }}">
                                <div class="flex flex-col items-center">
                                    <div class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0
                                        {{ $active ? 'bg-[#5586DB] text-white ring-2 ring-[#5586DB]/30' : ($done ? 'bg-[#5586DB] text-white' : 'bg-gray-100 text-gray-400') }}">
                                        @if($done && !$active)
                                            <span class="material-symbols-outlined text-[12px]">check</span>
                                        @else
                                            {{ $i + 1 }}
                                        @endif
                                    </div>
                                    <span class="text-[9px] font-medium mt-1 whitespace-nowrap
                                        {{ $active ? 'text-[#5586DB] font-bold' : ($done ? 'text-gray-500' : 'text-gray-300') }}">
                                        {{ $statusLabel[$step] }}
                                    </span>
                                </div>
                                @if($i < count($statusSteps) - 1)
                                    <div class="flex-1 h-0.5 mx-1 mb-4 {{ $i < $currentStep ? 'bg-[#5586DB]' : 'bg-gray-100' }}"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex items-center gap-2 text-xs text-red-500 font-medium">
                        <span class="material-symbols-outlined text-[14px]">cancel</span>
                        Lamaran tidak dilanjutkan{{ $app->notes ? ' — '.$app->notes : '' }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
