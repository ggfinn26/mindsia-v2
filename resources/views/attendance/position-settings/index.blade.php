@extends('layouts.dashboard')

@section('title', 'Pengaturan Absensi Per Posisi - MINDSIA')
@section('header_title', 'Pengaturan Absensi Per Posisi')

@section('content')

<div class="max-w-5xl mx-auto pt-8 sm:pt-16 space-y-4">
    <div class="mb-5 sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight text-[color:var(--dash-text-heading)]">Pengaturan Absensi Per Posisi</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px] leading-none">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        <div class="px-6 py-4 border-b flex items-center gap-2.5"
             style="border-color: var(--dash-card-border);">
            <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Daftar Posisi</h3>
            <span class="text-xs ml-1" style="color: var(--dash-text-muted);">Klik Edit untuk atur kebijakan absensi per posisi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Posisi</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Absen Kerja</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Absen Sesi</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Scope Lokasi</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Toleransi Masuk</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Toleransi Pulang</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($positions as $position)
                        @php $s = $position->attendanceSetting; @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);">
                            <td class="px-6 py-4 font-medium" style="color: var(--dash-text-heading);">
                                {{ $position->position_name }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if(!$s)
                                    <span class="text-xs" style="color: var(--dash-text-faint);">Pilih...</span>
                                @elseif($s->is_required)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium" style="background-color:#f0fdf4;color:#059669;">Manual</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">Auto Hadir</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if(!$s)
                                    <span class="text-xs" style="color: var(--dash-text-faint);">Pilih...</span>
                                @elseif($s->is_session_required)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium" style="background-color:#f0fdf4;color:#059669;">Ya</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: var(--dash-card-border); color: var(--dash-text-muted);">Tidak</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center text-xs" style="color: var(--dash-text-body);">
                                @if(!$s)
                                    <span style="color: var(--dash-text-faint);">Pilih...</span>
                                @else
                                    {{ match($s->attendance_scope) {
                                        'branch'   => 'Cabang Sendiri',
                                        'area'     => 'Semua Cabang Area',
                                        'anywhere' => 'Bebas (Remote OK)',
                                        default    => $s->attendance_scope,
                                    } }}
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center text-xs" style="color: var(--dash-text-body);">
                                @if(!$s || $s->early_checkin_minutes === null)
                                    <span style="color: var(--dash-text-faint);">Bebas</span>
                                @else
                                    {{ $s->early_checkin_minutes }} menit sebelum
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center text-xs" style="color: var(--dash-text-body);">
                                @if(!$s || $s->early_checkout_minutes === null)
                                    <span style="color: var(--dash-text-faint);">Tepat waktu</span>
                                @else
                                    {{ $s->early_checkout_minutes }} menit sebelum
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('attendance.position-settings.edit', $position) }}"
 class="inline-flex items-center justify-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] border border-[color:var(--dash-input-border)] shadow-sm hover:bg-[var(--dash-nav-hover-bg)] hover:border-[color:var(--dash-text-muted)]"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
