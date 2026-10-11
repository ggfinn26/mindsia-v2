@extends('layouts.dashboard')

@section('title', 'Detail Pegawai - MINDSIA')
@section('header_title', 'Detail Pegawai')

@section('content')
@php
    $eduLevels = [
        'sd' => 'SD', 'smp' => 'SMP', 'sma' => 'SMA/SMK',
        'd1' => 'D1', 'd2' => 'D2', 'd3' => 'D3', 'd4' => 'D4',
        's1' => 'S1', 's2' => 'S2', 's3' => 'S3',
    ];
    $contractTypes = [
        'permanent'  => 'Tetap',
        'contract'   => 'Kontrak',
        'probation'  => 'Probasi',
        'intern'     => 'Magang',
        'freelance'  => 'Freelance',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('employees.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Data Pegawai
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">SDM & Karyawan</p>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                    style="color: var(--dash-text-heading);">{{ $employee->full_name }}</h1>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $employee->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                    {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kode: <span class="font-mono font-semibold">{{ $employee->employee_code }}</span> · Profil, penempatan, kontrak, dan kompensasi.
            </p>
        </div>
        <div class="flex items-center gap-2">
            @can('update', $employee)
            <a href="{{ route('employees.edit', $employee) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                Edit Data
            </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Kolom kiri (2/3) --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Informasi Pribadi --}}
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-6 py-4"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Pribadi & Kontak</h2>
                </div>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-5 px-6 py-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Nama Lengkap</dt>
                        <dd class="mt-1 text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $employee->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kode Pegawai</dt>
                        <dd class="mt-1 font-mono text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $employee->employee_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm" style="color: var(--dash-text-body);">
                            {{ $employee->gender === 'L' ? 'Laki-laki' : ($employee->gender === 'P' ? 'Perempuan' : 'Belum diatur') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Lahir</dt>
                        <dd class="mt-1 text-sm" style="color: var(--dash-text-body);">
                            {{ $employee->birthdate?->format('d F Y') ?? 'Belum diatur' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">No. WhatsApp</dt>
                        <dd class="mt-1 text-sm" style="color: var(--dash-text-body);">{{ $employee->whatsapp_number ? '+62 ' . $employee->whatsapp_number : 'Belum diatur' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Email</dt>
                        <dd class="mt-1 text-sm" style="color: var(--dash-text-body);">{{ $employee->email ?? 'Belum diatur' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Riwayat Kontrak --}}
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-center justify-between px-6 py-4"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Riwayat Kontrak</h2>
                    @can('employee.update')
                    <a href="{{ route('employment-statuses.create', $employee) }}"
                       class="inline-flex items-center gap-1 text-xs font-semibold hover:underline"
                       style="color: var(--dash-primary-text);">
                        <span class="material-symbols-outlined text-[14px]">add</span> Buat Kontrak Baru
                    </a>
                    @endcan
                </div>
                @if ($employee->employmentStatuses->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                                    <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Posisi</th>
                                    <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tipe</th>
                                    <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Mulai Kontrak</th>
                                    <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Berakhir</th>
                                    @can('contract.manage')
                                    <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employee->employmentStatuses->sortByDesc('id') as $status)
                                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                        class="{{ $loop->first ? 'bg-emerald-50/30' : '' }}"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='{{ $loop->first ? 'rgba(16, 185, 129, 0.05)' : 'transparent' }}'">
                                        <td class="px-5 py-3.5">
                                            <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $status->position?->position_name ?? 'Posisi belum ditentukan' }}</p>
                                            @if ($loop->first)
                                                @if ($status->status === 'terminated')
                                                    <span class="mt-1 inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Selesai</span>
                                                @else
                                                    <span class="mt-1 inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">Aktif</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                                            {{ $contractTypes[$status->type_employment] ?? $status->type_employment ?? '-' }}
                                        </td>
                                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                                            {{ $status->contract_start_date?->format('d M Y') ?? '-' }}
                                        </td>
                                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                                            {{ $status->contract_end_date?->format('d M Y') ?? '-' }}
                                        </td>
                                        @can('contract.manage')
                                        <td class="px-5 py-3.5 text-right">
                                            @if ($loop->first)
                                                @if ($status->status === 'terminated')
                                                    @php
                                                        $isOffboarded = $status->offBoarding !== null;
                                                        $allDone = ! $isOffboarded && $status->terminationChecklist->count() > 0 && $status->terminationChecklist->every(fn($i) => $i->is_completed);
                                                    @endphp
                                                    @if ($isOffboarded)
                                                        <span class="text-xs" style="color: var(--dash-text-muted);">Offboarding selesai</span>
                                                    @else
                                                    <div class="flex items-center justify-end gap-2">
                                                        @if ($allDone)
                                                            <a href="{{ route('contract-terminate.complete.confirm', $status) }}"
                                                               class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                                                               style="background-color: #dc2626;">
                                                                <span class="material-symbols-outlined text-[14px]">delete_forever</span>
                                                                Selesaikan Terminasi
                                                            </a>
                                                        @else
                                                            <a href="{{ route('contract-terminate.checklist', $status) }}"
                                                               class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition-colors">
                                                                <span class="material-symbols-outlined text-[14px]">checklist</span>
                                                                Lanjut Checklist
                                                            </a>
                                                        @endif
                                                        <div x-data="{ showCancelModal: false }" class="inline-block">
                                                            <button type="button" @click="showCancelModal = true"
                                                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold transition-colors"
                                                                    style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                                <span class="material-symbols-outlined text-[14px]">undo</span>
                                                                Batalkan
                                                            </button>

                                                            {{-- Modal Batalkan Terminasi (tanpa overflow-hidden) --}}
                                                            <div x-show="showCancelModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                                <div x-show="showCancelModal"
                                                                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                                     class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>

                                                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                                                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                                                        <div x-show="showCancelModal" @click.away="showCancelModal = false"
                                                                             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                             class="relative transform rounded-2xl text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                                                                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                                                            <div class="rounded-t-2xl px-6 pb-4 pt-6" style="background-color: var(--dash-card-bg);">
                                                                                <div class="sm:flex sm:items-start">
                                                                                    <div class="mx-auto flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0">
                                                                                        <span class="material-symbols-outlined text-red-600">warning</span>
                                                                                    </div>
                                                                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                                                                        <h3 class="text-base font-bold font-jakarta" style="color: var(--dash-text-heading);" id="modal-title">Batalkan Proses Terminasi?</h3>
                                                                                        <div class="mt-2">
                                                                                            <p class="text-sm leading-relaxed" style="color: var(--dash-text-muted);">Seluruh progress checklist kewajiban akan dihapus permanen, dan status karyawan akan dikembalikan ke kondisi normal (Aktif). Apakah Anda yakin?</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="rounded-b-2xl px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t"
                                                                                 style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                                                                                <form method="POST" action="{{ route('contract-terminate.cancel', $status) }}" class="m-0">
                                                                                    @csrf
                                                                                    <button type="submit" class="inline-flex w-full justify-center items-center gap-1.5 rounded-lg px-5 py-2 text-sm font-semibold text-white sm:w-auto hover:opacity-90 transition-opacity"
                                                                                            style="background-color: #dc2626;">
                                                                                        <span class="material-symbols-outlined text-[16px]">undo</span>
                                                                                        Ya, Batalkan Terminasi
                                                                                    </button>
                                                                                </form>
                                                                                <button type="button" @click="showCancelModal = false"
                                                                                        class="mt-3 inline-flex w-full justify-center rounded-lg px-4 py-2 text-sm font-semibold sm:mt-0 sm:w-auto transition-colors"
                                                                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                                                    Kembali
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                @else
                                                    <div class="flex flex-wrap items-center justify-end gap-1.5">
                                                        @if ($status->contract_end_date)
                                                        <a href="{{ route('employment-statuses.extend.create', $status) }}"
                                                           class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition-colors">
                                                            <span class="material-symbols-outlined text-[13px]">update</span>
                                                            Perpanjang
                                                        </a>
                                                        @endif
                                                        <a href="{{ route('employment-statuses.change-position.create', $status) }}"
                                                           class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold transition-colors"
                                                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                            <span class="material-symbols-outlined text-[13px]">swap_horiz</span>
                                                            Ubah Posisi
                                                        </a>
                                                        @can('directBranchTransfer', $employee)
                                                        <a href="{{ route('branch-transfers.direct.edit', $employee) }}"
                                                           class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-200 hover:bg-purple-100 transition-colors">
                                                            <span class="material-symbols-outlined text-[13px]">alt_route</span>
                                                            Pindah Cabang
                                                        </a>
                                                        @endcan
                                                        <a href="{{ route('contract-terminate.initiate', $status) }}"
                                                           class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                                                           style="background-color: #dc2626;">
                                                            <span class="material-symbols-outlined text-[13px]">cancel</span>
                                                            Terminasi
                                                        </a>
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-10 text-center">
                        <span class="material-symbols-outlined text-[40px] block mx-auto mb-2" style="color: var(--dash-text-faint);">work_history</span>
                        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada riwayat jabatan.</p>
                        @can('employee.update')
                        <a href="{{ route('employment-statuses.create', $employee) }}"
                           class="mt-2 inline-flex items-center gap-1 text-xs font-semibold hover:underline"
                           style="color: var(--dash-primary-text);">
                            Buat kontrak sekarang →
                        </a>
                        @endcan
                    </div>
                @endif
            </div>

            {{-- Pendidikan (read-only) --}}
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-6 py-4"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Riwayat Pendidikan</h2>
                </div>
                @if ($employee->educations->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                                    <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Jenjang</th>
                                    <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Institusi</th>
                                    <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Jurusan</th>
                                    <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Lulus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employee->educations->sortByDesc('graduation_year') as $edu)
                                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='transparent'">
                                        <td class="px-5 py-3.5 font-semibold" style="color: var(--dash-text-heading);">{{ $eduLevels[$edu->level] ?? strtoupper($edu->level) }}</td>
                                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $edu->institution ?? '-' }}</td>
                                        <td class="px-5 py-3.5" style="color: var(--dash-text-muted);">{{ $edu->major ?? '-' }}</td>
                                        <td class="px-5 py-3.5" style="color: var(--dash-text-muted);">{{ $edu->graduation_year ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-8 text-center">
                        <span class="material-symbols-outlined text-[36px] block mx-auto mb-2" style="color: var(--dash-text-faint);">school</span>
                        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data pendidikan.</p>
                    </div>
                @endif
            </div>

            {{-- Pengalaman Kerja (read-only) --}}
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-6 py-4"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Pengalaman Kerja Sebelumnya</h2>
                </div>
                @if ($employee->workHistories->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                                    <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Perusahaan</th>
                                    <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Posisi</th>
                                    <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Periode</th>
                                    <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Alasan Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employee->workHistories->sortByDesc('start_date') as $work)
                                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='transparent'">
                                        <td class="px-5 py-3.5 font-semibold" style="color: var(--dash-text-heading);">{{ $work->company_name }}</td>
                                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">{{ $work->position ?? '-' }}</td>
                                        <td class="px-5 py-3.5" style="color: var(--dash-text-muted);">
                                            {{ $work->start_date?->format('M Y') ?? '-' }}
                                            @if ($work->end_date) - {{ $work->end_date->format('M Y') }}
                                            @else - sekarang
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5" style="color: var(--dash-text-faint);">{{ $work->reason_leaving ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-8 text-center">
                        <span class="material-symbols-outlined text-[36px] block mx-auto mb-2" style="color: var(--dash-text-faint);">business_center</span>
                        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada riwayat pekerjaan.</p>
                    </div>
                @endif
            </div>

        </div>{{-- /kolom kiri --}}

        {{-- Kolom kanan (1/3) --}}
        <div class="space-y-6">

            {{-- Penempatan & Jabatan --}}
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-6 py-4"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Penempatan & Jabatan</h2>
                </div>
                <dl class="space-y-4 px-6 py-5">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Jabatan Aktif</dt>
                        <dd class="mt-1 text-sm font-semibold" style="color: var(--dash-text-heading);">
                            {{ $employee->currentStatus?->position?->position_name ?? 'Belum ada jabatan' }}
                        </dd>
                        @if ($employee->join_date)
                            <dd class="mt-0.5 text-xs" style="color: var(--dash-text-muted);">
                                Bergabung {{ $employee->join_date->format('d M Y') }}
                            </dd>
                        @endif
                    </div>
                    <div class="pt-4" style="border-top: 1px solid var(--dash-card-border);">
                        <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Penempatan</dt>
                        <dd class="mt-1 text-sm font-medium" style="color: var(--dash-text-heading);">
                            @if ($employee->is_hq)
                                <span class="inline-flex rounded px-2 py-0.5 text-xs font-semibold bg-purple-100 text-purple-800">Headquarters</span>
                            @elseif ($employee->branch)
                                Cabang · {{ $employee->branch->branch_name }}
                            @elseif ($employee->area)
                                Area · {{ $employee->area->name }}
                            @elseif ($employee->region)
                                Region · {{ $employee->region->name }}
                            @else
                                Belum diatur
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Kompensasi Gaji --}}
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-center justify-between px-6 py-4"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Kompensasi Gaji</h2>
                    @canany(['payroll.employee_compensation.create', 'payroll.employee_compensation.update'])
                    <a href="{{ route('employees.payroll.compensations.index', $employee) }}"
                       class="text-xs font-semibold hover:underline"
                       style="color: var(--dash-primary-text);">
                        Kelola
                    </a>
                    @endcanany
                </div>
                @if ($employee->compensations->isNotEmpty())
                    <ul class="divide-y" style="border-color: var(--dash-card-border);">
                        @foreach ($employee->compensations->sortBy(fn($c) => $c->payrollComponent->component_type) as $comp)
                            <li class="flex items-center justify-between px-6 py-3"
                                style="border-bottom: 1px solid var(--dash-card-border);">
                                <div>
                                    <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $comp->payrollComponent->component_name }}</p>
                                    <span class="mt-0.5 inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $comp->payrollComponent->component_type === 'earning' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $comp->payrollComponent->component_type === 'earning' ? 'Pendapatan' : 'Potongan' }}
                                    </span>
                                </div>
                                <span class="font-semibold text-sm" style="color: var(--dash-text-heading);">Rp {{ number_format($comp->value, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    @php
                        $totalEarnings   = $employee->compensations->filter(fn($c) => $c->payrollComponent->component_type === 'earning')->sum('value');
                        $totalDeductions = $employee->compensations->filter(fn($c) => $c->payrollComponent->component_type === 'deduction')->sum('value');
                    @endphp
                    <div class="px-6 py-3" style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                        <div class="flex justify-between text-xs" style="color: var(--dash-text-muted);">
                            <span>Total Pendapatan</span>
                            <span class="font-semibold text-emerald-700">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-1.5 flex justify-between text-xs" style="color: var(--dash-text-muted);">
                            <span>Total Potongan</span>
                            <span class="font-semibold text-red-700">Rp {{ number_format($totalDeductions, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-2.5 flex justify-between pt-2.5 text-sm font-bold" style="border-top: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                            <span>Take Home Pay</span>
                            <span>Rp {{ number_format($totalEarnings - $totalDeductions, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @else
                    <div class="px-6 py-8 text-center">
                        <span class="material-symbols-outlined text-[36px] block mx-auto mb-2" style="color: var(--dash-text-faint);">payments</span>
                        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada kompensasi.</p>
                        @canany(['payroll.employee_compensation.create'])
                        <a href="{{ route('employees.payroll.compensations.index', $employee) }}"
                           class="mt-2 inline-flex items-center gap-1 text-xs font-semibold hover:underline"
                           style="color: var(--dash-primary-text);">
                            Tetapkan sekarang →
                        </a>
                        @endcanany
                    </div>
                @endif
            </div>

            {{-- Akun User --}}
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-6 py-4"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Akun Login</h2>
                </div>
                <dl class="px-6 py-5">
                    @if ($employee->user)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Email Akun</dt>
                            <dd class="mt-1 text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $employee->user->email }}</dd>
                        </div>
                        <div class="mt-3">
                            <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Login Terakhir</dt>
                            <dd class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                                {{ $employee->user->last_login_at?->format('d M Y H:i') ?? 'Belum pernah' }}
                            </dd>
                        </div>
                    @else
                        <p class="text-sm" style="color: var(--dash-text-muted);">Belum ada akun login.</p>
                    @endif
                </dl>
            </div>

        </div>{{-- /kolom kanan --}}

    </div>
</div>
@endsection
