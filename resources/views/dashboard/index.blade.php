@extends('layouts.dashboard')

@section('title', 'Overview - MINDSIA')
@section('header_title', 'Overview')

@push('scripts')
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
    <div x-data="{}" >
        <div class="mb-6 flex items-center justify-between">
            <p class="text-gray-500 font-medium text-sm sm:text-base">Selamat datang kembali, <span class="font-semibold text-gray-800">{{ Auth::user()->name ?? 'Pengguna' }}</span> 👋</p>

            {{-- Customize toggle --}}
            <button @click="$store.dashboard.customizing = !$store.dashboard.customizing"
                    :class="$store.dashboard.customizing ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-base">dashboard_customize</span>
                <span x-text="$store.dashboard.customizing ? 'Selesai' : 'Atur Widget'"></span>
            </button>
        </div>

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

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 items-start auto-rows-max"
                                 data-sortable-grid>

                                @foreach($categoryWidgets as $widget)
                                    {{-- Disabled widgets only visible during customize mode --}}
                                    <div class="group/widget relative min-w-0 {{ $widget['is_enabled'] ? '' : 'opacity-50' }} {{ in_array($widget['key'], ['financial_hub', 'member_growth', 'comparable', 'marketing_progress']) ? 'col-span-full' : '' }}"
                                         data-widget-key="{{ $widget['key'] }}"
                                         @if(!$widget['is_enabled']) x-show="$store.dashboard.customizing" style="display:none;" @endif>

                                        {{-- Customize overlay (drag handle + toggle) --}}
                                        <div x-show="$store.dashboard.customizing"
                                             class="absolute inset-0 z-50 rounded-xl ring-4 {{ $widget['is_enabled'] ? 'ring-indigo-500' : 'ring-gray-300' }} bg-slate-900/40 backdrop-blur-sm flex flex-col items-center justify-center gap-2"
                                             style="display: none;">
                                            <div class="drag-handle cursor-grab active:cursor-grabbing p-2 rounded-lg bg-white shadow border border-gray-100" title="Geser untuk mengubah urutan">
                                                <span class="material-symbols-outlined text-gray-500 text-xl">drag_indicator</span>
                                            </div>
                                            <label class="flex items-center gap-1.5 bg-white shadow border border-gray-100 rounded-lg px-2 py-1 cursor-pointer">
                                                <input type="checkbox" {{ $widget['is_enabled'] ? 'checked' : '' }}
                                                       class="accent-indigo-600"
                                                       onchange="toggleWidget('{{ $widget['key'] }}', this)">
                                                <span class="text-xs text-gray-600 font-medium">Tampilkan</span>
                                            </label>
                                        </div>

                                        {{-- Widget content --}}
                                        @if(view()->exists('dashboard.widgets.' . $widget['key']) && isset($widgetData[$widget['key']]))
                                            @include('dashboard.widgets.' . $widget['key'], ['data' => $widgetData[$widget['key']]])
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
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
        @endif
    </div>

    @if($widgets->where('is_enabled', true)->isEmpty() && !$widgets->isEmpty())
        {{-- All widgets disabled: hint user to re-enable via Atur Widget --}}
        <div class="flex flex-col items-center justify-center min-h-[60vh] h-full text-center">
            <div class="h-20 w-20 rounded-full bg-amber-50 flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-amber-400 text-4xl">visibility_off</span>
            </div>
            <h2 class="text-xl font-bold font-jakarta text-gray-800 mb-3">Semua Widget Disembunyikan</h2>
            <p class="text-sm text-gray-500 leading-relaxed max-w-sm">Klik <strong>Atur Widget</strong> di atas untuk menampilkan kembali widget yang Anda butuhkan.</p>
        </div>
    @elseif($widgets->isEmpty())
        <div class="flex flex-col items-center justify-center min-h-[60vh] h-full text-center">
            <div class="h-20 w-20 rounded-full bg-[#EEF4FF] flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[#5586DB] text-4xl">dashboard_customize</span>
            </div>
            <h2 class="text-xl font-bold font-jakarta text-gray-800 mb-3">Ruang Kerja Anda</h2>
            <p class="text-sm text-gray-500 leading-relaxed max-w-sm">Widget dan analitik akan ditampilkan di sini berdasarkan peran dan divisi Anda.</p>
        </div>
    @endif
@endsection
