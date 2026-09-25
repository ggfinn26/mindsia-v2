@props(['data'])

@php
    $isPositive = ($data['profit'] ?? 0) >= 0;
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow relative overflow-hidden flex flex-col h-full">
    <div class="flex items-center justify-between mb-4 z-10">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg {{ $isPositive ? 'bg-blue-50' : 'bg-red-50' }} flex items-center justify-center">
                <span class="material-symbols-outlined {{ $isPositive ? 'text-blue-600' : 'text-red-500' }} text-xl">trending_up</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Net Profit</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['trend_percent']))
            <div class="flex items-center gap-1 px-2 py-1 rounded-full {{ $data['trend_percent'] >= 0 ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-500' }}">
                <span class="material-symbols-outlined text-sm">{{ $data['trend_percent'] >= 0 ? 'trending_up' : 'trending_down' }}</span>
                <span class="text-xs font-bold">{{ abs($data['trend_percent']) }}%</span>
            </div>
        @endif
    </div>

    <div class="z-10 flex-grow">
        <div class="flex items-end gap-2 mb-1">
            <p class="text-3xl font-bold {{ $isPositive ? 'text-gray-900' : 'text-red-600' }} font-jakarta tracking-tight">Rp {{ number_format($data['profit'] ?? 0, 0, ',', '.') }}</p>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ ($data['margin_percent'] ?? 0) >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} mb-1">
                Margin: {{ $data['margin_percent'] ?? 0 }}%
            </span>
        </div>
    </div>
    
    @if(isset($data['history_data']) && !empty($data['history_data']))
        <div class="w-full mt-2 -mb-5 -mx-5 z-0" style="width: calc(100% + 2.5rem);">
            <div id="chart-profit-{{ Str::random(5) }}" data-history='@json($data['history_data'])' data-labels='@json($data['history_labels'])'></div>
        </div>
    @endif
</div>

@if(isset($data['history_data']) && !empty($data['history_data']))
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const chartContainers = document.querySelectorAll('[id^="chart-profit-"]');
                chartContainers.forEach(container => {
                    if (container.dataset.rendered) return;
                    
                    const data = JSON.parse(container.dataset.history || '[]');
                    const labels = JSON.parse(container.dataset.labels || '[]');
                    
                    const options = {
                        series: [{
                            name: 'Profit',
                            data: data
                        }],
                        chart: {
                            type: 'area',
                            height: 80,
                            sparkline: { enabled: true },
                            parentHeightOffset: 0,
                            toolbar: { show: false }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2,
                            colors: ['#2563eb']
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0,
                                stops: [0, 90, 100]
                            },
                            colors: ['#3b82f6']
                        },
                        labels: labels,
                        xaxis: { crosshairs: { width: 1 } },
                        tooltip: {
                            theme: 'light',
                            y: {
                                formatter: function (val) {
                                    return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                                }
                            }
                        }
                    };
                    
                    new ApexCharts(container, options).render();
                    container.dataset.rendered = "true";
                });
            });
        </script>
    @endpush
@endif
