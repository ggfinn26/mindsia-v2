@props(['data'])

@php
    $utilization = $data['utilization_percent'] ?? 0;
    $isOverBudget = $utilization > 100;
    $barColor = $isOverBudget ? 'bg-red-500' : ($utilization > 80 ? 'bg-amber-500' : 'bg-emerald-500');
    $categoryLabels = [
        'sewa' => 'Sewa', 'listrik' => 'Listrik', 'air' => 'Air',
        'internet' => 'Internet', 'gaji_non_employee' => 'Gaji Non-Employee',
        'peralatan' => 'Peralatan', 'kebersihan' => 'Kebersihan',
        'keamanan' => 'Keamanan', 'lainnya' => 'Lainnya',
    ];
@endphp

<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow relative overflow-hidden flex flex-col h-full">
    <div class="flex items-center justify-between mb-4 z-10">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg {{ $isOverBudget ? 'bg-red-50' : 'bg-violet-50' }} flex items-center justify-center">
                <span class="material-symbols-outlined {{ $isOverBudget ? 'text-red-500' : 'text-violet-600' }} text-xl">account_balance_wallet</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Cost Control</h3>
                <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['pending_budget_count']) && $data['pending_budget_count'] > 0)
            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-700">
                {{ $data['pending_budget_count'] }} draft
            </span>
        @endif
    </div>

    <div class="z-10 flex-grow space-y-4">
        <div>
            <div class="flex items-end justify-between mb-1">
                <div>
                    <p class="text-xs text-[color:var(--dash-text-faint)] mb-0.5">Budget</p>
                    <p class="text-lg font-bold text-[color:var(--dash-text-heading)] font-jakarta leading-none">Rp {{ number_format($data['budget_total'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-[color:var(--dash-text-faint)] mb-0.5">Actual</p>
                    <p class="text-lg font-bold {{ $isOverBudget ? 'text-red-600' : 'text-[color:var(--dash-text-heading)]' }} font-jakarta leading-none">Rp {{ number_format($data['actual_total'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
            
            <div class="mt-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider">Utilization</span>
                    <span class="text-xs font-bold {{ $isOverBudget ? 'text-red-600' : 'text-[color:var(--dash-text-body)]' }}">{{ $utilization }}%</span>
                </div>
                <div class="w-full bg-[var(--dash-nav-hover-bg)] rounded-full h-2.5 overflow-hidden">
                    <div class="{{ $barColor }} h-2.5 rounded-full transition-all" style="width: {{ min($utilization, 100) }}%"></div>
                </div>
            </div>
        </div>

        @if(isset($data['variance']))
        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs font-medium text-[color:var(--dash-text-muted)]">Variance</span>
            <span class="text-sm font-bold {{ $data['variance'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                {{ $data['variance'] >= 0 ? '+' : '' }}Rp {{ number_format($data['variance'], 0, ',', '.') }}
            </span>
        </div>
        @endif

        @if(!empty($data['cost_by_category']))
        <div class="pt-3 border-t border-gray-50">
            <p class="text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase tracking-wider mb-2">Cost Breakdown</p>
            <div class="relative" style="height: 140px;">
                @php
                    $chartData = array_values($data['cost_by_category']);
                    $chartLabels = array_map(fn($k) => $categoryLabels[$k] ?? ucfirst($k), array_keys($data['cost_by_category']));
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
                            colors: ['#ffffff']
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
                        colors: ['#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe', '#ede9fe', '#6366f1', '#818cf8', '#a5b4fc']
                    };
                    
                    new ApexCharts(container, options).render();
                    container.dataset.rendered = "true";
                });
            });
        </script>
    @endpush
@endif
