@extends('layouts.dashboard')

@section('title', 'Aturan Jadwal Kerja - MINDSIA')
@section('header_title', 'Aturan Jadwal Kerja')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Presensi</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Aturan Jadwal Kerja</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Konfigurasi jam kerja, toleransi keterlambatan, dan penugasan jadwal kerja karyawan.
            </p>
        </div>
        @can('attendance.schedule_rule.manage')
            <div>
                <a href="{{ route('work-schedule-rules.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Aturan
                </a>
            </div>
        @endcan
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
            <div class="flex-1">
                <p class="font-bold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Card Container --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        @if ($rules->isEmpty())
            <div class="px-6 py-12 text-center">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">schedule</span>
                <h3 class="font-jakarta text-base font-semibold" style="color: var(--dash-text-heading);">Belum ada aturan jadwal kerja</h3>
                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Anda belum membuat aturan jam kerja apapun.</p>
                @can('attendance.schedule_rule.manage')
                    <div class="mt-5">
                        <a href="{{ route('work-schedule-rules.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                           style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            Buat Aturan Baru
                        </a>
                    </div>
                @endcan
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Nama Aturan</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Jam Kerja</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Istirahat</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Toleransi Telat</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Toleransi Pulang Cepat</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Kewajiban</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                        @foreach ($rules as $rule)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3.5 whitespace-nowrap font-medium"
                                    style="color: var(--dash-text-heading);">
                                    {{ $rule->setting_name }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm tabular-nums"
                                    style="color: var(--dash-text-body);">
                                    @if($rule->start_time && $rule->end_time)
                                        {{ \Carbon\Carbon::parse($rule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($rule->end_time)->format('H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm tabular-nums"
                                    style="color: var(--dash-text-muted);">
                                    @if($rule->break_start_time && $rule->break_end_time)
                                        {{ \Carbon\Carbon::parse($rule->break_start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($rule->break_end_time)->format('H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm"
                                    style="color: var(--dash-text-body);">
                                    {{ $rule->late_tolerance_minutes }} menit
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm"
                                    style="color: var(--dash-text-body);">
                                    {{ $rule->early_leave_tolerance_minutes }} menit
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if ($rule->is_required)
                                        <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-md border"
                                              style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border); color: var(--dash-primary-text);">
                                            Wajib
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-md border"
                                              style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border); color: var(--dash-text-muted);">
                                            Opsional
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <a href="{{ route('work-schedule-rules.show', $rule) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                                            Detail
                                        </a>
                                        @can('attendance.schedule_rule.manage')
                                            <a href="{{ route('work-schedule-rules.edit', $rule) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">edit</span>
                                                Edit
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($rules->hasPages())
                <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                    {{ $rules->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
