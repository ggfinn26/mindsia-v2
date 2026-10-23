@props(['data'])

@php
    $score = $data['mpi_score'] ?? null;
    $scoreTextColor = match(true) {
        $score === null => 'var(--dash-text-muted)',
        $score >= 80 => 'var(--dash-status-success-text)',
        $score >= 60 => 'var(--dash-status-warning-text)',
        default => 'var(--dash-status-danger-text)',
    };
    $scoreBg = match(true) {
        $score === null => 'var(--dash-nav-hover-bg)',
        $score >= 80 => 'var(--dash-status-success-bg)',
        $score >= 60 => 'var(--dash-status-warning-bg)',
        default => 'var(--dash-status-danger-bg)',
    };
    
    $statusText = match(true) {
        $score === null => 'Belum Ada',
        $score >= 80 => 'Sangat Baik',
        $score >= 60 => 'Cukup',
        default => 'Perlu Perbaikan',
    };

    $formatCompact = function($num) {
        if ($num === null) return '-';
        if ($num >= 1000000000) return rtrim(rtrim(number_format($num / 1000000000, 1, ',', '.'), '0'), ',') . 'M';
        if ($num >= 1000000) return rtrim(rtrim(number_format($num / 1000000, 1, ',', '.'), '0'), ',') . 'Jt';
        if ($num >= 10000) return rtrim(rtrim(number_format($num / 1000, 1, ',', '.'), '0'), ',') . 'Rb';
        return number_format($num, 0, ',', '.');
    };
@endphp

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    {{-- Header --}}
    <div class="flex items-start justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-xl">speed</span>
            </div>
            <div>
                <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">MPI Progress</h3>
                <p class="text-xs" style="color: var(--dash-text-muted);">Periode {{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if($score !== null)
            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider" style="background-color: {{ $scoreBg }}; color: {{ $scoreTextColor }};">
                {{ $statusText }}
            </span>
        @endif
    </div>

    @if($score === null)
        <div class="flex-1 flex flex-col items-center justify-center py-6">
            <span class="material-symbols-outlined text-4xl mb-2" style="color: var(--dash-text-muted);">pending_actions</span>
            <p class="text-sm font-medium text-center" style="color: var(--dash-text-muted);">Belum ada data MPI bulan ini</p>
        </div>
    @else
        {{-- MPI score bar --}}
        <div class="rounded-lg p-4 mb-5" style="background-color: {{ $scoreBg }}; border: 1px solid var(--dash-card-border);">
            <div class="flex items-end gap-1.5 mb-2">
                <span class="text-4xl font-black font-jakarta leading-none" style="color: {{ $scoreTextColor }};">{{ number_format($score, 1, ',', '.') }}</span>
                <span class="text-sm font-bold mb-1" style="color: var(--dash-text-muted);">/ 100</span>
            </div>
            <div class="h-2.5 rounded-full overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="h-full rounded-full transition-all duration-1000 ease-out"
                    style="width: {{ min($score, 100) }}%; background-color: {{ $scoreTextColor }};">
                </div>
            </div>
        </div>

        {{-- Target vs Aktual --}}
        <div class="mb-5 overflow-x-auto custom-scrollbar">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-[10px] uppercase tracking-wider border-b" style="color: var(--dash-text-muted); border-color: var(--dash-card-border);">
                        <th class="text-left pb-2 font-semibold">Metrik</th>
                        <th class="text-right pb-2 font-semibold whitespace-nowrap">Target</th>
                        <th class="text-right pb-2 font-semibold whitespace-nowrap">Aktual</th>
                        <th class="text-right pb-2 font-semibold">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @foreach($data['metrics'] ?? [] as $m)
                        @php
                            $pct = $m['pct'];
                            $pctColor = match(true) {
                                $pct === null => 'var(--dash-text-muted)',
                                $pct >= 100 => 'var(--dash-status-success-text)',
                                $pct >= 80 => 'var(--dash-status-warning-text)',
                                default => 'var(--dash-status-danger-text)',
                            };
                        @endphp
                        <tr class="transition-colors" onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'" onmouseout="this.style.backgroundColor='transparent'">
                            <td class="py-2 flex items-center gap-2 font-medium whitespace-nowrap" style="color: var(--dash-text-body);">
                                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-text-muted);">{{ $m['icon'] }}</span>
                                {{ $m['label'] }}
                            </td>
                            <td class="py-2 text-right whitespace-nowrap" style="color: var(--dash-text-muted);">
                                {{ $formatCompact($m['target']) }}
                            </td>
                            <td class="py-2 text-right font-bold whitespace-nowrap" style="color: var(--dash-text-heading);">
                                {{ $formatCompact($m['actual']) }}
                            </td>
                            <td class="py-2 text-right whitespace-nowrap font-bold" style="color: {{ $pctColor }};">
                                {{ $pct !== null ? number_format($pct, 1, ',', '.').'%' : '-' }}
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
                        <span class="text-[11px] px-2.5 py-1 rounded-lg font-bold flex items-center gap-1" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text); border: 1px solid var(--dash-primary);">
                            <span class="material-symbols-outlined text-[14px]">workspace_premium</span>
                            #{{ $r['rank'] }} {{ $r['label'] }}
                        </span>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Criteria breakdown --}}
        @if(!empty($data['criteria']))
            <div class="pt-4 border-t space-y-2 mb-4" style="border-color: var(--dash-card-border);">
                <p class="text-[10px] font-bold uppercase tracking-wider mb-2" style="color: var(--dash-text-muted);">Komponen Nilai</p>
                @foreach($data['criteria'] as $c)
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium truncate max-w-[70%]" style="color: var(--dash-text-body);">{{ $c['name'] }}</span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">{{ number_format($c['value'], 1, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Spacer to push trend to bottom if needed --}}
        <div class="flex-1"></div>

        {{-- Trend history --}}
        <div class="pt-4 border-t mt-2" style="border-color: var(--dash-card-border);">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Riwayat 6 Bulan Terakhir</p>
                <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-muted);">show_chart</span>
            </div>
            
            @php 
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
                        $barFill = match(true) {
                            !$hasData => 'var(--dash-card-border)',
                            $h['mpi_score'] >= 80 => 'var(--dash-status-success-text)',
                            $h['mpi_score'] >= 60 => 'var(--dash-status-warning-text)',
                            default => 'var(--dash-status-danger-text)',
                        };
                        $tooltip = $hasData ? $h['period'] . ': ' . number_format($h['mpi_score'], 1, ',', '.') : $h['period'] . ': Belum ada data';
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1 group cursor-default" title="{{ $tooltip }}">
                        <div class="w-full max-w-[40px] rounded-t-sm transition-all duration-300"
                            style="height: {{ $barH }}%; background-color: {{ $barFill }};">
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex gap-1.5 mt-1.5">
                @foreach($filledHistory as $h)
                    @php $monthLabel = explode('/', $h['period'])[0]; @endphp
                    <div class="flex-1 text-center text-[9px] font-medium" style="color: {{ $h['mpi_score'] !== null ? 'var(--dash-text-body)' : 'var(--dash-text-muted)' }};">{{ $monthLabel }}</div>
                @endforeach
            </div>
        </div>
    @endif
</div>
