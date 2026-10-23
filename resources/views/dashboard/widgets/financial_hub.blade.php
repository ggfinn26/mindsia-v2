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

<div class="rounded-2xl shadow-sm overflow-hidden font-sans w-full"
     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
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

    {{-- Widget Header --}}
    <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
         style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
        <div>
            <h2 class="text-sm font-bold uppercase tracking-wide font-jakarta" style="color: var(--dash-text-heading);">Komparasi Performa Finansial</h2>
            <p class="text-xs mt-1 font-medium tracking-wide uppercase" style="color: var(--dash-text-muted);">Cabang, Area & Nasional • {{ $data['period'] ?? '-' }}</p>
        </div>
        
        <div class="flex items-center gap-4 flex-wrap">
            <form onsubmit="event.preventDefault(); const url = new window.URL(window.location.href); url.searchParams.set('financial_hub_month', this.month.value); url.searchParams.set('financial_hub_year', this.year.value); window.location.href = url.toString();" class="flex items-center gap-1.5">
                @php
                    $mVal = request('financial_hub_month', request('month', now()->month));
                    $yVal = request('financial_hub_year', request('year', now()->year));
                @endphp
                <select name="month" class="text-xs rounded-lg py-1 px-2.5 font-semibold focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $mVal == $m ? 'selected' : '' }}>{{ ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'][$m-1] }}</option>
                    @endforeach
                </select>
                <select name="year" class="text-xs rounded-lg py-1 px-2.5 font-semibold focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    @foreach(range(now()->year - 3, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ $yVal == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-1 rounded-lg text-xs font-semibold text-white transition-opacity shrink-0"
                        style="background-color: var(--dash-primary);">Filter</button>
            </form>

            <button @click="downloadModalOpen = true"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                    style="color: var(--dash-text-body); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[14px]">download</span>
                <span>Unduh Laporan</span>
            </button>

            {{-- Modal --}}
            <div x-show="downloadModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" style="display: none;" @keydown.escape.window="downloadModalOpen = false">
                <div @click.away="downloadModalOpen = false" class="rounded-2xl shadow-2xl w-full max-w-md"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="px-5 py-4 flex justify-between items-center rounded-t-2xl"
                         style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Unduh Laporan PDF</h3>
                        <button @click="downloadModalOpen = false" class="transition-colors" style="color: var(--dash-text-muted);">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                    <div class="p-5">
                        <div class="flex mb-4" style="border-bottom: 1px solid var(--dash-card-border);">
                            <button @click="dlType = 'tahunan'" :class="dlType === 'tahunan' ? 'border-b-2 font-bold' : ''"
                                    :style="dlType === 'tahunan' ? 'border-color: var(--dash-primary); color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'"
                                    class="px-3 py-2 text-xs transition-colors">Tahunan</button>
                            <button @click="dlType = 'semester'" :class="dlType === 'semester' ? 'border-b-2 font-bold' : ''"
                                    :style="dlType === 'semester' ? 'border-color: var(--dash-primary); color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'"
                                    class="px-3 py-2 text-xs transition-colors">Semester</button>
                            <button @click="dlType = 'bulanan'" :class="dlType === 'bulanan' ? 'border-b-2 font-bold' : ''"
                                    :style="dlType === 'bulanan' ? 'border-color: var(--dash-primary); color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'"
                                    class="px-3 py-2 text-xs transition-colors">Bulanan</button>
                        </div>

                        <form action="{{ route('dashboard.widget-export', ['widgetKey' => 'financial_hub', 'format' => 'pdf']) }}" method="GET">
                            <template x-if="dlType === 'tahunan'">
                                <div>
                                    <input type="hidden" name="period_type" value="yearly">
                                    <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">Pilih Tahun</label>
                                    <select name="year" x-model="dlYear" class="w-full rounded-lg py-2 px-3 text-sm focus:outline-none transition-all"
                                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
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
                                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">Semester</label>
                                            <select name="semester" x-model="dlSemester" class="w-full rounded-lg py-2 px-3 text-sm focus:outline-none transition-all"
                                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                    onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                                                <option value="1">Semester 1 (Jan-Jun)</option>
                                                <option value="2">Semester 2 (Jul-Des)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">Tahun</label>
                                            <select name="year" x-model="dlYear" class="w-full rounded-lg py-2 px-3 text-sm focus:outline-none transition-all"
                                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                    onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
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
                                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">Bulan</label>
                                            <select name="month" x-model="dlMonth" class="w-full rounded-lg py-2 px-3 text-sm focus:outline-none transition-all"
                                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                    onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                                                @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $m => $name)
                                                    <option value="{{ $m }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">Tahun</label>
                                            <select name="year" x-model="dlYear" class="w-full rounded-lg py-2 px-3 text-sm focus:outline-none transition-all"
                                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                    onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                                                @foreach(range(now()->year - 2, now()->year + 1) as $y)
                                                    <option value="{{ $y }}">{{ $y }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div class="mt-6 flex justify-end gap-2">
                                <button type="button" @click="downloadModalOpen = false" class="px-4 py-2 text-xs font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);">Batal</button>
                                <button type="submit" class="px-4 py-2 text-white text-xs font-semibold rounded-lg shadow hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-primary);">Unduh PDF</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- View Tabs --}}
            <nav class="flex items-center p-1 rounded-lg text-xs font-semibold"
                 style="background-color: var(--dash-page-bg); border: 1px solid var(--dash-card-border);">
                <button @click="activeTab = 'overview'"
                        :class="activeTab === 'overview' ? 'shadow-sm' : ''"
                        :style="activeTab === 'overview' ? 'background-color: var(--dash-card-bg); color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'"
                        class="px-4 py-1.5 rounded-lg transition">Nasional</button>
                <button @click="activeTab = 'comparison'"
                        :class="activeTab === 'comparison' ? 'shadow-sm' : ''"
                        :style="activeTab === 'comparison' ? 'background-color: var(--dash-card-bg); color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'"
                        class="px-4 py-1.5 rounded-lg transition">Komparasi</button>
                <button @click="activeTab = 'table'"
                        :class="activeTab === 'table' ? 'shadow-sm' : ''"
                        :style="activeTab === 'table' ? 'background-color: var(--dash-card-bg); color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'"
                        class="px-4 py-1.5 rounded-lg transition">Detail Data</button>
            </nav>
        </div>
    </div>

    {{-- National KPI Row (always visible) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-0 divide-x divide-y lg:divide-y-0"
         style="divide-color: var(--dash-card-border); background-color: var(--dash-card-bg); border-bottom: 1px solid var(--dash-card-border);">
        {{-- Revenue Nasional --}}
        <div class="p-5 flex flex-col justify-between" style="border-color: var(--dash-card-border);">
            <div>
                <p class="text-[10px] font-bold tracking-widest uppercase mb-1" style="color: var(--dash-text-muted);">Total Revenue Nasional</p>
                <span class="text-2xl font-bold font-mono tracking-tight" style="color: var(--dash-text-heading);">
                    Rp {{ number_format($data['national_revenue'] ?? 0, 0, ',', '.') }}
                </span>
            </div>
            @php $nt = $data['national_trend'] ?? 0; @endphp
            <div class="mt-3 flex items-center gap-2">
                <span class="inline-flex items-center gap-1 text-xs font-bold {{ $nt >= 0 ? 'text-[#059669]' : 'text-[#dc2626]' }}">
                    <span class="material-symbols-outlined text-[14px]">{{ $nt >= 0 ? 'trending_up' : 'trending_down' }}</span>
                    {{ $nt >= 0 ? '+' : '' }}{{ $nt }}%
                </span>
                <span class="text-[10px]" style="color: var(--dash-text-muted);">MoM</span>
            </div>
        </div>

        {{-- Net Profit Nasional --}}
        <div class="p-5 flex flex-col justify-between" style="border-color: var(--dash-card-border);">
            <div>
                <p class="text-[10px] font-bold tracking-widest uppercase mb-1" style="color: var(--dash-text-muted);">Net Profit Nasional</p>
                <span class="text-2xl font-bold font-mono tracking-tight" style="color: var(--dash-primary-text);">
                    Rp {{ number_format($data['national_profit'] ?? 0, 0, ',', '.') }}
                </span>
            </div>
            @php $pt = $data['profit_trend'] ?? 0; @endphp
            <div class="mt-3 flex items-center justify-between">
                <span class="inline-flex items-center gap-1 text-xs font-bold {{ $pt >= 0 ? 'text-[#059669]' : 'text-[#dc2626]' }}">
                    <span class="material-symbols-outlined text-[14px]">{{ $pt >= 0 ? 'trending_up' : 'trending_down' }}</span>
                    {{ $pt >= 0 ? '+' : '' }}{{ $pt }}%
                </span>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full"
                      style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    Margin: {{ $data['national_margin'] ?? 0 }}%
                </span>
            </div>
        </div>

        {{-- Rata-rata Cabang --}}
        <div class="p-5 flex flex-col justify-between" style="border-color: var(--dash-card-border);">
            <div>
                <p class="text-[10px] font-bold tracking-widest uppercase mb-1" style="color: var(--dash-text-muted);">Baseline Rerata Cabang</p>
                <p class="text-2xl font-bold font-mono tracking-tight" style="color: var(--dash-text-heading);">
                    Rp {{ number_format($data['avg_branch_revenue'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
            <p class="mt-3 text-[10px] font-medium" style="color: var(--dash-text-muted);">Standar Acuan Kinerja</p>
        </div>

        {{-- Jangkauan Wilayah --}}
        <div class="p-5 flex flex-col justify-between" style="border-color: var(--dash-card-border);">
            <div>
                <p class="text-[10px] font-bold tracking-widest uppercase mb-1" style="color: var(--dash-text-muted);">Jangkauan Aktif</p>
                <p class="text-2xl font-bold font-mono tracking-tight" style="color: var(--dash-text-heading);">
                    {{ $data['branch_count'] ?? 0 }} <span class="text-base" style="color: var(--dash-text-muted);">Cabang</span>
                </p>
            </div>
            <p class="mt-3 text-[10px] font-medium" style="color: var(--dash-text-muted);">
                {{ count($areas) }} Area &bull; {{ $data['superior_count'] ?? 0 }} Superior
            </p>
        </div>
    </div>

    {{-- Tab: Comparison Cards --}}
    <div x-show="activeTab === 'comparison'" class="p-6 space-y-6" style="background-color: var(--dash-page-bg);">

        {{-- Branch Cards --}}
        <div>
            <div class="flex items-center justify-between mb-4 pb-2" style="border-bottom: 1px solid var(--dash-card-border);">
                <h3 class="text-sm font-bold uppercase tracking-wide font-jakarta" style="color: var(--dash-text-heading);">Top Performa Cabang</h3>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium" style="color: var(--dash-text-muted);">Susun:</span>
                    <select x-model="sortBy" class="text-xs rounded-lg py-1 px-2.5 font-semibold focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
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
                    <article class="rounded-2xl p-5 shadow-sm flex flex-col justify-between"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); border-top: 4px solid var(--dash-primary);">
                        <div>
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <p class="text-sm font-bold truncate" style="color: var(--dash-text-heading);">{{ $branch['name'] }}</p>
                                    <p class="text-[10px] mt-0.5" style="color: var(--dash-text-muted);">{{ $branch['area_name'] }}</p>
                                </div>
                                <span class="inline-flex items-center gap-0.5 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $trend >= 0 ? 'bg-[#d1fae5] text-[#065f46]' : 'bg-[#fee2e2] text-[#991b1b]' }}">
                                    {{ $trend >= 0 ? '+' : '' }}{{ $trend }}%
                                </span>
                            </div>

                            <p class="text-[9px] font-bold tracking-widest uppercase" style="color: var(--dash-text-muted);">Total Revenue</p>
                            <p class="text-lg font-bold font-mono" style="color: var(--dash-text-heading);">Rp {{ number_format($branch['revenue'], 0, ',', '.') }}</p>

                            <div class="mt-3 pt-3 flex items-center justify-between" style="border-top: 1px solid var(--dash-card-border);">
                                <div>
                                    <p class="text-[9px] font-bold tracking-widest uppercase" style="color: var(--dash-text-muted);">Net Profit</p>
                                    <p class="text-sm font-bold font-mono" style="color: var(--dash-primary-text);">Rp {{ number_format($branch['profit'], 0, ',', '.') }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                      style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                    Margin: {{ $branch['margin'] }}%
                                </span>
                            </div>

                            <div class="mt-4 flex items-center gap-1.5 text-[10px] font-bold {{ $vsNat >= 0 ? 'text-[#059669]' : 'text-[#dc2626]' }}">
                                <span>{{ $vsNat >= 0 ? '▲' : '▼' }}</span>
                                <span>{{ $vsNat >= 0 ? '+' : '' }}{{ $vsNat }}% vs Rerata Nasional</span>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-4 text-center py-8 text-sm" style="color: var(--dash-text-muted);">Tidak ada data cabang</div>
                @endforelse
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            {{-- Bar Chart --}}
            <div class="lg:col-span-7 rounded-2xl p-5 shadow-sm"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-center justify-between mb-5 pb-2" style="border-bottom: 1px solid var(--dash-card-border);">
                    <h3 class="text-sm font-bold uppercase tracking-wide font-jakarta" style="color: var(--dash-text-heading);">Komparasi vs Benchmark Nasional</h3>
                    <div class="flex items-center gap-4 text-[10px] font-bold" style="color: var(--dash-text-muted);">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm" style="background-color: var(--dash-text-heading);"></span> Omzet</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm" style="background-color: var(--dash-primary);"></span> Profit</span>
                        <span class="flex items-center gap-1.5"><span class="w-4 h-0 border-t-2 border-dashed border-[#dbb940] inline-block"></span> Rerata</span>
                    </div>
                </div>

                @php
                    $chartMaxRev = $maxRev > 0 ? $maxRev : 1;
                    $calcBenchPct = ($avgRev / $chartMaxRev) * 100;
                @endphp

                <div class="relative h-48 flex gap-2 px-2">
                    {{-- Y-axis labels --}}
                    <div class="w-14 shrink-0 flex flex-col justify-between text-right text-[9px] font-medium pr-2 pb-6" style="color: var(--dash-text-muted);">
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
                        <div class="relative flex-1 flex items-end gap-1.5" style="border-bottom: 1px solid var(--dash-card-border);">
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
                                    <div class="flex-1 rounded-t-sm transition-all"
                                         style="height: {{ max($revPct, 1) }}%; background-color: var(--dash-text-heading); opacity: 0.85;"></div>
                                    <div class="flex-1 rounded-t-sm transition-all"
                                         style="height: {{ max($profPct, 1) }}%; background-color: var(--dash-primary);"></div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Labels Container --}}
                        <div class="flex items-start gap-1.5 h-6 pt-1.5">
                            @foreach (array_slice($branches, 0, 8) as $i => $branch)
                                <div class="flex-1 text-[9px] font-bold truncate text-center" style="color: var(--dash-text-muted);" title="{{ $branch['name'] }}">
                                    {{ Str::limit($branch['name'], 9, '') }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Donut / Progress Area Chart --}}
            <div class="lg:col-span-5 rounded-2xl p-5 shadow-sm flex flex-col"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-center justify-between pb-2 mb-4" style="border-bottom: 1px solid var(--dash-card-border);">
                    <h3 class="text-sm font-bold uppercase tracking-wide font-jakarta" style="color: var(--dash-text-heading);">Pangsa Area</h3>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                          style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">100% Total</span>
                </div>

                <div class="flex flex-col gap-4 flex-1 justify-center">
                    <div class="space-y-4">
                        @forelse ($areas as $area)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-bold flex items-center gap-2" style="color: var(--dash-text-heading);">
                                        <span class="w-2 h-2 rounded-sm" style="background:{{ $area['color'] }}"></span>
                                        {{ $area['name'] }}
                                    </span>
                                    <span class="font-bold font-mono" style="color: var(--dash-text-heading);">{{ $area['percentage'] }}%</span>
                                </div>
                                <div class="w-full h-2 rounded-full overflow-hidden" style="background-color: var(--dash-nav-hover-bg);">
                                    <div class="h-full rounded-full" style="width: {{ $area['percentage'] }}%; background-color: {{ $area['color'] }};"></div>
                                </div>
                                <p class="text-[9px] font-mono mt-1 text-right" style="color: var(--dash-text-muted);">Rp {{ number_format($area['revenue'] / 1000000, 1) }} Juta</p>
                            </div>
                        @empty
                            <p class="text-center text-sm py-4" style="color: var(--dash-text-muted);">Data area tidak tersedia</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab: Table --}}
    <div x-show="activeTab === 'table'" class="p-6" style="background-color: var(--dash-page-bg);">
        <div class="rounded-2xl overflow-hidden shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                 style="background-color: var(--dash-card-bg); border-bottom: 1px solid var(--dash-card-border);">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wide font-jakarta" style="color: var(--dash-text-heading);">Audit Terperinci Cabang</h3>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" x-model="search"
                               placeholder="Cari data..."
                               class="text-xs pl-8 pr-3 py-2 rounded-lg w-48 focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                        <span class="material-symbols-outlined text-[16px] absolute left-2.5 top-2.5 pointer-events-none" style="color: var(--dash-text-muted);">search</span>
                    </div>
                    <select x-model="sortBy" class="text-xs rounded-lg py-2 px-3 font-semibold focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="revenue">Urut: Revenue</option>
                        <option value="margin">Urut: Margin</option>
                        <option value="trend">Urut: Tren</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="uppercase tracking-wider font-bold text-[10px]"
                            style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                            <th class="py-3 px-5">Cabang & Area</th>
                            <th class="py-3 px-5 text-right">Revenue</th>
                            <th class="py-3 px-5 text-right">Net Profit</th>
                            <th class="py-3 px-5 text-center">Margin</th>
                            <th class="py-3 px-5 text-right">Deviasi (Rerata)</th>
                            <th class="py-3 px-5 text-center">Tren</th>
                            <th class="py-3 px-5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--dash-card-border);">
                        <template x-for="(b, i) in filteredBranches" :key="b.name">
                            <tr class="transition-colors"
                                style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="py-3 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 font-mono font-bold text-[10px]" style="color: var(--dash-text-muted);" x-text="String(i+1).padStart(2,'0')"></div>
                                        <div>
                                            <span class="font-bold block" style="color: var(--dash-text-heading);" x-text="b.name"></span>
                                            <span class="text-[10px]" style="color: var(--dash-text-muted);" x-text="b.area_name"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-5 text-right font-bold font-mono" style="color: var(--dash-text-heading);"
                                    x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(b.revenue)"></td>
                                <td class="py-3 px-5 text-right font-bold font-mono"
                                    :class="b.profit >= 0 ? 'text-[#059669]' : 'text-[#dc2626]'"
                                    x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(b.profit)"></td>
                                <td class="py-3 px-5 text-center">
                                    <span class="font-mono font-bold"
                                          :class="b.margin >= 25 ? 'text-[#059669]' : ''"
                                          :style="b.margin < 25 ? 'color: var(--dash-text-body);' : ''"
                                          x-text="b.margin + '%'"></span>
                                </td>
                                <td class="py-3 px-5 text-right font-bold font-mono text-[11px]"
                                    :class="b.vs_national >= 0 ? 'text-[#059669]' : 'text-[#dc2626]'"
                                    x-text="(b.vs_national >= 0 ? '+' : '') + b.vs_national + '%'"></td>
                                <td class="py-3 px-5 text-center">
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-bold px-2 py-0.5 rounded-full"
                                          :class="b.trend >= 0 ? 'bg-[#d1fae5] text-[#065f46]' : 'bg-[#fee2e2] text-[#991b1b]'"
                                          x-text="(b.trend >= 0 ? '+' : '') + b.trend + '%'"></span>
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                                          :class="{
                                              'bg-[#ffe07e] text-[#564500]': b.status === 'Top Tier',
                                              'bg-[#b3ebff] text-[#004e5f]': b.status === 'On Track',
                                              'bg-[#f1f5f9] text-[#475569]': b.status === 'Baseline',
                                              'bg-[#fee2e2] text-[#991b1b]': b.status === 'Need Attention',
                                          }">
                                        <span x-text="b.status"></span>
                                    </span>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredBranches.length === 0">
                            <tr>
                                <td colspan="7" class="py-8 text-center text-xs" style="color: var(--dash-text-muted);">Tidak ada data cabang ditemukan</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="p-3 flex items-center justify-between text-[10px] font-bold uppercase tracking-wide"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                <span>Total <span x-text="filteredBranches.length" style="color: var(--dash-text-heading);"></span> / {{ count($branches) }} Cabang</span>
                <span>Audit: {{ $data['period'] ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Tab: Overview Nasional --}}
    <div x-show="activeTab === 'overview'" class="p-6" style="background-color: var(--dash-page-bg);">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($branches as $branch)
                @php
                    $sc = $statusColors[$branch['status']] ?? $statusColors['Baseline'];
                @endphp
                <div class="flex items-center justify-between p-4 rounded-2xl shadow-sm"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-[10px]"
                             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                            {{ $branch['abbr'] }}
                        </div>
                        <div>
                            <p class="text-xs font-bold" style="color: var(--dash-text-heading);">{{ $branch['name'] }}</p>
                            <p class="text-[10px]" style="color: var(--dash-text-muted);">{{ $branch['area_name'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold font-mono" style="color: var(--dash-text-heading);">Rp {{ number_format($branch['revenue'] / 1000000, 2) }} Juta</p>
                        <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 mt-1 rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
                            {{ $branch['status'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
