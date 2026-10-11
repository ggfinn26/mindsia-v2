@extends('layouts.dashboard')

@section('title', 'Papan Krisis - MINDSIA')
@section('header_title', 'Papan Krisis')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Strategic Alert Center</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Papan Krisis</h1>
        </div>
        @if($total > 0)
            <span class="inline-flex items-center gap-1.5 rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-semibold text-red-700 shrink-0">
                <span class="inline-block h-2 w-2 rounded-full bg-red-500"></span>
                {{ $total }} alert aktif
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-700 shrink-0">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                Semua aman
            </span>
        @endif
    </div>

    @if($total === 0)
    <div class="rounded-2xl px-5 py-16 text-center"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
              style="color: var(--dash-text-faint);">check_circle</span>
        <p class="font-semibold" style="color: var(--dash-text-muted);">Tidak ada alert aktif</p>
        <p class="mt-1 text-sm" style="color: var(--dash-text-faint);">Semua indikator dalam kondisi normal</p>
    </div>
    @else

    {{-- Section: Segera --}}
    @if($totalSegera > 0)
    <div class="space-y-4">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="material-symbols-outlined text-[20px] text-red-500">error</span>
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Segera</h2>
            <span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">{{ $totalSegera }}</span>
            <span class="text-sm" style="color: var(--dash-text-faint);">Tindakan diperlukan hari ini</span>
        </div>

        @foreach($groupedSegera as $moduleLabel => $moduleAlerts)
        <div>
            <div class="mb-2 flex items-center gap-2">
                <span class="inline-block h-4 w-1 rounded-full bg-red-400"></span>
                <span class="text-xs font-bold uppercase tracking-wide" style="color: var(--dash-text-muted);">{{ $moduleLabel }}</span>
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">{{ count($moduleAlerts) }}</span>
            </div>
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Subjek</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Cabang</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Detail</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Kategori</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($moduleAlerts as $alert)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block h-1.5 w-1.5 shrink-0 rounded-full bg-red-400"></span>
                                        <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $alert->subject }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm" style="color: var(--dash-text-body);">{{ $alert->branchName }}</td>
                                <td class="px-5 py-3.5 text-sm max-w-xs" style="color: var(--dash-text-muted);">{{ $alert->detail }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">{{ $alert->category }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ $alert->url }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[13px]">visibility</span>Lihat
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Section: Perhatian --}}
    @if($totalPerhatian > 0)
    <div class="space-y-4">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="material-symbols-outlined text-[20px] text-amber-500">warning</span>
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Perhatian</h2>
            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">{{ $totalPerhatian }}</span>
            <span class="text-sm" style="color: var(--dash-text-faint);">Perlu ditindaklanjuti dalam waktu dekat</span>
        </div>

        @foreach($groupedPerhatian as $moduleLabel => $moduleAlerts)
        <div>
            <div class="mb-2 flex items-center gap-2">
                <span class="inline-block h-4 w-1 rounded-full bg-amber-400"></span>
                <span class="text-xs font-bold uppercase tracking-wide" style="color: var(--dash-text-muted);">{{ $moduleLabel }}</span>
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">{{ count($moduleAlerts) }}</span>
            </div>
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Subjek</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Cabang</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Detail</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Kategori</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                                    style="color: var(--dash-text-muted);">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($moduleAlerts as $alert)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block h-1.5 w-1.5 shrink-0 rounded-full bg-amber-400"></span>
                                        <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $alert->subject }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm" style="color: var(--dash-text-body);">{{ $alert->branchName }}</td>
                                <td class="px-5 py-3.5 text-sm max-w-xs" style="color: var(--dash-text-muted);">{{ $alert->detail }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">{{ $alert->category }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ $alert->url }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[13px]">visibility</span>Lihat
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @endif
</div>
@endsection
