@php
    $nat       = $data['national'] ?? [];
    $byBranch  = $data['by_branch'] ?? [];
    $byArea    = $data['by_area'] ?? [];
    $byProgram = $data['by_program'] ?? [];
    $trend     = $data['monthly_trend'] ?? [];
    $maxBranch = max(array_column($byBranch, 'this_month') ?: [1]);
    $maxProg   = max(array_column($byProgram, 'member_count') ?: [1]);
    $maxTrend  = max(array_column($trend, 'count') ?: [1]);
@endphp

<div class="bg-white rounded-lg border border-[#cfc6af] shadow-sm overflow-hidden font-sans"
     x-data="{
         activeTab: 'comparison',
         search: '',
         sortBy: 'member',
         get filteredBranches() {
             let list = @js($byBranch);
             if (this.search) {
                 const s = this.search.toLowerCase();
                 list = list.filter(b => b.name.toLowerCase().includes(s) || b.area_name.toLowerCase().includes(s));
             }
             if (this.sortBy === 'member') list = list.slice().sort((a,b) => b.this_month - a.this_month);
             if (this.sortBy === 'mom')    list = list.slice().sort((a,b) => b.mom_pct - a.mom_pct);
             if (this.sortBy === 'yoy')    list = list.slice().sort((a,b) => b.yoy_pct - a.yoy_pct);
             if (this.sortBy === 'revenue') list = list.slice().sort((a,b) => b.revenue - a.revenue);
             return list;
         }
     }">

    {{-- Header --}}
    <div class="px-6 py-5 border-b-2 border-[#1E293B] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-[#f9f9f8]">
        <div>
            <h2 class="text-lg font-bold text-[#1E293B] tracking-tight">ANALITIK MEMBER & PROGRAM</h2>
            <p class="text-xs text-[#7e7663] mt-1 font-medium tracking-wide uppercase">Per Cabang, Area & Nasional • {{ $data['period'] ?? '-' }}</p>
        </div>
        <div class="flex items-center gap-4 flex-wrap">
            <form onsubmit="event.preventDefault(); const url = new window.URL(window.location.href); url.searchParams.set('member_growth_month', this.month.value); url.searchParams.set('member_growth_year', this.year.value); window.location.href = url.toString();" class="flex items-center gap-1">
                @php
                    $mVal = request('member_growth_month', request('month', now()->month));
                    $yVal = request('member_growth_year', request('year', now()->year));
                @endphp
                <select name="month" class="text-xs border-[#cfc6af] rounded py-1 px-2 bg-white text-[#1E293B] font-semibold focus:ring-1 focus:ring-[#dbb940]">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $mVal == $m ? 'selected' : '' }}>{{ ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'][$m-1] }}</option>
                    @endforeach
                </select>
                <select name="year" class="text-xs border-[#cfc6af] rounded py-1 px-2 bg-white text-[#1E293B] font-semibold focus:ring-1 focus:ring-[#dbb940]">
                    @foreach(range(now()->year - 3, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ $yVal == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-[#1E293B] hover:bg-[#314865] text-white px-2 py-1 rounded text-xs font-semibold transition-colors">Filter</button>
            </form>

            <a href="{{ route('dashboard.widget-export', array_merge(['widgetKey' => 'member_growth', 'format' => 'pdf', 'month' => $mVal, 'year' => $yVal], request()->only(['branch_id']))) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded bg-white border border-[#cfc6af] text-[#1E293B] text-xs font-semibold hover:bg-[#f3f4f3] transition-colors">
                <span class="material-symbols-outlined text-[16px]">download</span>
                Unduh Laporan
            </a>
            <nav class="flex items-center p-1 bg-[#eeeeed] rounded border border-[#cfc6af] text-xs font-semibold">
                <button @click="activeTab = 'comparison'"
                        :class="activeTab === 'comparison' ? 'bg-white text-[#1E293B] shadow-sm' : 'text-[#7e7663] hover:text-[#1E293B]'"
                        class="px-4 py-1.5 rounded transition">Komparasi</button>
                <button @click="activeTab = 'program'"
                        :class="activeTab === 'program' ? 'bg-white text-[#1E293B] shadow-sm' : 'text-[#7e7663] hover:text-[#1E293B]'"
                        class="px-4 py-1.5 rounded transition">Program</button>
                <button @click="activeTab = 'table'"
                        :class="activeTab === 'table' ? 'bg-white text-[#1E293B] shadow-sm' : 'text-[#7e7663] hover:text-[#1E293B]'"
                        class="px-4 py-1.5 rounded transition">Detail Data</button>
                <button @click="activeTab = 'overview'"
                        :class="activeTab === 'overview' ? 'bg-white text-[#1E293B] shadow-sm' : 'text-[#7e7663] hover:text-[#1E293B]'"
                        class="px-4 py-1.5 rounded transition">Nasional</button>
            </nav>
        </div>
    </div>

    {{-- National KPI Row (always visible) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-0 divide-x divide-y lg:divide-y-0 divide-[#e2e2e2] bg-white border-b border-[#e2e2e2]">
        <div class="p-5 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-bold text-[#7e7663] tracking-widest uppercase mb-1">Member Bulan Ini</p>
                <span class="text-2xl font-bold text-[#1E293B] font-mono tracking-tight">{{ number_format($nat['this_month'] ?? 0) }}</span>
            </div>
            @php 
                $mom = $nat['mom_pct'] ?? 0; 
                $momColor = $mom > 0 ? 'text-[#00677d]' : ($mom < 0 ? 'text-[#ba1a1a]' : 'text-[#7e7663]');
                $momIcon = $mom > 0 ? 'trending_up' : ($mom < 0 ? 'trending_down' : 'remove');
                $momSign = $mom > 0 ? '+' : '';
            @endphp
            <div class="mt-3 flex items-center gap-2">
                <span class="inline-flex items-center gap-1 text-xs font-bold {{ $momColor }}">
                    <span class="material-symbols-outlined text-[14px]">{{ $momIcon }}</span>
                    {{ $momSign }}{{ $mom }}%
                </span>
                <span class="text-[10px] text-[#7e7663]">MoM</span>
            </div>
        </div>

        <div class="p-5 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-bold text-[#7e7663] tracking-widest uppercase mb-1">vs Tahun Lalu (YoY)</p>
                @php 
                    $yoy = $nat['yoy_pct'] ?? 0; 
                    $yoyColor = $yoy > 0 ? 'text-[#00677d]' : ($yoy < 0 ? 'text-[#ba1a1a]' : 'text-[#7e7663]');
                    $yoySign = $yoy > 0 ? '+' : '';
                @endphp
                <span class="text-2xl font-bold font-mono tracking-tight {{ $yoyColor }}">
                    {{ $yoySign }}{{ $yoy }}%
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <span class="text-[10px] text-[#7e7663]">Thn lalu: <strong>{{ number_format($nat['yoy'] ?? 0) }}</strong> member</span>
            </div>
        </div>

        <div class="p-5 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-bold text-[#7e7663] tracking-widest uppercase mb-1">Total Kumulatif</p>
                <span class="text-2xl font-bold text-[#1E293B] font-mono tracking-tight">{{ number_format($nat['cumulative'] ?? 0) }}</span>
            </div>
            <p class="mt-3 text-[10px] text-[#7e7663] font-medium">Seluruh registrasi aktif</p>
        </div>

        <div class="p-5 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-bold text-[#7e7663] tracking-widest uppercase mb-1">Jangkauan Aktif</p>
                <p class="text-2xl font-bold text-[#1E293B] font-mono tracking-tight">{{ $data['branch_count'] ?? 0 }} <span class="text-base text-[#7e7663]">Cabang</span></p>
            </div>
            <p class="mt-3 text-[10px] text-[#7e7663] font-medium">
                {{ $data['area_count'] ?? 0 }} Area &bull; {{ count($byProgram) }} Program Aktif
            </p>
        </div>
    </div>

    {{-- Tab: Komparasi --}}
    <div x-show="activeTab === 'comparison'" class="p-6 space-y-6 bg-[#f9f9f8]">
        {{-- Top Branch Cards --}}
        <div>
            <div class="flex items-center justify-between mb-4 border-b border-[#cfc6af] pb-2">
                <h3 class="text-sm font-bold text-[#1E293B] tracking-wide uppercase">Top Performa Cabang</h3>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-[#7e7663] font-medium">Susun:</span>
                    <select x-model="sortBy" class="text-xs border-[#cfc6af] rounded py-1 px-2 bg-white text-[#1E293B] font-semibold focus:ring-1 focus:ring-[#dbb940]">
                        <option value="member">Member</option>
                        <option value="mom">MoM%</option>
                        <option value="yoy">YoY%</option>
                        <option value="revenue">Revenue</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse (array_slice($byBranch, 0, 4) as $i => $branch)
                    @php 
                        $mom = $branch['mom_pct'] ?? 0; 
                        $yoy = $branch['yoy_pct'] ?? 0;
                        $momColorBg = $mom > 0 ? 'bg-[#d1fae5] text-[#065f46]' : ($mom < 0 ? 'bg-[#fee2e2] text-[#991b1b]' : 'bg-[#f3f4f3] text-[#4c4635]');
                        $momSign = $mom > 0 ? '+' : '';
                        $yoyColorText = $yoy > 0 ? 'text-[#00677d]' : ($yoy < 0 ? 'text-[#ba1a1a]' : 'text-[#7e7663]');
                        $yoySign = $yoy > 0 ? '+' : '';
                    @endphp
                    <article class="bg-white rounded-lg p-5 border border-[#cfc6af] border-t-4 border-t-[#1E293B] shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <p class="text-sm font-bold text-[#1E293B]">{{ $branch['name'] }}</p>
                                    <p class="text-[10px] text-[#7e7663] mt-0.5">{{ $branch['area_name'] }}</p>
                                </div>
                                <span class="inline-flex items-center gap-0.5 text-[10px] font-bold px-2 py-0.5 rounded {{ $momColorBg }}">
                                    {{ $momSign }}{{ $mom }}%
                                </span>
                            </div>
                            <p class="text-[9px] font-bold tracking-widest uppercase text-[#7e7663]">Member Bulan Ini</p>
                            <p class="text-lg font-bold text-[#1E293B] font-mono">{{ number_format($branch['this_month']) }}</p>

                            <div class="mt-3 pt-3 border-t border-[#e2e2e2] flex items-center justify-between">
                                <div>
                                    <p class="text-[9px] font-bold tracking-widest uppercase text-[#7e7663]">YoY</p>
                                    <p class="text-sm font-bold font-mono {{ $yoyColorText }}">{{ $yoySign }}{{ $yoy }}%</p>
                                </div>
                                <span class="px-2 py-1 rounded text-[10px] font-bold bg-[#f3f4f3] text-[#4c4635] border border-[#cfc6af]">
                                    {{ $branch['revenue'] > 0 ? 'Rp '.number_format($branch['revenue'] / 1000000, 1).'Jt' : 'Rp 0' }}
                                </span>
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
            {{-- Tren Bulanan Bar --}}
            <div class="lg:col-span-7 bg-white rounded-lg border border-[#cfc6af] p-5">
                <div class="flex items-center justify-between mb-5 border-b border-[#cfc6af] pb-2">
                    <h3 class="text-sm font-bold text-[#1E293B] uppercase tracking-wide">Tren Member {{ $data['period'] ? substr($data['period'], -4) : '' }}</h3>
                    <span class="text-[10px] font-bold bg-[#eeeeed] text-[#4c4635] px-2 py-1 rounded">Per Bulan</span>
                </div>
                @php $chartMax = $maxTrend > 0 ? $maxTrend : 1; @endphp
                <div class="relative h-40 flex gap-2 px-2">
                    <div class="w-10 shrink-0 flex flex-col justify-between text-right text-[9px] text-[#7e7663] font-medium pr-2 pb-6">
                        @if($chartMax <= 4)
                            <span>{{ $chartMax }}</span>
                            <span></span>
                            <span>{{ ceil($chartMax / 2) }}</span>
                            <span></span>
                            <span>0</span>
                        @else
                            <span>{{ $chartMax }}</span>
                            <span>{{ intdiv($chartMax * 3, 4) }}</span>
                            <span>{{ intdiv($chartMax, 2) }}</span>
                            <span>{{ intdiv($chartMax, 4) }}</span>
                            <span>0</span>
                        @endif
                    </div>
                    <div class="flex-1 flex flex-col h-full">
                        <div class="relative flex-1 flex items-end gap-1 border-b border-[#cfc6af]">
                            @foreach ($trend as $t)
                                @php $pct = $chartMax > 0 ? max(2, round(($t['count'] / $chartMax) * 100)) : 2; @endphp
                                <div class="flex-1 flex items-end h-full group relative">
                                    <div class="w-full bg-[#1E293B] rounded-t-sm transition-all group-hover:bg-[#314865]"
                                         style="height: {{ $pct }}%"></div>
                                    {{-- Tooltip on hover --}}
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block bg-[#1E293B] text-white text-[10px] py-0.5 px-1.5 rounded whitespace-nowrap z-10">
                                        {{ number_format($t['count']) }} Member
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-start gap-1 h-6 pt-1.5">
                            @php
                                $monthNames = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
                            @endphp
                            @foreach ($trend as $t)
                                <div class="flex-1 text-[9px] font-bold text-[#4c4635] text-center">{{ $monthNames[$t['month']] ?? $t['month'] }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Per Area --}}
            <div class="lg:col-span-5 bg-white rounded-lg border border-[#cfc6af] p-5 flex flex-col">
                <div class="flex items-center justify-between border-b border-[#cfc6af] pb-2 mb-4">
                    <h3 class="text-sm font-bold text-[#1E293B] uppercase tracking-wide">Distribusi Area</h3>
                    <span class="text-[10px] font-bold bg-[#eeeeed] text-[#4c4635] px-2 py-1 rounded">{{ $data['area_count'] ?? 0 }} Area</span>
                </div>
                <div class="space-y-4 flex-1 justify-center flex flex-col">
                    @forelse (array_slice($byArea, 0, 8) as $area)
                        @php
                            $areaPct = ($nat['this_month'] ?? 0) > 0
                                ? round(($area['this_month'] / $nat['this_month']) * 100)
                                : 0;
                            $mom = $area['mom_pct'] ?? 0;
                            $momColor = $mom > 0 ? 'text-[#00677d]' : ($mom < 0 ? 'text-[#ba1a1a]' : 'text-[#7e7663]');
                            $momSign = $mom > 0 ? '+' : '';
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-[#1E293B]">{{ $area['name'] }}</span>
                                <span class="font-bold text-[#1E293B] font-mono">{{ number_format($area['this_month']) }} <span class="text-[#7e7663] font-normal">({{ $areaPct }}%)</span></span>
                            </div>
                            <div class="w-full bg-[#e2e2e2] h-2 rounded-sm overflow-hidden">
                                <div class="h-full bg-[#1E293B] rounded-sm" style="width: {{ max($areaPct, 1) }}%"></div>
                            </div>
                            <div class="flex justify-between text-[9px] text-[#7e7663] mt-1">
                                <span>{{ $area['branch_count'] }} cabang</span>
                                <span class="{{ $momColor }} font-bold">MoM {{ $momSign }}{{ $mom }}%</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-[#7e7663] text-center text-sm py-4">Data area tidak tersedia</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Tab: Program --}}
    <div x-show="activeTab === 'program'" class="p-6 bg-[#f9f9f8]" style="display:none;">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            {{-- Ranking Cards --}}
            <div class="lg:col-span-7 space-y-3">
                <div class="border-b border-[#cfc6af] pb-2 mb-4">
                    <h3 class="text-sm font-bold text-[#1E293B] uppercase tracking-wide">Ranking Program — {{ $data['period'] ?? '-' }}</h3>
                </div>
                @forelse ($byProgram as $i => $prog)
                    @php
                        $isTop = $i === 0;
                        $pct = $maxProg > 0 ? max(4, round(($prog['member_count'] / $maxProg) * 100)) : 4;
                    @endphp
                    <div class="bg-white rounded-lg p-4 border {{ $isTop ? 'border-[#dbb940] border-t-2 border-t-[#dbb940]' : 'border-[#cfc6af]' }} flex items-center gap-4">
                        <div class="w-8 h-8 rounded flex items-center justify-center font-bold text-sm shrink-0
                            {{ $i === 0 ? 'bg-[#ffe07e] text-[#564500]' : ($i === 1 ? 'bg-[#e2e2e2] text-[#4c4635]' : ($i === 2 ? 'bg-[#f5ddb0] text-[#6b4400]' : 'bg-[#f3f4f3] text-[#7e7663]')) }}">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-[#1E293B] truncate">{{ $prog['program'] }}</p>
                            <div class="w-full bg-[#e2e2e2] h-1.5 rounded-sm mt-1.5 overflow-hidden">
                                <div class="h-full bg-[#1E293B] rounded-sm" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-[#1E293B] font-mono">{{ number_format($prog['member_count']) }} <span class="text-[10px] text-[#7e7663] font-normal">member</span></p>
                            <p class="text-[10px] text-[#7e7663] font-mono mt-0.5">{{ $prog['revenue'] > 0 ? 'Rp '.number_format($prog['revenue'] / 1000000, 1).'Jt' : '-' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-sm text-[#7e7663]">Tidak ada data program aktif</div>
                @endforelse
            </div>

            {{-- Summary Stats --}}
            <div class="lg:col-span-5 space-y-4">
                <div class="border-b border-[#cfc6af] pb-2 mb-4">
                    <h3 class="text-sm font-bold text-[#1E293B] uppercase tracking-wide">Statistik Program</h3>
                </div>
                @php
                    $totalProgramMember = array_sum(array_column($byProgram, 'member_count'));
                    $totalProgramRev = array_sum(array_column($byProgram, 'revenue'));
                @endphp
                <div class="bg-white rounded-lg p-4 border border-[#cfc6af]">
                    <p class="text-[10px] font-bold text-[#7e7663] tracking-widest uppercase mb-1">Total Member (Semua Program)</p>
                    <p class="text-2xl font-bold text-[#1E293B] font-mono">{{ number_format($totalProgramMember) }}</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-[#cfc6af]">
                    <p class="text-[10px] font-bold text-[#7e7663] tracking-widest uppercase mb-1">Total Revenue Program</p>
                    <p class="text-2xl font-bold text-[#00677d] font-mono">Rp {{ number_format($totalProgramRev / 1000000, 1) }}Jt</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-[#cfc6af]">
                    <p class="text-[10px] font-bold text-[#7e7663] tracking-widest uppercase mb-1">Jumlah Program Aktif</p>
                    <p class="text-2xl font-bold text-[#1E293B] font-mono">{{ count($byProgram) }}</p>
                </div>
                @if (!empty($byProgram[0]))
                    <div class="bg-[#fffbeb] rounded-lg p-4 border border-[#dbb940]">
                        <p class="text-[10px] font-bold text-[#7e7663] tracking-widest uppercase mb-1">Program Terbaik</p>
                        <p class="text-sm font-bold text-[#1E293B]">{{ $byProgram[0]['program'] }}</p>
                        <p class="text-[10px] text-[#7e7663] mt-1">{{ number_format($byProgram[0]['member_count']) }} member bulan ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tab: Detail Data --}}
    <div x-show="activeTab === 'table'" class="p-6 bg-[#f9f9f8]" style="display:none;">
        <div class="bg-white rounded-lg border border-[#cfc6af] overflow-hidden">
            <div class="p-5 border-b border-[#cfc6af] flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
                <h3 class="text-sm font-bold text-[#1E293B] uppercase tracking-wide">Data Terperinci per Cabang</h3>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" x-model="search"
                               placeholder="Cari cabang..."
                               class="text-xs pl-8 pr-3 py-2 border border-[#cfc6af] rounded w-48 focus:ring-1 focus:ring-[#dbb940] focus:border-[#dbb940] text-[#1a1c1c]">
                        <span class="material-symbols-outlined text-[#7e7663] text-[16px] absolute left-2.5 top-2">search</span>
                    </div>
                    <select x-model="sortBy" class="text-xs border border-[#cfc6af] rounded py-2 px-3 bg-white text-[#1a1c1c] font-semibold focus:ring-1 focus:ring-[#dbb940]">
                        <option value="member">Urut: Member</option>
                        <option value="mom">Urut: MoM%</option>
                        <option value="yoy">Urut: YoY%</option>
                        <option value="revenue">Urut: Revenue</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#f3f4f3] text-[#4c4635] uppercase tracking-wider font-bold border-b border-[#cfc6af] text-[10px]">
                            <th class="py-3 px-5">Cabang & Area</th>
                            <th class="py-3 px-5 text-right">Member Bln Ini</th>
                            <th class="py-3 px-5 text-center">MoM</th>
                            <th class="py-3 px-5 text-center">YoY</th>
                            <th class="py-3 px-5 text-right">Revenue</th>
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
                                <td class="py-3 px-5 text-right font-bold text-[#1E293B] font-mono" x-text="new Intl.NumberFormat('id-ID').format(b.this_month)"></td>
                                <td class="py-3 px-5 text-center font-bold font-mono text-[11px]"
                                    :class="b.mom_pct > 0 ? 'text-[#00677d]' : (b.mom_pct < 0 ? 'text-[#ba1a1a]' : 'text-[#7e7663]')"
                                    x-text="(b.mom_pct > 0 ? '+' : '') + b.mom_pct + '%'"></td>
                                <td class="py-3 px-5 text-center font-bold font-mono text-[11px]"
                                    :class="b.yoy_pct > 0 ? 'text-[#00677d]' : (b.yoy_pct < 0 ? 'text-[#ba1a1a]' : 'text-[#7e7663]')"
                                    x-text="(b.yoy_pct > 0 ? '+' : '') + b.yoy_pct + '%'"></td>
                                <td class="py-3 px-5 text-right font-bold text-[#1E293B] font-mono"
                                    x-text="b.revenue > 0 ? 'Rp ' + new Intl.NumberFormat('id-ID').format(b.revenue) : '-'"></td>
                            </tr>
                        </template>
                        <template x-if="filteredBranches.length === 0">
                            <tr><td colspan="5" class="py-8 text-center text-[#7e7663] text-xs">Tidak ada data cabang ditemukan</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t border-[#cfc6af] bg-[#f9f9f8] flex items-center justify-between text-[10px] font-bold text-[#7e7663] uppercase tracking-wide">
                <span>Total <span x-text="filteredBranches.length" class="text-[#1E293B]"></span> / {{ count($byBranch) }} Cabang</span>
                <span>Periode: {{ $data['period'] ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Tab: Nasional (semua cabang compact) --}}
    <div x-show="activeTab === 'overview'" class="p-6 bg-[#f9f9f8]" style="display:none;">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($byBranch as $branch)
                @php 
                    $mom = $branch['mom_pct'] ?? 0;
                    $momColorBg = $mom > 0 ? 'bg-[#d1fae5] text-[#065f46]' : ($mom < 0 ? 'bg-[#fee2e2] text-[#991b1b]' : 'bg-[#f3f4f3] text-[#4c4635]');
                    $momSign = $mom > 0 ? '+' : '';
                @endphp
                <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-[#cfc6af] shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-[#f3f4f3] border border-[#e2e2e2] flex items-center justify-center font-bold text-[10px] text-[#4c4635]">
                            {{ strtoupper(substr($branch['name'], 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[#1E293B]">{{ $branch['name'] }}</p>
                            <p class="text-[10px] text-[#7e7663]">{{ $branch['area_name'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-[#1E293B] font-mono">{{ number_format($branch['this_month']) }} member</p>
                        <span class="inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 mt-1 rounded {{ $momColorBg }}">
                            {{ $momSign }}{{ $mom }}% MoM
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
