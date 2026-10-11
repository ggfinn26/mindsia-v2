@extends('layouts.dashboard')

@section('title', 'Ranking MPI Saya')
@section('header_title', 'Ranking MPI Saya')

@section('content')
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $latest = $snapshots->last();
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Ranking MPI Saya</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Skor MPI dan ranking Anda berdasarkan kelas dan pendaftaran member.</p>
        </div>
        @if($latest?->isFinalized())
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full shrink-0"
                  style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
                <span class="material-symbols-outlined text-[14px]">verified</span>
                Final
            </span>
        @elseif($latest)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full shrink-0"
                  style="background-color: #fef3c7; border: 1px solid #fde68a; color: #d97706;">
                <span class="material-symbols-outlined text-[14px]">pending</span>
                Sementara
            </span>
        @endif
    </div>

    {{-- Filter --}}
    <form method="GET"
          class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Bulan</label>
            <select name="month"
                    class="block w-full min-w-[140px] px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ now()->year + 1 }}"
                   class="block w-28 px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
        </div>
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                Terapkan
            </button>
            @if(request('month') || request('year'))
                <a href="{{ route('marketing.my-performance') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                    Reset
                </a>
            @endif
        </div>
    </form>

    @if($latest)

        {{-- Ranking Cards --}}
        <div>
            <p class="text-[11px] font-bold uppercase tracking-wider mb-3"
               style="color: var(--dash-text-faint);">Ranking {{ $months[$month] }} {{ $year }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                <div class="rounded-2xl p-5 flex items-center gap-4"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center"
                         style="background-color: color-mix(in srgb, var(--dash-primary) 10%, transparent);">
                        <span class="material-symbols-outlined text-[22px]"
                              style="color: var(--dash-primary);">location_on</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Rank Area</p>
                        <p class="text-2xl font-bold font-jakarta leading-tight"
                           style="color: var(--dash-text-heading);">
                            {{ $latest->rank_area ? '#'.$latest->rank_area : '—' }}
                        </p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">{{ $latest->area_name_snapshot ?? '—' }}</p>
                    </div>
                </div>

                <div class="rounded-2xl p-5 flex items-center gap-4"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center"
                         style="background-color: rgba(139,92,246,0.08);">
                        <span class="material-symbols-outlined text-[22px]"
                              style="color: #7c3aed;">map</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Rank Region</p>
                        <p class="text-2xl font-bold font-jakarta leading-tight"
                           style="color: var(--dash-text-heading);">
                            {{ $latest->rank_region ? '#'.$latest->rank_region : '—' }}
                        </p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">{{ $latest->region_name_snapshot ?? '—' }}</p>
                    </div>
                </div>

                <div class="rounded-2xl p-5 flex items-center gap-4"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center"
                         style="background-color: rgba(217,119,6,0.08);">
                        <span class="material-symbols-outlined text-[22px]"
                              style="color: #d97706;">public</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Rank Nasional</p>
                        <p class="text-2xl font-bold font-jakarta leading-tight"
                           style="color: var(--dash-text-heading);">
                            {{ $latest->rank_national ? '#'.$latest->rank_national : '—' }}
                        </p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">Seluruh Indonesia</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- MPI Score + Breakdown --}}
        <div>
            <p class="text-[11px] font-bold uppercase tracking-wider mb-3"
               style="color: var(--dash-text-faint);">Skor MPI & Metrik</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- MPI Score --}}
                <div class="rounded-2xl p-5 sm:col-span-2 lg:col-span-1 flex items-center gap-4"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center"
                         style="background-color: color-mix(in srgb, var(--dash-primary) 10%, transparent);">
                        <span class="material-symbols-outlined text-[22px]"
                              style="color: var(--dash-primary);">format_list_numbered</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Skor MPI</p>
                        <p class="text-2xl font-bold font-jakarta leading-tight"
                           style="color: var(--dash-primary);">
                            {{ $latest->mpi_score !== null ? number_format((float)$latest->mpi_score, 2) : '—' }}
                        </p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">50% kelas + 50% omzet</p>
                    </div>
                </div>

                {{-- Kelas --}}
                <div class="rounded-2xl p-5"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Kelas</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ (float)$latest->classes_percentage >= 100 ? 'bg-emerald-50 text-emerald-700' : ((float)$latest->classes_percentage >= 50 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }}">
                            {{ number_format((float)$latest->classes_percentage, 1) }}%
                        </span>
                    </div>
                    <p class="text-xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $latest->classes_actual ?? 0 }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">Target: {{ $latest->classes_target ?? 0 }} kelas</p>
                    <div class="mt-3 h-1.5 rounded-full overflow-hidden"
                         style="background-color: var(--dash-card-border);">
                        <div class="h-full rounded-full transition-all"
                             style="width: {{ min(100, (float)$latest->classes_percentage) }}%; background-color: var(--dash-primary);"></div>
                    </div>
                </div>

                {{-- Pendaftaran --}}
                <div class="rounded-2xl p-5"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Pendaftaran</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ (float)$latest->registration_value_percentage >= 100 ? 'bg-emerald-50 text-emerald-700' : ((float)$latest->registration_value_percentage >= 50 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }}">
                            {{ number_format((float)$latest->registration_value_percentage, 1) }}%
                        </span>
                    </div>
                    <p class="text-xl font-bold font-jakarta" style="color: var(--dash-text-heading);">
                        Rp {{ number_format((float)$latest->registration_value_actual, 0, ',', '.') }}
                    </p>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">Target: Rp {{ number_format((float)$latest->omzet_target, 0, ',', '.') }}</p>
                    <div class="mt-3 h-1.5 rounded-full overflow-hidden"
                         style="background-color: var(--dash-card-border);">
                        <div class="h-full rounded-full transition-all"
                             style="width: {{ min(100, (float)$latest->registration_value_percentage) }}%; background-color: #7c3aed;"></div>
                    </div>
                </div>

                {{-- Member --}}
                <div class="rounded-2xl p-5"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <p class="text-xs font-medium mb-3" style="color: var(--dash-text-muted);">Member</p>
                    <div class="flex items-end gap-4">
                        <div>
                            <p class="text-xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $latest->fixed_members_count ?? 0 }}</p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">Fixed</p>
                        </div>
                        <div class="text-lg font-light mb-1" style="color: var(--dash-card-border);">/</div>
                        <div>
                            <p class="text-xl font-bold font-jakarta" style="color: var(--dash-text-muted);">{{ $latest->prospective_members_count ?? 0 }}</p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">Calon</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Snapshot History --}}
        @if($snapshots->count() > 1)
        <div>
            <p class="text-[11px] font-bold uppercase tracking-wider mb-3"
               style="color: var(--dash-text-faint);">Riwayat Snapshot</p>
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-5 py-4 flex items-center justify-between"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Riwayat Snapshot</h2>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                          style="background-color: var(--dash-card-border); color: var(--dash-text-muted);">{{ $snapshots->count() }} snapshot</span>
                </div>
                <div class="w-full overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--dash-card-border);">
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">#</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">MPI</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Rank Area</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Rank Nasional</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Kelas</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Omzet</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($snapshots->reverse() as $snap)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3.5 text-xs" style="color: var(--dash-text-faint);">{{ $snap->id }}</td>
                                <td class="px-5 py-3.5 font-bold" style="color: var(--dash-primary);">
                                    {{ $snap->mpi_score !== null ? number_format((float)$snap->mpi_score, 2) : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-center" style="color: var(--dash-text-body);">
                                    {{ $snap->rank_area ? '#'.$snap->rank_area : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-center" style="color: var(--dash-text-body);">
                                    {{ $snap->rank_national ? '#'.$snap->rank_national : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right tabular-nums" style="color: var(--dash-text-body);">
                                    {{ $snap->classes_actual ?? 0 }} / {{ $snap->classes_target ?? 0 }}
                                    <span class="text-xs" style="color: var(--dash-text-faint);">({{ number_format((float)$snap->classes_percentage, 1) }}%)</span>
                                </td>
                                <td class="px-5 py-3.5 text-right tabular-nums" style="color: var(--dash-text-body);">
                                    Rp {{ number_format((float)$snap->registration_value_actual, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($snap->isFinalized())
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700">Final</span>
                                    @elseif($loop->first)
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-700">Terbaru</span>
                                    @else
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Lama</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    @else

        {{-- Empty State --}}
        <div class="rounded-2xl px-5 py-20 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                  style="color: var(--dash-text-faint);">format_list_numbered</span>
            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data MPI untuk {{ $months[$month] }} {{ $year }}.</p>
            <p class="text-sm mt-1" style="color: var(--dash-text-faint);">Data akan muncul setelah KPI Anda diproses oleh sistem.</p>
        </div>

    @endif

</div>
@endsection
