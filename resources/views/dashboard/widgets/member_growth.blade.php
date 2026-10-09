<div class="bg-[var(--dash-card-bg)] rounded-lg shadow-sm overflow-hidden font-sans border border-[#cfc6af] w-full" x-data="{
    activeTab: 'segmentasi',
    activeProgram: '{{ $data['by_program'][0]['program'] ?? '' }}',
    branchId: '{{ $data['filters']['branch_id'] ?? '' }}',
    month: '{{ $data['filters']['month'] ?? now()->month }}',
    year: '{{ $data['filters']['year'] ?? now()->year }}',
    programs: {{ json_encode($data['by_program']) }},
    areas: {{ json_encode($data['by_area']) }},
    branches: {{ json_encode($data['by_branch']) }},
    get activeData() {
        return this.programs.find(p => p.program === this.activeProgram) || null;
    },
    get topBranches() {
        if(!this.activeProgram) return [];
        return this.branches.map(b => {
            const p = b.programs.find(x => x.name === this.activeProgram);
            return { id: b.id, name: b.name, area: b.area_name, count: p ? p.count : 0 };
        }).filter(b => b.count > 0).sort((a,b) => b.count - a.count).slice(0, 5);
    },
    get topAreas() {
        if(!this.activeProgram) return [];
        return this.areas.map(a => {
            const p = a.programs.find(x => x.name === this.activeProgram);
            return { name: a.name, count: p ? p.count : 0 };
        }).filter(a => a.count > 0).sort((a,b) => b.count - a.count).slice(0, 5);
    }
}">
    {{-- Header & Filters --}}
    <div class="bg-[#f3f4f3] px-5 py-0 border-b border-[#cfc6af] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="py-4">
            <h3 class="text-sm font-bold text-[#1E293B] uppercase tracking-wide">Analisis Segmentasi & Demografi</h3>
            <p class="text-xs text-[color:var(--dash-text-muted)] mt-0.5">Cabang, Area & Nasional • {{ $data['period'] }}</p>
        </div>
        
        <div class="flex items-center gap-3 py-4 md:py-0 h-full">
            <nav class="flex h-full space-x-1" aria-label="Tabs">
                <button @click="activeTab = 'segmentasi'"
                        :class="activeTab === 'segmentasi' ? 'bg-[var(--dash-card-bg)] text-[#1E293B] shadow-sm border-t border-x border-[#cfc6af] -mb-[1px]' : 'text-[#7e7663] hover:text-[#1E293B]'"
                        class="px-4 py-3 text-xs font-semibold rounded-t transition-colors h-full flex items-center">
                    Peta Segmentasi
                </button>
                <button @click="activeTab = 'table'"
                        :class="activeTab === 'table' ? 'bg-[var(--dash-card-bg)] text-[#1E293B] shadow-sm border-t border-x border-[#cfc6af] -mb-[1px]' : 'text-[#7e7663] hover:text-[#1E293B]' inline-flex items-center gap-1.5"
                        class="px-4 py-3 text-xs font-semibold rounded-t transition-colors h-full flex items-center">
                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                    Detail Data
                </button>
            </nav>

            <form class="flex items-center gap-2 text-xs ml-2" x-on:change="
                const url = new URL(window.location.href);
                url.searchParams.set('member_growth_branch_id', branchId);
                url.searchParams.set('member_growth_month', month);
                url.searchParams.set('member_growth_year', year);
                window.location.href = url.toString();
            " onsubmit="event.preventDefault()">
                <select x-model="month" class="rounded border-[#cfc6af] py-1.5 pl-2 pr-6 focus:ring-[#dbb940]">
                    @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'] as $m => $name)
                        <option value="{{ $m }}">{{ $name }}</option>
                    @endforeach
                </select>
                <select x-model="year" class="rounded border-[#cfc6af] py-1.5 pl-2 pr-6 focus:ring-[#dbb940]">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </form>
            <div x-data="{ downloadModalOpen: false, dlType: 'tahunan', dlYear: year, dlSemester: '1', dlMonth: month }">
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

                            <form action="{{ route('dashboard.widget-export', ['widgetKey' => 'member_growth', 'format' => 'pdf']) }}" method="GET">
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
                                    <button type="button" @click="downloadModalOpen = false" class="px-4 py-2 text-xs font-bold text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">Batal</button>
                                    <button type="submit" class="px-4 py-2 bg-[#1E293B] text-white text-xs font-bold rounded hover:bg-[#0f172a] shadow inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">download</span>Unduh PDF</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab 1: Peta Segmentasi (Sidebar Programs & Main Content) --}}
    <div x-show="activeTab === 'segmentasi'" class="flex flex-col md:flex-row h-auto md:h-[600px]">
        {{-- Sidebar: List of Programs --}}
        <div class="w-full md:w-1/3 lg:w-1/4 border-r border-[#cfc6af] bg-[#f9f9f8] flex flex-col h-full">
            <div class="p-3 border-b border-[#cfc6af] bg-[var(--dash-card-bg)]">
                <p class="text-[10px] font-bold text-[#7e7663] uppercase tracking-wide">Pilih Program</p>
            </div>
            <div class="overflow-y-auto flex-1 p-2 space-y-1">
                <template x-for="prog in programs" :key="prog.program">
                    <button @click="activeProgram = prog.program" 
                            class="w-full text-left p-3 rounded border transition-colors flex flex-col gap-1"
                            :class="activeProgram === prog.program ? 'bg-[var(--dash-card-bg)] border-[#dbb940] shadow-sm ring-1 ring-[#dbb940]' : 'border-transparent hover:bg-[#ebebeb]'">
                        <span class="text-xs font-bold text-[#1E293B] truncate" x-text="prog.program"></span>
                        <div class="flex justify-between items-center text-[10px] text-[#7e7663]">
                            <span class="font-mono font-bold text-[#4c4635]" x-text="new Intl.NumberFormat('id-ID').format(prog.member_count) + ' Member'"></span>
                            <span class="font-mono text-[#00677d]" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(prog.revenue/1000000) + 'M'"></span>
                        </div>
                    </button>
                </template>
                <template x-if="programs.length === 0">
                    <p class="text-xs text-center text-[color:var(--dash-text-muted)] py-4">Belum ada data program aktif.</p>
                </template>
            </div>
        </div>

        {{-- Main Content: Segmentation Details --}}
        <div class="w-full md:w-2/3 lg:w-3/4 bg-[var(--dash-card-bg)] overflow-y-auto p-5">
            <template x-if="activeData">
                <div>
                    {{-- Title Row --}}
                    <div class="mb-6 flex justify-between items-end border-b border-[#cfc6af] pb-3">
                        <div>
                            <h4 class="text-xl font-bold text-[#1E293B]" x-text="activeData.program"></h4>
                            <p class="text-xs text-[color:var(--dash-text-muted)] mt-1">Peta Segmentasi & Demografi</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase font-bold text-[#7e7663]">Total Member / Revenue</p>
                            <p class="text-lg font-mono font-bold text-[#1E293B]">
                                <span x-text="new Intl.NumberFormat('id-ID').format(activeData.member_count)"></span>
                                <span class="text-[#00677d]" x-text="' / Rp' + new Intl.NumberFormat('id-ID').format(activeData.revenue/1000000) + ' Juta'"></span>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        {{-- Skema Pembayaran --}}
                        <div class="border border-[#cfc6af] rounded bg-[#f9f9f8] p-4">
                            <h5 class="text-[10px] font-bold text-[#1E293B] uppercase tracking-wide border-b border-[#cfc6af] pb-2 mb-3">Skema Pembayaran</h5>
                            <ul class="space-y-2">
                                <template x-for="pay in activeData.payments" :key="pay.payment_status + pay.installment_type">
                                    <li class="flex justify-between items-center text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full" :class="pay.payment_status === 'paid_full' ? 'bg-[#00677d]' : (pay.payment_status === 'installments' ? 'bg-[#dbb940]' : 'bg-[#ba1a1a]')"></span>
                                            <span class="text-[#4c4635] font-medium" x-text="
                                                pay.payment_status === 'paid_full' ? 'Lunas' : 
                                                (pay.payment_status === 'installments' ? 'Cicilan (' + pay.installment_type + 'x)' : 'Belum Lunas')
                                            "></span>
                                        </div>
                                        <span class="font-mono font-bold text-[#1E293B]" x-text="pay.cnt"></span>
                                    </li>
                                </template>
                                <template x-if="activeData.payments.length === 0">
                                    <li class="text-xs text-[color:var(--dash-text-muted)] italic">Tidak ada data pembayaran.</li>
                                </template>
                            </ul>
                        </div>

                        {{-- Demografi Institusi --}}
                        <div class="border border-[#cfc6af] rounded bg-[#f9f9f8] p-4">
                            <h5 class="text-[10px] font-bold text-[#1E293B] uppercase tracking-wide border-b border-[#cfc6af] pb-2 mb-3">Sebaran Demografi Institusi</h5>
                            <ul class="space-y-2">
                                <template x-for="dem in activeData.demographics" :key="dem.jenjang_institution">
                                    <li class="flex justify-between items-center text-xs">
                                        <span class="text-[#4c4635] font-medium" x-text="dem.jenjang_institution || 'TIDAK DIKETAHUI'"></span>
                                        <span class="font-mono font-bold text-[#1E293B]" x-text="dem.cnt"></span>
                                    </li>
                                </template>
                                <template x-if="activeData.demographics.length === 0">
                                    <li class="text-xs text-[color:var(--dash-text-muted)] italic">Tidak ada data institusi.</li>
                                </template>
                            </ul>
                        </div>

                        {{-- Top Cabang --}}
                        <div class="border border-[#cfc6af] rounded bg-[#f9f9f8] p-4">
                            <h5 class="text-[10px] font-bold text-[#1E293B] uppercase tracking-wide border-b border-[#cfc6af] pb-2 mb-3">Top 5 Cabang</h5>
                            <ul class="space-y-2">
                                <template x-for="(b, idx) in topBranches" :key="b.id">
                                    <li class="flex justify-between items-center text-xs">
                                        <div class="flex gap-2">
                                            <span class="text-[#7e7663] font-mono" x-text="(idx+1)+'.'"></span>
                                            <div>
                                                <span class="text-[#4c4635] font-bold block" x-text="b.name"></span>
                                                <span class="text-[9px] text-[#7e7663]" x-text="b.area"></span>
                                            </div>
                                        </div>
                                        <span class="font-mono font-bold text-[#1E293B]" x-text="b.count"></span>
                                    </li>
                                </template>
                                <template x-if="topBranches.length === 0">
                                    <li class="text-xs text-[color:var(--dash-text-muted)] italic">Tidak ada data cabang.</li>
                                </template>
                            </ul>
                        </div>

                        {{-- Top Area --}}
                        <div class="border border-[#cfc6af] rounded bg-[#f9f9f8] p-4">
                            <h5 class="text-[10px] font-bold text-[#1E293B] uppercase tracking-wide border-b border-[#cfc6af] pb-2 mb-3">Top 5 Area</h5>
                            <ul class="space-y-2">
                                <template x-for="(a, idx) in topAreas" :key="a.name">
                                    <li class="flex justify-between items-center text-xs">
                                        <div class="flex gap-2">
                                            <span class="text-[#7e7663] font-mono" x-text="(idx+1)+'.'"></span>
                                            <span class="text-[#4c4635] font-bold" x-text="a.name"></span>
                                        </div>
                                        <span class="font-mono font-bold text-[#1E293B]" x-text="a.count"></span>
                                    </li>
                                </template>
                                <template x-if="topAreas.length === 0">
                                    <li class="text-xs text-[color:var(--dash-text-muted)] italic">Tidak ada data area.</li>
                                </template>
                            </ul>
                        </div>

                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Tab 2: Detail Data (Table View) --}}
    <div x-show="activeTab === 'table'" class="p-6 bg-[#f9f9f8]" style="display:none;" x-data="{
        search: '',
        sortCol: 'this_month', sortDir: 'desc',
        get filteredBranches() {
            const list = this.search === '' ? this.branches
                : this.branches.filter(b => b.name.toLowerCase().includes(this.search.toLowerCase()) || b.area_name.toLowerCase().includes(this.search.toLowerCase()));
            const col = this.sortCol, dir = this.sortDir;
            return list.slice().sort((a, b) => {
                const va = a[col] ?? '', vb = b[col] ?? '';
                if (typeof va === 'string') return dir === 'asc' ? va.localeCompare(vb) : vb.localeCompare(va);
                return dir === 'asc' ? va - vb : vb - va;
            });
        },
        toggleSort(col) {
            if (this.sortCol === col) { this.sortDir = this.sortDir === 'desc' ? 'asc' : 'desc'; }
            else { this.sortCol = col; this.sortDir = 'desc'; }
        },
        sortIcon(col) { return this.sortCol === col ? (this.sortDir === 'desc' ? ' ↓' : ' ↑') : ''; }
    }">
        <div class="bg-[var(--dash-card-bg)] rounded-lg border border-[#cfc6af] overflow-hidden">
            <div class="p-5 border-b border-[#cfc6af] flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[var(--dash-card-bg)]">
                <h3 class="text-sm font-bold text-[#1E293B] uppercase tracking-wide">Data Terperinci per Cabang</h3>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" x-model="search"
                               placeholder="Cari cabang..."
                               class="text-xs pl-8 pr-3 py-2 border border-[#cfc6af] rounded w-48 focus:ring-1 focus:ring-[#dbb940] focus:border-[#dbb940] text-[#1a1c1c]">
                        <span class="material-symbols-outlined text-[#7e7663] text-[16px] absolute left-2.5 top-2">search</span>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#f3f4f3] text-[#4c4635] uppercase tracking-wider font-bold border-b border-[#cfc6af] text-[10px]">
                            <th class="py-3 px-5 cursor-pointer select-none hover:text-[#1E293B]" @click="toggleSort('name')">Cabang & Area <span x-text="sortIcon('name')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none hover:text-[#1E293B]" @click="toggleSort('this_month')">Member Bln Ini <span x-text="sortIcon('this_month')"></span></th>
                            <th class="py-3 px-5 text-left pl-10">Demografi Terbanyak</th>
                            <th class="py-3 px-5">Program Terbanyak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e2e2e2]">
                        <template x-for="(b, i) in filteredBranches" :key="b.id">
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
                                <td class="py-3 px-5 text-left pl-10">
                                    <template x-if="b.demographics && b.demographics.length > 0">
                                        <div>
                                            <span class="font-bold text-[#dbb940]" x-text="b.demographics[0].name"></span>
                                            <span class="text-[#7e7663] font-mono ml-1" x-text="'(' + b.demographics[0].count + ' institusi)'"></span>
                                        </div>
                                    </template>
                                    <template x-if="!b.demographics || b.demographics.length === 0">
                                        <span class="text-[color:var(--dash-text-faint)] italic">Belum ada data</span>
                                    </template>
                                </td>
                                <td class="py-3 px-5">
                                    <template x-if="b.programs && b.programs.length > 0">
                                        <div>
                                            <span class="font-bold text-[#00677d]" x-text="b.programs[0].name"></span>
                                            <span class="text-[#7e7663] font-mono ml-1" x-text="'(' + b.programs[0].count + ' member)'"></span>
                                        </div>
                                    </template>
                                    <template x-if="!b.programs || b.programs.length === 0">
                                        <span class="text-[color:var(--dash-text-faint)] italic">Belum ada data</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredBranches.length === 0">
                            <tr><td colspan="4" class="py-8 text-center text-[#7e7663] text-xs">Tidak ada data cabang ditemukan</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 border-t border-[#cfc6af] bg-[#f9f9f8] flex items-center justify-between text-[10px] font-bold text-[#7e7663] uppercase tracking-wide">
                <span>Total <span x-text="filteredBranches.length" class="text-[#1E293B]"></span> Cabang</span>
                <span>Periode: {{ $data['period'] ?? '-' }}</span>
            </div>
        </div>
    </div>
</div>
