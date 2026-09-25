@extends('layouts.dashboard')

@section('title', 'Papan Krisis')
@section('header_title', 'Papan Krisis')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Strategic Alert Center</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Papan Krisis</h2>
        </div>
        @if ($total > 0)
            <span class="inline-flex items-center gap-1.5 rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-semibold text-red-700">
                <span class="inline-block h-2 w-2 rounded-full bg-red-500"></span>
                {{ $total }} alert aktif
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-700">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                Semua aman
            </span>
        @endif
    </div>

    @if ($total === 0)
        <div class="border border-slate-200 bg-white px-5 py-16 text-center">
            <span class="material-symbols-outlined text-4xl text-slate-300">check_circle</span>
            <p class="mt-2 font-semibold text-slate-700">Tidak ada alert aktif</p>
            <p class="mt-1 text-sm text-slate-400">Semua indikator dalam kondisi normal</p>
        </div>
    @else

        {{-- Section: Segera --}}
        @if ($totalSegera > 0)
        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-red-500">error</span>
                <h3 class="font-jakarta text-base font-bold text-slate-900">Segera</h3>
                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">{{ $totalSegera }}</span>
                <span class="text-sm text-slate-400">Tindakan diperlukan hari ini</span>
            </div>

            @foreach ($groupedSegera as $moduleLabel => $moduleAlerts)
                <div>
                    <div class="mb-2 flex items-center gap-2">
                        <span class="inline-block h-4 w-1 rounded-full bg-red-400"></span>
                        <span class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $moduleLabel }}</span>
                        <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ count($moduleAlerts) }}</span>
                    </div>
                    <div class="overflow-hidden border border-slate-200 bg-white">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                                    <tr>
                                        <th class="px-5 py-3">Subjek</th>
                                        <th class="px-5 py-3">Cabang</th>
                                        <th class="px-5 py-3">Detail</th>
                                        <th class="px-5 py-3">Kategori</th>
                                        <th class="px-5 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($moduleAlerts as $alert)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-block h-1.5 w-1.5 flex-shrink-0 rounded-full bg-red-400"></span>
                                                    <span class="font-semibold text-slate-900">{{ $alert->subject }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-slate-600">{{ $alert->branchName }}</td>
                                            <td class="max-w-xs px-5 py-4 text-slate-600">{{ $alert->detail }}</td>
                                            <td class="px-5 py-4">
                                                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">{{ $alert->category }}</span>
                                            </td>
                                            <td class="px-5 py-4 text-right">
                                                <a href="{{ $alert->url }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Lihat</a>
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
        @if ($totalPerhatian > 0)
        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-amber-500">warning</span>
                <h3 class="font-jakarta text-base font-bold text-slate-900">Perhatian</h3>
                <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">{{ $totalPerhatian }}</span>
                <span class="text-sm text-slate-400">Perlu ditindaklanjuti dalam waktu dekat</span>
            </div>

            @foreach ($groupedPerhatian as $moduleLabel => $moduleAlerts)
                <div>
                    <div class="mb-2 flex items-center gap-2">
                        <span class="inline-block h-4 w-1 rounded-full bg-amber-400"></span>
                        <span class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $moduleLabel }}</span>
                        <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ count($moduleAlerts) }}</span>
                    </div>
                    <div class="overflow-hidden border border-slate-200 bg-white">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                                    <tr>
                                        <th class="px-5 py-3">Subjek</th>
                                        <th class="px-5 py-3">Cabang</th>
                                        <th class="px-5 py-3">Detail</th>
                                        <th class="px-5 py-3">Kategori</th>
                                        <th class="px-5 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($moduleAlerts as $alert)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-block h-1.5 w-1.5 flex-shrink-0 rounded-full bg-amber-400"></span>
                                                    <span class="font-semibold text-slate-900">{{ $alert->subject }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-slate-600">{{ $alert->branchName }}</td>
                                            <td class="max-w-xs px-5 py-4 text-slate-600">{{ $alert->detail }}</td>
                                            <td class="px-5 py-4">
                                                <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">{{ $alert->category }}</span>
                                            </td>
                                            <td class="px-5 py-4 text-right">
                                                <a href="{{ $alert->url }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Lihat</a>
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
