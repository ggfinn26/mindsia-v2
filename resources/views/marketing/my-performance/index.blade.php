@extends('layouts.dashboard')

@section('title', 'Ranking MPI Saya - MINDSIA')
@section('header_title', 'Ranking MPI Saya')

@section('content')
<h1 class="sr-only">Ranking MPI Saya</h1>
@php
    $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $latest = $snapshots->last();
@endphp

<div class="max-w-6xl mx-auto space-y-5">

    {{-- Subheader --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <p class="text-sm" style="color: var(--dash-text-muted);">
            Skor MPI dan ranking Anda berdasarkan kelas dan pendaftaran member.
        </p>
        @if($latest?->isFinalized())
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold shrink-0"
                  style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
                <span class="material-symbols-outlined text-[14px]">verified</span>Final
            </span>
        @elseif($latest)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold shrink-0"
                  style="background-color: #fef3c7; border: 1px solid #fde68a; color: #d97706;">
                <span class="material-symbols-outlined text-[14px]">pending</span>Sementara (belum final)
            </span>
        @endif
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap gap-4 items-end px-5 py-4 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Bulan</label>
            <select name="month" class="block min-w-[140px]">
                @foreach (array_slice($months, 1, null, true) as $num => $label)
                    <option value="{{ $num }}" @selected($month == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ now()->year + 1 }}"
                   class="block w-28">
        </div>
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors inline-flex items-center gap-1.5"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                Terapkan Filter
            </button>
            @if(request('month') != now()->month || request('year') != now()->year)
                <a href="{{ route('marketing.my-performance') }}"
                   class="px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1.5"
                   style="background-color: var(--dash-primary-bg); color: var(--dash-text-muted);"><span class="material-symbols-outlined text-[16px]">restart_alt</span>Reset</a>
            @endif
        </div>
    </form>

    @if($latest)

        {{-- Ranking Cards --}}
        <div>
            <p class="text-[11px] font-bold uppercase tracking-wider mb-3" style="color: var(--dash-text-faint);">
                Ranking {{ $months[$month] }} {{ $year }}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach ([
                    ['icon' => 'location_on', 'label' => 'Rank Area',    'value' => $latest->rank_area ? '#'.$latest->rank_area : '-', 'sub' => $latest->area_name_snapshot ?? '-',   'color' => 'var(--dash-primary)'],
                    ['icon' => 'map',         'label' => 'Rank Region',  'value' => $latest->rank_region ? '#'.$latest->rank_region : '-', 'sub' => $latest->region_name_snapshot ?? '-', 'color' => '#7c3aed'],
                    ['icon' => 'public',      'label' => 'Rank Nasional','value' => $latest->rank_national ? '#'.$latest->rank_national : '-', 'sub' => 'Seluruh Indonesia',             'color' => '#d97706'],
                ] as $card)
                    <div class="flex items-center gap-4 p-5 rounded-2xl"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                        <div class="shrink-0 w-11 h-11 rounded-full flex items-center justify-center"
                             style="background-color: var(--dash-primary-bg);">
                            <span class="material-symbols-outlined text-[22px]" style="color: {{ $card['color'] }};">{{ $card['icon'] }}</span>
                        </div>
                        <div>
                            <p class="text-xs font-medium" style="color: var(--dash-text-muted);">{{ $card['label'] }}</p>
                            <p class="text-2xl font-bold font-jakarta leading-tight" style="color: var(--dash-text-heading);">{{ $card['value'] }}</p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">{{ $card['sub'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- MPI Score + Breakdown --}}
        <div>
            <p class="text-[11px] font-bold uppercase tracking-wider mb-3" style="color: var(--dash-text-faint);">Skor MPI & Metrik</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- MPI Score --}}
                <div class="flex items-center gap-4 p-5 rounded-2xl"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-primary); box-shadow: 0 0 0 1px color-mix(in srgb, var(--dash-primary) 15%, transparent);">
                    <div class="shrink-0 w-11 h-11 rounded-full flex items-center justify-center"
                         style="background-color: var(--dash-primary-bg);">
                        <span class="material-symbols-outlined text-[22px]" style="color: var(--dash-primary);">format_list_numbered</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Skor MPI</p>
                        <p class="text-2xl font-bold font-jakarta leading-tight" style="color: var(--dash-primary);">
                            {{ $latest->mpi_score !== null ? number_format((float)$latest->mpi_score, 2) : '-' }}
                        </p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">50% kelas + 50% omzet</p>
                    </div>
                </div>

                {{-- Kelas --}}
                <div class="p-5 rounded-2xl" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    @php $classPct = (float)$latest->classes_percentage; @endphp
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Kelas</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-lg"
                              style="{{ $classPct >= 100 ? 'background-color:#f0fdf4;color:#059669;' : ($classPct >= 50 ? 'background-color:#fef3c7;color:#d97706;' : 'background-color:#fee2e2;color:#dc2626;') }}">
                            {{ number_format($classPct, 1) }}%
                        </span>
                    </div>
                    <p class="text-xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $latest->classes_actual ?? 0 }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">Target: {{ $latest->classes_target ?? 0 }} kelas</p>
                    <div class="mt-3 h-1.5 rounded-full overflow-hidden" style="background-color: var(--dash-card-border);">
                        <div class="h-full rounded-full transition-all" style="width: {{ min(100, $classPct) }}%; background-color: var(--dash-primary);"></div>
                    </div>
                </div>

                {{-- Pendaftaran --}}
                <div class="p-5 rounded-2xl" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    @php $regPct = (float)$latest->registration_value_percentage; @endphp
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Pendaftaran</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-lg"
                              style="{{ $regPct >= 100 ? 'background-color:#f0fdf4;color:#059669;' : ($regPct >= 50 ? 'background-color:#fef3c7;color:#d97706;' : 'background-color:#fee2e2;color:#dc2626;') }}">
                            {{ number_format($regPct, 1) }}%
                        </span>
                    </div>
                    <p class="text-xl font-bold font-jakarta" style="color: var(--dash-text-heading);">
                        Rp {{ number_format((float)$latest->registration_value_actual, 0, ',', '.') }}
                    </p>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">Target: Rp {{ number_format((float)$latest->omzet_target, 0, ',', '.') }}</p>
                    <div class="mt-3 h-1.5 rounded-full overflow-hidden" style="background-color: var(--dash-card-border);">
                        <div class="h-full rounded-full transition-all" style="width: {{ min(100, $regPct) }}%; background-color: #7c3aed;"></div>
                    </div>
                </div>

                {{-- Member --}}
                <div class="p-5 rounded-2xl" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
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
        <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-5 py-4 border-b flex items-center justify-between"
                 style="border-color: var(--dash-card-border);">
                <div class="flex items-center gap-2.5">
                    <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                    <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Riwayat Snapshot</h3>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                      style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">{{ $snapshots->count() }} snapshot</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap" style="color: var(--dash-text-muted);">#</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap" style="color: var(--dash-text-muted);">MPI</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide whitespace-nowrap" style="color: var(--dash-text-muted);">Rank Area</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide whitespace-nowrap" style="color: var(--dash-text-muted);">Rank Nasional</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide whitespace-nowrap" style="color: var(--dash-text-muted);">Kelas</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide whitespace-nowrap" style="color: var(--dash-text-muted);">Omzet</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap" style="color: var(--dash-text-muted);">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($snapshots->reverse() as $snap)
                        <tr style="border-bottom: 1px solid var(--dash-card-border); {{ $loop->first ? 'background-color: var(--dash-primary-bg);' : '' }}">
                            <td class="px-5 py-3.5 text-xs" style="color: var(--dash-text-faint);">{{ $snap->id }}</td>
                            <td class="px-5 py-3.5 font-bold" style="color: var(--dash-primary);">
                                {{ $snap->mpi_score !== null ? number_format((float)$snap->mpi_score, 2) : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-center" style="color: var(--dash-text-body);">
                                {{ $snap->rank_area ? '#'.$snap->rank_area : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-center" style="color: var(--dash-text-body);">
                                {{ $snap->rank_national ? '#'.$snap->rank_national : '-' }}
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
                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold"
                                          style="background-color: #f0fdf4; color: #059669;">Final</span>
                                @elseif($loop->first)
                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold"
                                          style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">Terbaru</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold"
                                          style="background-color: var(--dash-card-border); color: var(--dash-text-muted);">Lama</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    @else
        <div class="rounded-2xl px-5 py-20 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined text-5xl block mb-3" style="color: var(--dash-text-faint);">format_list_numbered</span>
            <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">Belum ada data MPI untuk {{ $months[$month] }} {{ $year }}.</p>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Data muncul setelah KPI Anda diproses oleh sistem.</p>
        </div>
    @endif

</div>
@endsection
