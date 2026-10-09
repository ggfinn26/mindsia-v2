@php
    $branches   = $data['branches'] ?? [];
    $topBranches = $data['top_branches'] ?? [];
    $areas      = $data['area_contributions'] ?? [];
    $maxRev     = $data['max_revenue'] ?? 1;
    $avgRev     = $data['avg_branch_revenue'] ?? 0;
    $benchPct   = $data['benchmark_pct'] ?? 50;
    
    $statusColors = [
        'Top Tier'       => ['bg' => 'bg-[#ffe07e]', 'text' => 'text-[#564500]', 'border' => 'border-transparent', 'dot' => 'bg-[#564500]'],
        'On Track'       => ['bg' => 'bg-[#b3ebff]', 'text' => 'text-[#004e5f]', 'border' => 'border-transparent', 'dot' => 'bg-[#004e5f]'],
        'Baseline'       => ['bg' => 'bg-[#eeeeed]', 'text' => 'text-[#4c4635]', 'border' => 'border-transparent', 'dot' => 'bg-[#4c4635]'],
        'Need Attention' => ['bg' => 'bg-[#ffdad6]', 'text' => 'text-[#93000a]', 'border' => 'border-transparent', 'dot' => 'bg-[#93000a]'],
    ];

    // Executive bar colors (Sovereign Executive: Deep Slate and Teal)
    $revColor = 'bg-[#1E293B]'; // Deep Slate for Revenue
    $profColor = 'bg-[#00677d]'; // Teal for Profit
    $benchColor = 'border-[#dbb940]'; // Gold for Benchmark line
@endphp

<div class="bg-[var(--dash-card-bg)] rounded-lg border border-[#cfc6af] shadow-sm overflow-hidden font-sans"
     x-data="{
         activeTab: 'comparison',
         downloadModalOpen: false, dlType: 'tahunan', dlYear: '{{ request('year', now()->year) }}', dlSemester: '1', dlMonth: '{{ request('month', now()->month) }}',
         search: '',
         sortBy: 'revenue',
         get filteredBranches() {
             let list = @js($branches);
             if (this.search) {
                 const s = this.search.toLowerCase();
                 list = list.filter(b => b.name.toLowerCase().includes(s) || b.area_name.toLowerCase().includes(s));
             }
             if (this.sortBy === 'revenue') list = list.slice().sort((a,b) => b.revenue - a.revenue);
             if (this.sortBy === 'margin')  list = list.slice().sort((a,b) => b.margin - a.margin);
             if (this.sortBy === 'trend')   list = list.slice().sort((a,b) => b.trend - a.trend);
             return list;
         }
     }">

    {{-- Widget Header (Executive Style) --}}
    <div class="px-6 py-5 border-b-2 border-[#1E293B] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-[#f9f9f8]">
        <div>
            <h2 class="text-lg font-bold text-[#1E293B] tracking-tight">KOMPARASI PERFORMA FINANSIAL</h2>
            <p class="text-xs text-[#7e7663] mt-1 font-medium tracking-wide uppercase">Cabang, Area & Nasional • {{ $data['period'] ?? '-' }}</p>
        </div>
        
        <div class="flex items-center gap-4 flex-wrap">
            <form onsubmit="event.preventDefault(); const url = new window.URL(window.location.href); url.searchParams.set('financial_hub_month', this.month.value); url.searchParams.set('financial_hub_year', this.year.value); window.location.href = url.toString();" class="flex items-center gap-1">
                @php
                    $mVal = request('financial_hub_month', request('month', now()->month));
                    $yVal = request('financial_hub_year', request('year', now()->year));
                @endphp
                <select name="month" class="text-xs border-[#cfc6af] rounded py-1 px-2 bg-[var(--dash-card-bg)] text-[#1E293B] font-semibold focus:ring-1 focus:ring-[#dbb940]">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $mVal == $m ? 'selected' : '' }}>{{ ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'][$m-1] }}</option>
                    @endforeach
                </select>
                <select name="year" class="text-xs border-[#cfc6af] rounded py-1 px-2 bg-[var(--dash-card-bg)] text-[#1E293B] font-semibold focus:ring-1 focus:ring-[#dbb940]">
                    @foreach(range(now()->year - 3, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ $yVal == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-[#1E293B] hover:bg-[#314865] text-white px-2 py-1 rounded text-xs font-semibold transition-colors inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">filter_list</span>Filter</button>
            </form>

            <button @click="downloadModalOpen = true" class="p-1.5 rounded bg-[var(--dash-card-bg)] shadow-sm border border-[#cfc6af] hover:bg-[var(--dash-nav-hover-bg)] transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-[color:var(--dash-text-body)] text-sm">download</span>
                <span class="text-xs font-bold text-[#4c4635] pr-1">Unduh Laporan</span>
            </button>

            {{-- Modal --}}
            <div x-show="downloadModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;" @keydown.escape.window="downloadModalOpen = false">
                <div @click.away="downloadModalOpen = false" class="bg-[var(--dash-card-bg)] rounded-lg shadow-xl border border-[#cfc6af] w-full max-w-md">
                    <div class="px-5 py-4 border-b border-[#cfc6af] flex justify-between items-center bg-[#f9f9f8] rounded-t-lg">
                        <h3 class="text-sm font-bold text-[#1E293B]">Unduh Laporan PDF</h3>
                        <button @click="downloadModalOpen = false" class="text-[color:var(--dash-text-faint)] hover:text-[color:var(--dash-text-body)]">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                    <div class="p-5">
                        <div class="flex border-b border-[#cfc6af] mb-4">
                            <button @click="dlType = 'tahunan'" :class="dlType === 'tahunan' ? 'border-b-2 border-[#dbb940] text-[#1E293B]' : 'text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]'" class="px-3 py-2 text-xs font-bold transition-colors">Tahunan</button>
                            <button @click="dlType = 'semester'" :class="dlType === 'semester' ? 'border-b-2 border-[#dbb940] text-[#1E293B]' : 'text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]'" class="px-3 py-2 text-xs font-bold transition-colors">Semester</button>
                            <button @click="dlType = 'bulanan'" :class="dlType === 'bulanan' ? 'border-b-2 border-[#dbb940] text-[#1E293B]' : 'text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]'" class="px-3 py-2 text-xs font-bold transition-colors">Bulanan</button>
                        </div>

                        <form action="{{ route('dashboard.widget-export', ['widgetKey' => 'financial_hub', 'format' => 'pdf']) }}" method="GET">
                            <template x-if="dlType === 'tahunan'">
                                <div>
                                    <input type="hidden" name="period_type" value="yearly">
                                    <label class="block text-xs font-bold text-[#4c4635] mb-1">Pilih Tahun</label>
                                    <select name="year" x-model="dlYear" class="w-full rounded border-[#cfc6af] py-2 px-3 text-sm focus:ring-[#dbb940]">
                                        @foreach(range(now()->year - 2, now()->year + 1) as $y)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </template>

                            <template x-if="dlType === 'semester'">
                                <div>
                                    <input type="hidden" name="period_type" value="semester">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-[#4c4635] mb-1">Semester</label>
                                            <select name="semester" x-model="dlSemester" class="w-full rounded border-[#cfc6af] py-2 px-3 text-sm focus:ring-[#dbb940]">
                                                <option value="1">Semester 1 (Jan-Jun)</option>
                                                <option value="2">Semester 2 (Jul-Des)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-[#4c4635] mb-1">Tahun</label>
                                            <select name="year" x-model="dlYear" class="w-full rounded border-[#cfc6af] py-2 px-3 text-sm focus:ring-[#dbb940]">
                                                @foreach(range(now()->year - 2, now()->year + 1) as $y)
                                                    <option value="{{ $y }}">{{ $y }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="dlType === 'bulanan'">
                                <div>
                                    <input type="hidden" name="period_type" value="monthly">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-[#4c4635] mb-1">Bulan</label>
                                            <select name="month" x-model="dlMonth" class="w-full rounded border-[#cfc6af] py-2 px-3 text-sm focus:ring-[#dbb940]">
                                                @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $m => $name)
                                                    <option value="{{ $m }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-[#4c4635] mb-1">Tahun</label>
                                            <select name="year" x-model="dlYear" class="w-full rounded border-[#cfc6af] py-2 px-3 text-sm focus:ring-[#dbb940]">
                                                @foreach(range(now()->year - 2, now()->year + 1) as $y)
                                                    <option value="{{ $y }}">{{ $y }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div class="mt-6 flex justify-end gap-2">
                                <button type="button" @click="downloadModalOpen = false" class="px-4 py-2 text-xs font-bold border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">close</span>Batal</button>
                                <button type="submit" class="px-4 py-2 bg-[#1E293B] text-white text-xs font-bold rounded-lg hover:bg-[#0f172a] shadow inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">download</span>Unduh PDF</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- View Tabs --}}
            <nav class="flex items-center p-1 bg-[#eeeeed] rounded border border-[#cfc6af] text-xs font-semibold">
                <button @click="activeTab = 'overview'"
                        :class="activeTab === 'overview' ? 'bg-[var(--dash-card-bg)] text-[#1E293B] shadow-sm' : 'text-[#7e7663] hover:text-[#1E293B]'"
                        class="px-4 py-1.5 rounded transition">Nasional</button>
                <button @click="activeTab = 'comparison'"
                        :class="activeTab === 'comparison' ? 'bg-[var(--dash-card-bg)] text-[#1E293B] shadow-sm' : 'text-[#7e7663] hover:text-[#1E293B]'"
                        class="px-4 py-1.5 rounded transition">Komparasi</button>
                <button @click="activeTab = 'table'"
                        :class="activeTab === 'table' ? 'bg-[var(--dash-card-bg)] text-[#1E293B] shadow-sm' : 'text-[#7e7663] hover:text-[#1E293B]' inline-flex items-center gap-1.5"
                        class="px-4 py-1.5 rounded transition"><span class="material-symbols-outlined text-[16px]">visibility</span>Detail Data</button>
            </nav>
        </div>
    </div>

    {{-- National KPI Row (always visible) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-0 divide-x divide-y lg:divide-y-0 divide-[#e2e2e2] bg-[var(--dash-card-bg)] border-b border-[#e2e2e2]">
        {{-- Revenue Nasional --}}
        <div class="p-5 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-bold text-[#7e7663] tracking-widest uppercase mb-1">Total Revenue Nasional</p>
                <span class="text-2xl font-bold text-[#1E293B] font-mono tracking-tight">
                    Rp {{ number_format($data['national_revenue'] ?? 0, 0, ',', '.') }}
                </span>
            </div>
            @php $nt = $data['national_trend'] ?? 0; @endphp
            <div class="mt-3 flex items-center gap-2">
                <span class="inline-flex items-center gap-1 text-xs font-bold {{ $nt >= 0 ? 'text-[#00677d]' : 'text-[#ba1a1a]' }}">
                    <span class="material-symbols-outlined text-[14px]">{{ $nt >= 0 ? 'trending_up' : 'trending_down' }}</span>
                    {{ $nt >= 0 ? '+' : '' }}{{ $nt }}%
                </span>
                <span class="text-[10px] text-[#7e7663]">MoM</span>
            </div>
        </div>

        {{-- Net Profit Nasional --}}
        <div class="p-5 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-bold text-[#7e7663] tracking-widest uppercase mb-1">Net Profit Nasional</p>
                <span class="text-2xl font-bold text-[#00677d] font-mono tracking-tight">
                    Rp {{ number_format($data['national_profit'] ?? 0, 0, ',', '.') }}
                </span>
            </div>
            @php $pt = $data['profit_trend'] ?? 0; @endphp
            <div class="mt-3 flex items-center justify-between">
                <span class="inline-flex items-center gap-1 text-xs font-bold {{ $pt >= 0 ? 'text-[#00677d]' : 'text-[#ba1a1a]' }}">
                    <span class="material-symbols-outlined text-[14px]">{{ $pt >= 0 ? 'trending_up' : 'trending_down' }}</span>
                    {{ $pt >= 0 ? '+' : '' }}{{ $pt }}%
                </span>
                <span class="px-2 py-0.5 bg-[#f3f4f3] border border-[#cfc6af] text-[#4c4635] text-[10px] font-bold rounded">
                    Margin: {{ $data['national_margin'] ?? 0 }}%
                </span>
            </div>
        </div>

        {{-- Rata-rata Cabang --}}
        <div class="p-5 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-bold text-[#7e7663] tracking-widest uppercase mb-1">Baseline Rerata Cabang</p>
                <p class="text-2xl font-bold text-[#1E293B] font-mono tracking-tight">
                    Rp {{ number_format($data['avg_branch_revenue'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
            <p class="mt-3 text-[10px] text-[#7e7663] font-medium">Standar Acuan Kinerja</p>
        </div>

        {{-- Jangkauan Wilayah --}}
        <div class="p-5 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-bold text-[#7e7663] tracking-widest uppercase mb-1">Jangkauan Aktif</p>
                <p class="text-2xl font-bold text-[#1E293B] font-mono tracking-tight">{{ $data['branch_count'] ?? 0 }} <span class="text-base text-[#7e7663]">Cabang</span></p>
            </div>
            <p class="mt-3 text-[10px] text-[#7e7663] font-medium">
                {{ count($areas) }} Area &bull; {{ $data['superior_count'] ?? 0 }} Superior
            </p>
        </div>
    </div>

    {{-- Tab: Comparison Cards --}}
    <div x-show="activeTab === 'comparison'" class="p-6 space-y-6 bg-[#f9f9f8]">

        {{-- Branch Cards --}}
        <div>
            <div class="flex items-center justify-between mb-4 border-b border-[#cfc6af] pb-2">
                <h3 class="text-sm font-bold text-[#1E293B] tracking-wide uppercase">Top Performa Cabang</h3>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-[#7e7663] font-medium">Susun:</span>
                    <select x-model="sortBy" class="text-xs border-[#cfc6af] rounded py-1 px-2 bg-[var(--dash-card-bg)] text-[#1E293B] font-semibold focus:ring-1 focus:ring-[#dbb940]">
                        <option value="revenue">Revenue</option>
                        <option value="margin">Margin</option>
                        <option value="trend">Tren MoM</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse ($topBranches as $i => $branch)
                    @php
                        $trend = $branch['trend'];
                        $vsNat = $branch['vs_national'];
                        $sc = $statusColors[$branch['status']] ?? $statusColors['Baseline'];
                    @endphp
                    <article class="bg-[var(--dash-card-bg)] rounded-lg p-5 border border-[#cfc6af] border-t-4 border-t-[#1E293B] shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <p class="text-sm font-bold text-[#1E293B]">{{ $branch['name'] }}</p>
                                    <p class="text-[10px] text-[#7e7663] mt-0.5">{{ $branch['area_name'] }}</p>
                                </div>
                                <span class="inline-flex items-center gap-0.5 text-[10px] font-bold px-2 py-0.5 rounded {{ $trend >= 0 ? 'bg-[#d1fae5] text-[#065f46]' : 'bg-[#fee2e2] text-[#991b1b]' }}">
                                    {{ $trend >= 0 ? '+' : '' }}{{ $trend }}%
                                </span>
                            </div>

                            <p class="text-[9px] font-bold tracking-widest uppercase text-[#7e7663]">Total Revenue</p>
                            <p class="text-lg font-bold text-[#1E293B] font-mono">Rp {{ number_format($branch['revenue'], 0, ',', '.') }}</p>

                            <div class="mt-3 pt-3 border-t border-[#e2e2e2] flex items-center justify-between">
                                <div>
                                    <p class="text-[9px] font-bold tracking-widest uppercase text-[#7e7663]">Net Profit</p>
                                    <p class="text-sm font-bold text-[#00677d] font-mono">Rp {{ number_format($branch['profit'], 0, ',', '.') }}</p>
                                </div>
                                <span class="px-2 py-1 rounded text-[10px] font-bold bg-[#f3f4f3] text-[#4c4635] border border-[#cfc6af]">
                                    Margin: {{ $branch['margin'] }}%
                                </span>
                            </div>

                            <div class="mt-4 flex items-center gap-1.5 text-[10px] font-bold {{ $vsNat >= 0 ? 'text-[#00677d]' : 'text-[#ba1a1a]' }}">
                                <span>{{ $vsNat >= 0 ? '▲' : '▼' }}</span>
                                <span>{{ $vsNat >= 0 ? '+' : '' }}{{ $vsNat }}% vs Rerata Nasional</span>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-4 text-center py-8 text-sm text-[#7e7663]">Tidak ada data cabang</div>
                @endforelse
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            {{-- Bar Chart --}}
            <div class="lg:col-span-7 bg-[var(--dash-card-bg)] rounded-lg border border-[#cfc6af] p-5">
                <div class="flex items-center justify-between mb-5 border-b border-[#cfc6af] pb-2">
                    <h3 class="text-sm font-bold text-[#1E293B] uppercase tracking-wide">Komparasi vs Benchmark Nasional</h3>
                    <div class="flex items-center gap-4 text-[10px] font-bold text-[#4c4635]">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-[#1E293B] rounded-sm"></span> Omzet</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-[#00677d] rounded-sm"></span> Profit</span>
                        <span class="flex items-center gap-1.5"><span class="w-4 h-0 border-t-2 border-dashed border-[#dbb940] inline-block"></span> Rerata</span>
                    </div>
                </div>

                @php
                    $chartMaxRev = $maxRev > 0 ? $maxRev : 1;
                    $calcBenchPct = ($avgRev / $chartMaxRev) * 100;
                @endphp

                <div class="relative h-48 flex gap-2 px-2">
                    {{-- Y-axis labels --}}
                    <div class="w-14 shrink-0 flex flex-col justify-between text-right text-[9px] text-[#7e7663] font-medium pr-2 pb-6">
                        @php $maxM = $chartMaxRev / 1000000; @endphp
                        <span class="whitespace-nowrap">{{ number_format($maxM, 1) }} Jt</span>
                        <span class="whitespace-nowrap">{{ number_format($maxM * 0.75, 1) }} Jt</span>
                        <span class="whitespace-nowrap">{{ number_format($maxM * 0.5, 1) }} Jt</span>
                        <span class="whitespace-nowrap">{{ number_format($maxM * 0.25, 1) }} Jt</span>
                        <span>0</span>
                    </div>

                    {{-- Chart Area --}}
                    <div class="flex-1 flex flex-col h-full">
                        {{-- Bars Container --}}
                        <div class="relative flex-1 flex items-end gap-1.5 border-b border-[#cfc6af]">
                            {{-- Benchmark line --}}
                            <div class="absolute left-0 right-0 border-t-2 border-dashed border-[#dbb940] pointer-events-none"
                                 style="bottom: {{ min($calcBenchPct, 100) }}%"></div>

                            {{-- Bars --}}
                            @foreach (array_slice($branches, 0, 8) as $i => $branch)
                                @php
                                    $revPct = $chartMaxRev > 0 ? round(($branch['revenue'] / $chartMaxRev) * 100) : 0;
                                    $profPct = $chartMaxRev > 0 ? round(($branch['profit'] / $chartMaxRev) * 100) : 0;
                                @endphp
                                <div class="flex-1 flex items-end justify-center gap-[1px] h-full group cursor-pointer relative z-10">
                                    <div class="flex-1 bg-[#1E293B] rounded-t-sm transition-all group-hover:bg-[#314865]"
                                         style="height: {{ max($revPct, 1) }}%"></div>
                                    <div class="flex-1 bg-[#00677d] rounded-t-sm transition-all group-hover:bg-[#008ba6]"
                                         style="height: {{ max($profPct, 1) }}%"></div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Labels Container --}}
                        <div class="flex items-start gap-1.5 h-6 pt-1.5">
                            @foreach (array_slice($branches, 0, 8) as $i => $branch)
                                <div class="flex-1 text-[9px] font-bold text-[#4c4635] truncate text-center" title="{{ $branch['name'] }}">
                                    {{ Str::limit($branch['name'], 9, '') }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Donut Chart --}}
            <div class="lg:col-span-5 bg-[var(--dash-card-bg)] rounded-lg border border-[#cfc6af] p-5 flex flex-col">
                <div class="flex items-center justify-between border-b border-[#cfc6af] pb-2 mb-4">
                    <h3 class="text-sm font-bold text-[#1E293B] uppercase tracking-wide">Pangsa Area</h3>
                    <span class="text-[10px] font-bold bg-[#eeeeed] text-[#4c4635] px-2 py-1 rounded">100% Total</span>
                </div>

                <div class="flex flex-col gap-4 flex-1 justify-center">
                    {{-- Simplified Progress Bars instead of SVG Donut for more Executive feel --}}
                    <div class="space-y-4">
                        @forelse ($areas as $area)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-bold text-[#1E293B] flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-sm" style="background:{{ $area['color'] }}"></span>
                                        {{ $area['name'] }}
                                    </span>
                                    <span class="font-bold text-[#1E293B] font-mono">{{ $area['percentage'] }}%</span>
                                </div>
                                <div class="w-full bg-[#e2e2e2] h-2 rounded-sm overflow-hidden">
                                    <div class="h-full rounded-sm" style="width: {{ $area['percentage'] }}%; background-color: {{ $area['color'] }};"></div>
                                </div>
                                <p class="text-[9px] text-[#7e7663] font-mono mt-1 text-right">Rp {{ number_format($area['revenue'] / 1000000, 1) }} Juta</p>
                            </div>
                        @empty
                            <p class="text-[#7e7663] text-center text-sm py-4">Data area tidak tersedia</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab: Table --}}
    <div x-show="activeTab === 'table'" class="p-6 bg-[#f9f9f8]">
        <div class="bg-[var(--dash-card-bg)] rounded-lg border border-[#cfc6af] overflow-hidden">
            <div class="p-5 border-b border-[#cfc6af] flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[var(--dash-card-bg)]">
                <div>
                    <h3 class="text-sm font-bold text-[#1E293B] uppercase tracking-wide">Audit Terperinci Cabang</h3>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" x-model="search"
                               placeholder="Cari data..."
                               class="text-xs pl-8 pr-3 py-2 border border-[#cfc6af] rounded w-48 focus:ring-1 focus:ring-[#dbb940] focus:border-[#dbb940] text-[#1a1c1c]">
                        <span class="material-symbols-outlined text-[#7e7663] text-[16px] absolute left-2.5 top-2">search</span>
                    </div>
                    <select x-model="sortBy" class="text-xs border border-[#cfc6af] rounded py-2 px-3 bg-[var(--dash-card-bg)] text-[#1a1c1c] font-semibold focus:ring-1 focus:ring-[#dbb940] focus:border-[#dbb940]">
                        <option value="revenue">Urut: Revenue</option>
                        <option value="margin">Urut: Margin</option>
                        <option value="trend">Urut: Tren</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#f3f4f3] text-[#4c4635] uppercase tracking-wider font-bold border-b border-[#cfc6af] text-[10px]">
                            <th class="py-3 px-5">Cabang & Area</th>
                            <th class="py-3 px-5 text-right">Revenue</th>
                            <th class="py-3 px-5 text-right">Net Profit</th>
                            <th class="py-3 px-5 text-center">Margin</th>
                            <th class="py-3 px-5 text-right">Deviasi (Rerata)</th>
                            <th class="py-3 px-5 text-center">Tren</th>
                            <th class="py-3 px-5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e2e2e2]">
                        <template x-for="(b, i) in filteredBranches" :key="b.name">
                            <tr class="hover:bg-[#f9f9f8] transition-colors">
                                <td class="py-3 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 text-[#7e7663] font-mono font-bold text-[10px]" x-text="String(i+1).padStart(2,'0')"></div>
                                        <div>
                                            <span class="font-bold text-[#1E293B] block" x-text="b.name"></span>
                                            <span class="text-[10px] text-[#7e7663]" x-text="b.area_name"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-5 text-right font-bold text-[#1E293B] font-mono"
                                    x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(b.revenue)"></td>
                                <td class="py-3 px-5 text-right font-bold font-mono"
                                    :class="b.profit >= 0 ? 'text-[#00677d]' : 'text-[#ba1a1a]'"
                                    x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(b.profit)"></td>
                                <td class="py-3 px-5 text-center">
                                    <span class="font-mono font-bold"
                                          :class="b.margin >= 25 ? 'text-[#00677d]' : 'text-[#4c4635]'"
                                          x-text="b.margin + '%'"></span>
                                </td>
                                <td class="py-3 px-5 text-right font-bold font-mono text-[11px]"
                                    :class="b.vs_national >= 0 ? 'text-[#00677d]' : 'text-[#ba1a1a]'"
                                    x-text="(b.vs_national >= 0 ? '+' : '') + b.vs_national + '%'"></td>
                                <td class="py-3 px-5 text-center">
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-bold px-2 py-0.5 rounded"
                                          :class="b.trend >= 0 ? 'bg-[#d1fae5] text-[#065f46]' : 'bg-[#fee2e2] text-[#991b1b]'"
                                          x-text="(b.trend >= 0 ? '+' : '') + b.trend + '%'"></span>
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[9px] font-bold uppercase tracking-wider"
                                          :class="{
                                              'bg-[#ffe07e] text-[#564500]': b.status === 'Top Tier',
                                              'bg-[#b3ebff] text-[#004e5f]': b.status === 'On Track',
                                              'bg-[#eeeeed] text-[#4c4635]': b.status === 'Baseline',
                                              'bg-[#ffdad6] text-[#93000a]': b.status === 'Need Attention',
                                          }">
                                        <span x-text="b.status"></span>
                                    </span>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredBranches.length === 0">
                            <tr>
                                <td colspan="7" class="py-8 text-center text-[#7e7663] text-xs">Tidak ada data cabang ditemukan</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-t border-[#cfc6af] bg-[#f9f9f8] flex items-center justify-between text-[10px] font-bold text-[#7e7663] uppercase tracking-wide">
                <span>Total <span x-text="filteredBranches.length" class="text-[#1E293B]"></span> / {{ count($branches) }} Cabang</span>
                <span>Audit: {{ $data['period'] ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Tab: Overview Nasional --}}
    <div x-show="activeTab === 'overview'" class="p-6 bg-[#f9f9f8]">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($branches as $branch)
                @php
                    $sc = $statusColors[$branch['status']] ?? $statusColors['Baseline'];
                @endphp
                <div class="flex items-center justify-between p-4 bg-[var(--dash-card-bg)] rounded-lg border border-[#cfc6af] shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-[#f3f4f3] border border-[#e2e2e2] flex items-center justify-center font-bold text-[10px] text-[#4c4635]">
                            {{ $branch['abbr'] }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[#1E293B]">{{ $branch['name'] }}</p>
                            <p class="text-[10px] text-[#7e7663]">{{ $branch['area_name'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-[#1E293B] font-mono">Rp {{ number_format($branch['revenue'] / 1000000, 2) }} Juta</p>
                        <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 mt-1 rounded {{ $sc['bg'] }} {{ $sc['text'] }}">
                            {{ $branch['status'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
