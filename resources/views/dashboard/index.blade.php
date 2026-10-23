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
            <p class="font-medium text-sm sm:text-base" style="color: var(--dash-text-muted);">Selamat datang kembali, <span class="font-semibold" style="color: var(--dash-text-heading);">{{ Auth::user()->name ?? 'Pengguna' }}</span> 👋</p>

            {{-- Customize toggle --}}
            <button @click="$store.dashboard.customizing = !$store.dashboard.customizing"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                    :style="$store.dashboard.customizing
                        ? 'background-color: var(--dash-primary); color: #ffffff;'
                        : 'background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);'"
                    onmouseover="if(!$store.dashboard.customizing) this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="if(!$store.dashboard.customizing) this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">dashboard_customize</span>
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
                                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-text-muted);">{{ $catInfo['icon'] }}</span>
                                <h2 class="text-xs font-semibold uppercase tracking-wider font-jakarta" style="color: var(--dash-primary-text);">{{ $catInfo['label'] }}</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 items-start auto-rows-max"
                                 data-sortable-grid>

                                @foreach($categoryWidgets as $widget)
                                    {{-- Disabled widgets only visible during customize mode --}}
                                    <div class="group/widget relative min-w-0 {{ $widget['is_enabled'] ? '' : 'opacity-50' }} {{ in_array($widget['key'], ['financial_hub', 'member_growth', 'comparable', 'marketing_progress', 'branch_financial_performance', 'area_performance']) ? 'col-span-full' : '' }}"
                                         data-widget-key="{{ $widget['key'] }}"
                                         @if(!$widget['is_enabled']) x-show="$store.dashboard.customizing" style="display:none;" @endif>

                                        {{-- Customize overlay (drag handle + toggle) --}}
                                        <div x-show="$store.dashboard.customizing"
                                             class="absolute inset-0 z-50 rounded-2xl ring-2 bg-slate-900/40 backdrop-blur-sm flex flex-col items-center justify-center gap-2"
                                             style="display: none; outline: 2px solid {{ $widget['is_enabled'] ? 'var(--dash-primary)' : 'var(--dash-card-border)' }};">
                                            <div class="drag-handle cursor-grab active:cursor-grabbing p-2 rounded-lg shadow"
                                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                                                 title="Geser untuk mengubah urutan">
                                                <span class="material-symbols-outlined text-xl" style="color: var(--dash-text-muted);">drag_indicator</span>
                                            </div>
                                            <label class="flex items-center gap-1.5 shadow rounded-lg px-2.5 py-1.5 cursor-pointer"
                                                   style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                                <input type="checkbox" {{ $widget['is_enabled'] ? 'checked' : '' }}
                                                       style="accent-color: var(--dash-primary);"
                                                       onchange="toggleWidget('{{ $widget['key'] }}', this)">
                                                <span class="text-xs font-medium" style="color: var(--dash-text-body);">Tampilkan</span>
                                            </label>
                                        </div>

                                        {{-- Widget content --}}
                                        @if(view()->exists('dashboard.widgets.' . $widget['key']) && isset($widgetData[$widget['key']]))
                                            @include('dashboard.widgets.' . $widget['key'], ['data' => $widgetData[$widget['key']]])
                                        @else
                                            <div class="rounded-2xl p-5 shadow-sm"
                                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <div class="h-10 w-10 rounded-lg flex items-center justify-center"
                                                         style="background-color: var(--dash-nav-hover-bg);">
                                                        <span class="material-symbols-outlined text-xl" style="color: var(--dash-text-muted);">{{ $widget['icon'] }}</span>
                                                    </div>
                                                    <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $widget['label'] }}</h3>
                                                </div>
                                                <p class="text-xs" style="color: var(--dash-text-muted);">Widget view belum tersedia.</p>
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
            <div class="h-20 w-20 rounded-full flex items-center justify-center mb-5"
                 style="background-color: #fef3c7;">
                <span class="material-symbols-outlined text-4xl" style="color: #d97706;">visibility_off</span>
            </div>
            <h2 class="text-xl font-bold font-jakarta mb-3" style="color: var(--dash-text-heading);">Semua Widget Disembunyikan</h2>
            <p class="text-sm leading-relaxed max-w-sm" style="color: var(--dash-text-muted);">Klik <strong>Atur Widget</strong> di atas untuk menampilkan kembali widget yang Anda butuhkan.</p>
        </div>
    @elseif($widgets->isEmpty())
        <div class="flex flex-col items-center justify-center min-h-[60vh] h-full text-center">
            <div class="h-20 w-20 rounded-full flex items-center justify-center mb-5"
                 style="background-color: var(--dash-nav-hover-bg);">
                <span class="material-symbols-outlined text-4xl" style="color: var(--dash-primary);">dashboard_customize</span>
            </div>
            <h2 class="text-xl font-bold font-jakarta mb-3" style="color: var(--dash-text-heading);">Ruang Kerja Anda</h2>
            <p class="text-sm leading-relaxed max-w-sm" style="color: var(--dash-text-muted);">Widget dan analitik akan ditampilkan di sini berdasarkan peran dan divisi Anda.</p>
        </div>
    @endif
@endsection
