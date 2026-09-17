@extends('layouts.dashboard')

@section('title', 'Overview - MINDSIA')
@section('header_title', 'Overview')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const grids = document.querySelectorAll('[data-sortable-grid]');

            grids.forEach(function (grid) {
                Sortable.create(grid, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'opacity-40',
                    onEnd: function () {
                        const orders = {};
                        grid.querySelectorAll('[data-widget-key]').forEach(function (el, idx) {
                            orders[el.dataset.widgetKey] = idx + 1;
                        });

                        fetch('{{ route('dashboard.widget-order') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ orders }),
                        });
                    },
                });
            });
        });

        function toggleWidget(widgetKey, checkbox) {
            fetch('{{ route('dashboard.widget-toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ widget_key: widgetKey, is_enabled: checkbox.checked ? 1 : 0 }),
            }).then(function () {
                const card = document.querySelector('[data-widget-key="' + widgetKey + '"]');
                if (card) {
                    card.style.opacity = checkbox.checked ? '1' : '0.4';
                }
            });
        }
    </script>
@endpush

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <p class="text-gray-500 font-medium text-sm sm:text-base">Selamat datang kembali, <span class="font-semibold text-gray-800">{{ Auth::user()->name ?? 'Pengguna' }}</span> 👋</p>

        {{-- Customize toggle --}}
        <div x-data="{ customizing: false }">
            <button @click="customizing = !customizing"
                    :class="customizing ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-base">dashboard_customize</span>
                <span x-text="customizing ? 'Selesai' : 'Atur Widget'"></span>
            </button>

            @if($widgets->isNotEmpty())
                @php
                    $categories = [
                        'financial'    => ['label' => 'Financial & Business',       'icon' => 'payments'],
                        'operations'   => ['label' => 'Operations & Compliance',     'icon' => 'settings'],
                        'hr'           => ['label' => 'HR & Recruitment',            'icon' => 'groups'],
                        'marketing'    => ['label' => 'Marketing & Growth',          'icon' => 'campaign'],
                        'finance_admin'=> ['label' => 'Finance & Administrative',    'icon' => 'receipt_long'],
                        'personal'     => ['label' => 'Personal Performance',        'icon' => 'person'],
                        'analytics'    => ['label' => 'Analytics & Insight',         'icon' => 'show_chart'],
                        'system'       => ['label' => 'System & Administration',     'icon' => 'admin_panel_settings'],
                    ];
                    $grouped = $widgets->groupBy('category');
                @endphp

                <div class="mt-6">
                    @foreach($grouped as $categoryKey => $categoryWidgets)
                        @php($catInfo = $categories[$categoryKey] ?? ['label' => ucfirst($categoryKey), 'icon' => 'dashboard'])

                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-gray-400 text-lg">{{ $catInfo['icon'] }}</span>
                                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">{{ $catInfo['label'] }}</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5"
                                 data-sortable-grid>

                                @foreach($categoryWidgets as $widget)
                                    <div class="relative" data-widget-key="{{ $widget['key'] }}">

                                        {{-- Customize overlay (drag handle + toggle) --}}
                                        <div x-show="customizing"
                                             class="absolute inset-0 z-20 rounded-xl ring-2 ring-indigo-300 bg-white/10 backdrop-blur-[1px] flex flex-col items-center justify-center gap-2"
                                             style="display: none;">
                                            <div class="drag-handle cursor-grab active:cursor-grabbing p-2 rounded-lg bg-white shadow border border-gray-100" title="Geser untuk mengubah urutan">
                                                <span class="material-symbols-outlined text-gray-500 text-xl">drag_indicator</span>
                                            </div>
                                            <label class="flex items-center gap-1.5 bg-white shadow border border-gray-100 rounded-lg px-2 py-1 cursor-pointer">
                                                <input type="checkbox" checked
                                                       class="accent-indigo-600"
                                                       onchange="toggleWidget('{{ $widget['key'] }}', this)">
                                                <span class="text-xs text-gray-600 font-medium">Tampilkan</span>
                                            </label>
                                        </div>

                                        {{-- Widget content --}}
                                        @if(view()->exists('dashboard.widgets.' . $widget['key']))
                                            @include('dashboard.widgets.' . $widget['key'], ['data' => $widgetData[$widget['key']] ?? []])
                                        @else
                                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <div class="h-10 w-10 rounded-lg bg-gray-50 flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-gray-400 text-xl">{{ $widget['icon'] }}</span>
                                                    </div>
                                                    <h3 class="text-sm font-semibold text-gray-800">{{ $widget['label'] }}</h3>
                                                </div>
                                                <p class="text-xs text-gray-400">Widget view belum tersedia.</p>
                                            </div>
                                        @endif

                                        {{-- Export button (hidden while customizing) --}}
                                        @if($widget['is_exportable'] && !empty($widgetData[$widget['key']]))
                                            <div class="absolute top-3 right-3" x-show="!customizing">
                                                <div class="relative" x-data="{ open: false }">
                                                    <button @click="open = !open" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                                                        <span class="material-symbols-outlined text-gray-400 text-sm">download</span>
                                                    </button>
                                                    <div x-show="open" @click.away="open = false"
                                                         class="absolute right-0 mt-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-10"
                                                         style="display: none;">
                                                        <a href="{{ route('dashboard.widget-export', ['widgetKey' => $widget['key'], 'format' => 'csv']) }}"
                                                           class="block px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50">Export CSV</a>
                                                        <a href="{{ route('dashboard.widget-export', ['widgetKey' => $widget['key'], 'format' => 'pdf']) }}"
                                                           class="block px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50">Export PDF</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if($widgets->isEmpty())
        <div class="flex flex-col items-center justify-center min-h-[60vh] h-full text-center">
            <div class="h-20 w-20 rounded-full bg-[#EEF4FF] flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[#5586DB] text-4xl">dashboard_customize</span>
            </div>
            <h2 class="text-xl font-bold font-jakarta text-gray-800 mb-3">Ruang Kerja Anda</h2>
            <p class="text-sm text-gray-500 leading-relaxed max-w-sm">Widget dan analitik akan ditampilkan di sini berdasarkan peran dan divisi Anda.</p>
        </div>
    @endif
@endsection
