@extends('layouts.dashboard')

@section('title', $class->class_name . ' - Detail Kelas')
@section('header_title', 'Detail Kelas')

@section('content')
@php
    $formattedMemberOptions = collect($memberOptions)->map(function ($m) {
        return [
            'id' => $m['id'],
            'label' => str_replace('—', ' - ', $m['label']),
        ];
    })->values()->all();
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
         addMemberModal: false,
         transferModal: false,
         transferData: { memberClassId: null, memberName: '', newClassId: '' },
         confirmModal: false,
         confirmTitle: '',
         confirmMessage: '',
         confirmActionText: 'Lanjutkan',
         confirmFormId: '',
         confirmIsDanger: false,
         confirmIcon: 'warning',

         openConfirm(title, message, formId, actionText = 'Lanjutkan', isDanger = false, icon = 'warning') {
             this.confirmTitle = title;
             this.confirmMessage = message;
             this.confirmFormId = formId;
             this.confirmActionText = actionText;
             this.confirmIsDanger = isDanger;
             this.confirmIcon = icon;
             this.confirmModal = true;
         },
         submitConfirm() {
             this.confirmModal = false;
             if (this.confirmFormId) {
                 const f = document.getElementById(this.confirmFormId);
                 if (f) f.submit();
             }
         },
         openTransfer(memberClassId, memberName) {
             this.transferData.memberClassId = memberClassId;
             this.transferData.memberName = memberName;
             this.transferData.newClassId = '';
             this.transferModal = true;
         }
     }">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
            <a href="{{ route('classrooms.index') }}" class="dash-back inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold mb-3 transition-colors"
               style="border-color: var(--dash-card-border); color: var(--dash-text-muted); background-color: var(--dash-card-bg);">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Kembali ke Semua Kelas
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Akademik</p>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">
                    {{ $class->class_name }}
                </h1>
                @php
                    $statusStyles = [
                        'planned' => 'background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);',
                        'active' => 'background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);',
                        'completed' => 'background-color: var(--dash-status-info-bg); color: var(--dash-status-info-text);',
                        'cancelled' => 'background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text);',
                    ];
                    $statusLabels = [
                        'planned' => 'Direncanakan',
                        'active' => 'Aktif',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ];
                @endphp
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full" style="{{ $statusStyles[$class->status] ?? 'background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);' }}">
                    {{ $statusLabels[$class->status] ?? ucfirst($class->status) }}
                </span>
            </div>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                {{ $class->program?->program_name ?? 'Program' }} · {{ $class->branch?->branch_name ?? 'Cabang' }}
            </p>
        </div>

        {{-- Header Actions --}}
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            @can('class.manage')
            @if($class->status === 'planned')
                <a href="{{ route('classrooms.edit', $class) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Edit Kelas
                </a>
                <button type="button"
                        @click="openConfirm('Aktifkan Kelas', 'Aktifkan kelas ini? Status kelas akan berubah menjadi Aktif dan seluruh sesi pertemuan siap berjalan.', 'activate-form', 'Aktifkan Kelas', false, 'play_circle')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 cursor-pointer"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">play_circle</span>
                    Aktifkan
                </button>
                <form id="activate-form" action="{{ route('classrooms.activate', $class) }}" method="POST" class="hidden">
                    @csrf @method('PATCH')
                </form>
            @endif
            @if(in_array($class->status, ['planned', 'active']))
                <button type="button"
                        @click="openConfirm('Batalkan Kelas', 'Batalkan kelas ini? Status akan berubah menjadi Dibatalkan dan tidak dapat dikembalikan lagi.', 'cancel-form', 'Batalkan Kelas', true, 'cancel')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors cursor-pointer"
                        style="color: var(--dash-danger); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                    Batalkan Kelas
                </button>
                <form id="cancel-form" action="{{ route('classrooms.cancel', $class) }}" method="POST" class="hidden">
                    @csrf @method('PATCH')
                </form>
            @endif
            @endcan

            @can('class.graduate')
            @if($class->status === 'active')
                <button type="button"
                        @click="openConfirm('Luluskan Semua Member', 'Luluskan semua member aktif di kelas ini? Tindakan ini tidak dapat dibatalkan.', 'graduate-form', 'Luluskan Semua', false, 'school')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 cursor-pointer"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">school</span>
                    Luluskan Semua
                </button>
                <form id="graduate-form" action="{{ route('classrooms.graduate', $class) }}" method="POST" class="hidden">
                    @csrf
                </form>
            @endif
            @endcan
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-success-text);">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-status-danger-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-status-danger-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
            <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5">error</span>
            <div>
                <p class="font-semibold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Card Ringkasan & Informasi Kelas --}}
    <div class="rounded-2xl shadow-sm p-6"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <h2 class="font-jakarta text-base font-bold mb-4 flex items-center gap-2" style="color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">info</span>
            Informasi & Jadwal Kelas
        </h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <dt class="text-xs font-semibold uppercase tracking-wider flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[16px]">person</span>
                    Tutor
                </dt>
                <dd class="mt-1 text-sm font-bold" style="color: var(--dash-text-heading);">{{ $class->tutor?->full_name ?? '-' }}</dd>
            </div>
            <div class="p-4 rounded-xl" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <dt class="text-xs font-semibold uppercase tracking-wider flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                    Hari Kelas
                </dt>
                <dd class="mt-1 text-sm font-bold" style="color: var(--dash-text-heading);">{{ $class->day_of_week }}{{ $class->day_of_week2 ? ' & ' . $class->day_of_week2 : '' }}</dd>
            </div>
            <div class="p-4 rounded-xl" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <dt class="text-xs font-semibold uppercase tracking-wider flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[16px]">date_range</span>
                    Periode Belajar
                </dt>
                <dd class="mt-1 text-sm font-bold" style="color: var(--dash-text-heading);">{{ $class->start_date?->format('d M Y') ?? '-' }} - {{ $class->end_date?->format('d M Y') ?? '-' }}</dd>
            </div>
            <div class="p-4 rounded-xl" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <dt class="text-xs font-semibold uppercase tracking-wider flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[16px]">group</span>
                    Member Aktif
                </dt>
                <dd class="mt-1 text-sm font-bold" style="color: var(--dash-text-heading);">{{ $class->memberClasses->where('status', 'active')->count() }} Siswa</dd>
            </div>
            <div class="p-4 rounded-xl" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <dt class="text-xs font-semibold uppercase tracking-wider flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                    Jam Hari Utama
                </dt>
                <dd class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $class->start_time_primary }} - {{ $class->end_time_primary }}</dd>
            </div>
            <div class="p-4 rounded-xl" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <dt class="text-xs font-semibold uppercase tracking-wider flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                    Jam Hari Kedua
                </dt>
                <dd class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $class->start_time_secondary && $class->end_time_secondary ? $class->start_time_secondary . ' - ' . $class->end_time_secondary : '-' }}</dd>
            </div>
            <div class="p-4 rounded-xl" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <dt class="text-xs font-semibold uppercase tracking-wider flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[16px]">timelapse</span>
                    Jumlah Minggu
                </dt>
                <dd class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $class->week_count }} minggu</dd>
            </div>
            <div class="p-4 rounded-xl" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <dt class="text-xs font-semibold uppercase tracking-wider flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[16px]">apartment</span>
                    Cabang
                </dt>
                <dd class="mt-1 text-sm font-medium" style="color: var(--dash-text-body);">{{ $class->branch?->branch_name ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Card Anggota Kelas --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[22px]" style="color: var(--dash-primary-text);">group</span>
                <div>
                    <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Anggota Kelas</h2>
                    <p class="text-xs" style="color: var(--dash-text-muted);">
                        Total {{ $class->memberClasses->count() }} member ({{ $class->memberClasses->where('status', 'active')->count() }} aktif)
                    </p>
                </div>
            </div>
            @can('class.member.assign')
                <button type="button" @click="addMemberModal = true"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 cursor-pointer"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    Daftarkan Member
                </button>
            @endcan
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th scope="col" class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Nama Siswa</th>
                        <th scope="col" class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Status Keanggotaan</th>
                        <th scope="col" class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Tanggal Mulai</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($class->memberClasses as $mc)
                        @php
                            $mcMemberName = $mc->memberRegistration?->memberData?->full_name ?? 'Member #' . $mc->member_registration_id;
                            $mcStatusStyles = [
                                'active' => 'background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);',
                                'inactive' => 'background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);',
                                'completed' => 'background-color: var(--dash-status-info-bg); color: var(--dash-status-info-text);',
                            ];
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3 font-medium" style="color: var(--dash-text-heading);">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                                         style="background-color: var(--dash-nav-hover-bg); color: var(--dash-primary-text); border: 1px solid var(--dash-card-border);">
                                        {{ strtoupper(substr($mcMemberName, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold">{{ $mcMemberName }}</p>
                                        <p class="text-xs" style="color: var(--dash-text-faint);">Reg ID: #{{ $mc->member_registration_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                      style="{{ $mcStatusStyles[$mc->status] ?? 'background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);' }}">
                                    {{ ucfirst($mc->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm" style="color: var(--dash-text-muted);">
                                {{ $mc->start_date?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                @can('class.manage')
                                @if($mc->status === 'active')
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        @if($transferableClasses->isNotEmpty())
                                            <button type="button"
                                                    @click="openTransfer({{ $mc->id }}, '{{ addslashes($mcMemberName) }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors cursor-pointer"
                                                    style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-primary-text);"
                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[15px]">swap_horiz</span>
                                                Pindahkan
                                            </button>
                                        @endif

                                        <form id="deact-mc-{{ $mc->id }}" action="{{ route('member-class.deactivate', $mc) }}" method="POST" class="hidden">
                                            @csrf @method('PATCH')
                                        </form>
                                        <button type="button"
                                                @click="openConfirm('Nonaktifkan Member', 'Nonaktifkan member {{ addslashes($mcMemberName) }} dari kelas? Data absensi dan evaluasi tetap tersimpan.', 'deact-mc-{{ $mc->id }}', 'Nonaktifkan', false, 'person_off')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors cursor-pointer"
                                                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-status-warning-text);"
                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[15px]">pause_circle</span>
                                            Nonaktifkan
                                        </button>

                                        <form id="rm-mc-{{ $mc->id }}" action="{{ route('member-class.destroy', $mc) }}" method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button"
                                                @click="openConfirm('Hapus Member dari Kelas', 'Hapus permanen member {{ addslashes($mcMemberName) }} dari kelas? Progress kurikulum dan riwayat belajar kelas ini akan dihapus.', 'rm-mc-{{ $mc->id }}', 'Hapus Member', true, 'delete')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90 cursor-pointer"
                                                style="background-color: var(--dash-danger);">
                                            <span class="material-symbols-outlined text-[15px]">delete</span>
                                            Hapus
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs" style="color: var(--dash-text-faint);">-</span>
                                @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-[44px] block mx-auto mb-2" style="color: var(--dash-text-faint);">person_off</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada member di kelas ini.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Gunakan tombol 'Daftarkan Member' di atas untuk menambahkan peserta didik.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Card Sesi & Absensi --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[22px]" style="color: var(--dash-primary-text);">event_available</span>
                <div>
                    <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Sesi Pertemuan & Absensi</h2>
                    <p class="text-xs" style="color: var(--dash-text-muted);">
                        Total {{ $class->schedules->count() }} sesi pertemuan
                    </p>
                </div>
            </div>
        </div>

        @if($class->schedules->isEmpty())
            <div class="px-6 py-12 text-center">
                <span class="material-symbols-outlined text-[44px] block mx-auto mb-2" style="color: var(--dash-text-faint);">calendar_month</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Jadwal sesi belum dibuat.</p>
                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Jadwal pertemuan akan otomatis dibuat saat kelas berstatus aktif.</p>
            </div>
        @else
            @php $activeMembers = $class->memberClasses->where('status', 'active'); @endphp
            <div class="divide-y" style="border-color: var(--dash-card-border);">
                @foreach($class->schedules->sortBy('schedule_date') as $schedule)
                    @php $isPast = $schedule->schedule_date <= today(); @endphp
                    <div x-data="{ open: false }">
                        {{-- Row Accordion Header --}}
                        <div class="px-6 py-4 flex flex-wrap md:flex-nowrap items-center gap-3 cursor-pointer transition-colors"
                             @click="open = !open"
                             onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                             onmouseout="this.style.backgroundColor='transparent'">
                            <span class="text-sm font-bold w-28 shrink-0" style="color: var(--dash-text-heading);">
                                {{ $schedule->schedule_date?->format('d M Y') }}
                            </span>
                            <span class="text-xs font-medium w-20 shrink-0" style="color: var(--dash-text-muted);">
                                {{ $schedule->schedule_date?->locale('id')->isoFormat('dddd') }}
                            </span>
                            <span class="text-xs font-semibold w-36 shrink-0" style="color: var(--dash-text-muted);">
                                {{ $schedule->start_time }} - {{ $schedule->end_time }}
                            </span>
                            <span class="text-xs flex-1 truncate" style="color: var(--dash-text-body);">
                                {{ $schedule->material_taught ?? ($isPast ? 'Materi belum diisi' : 'Sesi mendatang') }}
                            </span>
                            <div class="flex items-center gap-2 shrink-0">
                                @if(!$isPast)
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                          style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                                        Mendatang
                                    </span>
                                @elseif($schedule->schedule_date->isToday())
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                          style="background-color: var(--dash-status-info-bg); color: var(--dash-status-info-text);">
                                        Hari Ini
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                          style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                                        Selesai
                                    </span>
                                @endif
                                <span class="material-symbols-outlined text-[20px] transition-transform"
                                      style="color: var(--dash-text-faint);"
                                      :class="open ? 'rotate-180' : ''">expand_more</span>
                            </div>
                        </div>

                        {{-- Row Accordion Expanded Body --}}
                        <div x-show="open" x-cloak class="p-6 space-y-6"
                             style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">

                            {{-- Material Input --}}
                            @can('class.session.update')
                            @if($isPast)
                            <div class="p-4 rounded-xl" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                <h4 class="text-xs font-bold uppercase tracking-wider mb-2 flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[16px]">menu_book</span>
                                    Materi Pembelajaran Sesi Ini
                                </h4>
                                <form action="{{ route('class-schedules.material', $schedule) }}" method="POST" class="flex flex-col sm:flex-row gap-2.5">
                                    @csrf @method('PATCH')
                                    <input type="text" name="material_taught"
                                           value="{{ old('material_taught', $schedule->material_taught) }}"
                                           placeholder="Tulis materi pembelajaran yang diajarkan..."
                                           class="flex-1 px-3.5 py-2 rounded-lg text-sm border focus:outline-none transition-colors"
                                           style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <button type="submit"
                                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0 cursor-pointer"
                                            style="background-color: var(--dash-primary);">
                                        <span class="material-symbols-outlined text-[16px]">save</span>
                                        Simpan Materi
                                    </button>
                                </form>
                            </div>
                            @else
                            <div class="p-3.5 rounded-xl text-xs" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                                <span class="material-symbols-outlined text-[15px] align-middle mr-1" style="color: var(--dash-primary-text);">info</span>
                                Materi sesi hanya dapat diisi pada hari jadwal sesi atau setelahnya.
                            </div>
                            @endif
                            @endcan

                            {{-- Attendance Input --}}
                            @can('class.attendance.record')
                            @if($isPast && $activeMembers->isNotEmpty())
                            <div class="p-4 rounded-xl" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                <h4 class="text-xs font-bold uppercase tracking-wider mb-3 flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
                                    Presensi Kehadiran Siswa
                                </h4>
                                <form action="{{ route('member-attendance.store', $schedule) }}" method="POST">
                                    @csrf
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-xs text-left">
                                            <thead>
                                                <tr style="border-bottom: 1px solid var(--dash-card-border);">
                                                    <th class="pb-2 font-bold uppercase" style="color: var(--dash-text-muted);">Nama Siswa</th>
                                                    <th class="pb-2 font-bold uppercase w-44" style="color: var(--dash-text-muted);">Status Presensi</th>
                                                    <th class="pb-2 font-bold uppercase" style="color: var(--dash-text-muted);">Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                                                @foreach($activeMembers as $mc)
                                                <tr>
                                                    <td class="py-2.5 font-semibold pr-3" style="color: var(--dash-text-heading);">
                                                        {{ $mc->memberRegistration?->memberData?->full_name ?? 'Member #'.$mc->id }}
                                                    </td>
                                                    <td class="py-2.5 pr-3">
                                                        <input type="hidden" name="attendances[{{ $loop->index }}][member_class_id]" value="{{ $mc->id }}">
                                                        <select name="attendances[{{ $loop->index }}][status]"
                                                                class="w-full px-2.5 py-1.5 rounded-lg text-xs border focus:outline-none transition-colors"
                                                                style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                                                onblur="this.style.borderColor='var(--dash-input-border)'">
                                                            <option value="present">Hadir</option>
                                                            <option value="absent">Absen</option>
                                                            <option value="late">Terlambat</option>
                                                            <option value="excused">Izin</option>
                                                            <option value="sick">Sakit</option>
                                                        </select>
                                                    </td>
                                                    <td class="py-2.5">
                                                        <input type="text" name="attendances[{{ $loop->index }}][notes]"
                                                               placeholder="Catatan opsional..."
                                                               class="w-full px-2.5 py-1.5 rounded-lg text-xs border focus:outline-none transition-colors"
                                                               style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                                               onfocus="this.style.borderColor='var(--dash-primary)'"
                                                               onblur="this.style.borderColor='var(--dash-input-border)'">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3.5 flex justify-end">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-white shadow-sm transition-opacity hover:opacity-90 cursor-pointer"
                                                style="background-color: var(--dash-primary);">
                                            <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                            Simpan Presensi
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @endif
                            @endcan

                            {{-- Assessment Input --}}
                            @can('class.assessment.record')
                            @if($isPast && $activeMembers->isNotEmpty())
                            <div class="p-4 rounded-xl" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                <h4 class="text-xs font-bold uppercase tracking-wider mb-3 flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[16px]">grade</span>
                                    Penilaian Sesi Pertemuan
                                </h4>
                                <div class="space-y-2.5">
                                    @foreach($activeMembers as $mc)
                                    <form action="{{ route('member-assessments.store', $schedule) }}" method="POST"
                                          class="flex flex-col sm:flex-row items-start sm:items-center gap-2.5 p-2 rounded-lg"
                                          style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                        @csrf
                                        <input type="hidden" name="member_class_id" value="{{ $mc->id }}">
                                        <span class="text-xs font-semibold w-44 truncate shrink-0" style="color: var(--dash-text-heading);">
                                            {{ $mc->memberRegistration?->memberData?->full_name ?? 'Member #'.$mc->id }}
                                        </span>
                                        <input type="number" name="score" min="0" max="100" placeholder="Skor (0-100)"
                                               class="w-24 px-2.5 py-1.5 rounded-lg text-xs border focus:outline-none transition-colors"
                                               style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                               onfocus="this.style.borderColor='var(--dash-primary)'"
                                               onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <input type="text" name="notes" placeholder="Catatan evaluasi sesi..."
                                               class="flex-1 w-full px-2.5 py-1.5 rounded-lg text-xs border focus:outline-none transition-colors"
                                               style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                               onfocus="this.style.borderColor='var(--dash-primary)'"
                                               onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0 cursor-pointer"
                                                style="background-color: var(--dash-primary);">
                                            <span class="material-symbols-outlined text-[14px]">save</span>
                                            Simpan
                                        </button>
                                    </form>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @endcan

                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Card Ujian & Sertifikasi --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[22px]" style="color: var(--dash-primary-text);">quiz</span>
                <div>
                    <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Test & Ujian Siswa</h2>
                    <p class="text-xs" style="color: var(--dash-text-muted);">
                        Total {{ $class->tests->count() }} test terdaftar di kelas ini
                    </p>
                </div>
            </div>
            @can('class.test.create')
                <a href="{{ route('class-tests.create', $class) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    Tambah Test
                </a>
            @endcan
        </div>

        @if($class->tests->isEmpty())
            <div class="px-6 py-12 text-center">
                <span class="material-symbols-outlined text-[44px] block mx-auto mb-2" style="color: var(--dash-text-faint);">assignment</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada test untuk kelas ini.</p>
                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Klik tombol 'Tambah Test' untuk membuat Pre-Test atau Post-Test.</p>
            </div>
        @else
            <div class="divide-y" style="border-color: var(--dash-card-border);">
                @foreach($class->tests as $test)
                    <div x-data="{ open: false }">
                        <div class="px-6 py-4 flex flex-wrap sm:flex-nowrap items-center gap-3 cursor-pointer transition-colors"
                             @click="open = !open"
                             onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                             onmouseout="this.style.backgroundColor='transparent'">
                            <span class="text-sm font-bold flex-1" style="color: var(--dash-text-heading);">{{ $test->test_name }}</span>
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                  style="{{ $test->test_type === 'pre_test' ? 'background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);' : 'background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);' }}">
                                {{ $test->test_type === 'pre_test' ? 'Pre-Test' : 'Post-Test' }}
                            </span>
                            <span class="text-xs" style="color: var(--dash-text-muted);">
                                {{ $test->date?->format('d M Y') ?? '-' }}
                            </span>
                            <div class="flex items-center gap-2 shrink-0">
                                @can('class.test.update')
                                    <a href="{{ route('class-tests.edit', [$class, $test]) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                                       @click.stop>
                                        <span class="material-symbols-outlined text-[14px]">edit</span>
                                        Edit
                                    </a>
                                @endcan
                                @can('class.test.delete')
                                    <form id="del-test-{{ $test->id }}" action="{{ route('class-tests.destroy', [$class, $test]) }}" method="POST" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button"
                                            @click.stop="openConfirm('Hapus Test', 'Hapus test {{ addslashes($test->test_name) }}? Seluruh nilai siswa yang tersimpan akan ikut terhapus.', 'del-test-{{ $test->id }}', 'Hapus Test', true, 'delete')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90 cursor-pointer"
                                            style="background-color: var(--dash-danger);">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                        Hapus
                                    </button>
                                @endcan
                                <span class="material-symbols-outlined text-[20px] transition-transform"
                                      style="color: var(--dash-text-faint);"
                                      :class="open ? 'rotate-180' : ''">expand_more</span>
                            </div>
                        </div>

                        {{-- Input Nilai Member --}}
                        @can('class.test.score')
                        <div x-show="open" x-cloak class="p-6 space-y-3"
                             style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                            <h4 class="text-xs font-bold uppercase tracking-wider mb-2 flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                                <span class="material-symbols-outlined text-[16px]">fact_check</span>
                                Input Nilai & Evaluasi Siswa
                            </h4>
                            @php
                                $resultsByMcId = $test->memberTestResults->keyBy('member_class_id');
                            @endphp
                            <div class="space-y-2">
                                @foreach($class->memberClasses as $mc)
                                    @php $existing = $resultsByMcId->get($mc->id); @endphp
                                    <form action="{{ route('member-test-results.store', [$mc, $test]) }}" method="POST"
                                          class="flex flex-col sm:flex-row items-start sm:items-center gap-2.5 p-2.5 rounded-xl"
                                          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                        @csrf
                                        <span class="text-xs font-semibold w-44 truncate shrink-0" style="color: var(--dash-text-heading);">
                                            {{ $mc->memberRegistration?->memberData?->full_name ?? 'Member #'.$mc->id }}
                                        </span>
                                        <input type="text" name="level" value="{{ old('level', $existing?->level) }}"
                                               placeholder="Level (A/B/C/Lulus)"
                                               class="w-32 px-2.5 py-1.5 rounded-lg text-xs border focus:outline-none transition-colors"
                                               style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                               onfocus="this.style.borderColor='var(--dash-primary)'"
                                               onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <input type="number" name="final_score" value="{{ old('final_score', $existing?->final_score) }}"
                                               min="0" placeholder="Nilai Akhir"
                                               class="w-24 px-2.5 py-1.5 rounded-lg text-xs border focus:outline-none transition-colors"
                                               style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                               onfocus="this.style.borderColor='var(--dash-primary)'"
                                               onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0 cursor-pointer"
                                                style="background-color: var(--dash-primary);">
                                            <span class="material-symbols-outlined text-[14px]">save</span>
                                            {{ $existing ? 'Update' : 'Simpan' }}
                                        </button>
                                        @if($existing)
                                            <span class="text-xs" style="color: var(--dash-text-muted);">
                                                Tersimpan: Level <strong style="color: var(--dash-text-heading);">{{ $existing->level }}</strong>, Nilai <strong style="color: var(--dash-text-heading);">{{ $existing->final_score }}</strong>
                                            </span>
                                        @endif
                                    </form>
                                @endforeach
                            </div>
                        </div>
                        @endcan
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Card Sertifikat Member Lulus --}}
    @php $graduatedMembers = $class->memberClasses->filter(fn($mc) => $mc->memberRegistration?->graduation_status === 'LULUS'); @endphp
    @if($graduatedMembers->isNotEmpty())
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[22px]" style="color: var(--dash-primary-text);">workspace_premium</span>
                <div>
                    <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Sertifikat Kelulusan Member</h2>
                    <p class="text-xs" style="color: var(--dash-text-muted);">Daftar sertifikat untuk siswa yang telah menyelesaikan program</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Nama Siswa</th>
                        <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">No. Sertifikat</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Status Fisik</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Hardcopy</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Pengambilan</th>
                        @can('class.certificate.update')
                            <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach($graduatedMembers as $mc)
                        @php $cert = $mc->memberRegistration?->certificate; @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3 whitespace-nowrap font-medium" style="color: var(--dash-text-heading);">
                                {{ $mc->memberRegistration?->memberData?->full_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs" style="color: var(--dash-text-body);">
                                {{ $cert?->certificate_number ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($cert?->certificate_available === 'available')
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                          style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                          style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                                        Belum Tersedia
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-xs" style="color: var(--dash-text-muted);">
                                {{ $cert?->certificate_hardcopy ? 'Ya' : 'Tidak' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($cert?->certificate_taken === 'taken')
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                          style="background-color: var(--dash-status-info-bg); color: var(--dash-status-info-text);">
                                        Sudah Diambil
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                          style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                                        Belum Diambil
                                    </span>
                                @endif
                            </td>
                            @can('class.certificate.update')
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                @if($cert)
                                <form action="{{ route('member-certificates.update', $cert) }}" method="POST"
                                      class="inline-flex items-center justify-end gap-2">
                                    @csrf @method('PATCH')
                                    <input type="text" name="certificate_number"
                                           value="{{ $cert->certificate_number }}"
                                           placeholder="No. Sertifikat"
                                           class="w-28 px-2.5 py-1 rounded-lg text-xs border focus:outline-none transition-colors"
                                           style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <select name="certificate_available"
                                            class="px-2 py-1 rounded-lg text-xs border focus:outline-none transition-colors"
                                            style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'"
                                            onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <option value="not_available" @selected($cert->certificate_available === 'not_available')>Belum Tersedia</option>
                                        <option value="available" @selected($cert->certificate_available === 'available')>Tersedia</option>
                                    </select>
                                    <select name="certificate_taken"
                                            class="px-2 py-1 rounded-lg text-xs border focus:outline-none transition-colors"
                                            style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'"
                                            onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <option value="not_taken" @selected($cert->certificate_taken === 'not_taken')>Belum Diambil</option>
                                        <option value="taken" @selected($cert->certificate_taken === 'taken')>Sudah Diambil</option>
                                    </select>
                                    <input type="hidden" name="certificate_hardcopy" value="{{ $cert->certificate_hardcopy ? '1' : '0' }}">
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0 cursor-pointer"
                                            style="background-color: var(--dash-primary);">
                                        <span class="material-symbols-outlined text-[13px]">save</span>
                                        Simpan
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Card Riwayat Perubahan Tutor --}}
    @if($class->tutorChangeHistories->isNotEmpty())
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[22px]" style="color: var(--dash-primary-text);">history</span>
                <div>
                    <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Riwayat Perubahan Tutor</h2>
                    <p class="text-xs" style="color: var(--dash-text-muted);">Catatan perubahan pengajar pada kelas ini</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Tanggal</th>
                        <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Tutor Sebelumnya</th>
                        <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Tutor Pengganti</th>
                        <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-[11px]" style="color: var(--dash-text-muted);">Alasan Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($class->tutorChangeHistories as $history)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3 whitespace-nowrap text-xs font-medium" style="color: var(--dash-text-muted);">
                                {{ $history->changed_at?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold" style="color: var(--dash-text-heading);">
                                {{ $history->fromTutor?->full_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold" style="color: var(--dash-text-heading);">
                                {{ $history->toTutor?->full_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-xs" style="color: var(--dash-text-body);">
                                {{ $history->reason ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Modal Tambah Member (Alpine.js) --}}
    @can('class.member.assign')
    <div x-show="addMemberModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="addMemberModal" @click="addMemberModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="addMemberModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                 x-data="memberDropdown({{ Illuminate\Support\Js::from($formattedMemberOptions) }})">
                {{-- Modal Header (rounded-t-2xl) --}}
                <div class="p-6 rounded-t-2xl flex items-start gap-4" style="border-bottom: 1px solid var(--dash-card-border);">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                         style="background-color: var(--dash-nav-hover-bg); color: var(--dash-primary-text);">
                        <span class="material-symbols-outlined text-[22px]">person_add</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Daftarkan Member ke Kelas</h3>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                            Pilih siswa terdaftar pada program dan cabang yang sama untuk didaftarkan ke kelas ini.
                        </p>
                    </div>
                </div>

                <form action="{{ route('member-class.store', $class) }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                                    Pilih Member <span style="color: var(--dash-danger);">*</span>
                                </label>
                                <span class="text-xs" style="color: var(--dash-text-faint);">{{ $availableRegistrations->count() }} member tersedia</span>
                            </div>
                            <div class="relative" @click.away="open = false">
                                <div @click="open = !open"
                                     class="w-full px-3.5 py-2.5 rounded-lg text-sm border flex justify-between items-center cursor-pointer transition-colors"
                                     :style="open ? 'background-color: var(--dash-input-bg); border-color: var(--dash-primary); color: var(--dash-text-body);' : 'background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);'">
                                    <span x-text="selectedId ? selectedLabel : 'Pilih member...'"
                                          :style="selectedId ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-faint);'"
                                          class="truncate block pr-2"></span>
                                    <span class="material-symbols-outlined text-[18px] shrink-0" style="color: var(--dash-text-faint);">expand_more</span>
                                </div>
                                <div x-show="open" x-transition x-cloak
                                     class="absolute z-50 w-full mt-1 rounded-xl shadow-xl border"
                                     style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
                                    <div class="p-2 border-b" style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-[16px]" style="color: var(--dash-text-faint);">search</span>
                                            <input type="text" x-model="search" placeholder="Cari nama member..."
                                                   style="padding-left: 2.25rem; background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                                   class="w-full pr-3 py-1.5 rounded-lg text-xs border focus:outline-none"
                                                   onfocus="this.style.borderColor='var(--dash-primary)'"
                                                   onblur="this.style.borderColor='var(--dash-input-border)'"
                                                   @click.stop>
                                        </div>
                                    </div>
                                    <ul class="max-h-52 overflow-y-auto py-1">
                                        <li x-show="filtered.length === 0" class="px-4 py-3 text-xs text-center" style="color: var(--dash-text-muted);">
                                            Tidak ada member yang cocok
                                        </li>
                                        <template x-for="m in filtered" :key="m.id">
                                            <li @click="select(m)"
                                                class="px-4 py-2 text-xs cursor-pointer transition-colors"
                                                :style="selectedId == m.id ? 'background-color: var(--dash-nav-hover-bg); color: var(--dash-primary-text); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                onmouseout="if(selectedId != m.id) this.style.backgroundColor='transparent'"
                                                x-text="m.label"></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            <input type="hidden" name="member_registration_id" x-model="selectedId" required />
                            @error('member_registration_id')
                                <p class="text-xs mt-1" style="color: var(--dash-danger);">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">
                                Tanggal Mulai <span style="color: var(--dash-danger);">*</span>
                            </label>
                            <input type="date" name="start_date" required
                                   class="w-full px-3.5 py-2.5 rounded-lg text-sm border focus:outline-none transition-colors"
                                   style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'"
                                   value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" />
                            @error('start_date')
                                <p class="text-xs mt-1" style="color: var(--dash-danger);">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Modal Footer (rounded-b-2xl) --}}
                    <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5"
                         style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                        <button type="button" @click="addMemberModal = false"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors cursor-pointer"
                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="!selectedId"
                                :class="selectedId ? 'hover:opacity-90 cursor-pointer' : 'opacity-50 cursor-not-allowed'"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-opacity"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">person_add</span>
                            Daftarkan Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    {{-- Modal Transfer Member (Alpine.js) --}}
    @can('class.manage')
    <div x-show="transferModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="transferModal" @click="transferModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="transferModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                {{-- Modal Header (rounded-t-2xl) --}}
                <div class="p-6 rounded-t-2xl flex items-start gap-4" style="border-bottom: 1px solid var(--dash-card-border);">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                         style="background-color: var(--dash-nav-hover-bg); color: var(--dash-primary-text);">
                        <span class="material-symbols-outlined text-[22px]">swap_horiz</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Transfer Kelas Member</h3>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                            Pindahkan siswa ke kelas aktif lain pada program yang sama. Status siswa di kelas ini akan dinonaktifkan.
                        </p>
                    </div>
                </div>

                <form :action="'{{ url('/member-class') }}/' + transferData.memberClassId + '/transfer'" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div class="p-3.5 rounded-xl" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                            <p class="text-xs font-semibold" style="color: var(--dash-text-muted);">Nama Siswa:</p>
                            <p class="text-sm font-bold mt-0.5" style="color: var(--dash-text-heading);" x-text="transferData.memberName"></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">
                                Pilih Kelas Tujuan <span style="color: var(--dash-danger);">*</span>
                            </label>
                            <select name="new_class_id" required
                                    class="w-full px-3.5 py-2.5 rounded-lg text-sm border focus:outline-none transition-colors"
                                    style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-body);"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                <option value="">Pilih kelas tujuan...</option>
                                @foreach($transferableClasses as $tc)
                                    <option value="{{ $tc->id }}">{{ $tc->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Modal Footer (rounded-b-2xl) --}}
                    <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5"
                         style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                        <button type="button" @click="transferModal = false"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors cursor-pointer"
                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity cursor-pointer"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">swap_horiz</span>
                            Pindahkan Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    {{-- Modal Konfirmasi Terpadu (Alpine.js) --}}
    <div x-show="confirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="confirmModal" @click="confirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="confirmModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                {{-- Modal Header (rounded-t-2xl) --}}
                <div class="p-6 rounded-t-2xl">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center shrink-0"
                             :style="confirmIsDanger ? 'background-color: var(--dash-status-danger-bg); color: var(--dash-danger);' : 'background-color: var(--dash-nav-hover-bg); color: var(--dash-primary-text);'">
                            <span class="material-symbols-outlined text-[24px]" x-text="confirmIcon"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                            <div class="mt-1.5">
                                <p class="text-sm" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer (rounded-b-2xl) --}}
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="confirmModal = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors cursor-pointer"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="button" @click="submitConfirm()"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-opacity hover:opacity-90 cursor-pointer"
                            :style="confirmIsDanger ? 'background-color: var(--dash-danger);' : 'background-color: var(--dash-primary);'">
                        <span class="material-symbols-outlined text-[16px]" :x-text="confirmIcon"></span>
                        <span x-text="confirmActionText"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('head-scripts')
    @vite('resources/js/classroom-show.js')
@endpush
