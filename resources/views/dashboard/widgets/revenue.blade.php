@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow relative overflow-hidden flex flex-col h-full">
    <div class="flex items-center justify-between mb-4 z-10">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-600 text-xl">payments</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Revenue</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['trend_percent']))
            <div class="flex items-center gap-1 px-2 py-1 rounded-full {{ $data['trend_percent'] >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' }}">
                <span class="material-symbols-outlined text-sm">{{ $data['trend_percent'] >= 0 ? 'trending_up' : 'trending_down' }}</span>
                <span class="text-xs font-bold">{{ abs($data['trend_percent']) }}%</span>
            </div>
        @endif
    </div>

    <div class="z-10 flex-grow">
        <p class="text-xs text-gray-400 mb-1 font-medium uppercase tracking-wider">Total Revenue</p>
        <p class="text-3xl font-bold text-gray-900 font-jakarta tracking-tight">Rp {{ number_format($data['member_revenue'] ?? 0, 0, ',', '.') }}</p>
    </div>
    
    @if(isset($data['history_data']) && !empty($data['history_data']))
        <div class="w-full mt-2 -mb-5 -mx-5 z-0" style="width: calc(100% + 2.5rem);">
            <div id="chart-revenue-{{ Str::random(5) }}" data-history='@json($data['history_data'])' data-labels='@json($data['history_labels'])'></div>
        </div>
    @else
        <div class="mt-4 pt-4 border-t border-gray-50 z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400">Payroll Cost</p>
                    <p class="text-sm font-semibold text-gray-600">Rp {{ number_format($data['payroll_cost'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>

@if(isset($data['history_data']) && !empty($data['history_data']))
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const chartContainers = document.querySelectorAll('[id^="chart-revenue-"]');
                chartContainers.forEach(container => {
                    if (container.dataset.rendered) return;
                    
                    const data = JSON.parse(container.dataset.history || '[]');
                    const labels = JSON.parse(container.dataset.labels || '[]');
                    
                    const options = {
                        series: [{
                            name: 'Revenue',
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
                            colors: ['#059669']
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0,
                                stops: [0, 90, 100]
                            },
                            colors: ['#34d399']
                        },
                        labels: labels,
                        xaxis: { crosshairs: { width: 1 } },
                        yaxis: { min: 0 },
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
