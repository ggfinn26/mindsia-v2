@props(['data'])

@php
    $score = $data['mpi_score'] ?? null;
    $scoreColor = match(true) {
        $score === null => 'text-gray-400',
        $score >= 80 => 'text-emerald-600',
        $score >= 60 => 'text-amber-500',
        default => 'text-red-500',
    };
    $bgColor = match(true) {
        $score === null => 'bg-gray-50',
        $score >= 80 => 'bg-emerald-50',
        $score >= 60 => 'bg-amber-50',
        default => 'bg-red-50',
    };
    
    // Status text based on score
    $statusText = match(true) {
        $score === null => 'Belum Ada',
        $score >= 80 => 'Sangat Baik',
        $score >= 60 => 'Cukup',
        default => 'Perlu Perbaikan',
    };

    $formatCompact = function($num) {
        if ($num === null) return '—';
        if ($num >= 1000000000) return rtrim(rtrim(number_format($num / 1000000000, 1, ',', '.'), '0'), ',') . 'M';
        if ($num >= 1000000) return rtrim(rtrim(number_format($num / 1000000, 1, ',', '.'), '0'), ',') . 'Jt';
        if ($num >= 10000) return rtrim(rtrim(number_format($num / 1000, 1, ',', '.'), '0'), ',') . 'Rb';
        return number_format($num, 0, ',', '.');
    };
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow flex flex-col h-full">
    {{-- Header --}}
    <div class="flex items-start justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-violet-50 border border-violet-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-violet-600 text-xl">speed</span>
            </div>
            <div>
                <h3 class="text-sm font-bold font-jakarta text-gray-900">MPI Progress</h3>
                <p class="text-xs font-medium text-gray-500">Periode {{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if($score !== null)
            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider {{ $score >= 80 ? 'bg-emerald-100 text-emerald-700' : ($score >= 60 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                {{ $statusText }}
            </span>
        @endif
    </div>

    @if($score === null)
        <div class="flex-1 flex flex-col items-center justify-center py-6">
            <span class="material-symbols-outlined text-gray-200 text-4xl mb-2">pending_actions</span>
            <p class="text-sm font-medium text-gray-400 text-center">Belum ada data MPI bulan ini</p>
        </div>
    @else
        {{-- MPI score bar --}}
        <div class="{{ $bgColor }} rounded-xl p-4 mb-5 border {{ $score >= 80 ? 'border-emerald-100' : ($score >= 60 ? 'border-amber-100' : 'border-red-100') }}">
            <div class="flex items-end gap-1.5 mb-2">
                <span class="text-4xl font-black font-jakarta {{ $scoreColor }} leading-none">{{ number_format($score, 1, ',', '.') }}</span>
                <span class="text-sm font-bold text-gray-400 mb-1">/ 100</span>
            </div>
            <div class="h-2.5 bg-white/60 rounded-full overflow-hidden border border-black/5 shadow-inner">
                <div class="h-full rounded-full transition-all duration-1000 ease-out
                    {{ $score >= 80 ? 'bg-emerald-500' : ($score >= 60 ? 'bg-amber-400' : 'bg-red-500') }}"
                    style="width: {{ min($score, 100) }}%">
                </div>
            </div>
        </div>

        {{-- Target vs Aktual --}}
        <div class="mb-5 overflow-x-auto custom-scrollbar">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-[10px] text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="text-left pb-2 font-semibold">Metrik</th>
                        <th class="text-right pb-2 font-semibold whitespace-nowrap">Target</th>
                        <th class="text-right pb-2 font-semibold whitespace-nowrap">Aktual</th>
                        <th class="text-right pb-2 font-semibold">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($data['metrics'] ?? [] as $m)
                        @php
                            $pct = $m['pct'];
                            $pctColor = match(true) {
                                $pct === null => 'text-gray-400',
                                $pct >= 100 => 'text-emerald-600 font-bold',
                                $pct >= 80 => 'text-amber-500 font-semibold',
                                default => 'text-red-500 font-semibold',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-2 flex items-center gap-2 text-gray-700 font-medium whitespace-nowrap">
                                <span class="material-symbols-outlined text-gray-400 text-[18px]">{{ $m['icon'] }}</span>
                                {{ $m['label'] }}
                            </td>
                            <td class="py-2 text-right text-gray-500 whitespace-nowrap">
                                {{ $formatCompact($m['target']) }}
                            </td>
                            <td class="py-2 text-right text-gray-900 font-bold whitespace-nowrap">
                                {{ $formatCompact($m['actual']) }}
                            </td>
                            <td class="py-2 text-right whitespace-nowrap {{ $pctColor }}">
                                {{ $pct !== null ? number_format($pct, 1, ',', '.').'%' : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Rankings --}}
        @if($data['rank_area'] || $data['rank_region'] || $data['rank_national'])
            <div class="flex items-center gap-2 mb-5 flex-wrap">
                @foreach([
                    ['label' => 'Area', 'rank' => $data['rank_area']],
                    ['label' => 'Region', 'rank' => $data['rank_region']],
                    ['label' => 'Nasional', 'rank' => $data['rank_national']],
                ] as $r)
                    @if($r['rank'])
                        <span class="text-[11px] px-2.5 py-1 rounded-md bg-violet-50 text-violet-700 font-bold border border-violet-100 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">workspace_premium</span>
                            #{{ $r['rank'] }} {{ $r['label'] }}
                        </span>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Criteria breakdown --}}
        @if(!empty($data['criteria']))
            <div class="pt-4 border-t border-gray-100 space-y-2 mb-4">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Komponen Nilai</p>
                @foreach($data['criteria'] as $c)
                    <div class="flex items-center justify-between group">
                        <span class="text-xs font-medium text-gray-600 truncate max-w-[70%] group-hover:text-gray-900 transition-colors">{{ $c['name'] }}</span>
                        <span class="text-xs font-bold text-violet-600 bg-violet-50 px-2 py-0.5 rounded">{{ number_format($c['value'], 1, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Spacer to push trend to bottom if needed --}}
        <div class="flex-1"></div>

        {{-- Trend history --}}
        <div class="pt-4 border-t border-gray-100 mt-2">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Riwayat 6 Bulan Terakhir</p>
                <span class="material-symbols-outlined text-gray-300 text-[16px]">show_chart</span>
            </div>
            
            @php 
                // Generate last 6 months exactly
                $historyData = $data['history'] ?? [];
                $currentPeriod = $data['period'] ?? date('n/Y');
                $parts = explode('/', $currentPeriod);
                $currMonth = (int)($parts[0] ?? date('n'));
                $currYear = (int)($parts[1] ?? date('Y'));
                
                $filledHistory = [];
                for ($i = 5; $i >= 0; $i--) {
                    $m = $currMonth - $i;
                    $y = $currYear;
                    if ($m <= 0) {
                        $m += 12;
                        $y -= 1;
                    }
                    $periodKey = $m . '/' . $y;
                    
                    $found = null;
                    foreach ($historyData as $h) {
                        if (isset($h['period']) && $h['period'] === $periodKey) {
                            $found = $h;
                            break;
                        }
                    }
                    
                    if ($found) {
                        $filledHistory[] = $found;
                    } else {
                        $filledHistory[] = ['period' => $periodKey, 'mpi_score' => null];
                    }
                }

                $scores = array_filter(array_column($filledHistory, 'mpi_score'), fn($s) => $s !== null);
                $maxScore = !empty($scores) ? max($scores) : 100;
                if ($maxScore == 0) $maxScore = 100;
            @endphp

            <div class="flex items-end gap-1.5 h-12">
                @foreach($filledHistory as $h)
                    @php 
                        $hasData = $h['mpi_score'] !== null;
                        $barH = $hasData ? max(15, round(($h['mpi_score'] / $maxScore) * 100)) : 100; 
                        $barColor = match(true) {
                            !$hasData => 'bg-gray-100',
                            $h['mpi_score'] >= 80 => 'bg-emerald-500',
                            $h['mpi_score'] >= 60 => 'bg-amber-400',
                            default => 'bg-red-500',
                        };
                        $tooltip = $hasData ? $h['period'] . ': ' . number_format($h['mpi_score'], 1, ',', '.') : $h['period'] . ': Belum ada data';
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1 group cursor-default" title="{{ $tooltip }}">
                        <div class="w-full max-w-[40px] rounded-t-sm transition-all duration-300 {{ $hasData ? 'group-hover:opacity-80' : '' }} {{ $barColor }}"
                            style="height: {{ $barH }}%;">
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex gap-1.5 mt-1.5">
                @foreach($filledHistory as $h)
                    @php $monthLabel = explode('/', $h['period'])[0]; @endphp
                    <div class="flex-1 text-center text-[9px] font-medium {{ $h['mpi_score'] !== null ? 'text-gray-600' : 'text-gray-300' }}">{{ $monthLabel }}</div>
                @endforeach
            </div>
        </div>
    @endif
</div>
