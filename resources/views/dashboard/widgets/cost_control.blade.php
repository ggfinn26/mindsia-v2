@props(['data'])

@php
    $utilization = $data['utilization_percent'] ?? 0;
    $isOverBudget = $utilization > 100;
    $barBg = $isOverBudget ? 'var(--dash-status-danger-text)' : ($utilization > 80 ? 'var(--dash-status-warning-text)' : 'var(--dash-status-success-text)');
    $categoryLabels = [
        'sewa' => 'Sewa', 'listrik' => 'Listrik', 'air' => 'Air',
        'internet' => 'Internet', 'gaji_non_employee' => 'Gaji Non-Employee',
        'peralatan' => 'Peralatan', 'kebersihan' => 'Kebersihan',
        'keamanan' => 'Keamanan', 'lainnya' => 'Lainnya',
    ];
@endphp

<div class="rounded-2xl transition-all p-5 relative overflow-hidden flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4 z-10">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: {{ $isOverBudget ? 'var(--dash-status-danger-bg)' : 'var(--dash-primary-bg)' }}; color: {{ $isOverBudget ? 'var(--dash-status-danger-text)' : 'var(--dash-primary-text)' }};">
                <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Cost Control</h3>
                <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['pending_budget_count']) && $data['pending_budget_count'] > 0)
            <span class="px-2.5 py-1 text-xs font-bold rounded-full" style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
                {{ $data['pending_budget_count'] }} draft
            </span>
        @endif
    </div>

    <div class="z-10 flex-grow space-y-4">
        <div>
            <div class="flex items-end justify-between mb-1">
                <div>
                    <p class="text-xs font-medium mb-0.5" style="color: var(--dash-text-muted);">Budget</p>
                    <p class="text-base sm:text-lg font-bold font-jakarta leading-none" style="color: var(--dash-text-heading);">Rp {{ number_format($data['budget_total'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-medium mb-0.5" style="color: var(--dash-text-muted);">Actual</p>
                    <p class="text-base sm:text-lg font-bold font-jakarta leading-none" style="color: {{ $isOverBudget ? 'var(--dash-status-danger-text)' : 'var(--dash-text-heading)' }};">Rp {{ number_format($data['actual_total'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
            
            <div class="mt-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Utilization</span>
                    <span class="text-xs font-bold" style="color: {{ $isOverBudget ? 'var(--dash-status-danger-text)' : 'var(--dash-text-heading)' }};">{{ $utilization }}%</span>
                </div>
                <div class="w-full rounded-full h-2.5 overflow-hidden" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <div class="h-2.5 rounded-full transition-all" style="width: {{ min($utilization, 100) }}%; background-color: {{ $barBg }};"></div>
                </div>
            </div>
        </div>

        @if(isset($data['variance']))
            <div class="flex items-center justify-between pt-2 border-t" style="border-color: var(--dash-card-border);">
                <span class="text-xs font-medium" style="color: var(--dash-text-muted);">Variance</span>
                <span class="text-sm font-bold" style="color: {{ $data['variance'] >= 0 ? 'var(--dash-status-success-text)' : 'var(--dash-status-danger-text)' }};">
                    {{ $data['variance'] >= 0 ? '+' : '' }}Rp {{ number_format($data['variance'], 0, ',', '.') }}
                </span>
            </div>
        @endif

        @if(!empty($data['cost_by_category']))
            <div class="pt-3 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: var(--dash-text-muted);">Cost Breakdown</p>
                <div class="relative" style="height: 140px;">
                    @php
                        $chartData = array_values($data['cost_by_category']);
                        $chartLabels = array_map(fn($k) => $categoryLabels[$k] ?? \App\Support\EnumLabels::get('cost_category', $k), array_keys($data['cost_by_category']));
                    @endphp
                    <div id="chart-cost-{{ Str::random(5) }}" data-series='@json($chartData)' data-labels='@json($chartLabels)'></div>
                </div>
            </div>
        @endif
    </div>
</div>

@if(!empty($data['cost_by_category']))
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const chartContainers = document.querySelectorAll('[id^="chart-cost-"]');
                chartContainers.forEach(container => {
                    if (container.dataset.rendered) return;
                    
                    const series = JSON.parse(container.dataset.series || '[]');
                    const labels = JSON.parse(container.dataset.labels || '[]');
                    
                    const options = {
                        series: series,
                        labels: labels,
                        chart: {
                            type: 'donut',
                            height: 140,
                            parentHeightOffset: 0,
                            sparkline: { enabled: true }
                        },
                        stroke: {
                            width: 2,
                            colors: ['transparent']
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '70%',
                                    labels: {
                                        show: true,
                                        name: { show: false },
                                        value: {
                                            show: true,
                                            fontSize: '14px',
                                            fontWeight: 700,
                                            formatter: function (val) {
                                                if(val >= 1000000) return (val/1000000).toFixed(1) + 'M';
                                                if(val >= 1000) return (val/1000).toFixed(1) + 'K';
                                                return val;
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        tooltip: {
                            theme: 'light',
                            y: {
                                formatter: function (val) {
                                    return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                                }
                            }
                        },
                        colors: ['#5586db', '#38bdf8', '#fbbf24', '#059669', '#dc2626', '#818cf8', '#a78bfa', '#f97316']
                    };
                    
                    new ApexCharts(container, options).render();
                    container.dataset.rendered = "true";
                });
            });
        </script>
    @endpush
@endif
