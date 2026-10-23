<div class="rounded-2xl shadow-sm overflow-hidden font-sans w-full"
     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
     x-data="{
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
    <div class="px-5 py-0 flex flex-col md:flex-row md:items-center justify-between gap-4"
         style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
        <div class="py-4">
            <h3 class="text-sm font-bold uppercase tracking-wide font-jakarta" style="color: var(--dash-text-heading);">Analisis Segmentasi & Demografi</h3>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Cabang, Area & Nasional • {{ $data['period'] }}</p>
        </div>
        
        <div class="flex items-center gap-3 py-4 md:py-0 h-full">
            <nav class="flex h-full space-x-1" aria-label="Tabs">
                <button @click="activeTab = 'segmentasi'"
                        :style="activeTab === 'segmentasi' ? 'background-color: var(--dash-card-bg); color: var(--dash-text-heading); border-color: var(--dash-card-border);' : 'color: var(--dash-text-muted);'"
                        class="px-4 py-3 text-xs font-semibold rounded-t-lg transition-colors h-full flex items-center border-t border-x -mb-[1px]">
                    Peta Segmentasi
                </button>
                <button @click="activeTab = 'table'"
                        :style="activeTab === 'table' ? 'background-color: var(--dash-card-bg); color: var(--dash-text-heading); border-color: var(--dash-card-border);' : 'color: var(--dash-text-muted);'"
                        class="px-4 py-3 text-xs font-semibold rounded-t-lg transition-colors h-full flex items-center border-t border-x -mb-[1px]">
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
                <select x-model="month" class="rounded-lg py-1.5 pl-2 pr-6 text-xs focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'] as $m => $name)
                        <option value="{{ $m }}">{{ $name }}</option>
                    @endforeach
                </select>
                <select x-model="year" class="rounded-lg py-1.5 pl-2 pr-6 text-xs focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </form>
            <div x-data="{ downloadModalOpen: false, dlType: 'tahunan', dlYear: year, dlSemester: '1', dlMonth: month }">
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
                            <button @click="downloadModalOpen = false" style="color: var(--dash-text-muted);">
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

                            <form action="{{ route('dashboard.widget-export', ['widgetKey' => 'member_growth', 'format' => 'pdf']) }}" method="GET">
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
            </div>
        </div>
    </div>

    {{-- Tab 1: Peta Segmentasi (Sidebar Programs & Main Content) --}}
    <div x-show="activeTab === 'segmentasi'" class="flex flex-col md:flex-row h-auto md:h-[600px]">
        {{-- Sidebar: List of Programs --}}
        <div class="w-full md:w-1/3 lg:w-1/4 flex flex-col h-full"
             style="border-right: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="p-3" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);">
                <p class="text-[10px] font-bold uppercase tracking-wide" style="color: var(--dash-text-muted);">Pilih Program</p>
            </div>
            <div class="overflow-y-auto flex-1 p-2 space-y-1">
                <template x-for="prog in programs" :key="prog.program">
                    <button @click="activeProgram = prog.program" 
                            class="w-full text-left p-3 rounded-lg border transition-colors flex flex-col gap-1"
                            :style="activeProgram === prog.program
                                ? 'background-color: var(--dash-card-bg); border: 1px solid var(--dash-primary); box-shadow: 0 1px 2px rgba(0,0,0,0.05);'
                                : 'border: 1px solid transparent; background-color: transparent;'">
                        <span class="text-xs font-bold truncate" style="color: var(--dash-text-heading);" x-text="prog.program"></span>
                        <div class="flex justify-between items-center text-[10px]">
                            <span class="font-mono font-bold" style="color: var(--dash-text-body);" x-text="new Intl.NumberFormat('id-ID').format(prog.member_count) + ' Member'"></span>
                            <span class="font-mono" style="color: var(--dash-primary-text);" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(prog.revenue/1000000) + 'M'"></span>
                        </div>
                    </button>
                </template>
                <template x-if="programs.length === 0">
                    <p class="text-xs text-center py-4" style="color: var(--dash-text-muted);">Belum ada data program aktif.</p>
                </template>
            </div>
        </div>

        {{-- Main Content: Segmentation Details --}}
        <div class="w-full md:w-2/3 lg:w-3/4 overflow-y-auto p-5" style="background-color: var(--dash-card-bg);">
            <template x-if="activeData">
                <div>
                    {{-- Title Row --}}
                    <div class="mb-6 flex justify-between items-end pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
                        <div>
                            <h4 class="text-xl font-bold font-jakarta" style="color: var(--dash-text-heading);" x-text="activeData.program"></h4>
                            <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Peta Segmentasi & Demografi</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase font-bold" style="color: var(--dash-text-muted);">Total Member / Revenue</p>
                            <p class="text-lg font-mono font-bold" style="color: var(--dash-text-heading);">
                                <span x-text="new Intl.NumberFormat('id-ID').format(activeData.member_count)"></span>
                                <span style="color: var(--dash-primary-text);" x-text="' / Rp' + new Intl.NumberFormat('id-ID').format(activeData.revenue/1000000) + ' Juta'"></span>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        {{-- Skema Pembayaran --}}
                        <div class="rounded-2xl p-4" style="border: 1px solid var(--dash-card-border); background-color: var(--dash-page-bg);">
                            <h5 class="text-[10px] font-bold uppercase tracking-wide pb-2 mb-3"
                                style="border-bottom: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">Skema Pembayaran</h5>
                            <ul class="space-y-2">
                                <template x-for="pay in activeData.payments" :key="pay.payment_status + pay.installment_type">
                                    <li class="flex justify-between items-center text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full" :class="pay.payment_status === 'paid_full' ? 'bg-[#059669]' : (pay.payment_status === 'installments' ? 'bg-[#d97706]' : 'bg-[#dc2626]')"></span>
                                            <span class="font-medium" style="color: var(--dash-text-body);" x-text="
                                                pay.payment_status === 'paid_full' ? 'Lunas' : 
                                                (pay.payment_status === 'installments' ? 'Cicilan (' + pay.installment_type + 'x)' : 'Belum Lunas')
                                            "></span>
                                        </div>
                                        <span class="font-mono font-bold" style="color: var(--dash-text-heading);" x-text="pay.cnt"></span>
                                    </li>
                                </template>
                                <template x-if="activeData.payments.length === 0">
                                    <li class="text-xs italic" style="color: var(--dash-text-muted);">Tidak ada data pembayaran.</li>
                                </template>
                            </ul>
                        </div>

                        {{-- Demografi Institusi --}}
                        <div class="rounded-2xl p-4" style="border: 1px solid var(--dash-card-border); background-color: var(--dash-page-bg);">
                            <h5 class="text-[10px] font-bold uppercase tracking-wide pb-2 mb-3"
                                style="border-bottom: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">Sebaran Demografi Institusi</h5>
                            <ul class="space-y-2">
                                <template x-for="dem in activeData.demographics" :key="dem.jenjang_institution">
                                    <li class="flex justify-between items-center text-xs">
                                        <span class="font-medium" style="color: var(--dash-text-body);" x-text="dem.jenjang_institution || 'TIDAK DIKETAHUI'"></span>
                                        <span class="font-mono font-bold" style="color: var(--dash-text-heading);" x-text="dem.cnt"></span>
                                    </li>
                                </template>
                                <template x-if="activeData.demographics.length === 0">
                                    <li class="text-xs italic" style="color: var(--dash-text-muted);">Tidak ada data institusi.</li>
                                </template>
                            </ul>
                        </div>

                        {{-- Top Cabang --}}
                        <div class="rounded-2xl p-4" style="border: 1px solid var(--dash-card-border); background-color: var(--dash-page-bg);">
                            <h5 class="text-[10px] font-bold uppercase tracking-wide pb-2 mb-3"
                                style="border-bottom: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">Top 5 Cabang</h5>
                            <ul class="space-y-2">
                                <template x-for="(b, idx) in topBranches" :key="b.id">
                                    <li class="flex justify-between items-center text-xs">
                                        <div class="flex gap-2">
                                            <span class="font-mono" style="color: var(--dash-text-muted);" x-text="(idx+1)+'.'"></span>
                                            <div>
                                                <span class="font-bold block" style="color: var(--dash-text-heading);" x-text="b.name"></span>
                                                <span class="text-[9px]" style="color: var(--dash-text-muted);" x-text="b.area"></span>
                                            </div>
                                        </div>
                                        <span class="font-mono font-bold" style="color: var(--dash-text-heading);" x-text="b.count"></span>
                                    </li>
                                </template>
                                <template x-if="topBranches.length === 0">
                                    <li class="text-xs italic" style="color: var(--dash-text-muted);">Tidak ada data cabang.</li>
                                </template>
                            </ul>
                        </div>

                        {{-- Top Area --}}
                        <div class="rounded-2xl p-4" style="border: 1px solid var(--dash-card-border); background-color: var(--dash-page-bg);">
                            <h5 class="text-[10px] font-bold uppercase tracking-wide pb-2 mb-3"
                                style="border-bottom: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">Top 5 Area</h5>
                            <ul class="space-y-2">
                                <template x-for="(a, idx) in topAreas" :key="a.name">
                                    <li class="flex justify-between items-center text-xs">
                                        <div class="flex gap-2">
                                            <span class="font-mono" style="color: var(--dash-text-muted);" x-text="(idx+1)+'.'"></span>
                                            <span class="font-bold" style="color: var(--dash-text-heading);" x-text="a.name"></span>
                                        </div>
                                        <span class="font-mono font-bold" style="color: var(--dash-text-heading);" x-text="a.count"></span>
                                    </li>
                                </template>
                                <template x-if="topAreas.length === 0">
                                    <li class="text-xs italic" style="color: var(--dash-text-muted);">Tidak ada data area.</li>
                                </template>
                            </ul>
                        </div>

                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Tab 2: Detail Data (Table View) --}}
    <div x-show="activeTab === 'table'" class="p-6" style="background-color: var(--dash-page-bg); display:none;" x-data="{
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
        <div class="rounded-2xl overflow-hidden shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                 style="background-color: var(--dash-card-bg); border-bottom: 1px solid var(--dash-card-border);">
                <h3 class="text-sm font-bold uppercase tracking-wide font-jakarta" style="color: var(--dash-text-heading);">Data Terperinci per Cabang</h3>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" x-model="search"
                               placeholder="Cari cabang..."
                               class="text-xs pl-8 pr-3 py-2 rounded-lg w-48 focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                        <span class="material-symbols-outlined text-[16px] absolute left-2.5 top-2.5 pointer-events-none" style="color: var(--dash-text-muted);">search</span>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="uppercase tracking-wider font-bold text-[10px]"
                            style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                            <th class="py-3 px-5 cursor-pointer select-none" @click="toggleSort('name')">Cabang & Area <span x-text="sortIcon('name')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('this_month')">Member Bln Ini <span x-text="sortIcon('this_month')"></span></th>
                            <th class="py-3 px-5 text-left pl-10">Demografi Terbanyak</th>
                            <th class="py-3 px-5">Program Terbanyak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(b, i) in filteredBranches" :key="b.id">
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
                                    x-text="new Intl.NumberFormat('id-ID').format(b.this_month)"></td>
                                <td class="py-3 px-5 text-left pl-10">
                                    <template x-if="b.demographics && b.demographics.length > 0">
                                        <div>
                                            <span class="font-bold" style="color: var(--dash-primary-text);" x-text="b.demographics[0].name"></span>
                                            <span class="font-mono ml-1 text-[11px]" style="color: var(--dash-text-muted);" x-text="'(' + b.demographics[0].count + ' institusi)'"></span>
                                        </div>
                                    </template>
                                    <template x-if="!b.demographics || b.demographics.length === 0">
                                        <span class="italic" style="color: var(--dash-text-faint);">Belum ada data</span>
                                    </template>
                                </td>
                                <td class="py-3 px-5">
                                    <template x-if="b.programs && b.programs.length > 0">
                                        <div>
                                            <span class="font-bold" style="color: var(--dash-primary-text);" x-text="b.programs[0].name"></span>
                                            <span class="font-mono ml-1 text-[11px]" style="color: var(--dash-text-muted);" x-text="'(' + b.programs[0].count + ' member)'"></span>
                                        </div>
                                    </template>
                                    <template x-if="!b.programs || b.programs.length === 0">
                                        <span class="italic" style="color: var(--dash-text-faint);">Belum ada data</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredBranches.length === 0">
                            <tr><td colspan="4" class="py-8 text-center text-xs" style="color: var(--dash-text-muted);">Tidak ada data cabang ditemukan</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 flex items-center justify-between text-[10px] font-bold uppercase tracking-wide"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                <span>Total <span x-text="filteredBranches.length" style="color: var(--dash-text-heading);"></span> Cabang</span>
                <span>Periode: {{ $data['period'] ?? '-' }}</span>
            </div>
        </div>
    </div>
</div>
