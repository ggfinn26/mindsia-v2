@extends('layouts.dashboard')

@section('title', 'Papan Krisis - MINDSIA')
@section('header_title', 'Papan Krisis')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

    {{-- Subheader row --}}
    <div class="flex items-center justify-between gap-4">
        <p class="text-xs font-semibold uppercase tracking-widest" style="color: var(--dash-primary-text);">
            Strategic Alert Center
        </p>
        @if ($total > 0)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold"
                  style="background-color: #fee2e2; border: 1px solid #fca5a5; color: #dc2626;">
                <span class="inline-block h-2 w-2 rounded-full" style="background-color: #dc2626;"></span>
                {{ $total }} alert aktif
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold"
                  style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
                <span class="inline-block h-2 w-2 rounded-full" style="background-color: #059669;"></span>
                Semua aman
            </span>
        @endif
    </div>

    @if ($total === 0)
        <div class="rounded-2xl px-5 py-16 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined text-4xl" style="color: var(--dash-text-faint);">check_circle</span>
            <p class="mt-2 font-semibold" style="color: var(--dash-text-heading);">Tidak ada alert aktif</p>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Semua indikator dalam kondisi normal</p>
        </div>
    @else

        {{-- Section: Segera --}}
        @if ($totalSegera > 0)
        <div class="space-y-4">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[20px]" style="color: #dc2626;">error</span>
                <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Segera</h3>
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                      style="background-color: #fee2e2; color: #dc2626;">{{ $totalSegera }}</span>
                <span class="text-xs" style="color: var(--dash-text-muted);">Tindakan diperlukan hari ini</span>
            </div>

            @foreach ($groupedSegera as $moduleLabel => $moduleAlerts)
                <div class="rounded-2xl overflow-hidden"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="flex items-center gap-2.5 px-5 py-3 border-b"
                         style="border-color: var(--dash-card-border);">
                        <span class="inline-block h-4 w-[3px] rounded-full" style="background-color: #dc2626;"></span>
                        <span class="text-xs font-bold uppercase tracking-wide" style="color: var(--dash-text-muted);">{{ $moduleLabel }}</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium"
                              style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">{{ count($moduleAlerts) }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--dash-card-border);">
                                    <th class="px-5 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Subjek</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Cabang</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Detail</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Kategori</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold" style="color: var(--dash-text-muted);">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($moduleAlerts as $alert)
                                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-block h-1.5 w-1.5 shrink-0 rounded-full" style="background-color: #dc2626;"></span>
                                                <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $alert->subject }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4" style="color: var(--dash-text-body);">{{ $alert->branchName }}</td>
                                        <td class="max-w-xs px-5 py-4" style="color: var(--dash-text-body);">{{ $alert->detail }}</td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold"
                                                  style="background-color: #fee2e2; color: #dc2626;">{{ $alert->category }}</span>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ $alert->url }}"
                                               class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">Lihat</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Section: Perhatian --}}
        @if ($totalPerhatian > 0)
        <div class="space-y-4">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[20px]" style="color: #d97706;">warning</span>
                <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Perhatian</h3>
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                      style="background-color: #fef3c7; color: #d97706;">{{ $totalPerhatian }}</span>
                <span class="text-xs" style="color: var(--dash-text-muted);">Perlu ditindaklanjuti dalam waktu dekat</span>
            </div>

            @foreach ($groupedPerhatian as $moduleLabel => $moduleAlerts)
                <div class="rounded-2xl overflow-hidden"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="flex items-center gap-2.5 px-5 py-3 border-b"
                         style="border-color: var(--dash-card-border);">
                        <span class="inline-block h-4 w-[3px] rounded-full" style="background-color: #d97706;"></span>
                        <span class="text-xs font-bold uppercase tracking-wide" style="color: var(--dash-text-muted);">{{ $moduleLabel }}</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium"
                              style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">{{ count($moduleAlerts) }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--dash-card-border);">
                                    <th class="px-5 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Subjek</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Cabang</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Detail</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Kategori</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold" style="color: var(--dash-text-muted);">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($moduleAlerts as $alert)
                                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-block h-1.5 w-1.5 shrink-0 rounded-full" style="background-color: #d97706;"></span>
                                                <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $alert->subject }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4" style="color: var(--dash-text-body);">{{ $alert->branchName }}</td>
                                        <td class="max-w-xs px-5 py-4" style="color: var(--dash-text-body);">{{ $alert->detail }}</td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold"
                                                  style="background-color: #fef3c7; color: #d97706;">{{ $alert->category }}</span>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ $alert->url }}"
                                               class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">Lihat</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

    @endif
</div>
@endsection
