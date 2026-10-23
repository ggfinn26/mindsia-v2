<div class="rounded-2xl shadow-sm overflow-hidden font-sans w-full"
     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
     x-data="{
    activeTab: 'nasional',
    scope: '{{ $data['filters']['branch_id'] ? 'cabang' : ($data['filters']['area_id'] ? 'area' : 'nasional') }}',
    areaId: '{{ $data['filters']['area_id'] ?? '' }}',
    branchId: '{{ $data['filters']['branch_id'] ?? '' }}',
    month: '{{ $data['filters']['month'] ?? now()->month }}',
    year: '{{ $data['filters']['year'] ?? now()->year }}',
    search: '',
    cabangSortCol: 'omzet', cabangSortDir: 'desc',
    areaSortCol: 'omzet', areaSortDir: 'desc',
    empSortCol: 'omzet', empSortDir: 'desc',
    doSort(list, col, dir) {
        return list.slice().sort((a, b) => {
            const va = a[col] ?? '', vb = b[col] ?? '';
            if (typeof va === 'string') return dir === 'asc' ? va.localeCompare(vb) : vb.localeCompare(va);
            return dir === 'asc' ? va - vb : vb - va;
        });
    },
    toggleSort(tab, col) {
        const c = tab + 'SortCol', d = tab + 'SortDir';
        if (this[c] === col) { this[d] = this[d] === 'desc' ? 'asc' : 'desc'; }
        else { this[c] = col; this[d] = 'desc'; }
    },
    sortIcon(tab, col) {
        const c = tab + 'SortCol', d = tab + 'SortDir';
        return this[c] === col ? (this[d] === 'desc' ? ' ↓' : ' ↑') : '';
    },
    get filteredBranches() {
        const raw = {{ json_encode($data['by_branch']) }};
        const list = this.search === '' ? raw : raw.filter(b => b.name.toLowerCase().includes(this.search.toLowerCase()) || b.area_name.toLowerCase().includes(this.search.toLowerCase()));
        return this.doSort(list, this.cabangSortCol, this.cabangSortDir);
    },
    get filteredAreas() {
        return this.doSort({{ json_encode($data['by_area']) }}, this.areaSortCol, this.areaSortDir);
    },
    get filteredEmployees() {
        const raw = {{ json_encode($data['by_employee']) }};
        const list = this.search === '' ? raw : raw.filter(e => e.name.toLowerCase().includes(this.search.toLowerCase()) || e.branch_name.toLowerCase().includes(this.search.toLowerCase()));
        return this.doSort(list, this.empSortCol, this.empSortDir);
    },
    applyFilter() {
        if (this.scope === 'area' && !this.areaId) return;
        if (this.scope === 'cabang' && !this.branchId) return;
        const url = new URL(window.location.href);
        url.searchParams.set('marketing_progress_scope', this.scope);
        url.searchParams.set('marketing_progress_area_id', this.scope === 'area' ? this.areaId : '');
        url.searchParams.set('marketing_progress_branch_id', this.scope === 'cabang' ? this.branchId : '');
        url.searchParams.set('marketing_progress_month', this.month);
        url.searchParams.set('marketing_progress_year', this.year);
        window.location.href = url.toString();
    }
}">
    {{-- Header --}}
    <div class="px-5 py-0 flex flex-col md:flex-row md:items-center justify-between gap-4"
         style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
        <div class="py-4">
            <h3 class="text-sm font-bold uppercase tracking-wide font-jakarta" style="color: var(--dash-text-heading);">Progress Marketing</h3>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Omzet, Sosialisasi, Member & Prospek • {{ $data['period'] }}</p>
        </div>

        <div class="flex items-center gap-3 py-4 md:py-0 h-full flex-wrap">
            {{-- Tabs --}}
            <nav class="flex h-full space-x-1" aria-label="Tabs">
                <button @click="activeTab = 'cabang'"
                        :style="activeTab === 'cabang' ? 'background-color: var(--dash-card-bg); color: var(--dash-text-heading); border-color: var(--dash-card-border);' : 'color: var(--dash-text-muted);'"
                        class="px-4 py-3 text-xs font-semibold rounded-t-lg transition-colors h-full flex items-center border-t border-x -mb-[1px]">
                    Cabang
                </button>
                <button @click="activeTab = 'area'"
                        :style="activeTab === 'area' ? 'background-color: var(--dash-card-bg); color: var(--dash-text-heading); border-color: var(--dash-card-border);' : 'color: var(--dash-text-muted);'"
                        class="px-4 py-3 text-xs font-semibold rounded-t-lg transition-colors h-full flex items-center border-t border-x -mb-[1px]">
                    Area
                </button>
                <button @click="activeTab = 'nasional'"
                        :style="activeTab === 'nasional' ? 'background-color: var(--dash-card-bg); color: var(--dash-text-heading); border-color: var(--dash-card-border);' : 'color: var(--dash-text-muted);'"
                        class="px-4 py-3 text-xs font-semibold rounded-t-lg transition-colors h-full flex items-center border-t border-x -mb-[1px]">
                    Nasional
                </button>
            </nav>

            {{-- Filters --}}
            <div class="flex items-center gap-2 text-xs ml-2 flex-wrap">
                <select x-model="scope" @change="if(scope === 'nasional') applyFilter()"
                        class="rounded-lg py-1.5 pl-2 pr-6 text-xs focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="nasional">Nasional</option>
                    <option value="area">Per Area</option>
                    <option value="cabang">Per Cabang</option>
                </select>
                <template x-if="scope === 'area'">
                    <select x-model="areaId" @change="if(areaId) applyFilter()"
                            class="rounded-lg py-1.5 pl-2 pr-6 text-xs focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Area</option>
                        @foreach($data['areas'] as $area)
                            <option value="{{ $area['id'] }}">{{ $area['name'] }}</option>
                        @endforeach
                    </select>
                </template>
                <template x-if="scope === 'cabang'">
                    <select x-model="branchId" @change="if(branchId) applyFilter()"
                            class="rounded-lg py-1.5 pl-2 pr-6 text-xs focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Cabang</option>
                        @foreach($data['branches'] as $branch)
                            <option value="{{ $branch['id'] }}">{{ $branch['branch_name'] }}</option>
                        @endforeach
                    </select>
                </template>
                <select x-model="month" @change="applyFilter()"
                        class="rounded-lg py-1.5 pl-2 pr-6 text-xs focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'] as $m => $name)
                        <option value="{{ $m }}">{{ $name }}</option>
                    @endforeach
                </select>
                <select x-model="year" @change="applyFilter()"
                        class="rounded-lg py-1.5 pl-2 pr-6 text-xs focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Download modal --}}
            <div x-data="{ open: false, dlType: 'bulanan', dlYear: year, dlSemester: '1', dlMonth: month }">
                <button @click="open = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                        style="color: var(--dash-text-body); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[14px]">download</span>
                    <span>Unduh Laporan</span>
                </button>

                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" style="display:none;" @keydown.escape.window="open = false">
                    <div @click.away="open = false" class="rounded-2xl shadow-2xl w-full max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                        <div class="px-5 py-4 flex justify-between items-center rounded-t-2xl"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Unduh Laporan PDF</h3>
                            <button @click="open = false" style="color: var(--dash-text-muted);">
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

                            <form :action="'{{ route('dashboard.widget-export', ['widgetKey' => 'marketing_progress', 'format' => 'pdf']) }}'" method="GET">
                                <input type="hidden" name="period_type" :value="dlType === 'tahunan' ? 'yearly' : (dlType === 'semester' ? 'semester' : 'monthly')">
                                <input type="hidden" name="scope" :value="scope">
                                <input type="hidden" name="area_id" :value="areaId">
                                <input type="hidden" name="branch_id" :value="branchId">

                                <template x-if="dlType === 'tahunan'">
                                    <div>
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
                                </template>
                                <template x-if="dlType === 'bulanan'">
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
                                </template>

                                <div class="mt-6 flex justify-end gap-2">
                                    <button type="button" @click="open = false" class="px-4 py-2 text-xs font-semibold rounded-lg transition-colors"
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

    {{-- National KPI Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-0"
         style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);">
        <div class="p-4" style="border-right: 1px solid var(--dash-card-border);">
            <p class="text-[10px] uppercase tracking-wide font-bold mb-1" style="color: var(--dash-text-muted);">Total Omzet</p>
            <p class="text-lg font-mono font-bold" style="color: var(--dash-primary-text);">Rp {{ number_format($data['national']['omzet'] / 1000000, 2) }} Juta</p>
        </div>
        <div class="p-4 sm:border-r" style="border-color: var(--dash-card-border);">
            <p class="text-[10px] uppercase tracking-wide font-bold mb-1" style="color: var(--dash-text-muted);">Sosialisasi</p>
            <p class="text-lg font-mono font-bold" style="color: var(--dash-text-heading);">{{ number_format($data['national']['sosialisasi']) }}</p>
        </div>
        <div class="p-4 border-t sm:border-t-0" style="border-right: 1px solid var(--dash-card-border); border-color: var(--dash-card-border);">
            <p class="text-[10px] uppercase tracking-wide font-bold mb-1" style="color: var(--dash-text-muted);">Member Baru</p>
            <p class="text-lg font-mono font-bold" style="color: var(--dash-text-heading);">{{ number_format($data['national']['member']) }}</p>
        </div>
        <div class="p-4 border-t sm:border-t-0" style="border-color: var(--dash-card-border);">
            <p class="text-[10px] uppercase tracking-wide font-bold mb-1" style="color: var(--dash-text-muted);">Prospek</p>
            <p class="text-lg font-mono font-bold" style="color: var(--dash-text-heading);">{{ number_format($data['national']['prospective']) }}</p>
        </div>
    </div>

    {{-- Tab: Per Cabang --}}
    <div x-show="activeTab === 'cabang'" class="p-5" style="background-color: var(--dash-page-bg); display:none;">
        <div class="rounded-2xl overflow-hidden shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                 style="background-color: var(--dash-card-bg); border-bottom: 1px solid var(--dash-card-border);">
                <h3 class="text-sm font-bold uppercase tracking-wide font-jakarta" style="color: var(--dash-text-heading);">Rincian per Cabang</h3>
                <div class="relative">
                    <input type="text" x-model="search" placeholder="Cari cabang atau area..."
                           class="text-xs pl-8 pr-3 py-2 rounded-lg w-56 focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    <span class="material-symbols-outlined text-[16px] absolute left-2.5 top-2.5 pointer-events-none" style="color: var(--dash-text-muted);">search</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="uppercase tracking-wider font-bold text-[10px]"
                            style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                            <th class="py-3 px-5 cursor-pointer select-none" @click="toggleSort('cabang','name')">Cabang & Area <span x-text="sortIcon('cabang','name')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('cabang','omzet')">Omzet <span x-text="sortIcon('cabang','omzet')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('cabang','sosialisasi')">Sosialisasi <span x-text="sortIcon('cabang','sosialisasi')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('cabang','member')">Member Baru <span x-text="sortIcon('cabang','member')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('cabang','prospective')">Prospek <span x-text="sortIcon('cabang','prospective')"></span></th>
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
                                        <span class="font-mono font-bold text-[10px]" style="color: var(--dash-text-muted);" x-text="String(i+1).padStart(2,'0')"></span>
                                        <div>
                                            <span class="font-bold block" style="color: var(--dash-text-heading);" x-text="b.name"></span>
                                            <span class="text-[10px]" style="color: var(--dash-text-muted);" x-text="b.area_name"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-5 text-right font-mono font-bold" style="color: var(--dash-primary-text);" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(b.omzet / 1000000) + ' Juta'"></td>
                                <td class="py-3 px-5 text-right font-mono font-bold" style="color: var(--dash-text-heading);" x-text="new Intl.NumberFormat('id-ID').format(b.sosialisasi)"></td>
                                <td class="py-3 px-5 text-right font-mono font-bold" style="color: var(--dash-text-heading);" x-text="new Intl.NumberFormat('id-ID').format(b.member)"></td>
                                <td class="py-3 px-5 text-right font-mono font-bold" style="color: var(--dash-text-heading);" x-text="new Intl.NumberFormat('id-ID').format(b.prospective)"></td>
                            </tr>
                        </template>
                        <template x-if="filteredBranches.length === 0">
                            <tr><td colspan="5" class="py-8 text-center text-xs" style="color: var(--dash-text-muted);">Tidak ada cabang ditemukan</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="p-3 flex items-center justify-between text-[10px] font-bold uppercase tracking-wide"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                <span>Total <span x-text="filteredBranches.length" style="color: var(--dash-text-heading);"></span> Cabang</span>
                <span>Periode: {{ $data['period'] }}</span>
            </div>
        </div>
    </div>

    {{-- Tab: Per Area --}}
    <div x-show="activeTab === 'area'" class="p-5" style="background-color: var(--dash-page-bg); display:none;">
        <div class="rounded-2xl overflow-hidden shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="uppercase tracking-wider font-bold text-[10px]"
                            style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                            <th class="py-3 px-5 cursor-pointer select-none" @click="toggleSort('area','name')">Area <span x-text="sortIcon('area','name')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('area','omzet')">Omzet <span x-text="sortIcon('area','omzet')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('area','sosialisasi')">Sosialisasi <span x-text="sortIcon('area','sosialisasi')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('area','member')">Member Baru <span x-text="sortIcon('area','member')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('area','prospective')">Prospek <span x-text="sortIcon('area','prospective')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('area','branch_count')">Cabang <span x-text="sortIcon('area','branch_count')"></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(area, i) in filteredAreas" :key="area.name">
                            <tr class="transition-colors"
                                style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="py-3 px-5">
                                    <div class="flex items-center gap-3">
                                        <span class="font-mono font-bold text-[10px]" style="color: var(--dash-text-muted);" x-text="String(i+1).padStart(2,'0')"></span>
                                        <span class="font-bold" style="color: var(--dash-text-heading);" x-text="area.name"></span>
                                    </div>
                                </td>
                                <td class="py-3 px-5 text-right font-mono font-bold" style="color: var(--dash-primary-text);" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(area.omzet / 1000000) + ' Juta'"></td>
                                <td class="py-3 px-5 text-right font-mono font-bold" style="color: var(--dash-text-heading);" x-text="new Intl.NumberFormat('id-ID').format(area.sosialisasi)"></td>
                                <td class="py-3 px-5 text-right font-mono font-bold" style="color: var(--dash-text-heading);" x-text="new Intl.NumberFormat('id-ID').format(area.member)"></td>
                                <td class="py-3 px-5 text-right font-mono font-bold" style="color: var(--dash-text-heading);" x-text="new Intl.NumberFormat('id-ID').format(area.prospective)"></td>
                                <td class="py-3 px-5 text-right" style="color: var(--dash-text-muted);" x-text="area.branch_count"></td>
                            </tr>
                        </template>
                        <template x-if="filteredAreas.length === 0">
                            <tr><td colspan="6" class="py-8 text-center text-xs" style="color: var(--dash-text-muted);">Belum ada data untuk periode ini.</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="p-3 text-[10px] font-bold uppercase tracking-wide"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                <span x-text="filteredAreas.length"></span> Area • Periode: {{ $data['period'] }}
            </div>
        </div>
    </div>

    {{-- Tab: Nasional (Employee Ranking) --}}
    <div x-show="activeTab === 'nasional'" class="p-5" style="background-color: var(--dash-page-bg);">
        <div class="rounded-2xl overflow-hidden shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                 style="background-color: var(--dash-card-bg); border-bottom: 1px solid var(--dash-card-border);">
                <h3 class="text-sm font-bold uppercase tracking-wide font-jakarta" style="color: var(--dash-text-heading);">Ranking Marketing</h3>
                <div class="relative">
                    <input type="text" x-model="search" placeholder="Cari nama atau cabang..."
                           class="text-xs pl-8 pr-3 py-2 rounded-lg w-56 focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    <span class="material-symbols-outlined text-[16px] absolute left-2.5 top-2.5 pointer-events-none" style="color: var(--dash-text-muted);">search</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="uppercase tracking-wider font-bold text-[10px]"
                            style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                            <th class="py-3 px-5">#</th>
                            <th class="py-3 px-5 cursor-pointer select-none" @click="toggleSort('emp','name')">Nama Marketing <span x-text="sortIcon('emp','name')"></span></th>
                            <th class="py-3 px-5 cursor-pointer select-none" @click="toggleSort('emp','branch_name')">Cabang <span x-text="sortIcon('emp','branch_name')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('emp','kelas')">Kelas <span x-text="sortIcon('emp','kelas')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('emp','omzet')">Omzet <span x-text="sortIcon('emp','omzet')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('emp','member')">Member <span x-text="sortIcon('emp','member')"></span></th>
                            <th class="py-3 px-5 text-right cursor-pointer select-none" @click="toggleSort('emp','prospective')">Prospek <span x-text="sortIcon('emp','prospective')"></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(e, i) in filteredEmployees" :key="e.id">
                            <tr class="transition-colors"
                                style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="py-3 px-5 font-mono font-bold text-[10px]" style="color: var(--dash-text-muted);" x-text="String(i+1).padStart(2,'0')"></td>
                                <td class="py-3 px-5 font-bold" style="color: var(--dash-text-heading);" x-text="e.name"></td>
                                <td class="py-3 px-5" style="color: var(--dash-text-muted);" x-text="e.branch_name"></td>
                                <td class="py-3 px-5 text-right font-mono" style="color: var(--dash-text-heading);">
                                    <span class="font-bold" x-text="e.kelas"></span><span style="color: var(--dash-text-muted);" x-text="'/' + e.kelas_target"></span>
                                </td>
                                <td class="py-3 px-5 text-right font-mono" style="color: var(--dash-primary-text);">
                                    <span class="font-bold" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(e.omzet / 1000000) + ' Juta'"></span><br>
                                    <span class="text-[10px]" style="color: var(--dash-text-muted);" x-text="'Target: Rp ' + new Intl.NumberFormat('id-ID').format(e.omzet_target / 1000000) + ' Juta'"></span>
                                </td>
                                <td class="py-3 px-5 text-right font-mono font-bold" style="color: var(--dash-text-heading);" x-text="new Intl.NumberFormat('id-ID').format(e.member)"></td>
                                <td class="py-3 px-5 text-right font-mono font-bold" style="color: var(--dash-text-heading);" x-text="new Intl.NumberFormat('id-ID').format(e.prospective)"></td>
                            </tr>
                        </template>
                        <template x-if="filteredEmployees.length === 0">
                            <tr><td colspan="7" class="py-8 text-center text-xs" style="color: var(--dash-text-muted);">Belum ada data marketing untuk periode ini.</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="p-3 flex items-center justify-between text-[10px] font-bold uppercase tracking-wide"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                <span>Total <span x-text="filteredEmployees.length" style="color: var(--dash-text-heading);"></span> Marketing</span>
                <span>Periode: {{ $data['period'] }}</span>
            </div>
        </div>
    </div>
</div>
