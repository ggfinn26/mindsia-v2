<div class="bg-white rounded-lg shadow-sm overflow-hidden font-sans border border-[#cfc6af] w-full" x-data="{
    branchA: '{{ $data['branch_a']['id'] ?? '' }}',
    branchB: '{{ $data['branch_b']['id'] ?? '' }}',
    monthStart: '{{ $data['filters']['month_start'] }}',
    monthEnd: '{{ $data['filters']['month_end'] }}',
    year: '{{ $data['filters']['year'] }}'
}">
    {{-- Header --}}
    <div class="bg-[#f3f4f3] px-5 py-4 border-b border-[#cfc6af] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold text-[#1E293B] uppercase tracking-wide">Komparasi Cabang</h3>
            <p class="text-xs text-gray-500 mt-0.5">Bandingkan performa antar cabang • {{ $data['period_label'] }}</p>
        </div>
        
        {{-- Export / Actions --}}
        <div class="flex items-center gap-2">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="p-1.5 rounded bg-white shadow-sm border border-[#cfc6af] hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-outlined text-gray-600 text-sm">download</span>
                </button>
                <div x-show="open" @click.away="open = false" 
                     class="absolute right-0 mt-1 w-32 bg-white rounded shadow-lg border border-[#cfc6af] py-1 z-10"
                     style="display: none;">
                     <a href="{{ route('dashboard.widget-export', ['widgetKey' => 'comparable', 'format' => 'pdf', 'comparable_branch_a' => $data['branch_a']['id'] ?? '', 'comparable_branch_b' => $data['branch_b']['id'] ?? '', 'comparable_month_start' => $data['filters']['month_start'], 'comparable_month_end' => $data['filters']['month_end'], 'comparable_year' => $data['filters']['year']]) }}"
                        class="block px-3 py-1.5 text-xs text-[#1E293B] hover:bg-gray-50 font-medium">Export PDF</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="p-4 border-b border-[#cfc6af] bg-gray-50/50">
        <form class="flex flex-wrap items-end gap-3 text-xs" x-on:change="
            const url = new URL(window.location.href);
            url.searchParams.set('comparable_branch_a', branchA);
            url.searchParams.set('comparable_branch_b', branchB);
            url.searchParams.set('comparable_month_start', monthStart);
            url.searchParams.set('comparable_month_end', monthEnd);
            url.searchParams.set('comparable_year', year);
            window.location.href = url.toString();
        " onsubmit="event.preventDefault()">
            <div class="flex flex-col gap-1">
                <label class="font-medium text-gray-500">Cabang A</label>
                <select x-model="branchA" class="rounded border-[#cfc6af] py-1 pl-2 pr-6 text-xs focus:ring-[#1E293B] focus:border-[#1E293B]">
                    @foreach($data['branches'] as $b)
                        <option value="{{ $b['id'] }}">{{ $b['branch_name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="font-medium text-gray-500">Cabang B</label>
                <select x-model="branchB" class="rounded border-[#cfc6af] py-1 pl-2 pr-6 text-xs focus:ring-[#1E293B] focus:border-[#1E293B]">
                    @foreach($data['branches'] as $b)
                        <option value="{{ $b['id'] }}">{{ $b['branch_name'] }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-px h-8 bg-gray-200 mx-1 hidden sm:block"></div>
            
            <div class="flex flex-col gap-1">
                <label class="font-medium text-gray-500">Bulan Mulai</label>
                <select x-model="monthStart" class="rounded border-[#cfc6af] py-1 pl-2 pr-6 text-xs focus:ring-[#1E293B] focus:border-[#1E293B]">
                    @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'] as $m => $name)
                        <option value="{{ $m }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="font-medium text-gray-500">Sampai</label>
                <select x-model="monthEnd" class="rounded border-[#cfc6af] py-1 pl-2 pr-6 text-xs focus:ring-[#1E293B] focus:border-[#1E293B]">
                    @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'] as $m => $name)
                        <option value="{{ $m }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="font-medium text-gray-500">Tahun</label>
                <select x-model="year" class="rounded border-[#cfc6af] py-1 pl-2 pr-6 text-xs focus:ring-[#1E293B] focus:border-[#1E293B]">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- Content --}}
    <div class="p-5 grid grid-cols-2 gap-4">
        {{-- Cabang A --}}
        <div class="border-r border-[#cfc6af] pr-4">
            <h4 class="text-sm font-bold text-[#1E293B] mb-4 truncate">{{ $data['branch_a']['branch_name'] ?? 'Pilih Cabang A' }}</h4>
            
            <div class="space-y-4">
                <div>
                    <p class="text-[10px] uppercase tracking-wide text-gray-500 font-bold mb-1">Total Member</p>
                    <p class="text-lg font-mono font-bold text-[#1E293B]">{{ number_format($data['data_a']['members']) }}</p>
                </div>
                
                <div>
                    <p class="text-[10px] uppercase tracking-wide text-gray-500 font-bold mb-1">Total Revenue</p>
                    <p class="text-lg font-mono font-bold text-[#00677d]">Rp {{ number_format($data['data_a']['revenue'] / 1000000, 2) }}M</p>
                </div>
                
                <div>
                    <p class="text-[10px] uppercase tracking-wide text-gray-500 font-bold mb-2">Program Teratas</p>
                    @if(empty($data['data_a']['programs']))
                        <p class="text-xs text-gray-400 italic">Belum ada data program.</p>
                    @else
                        <ul class="space-y-1.5">
                            @foreach($data['data_a']['programs'] as $prog)
                            <li class="flex items-center justify-between text-xs">
                                <span class="text-[#1E293B] truncate pr-2">{{ $prog->name }}</span>
                                <span class="font-mono text-gray-600 shrink-0">{{ $prog->cnt }}</span>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Cabang B --}}
        <div class="pl-2">
            <h4 class="text-sm font-bold text-[#1E293B] mb-4 truncate">{{ $data['branch_b']['branch_name'] ?? 'Pilih Cabang B' }}</h4>
            
            <div class="space-y-4">
                <div>
                    <p class="text-[10px] uppercase tracking-wide text-gray-500 font-bold mb-1">Total Member</p>
                    <p class="text-lg font-mono font-bold text-[#1E293B]">{{ number_format($data['data_b']['members']) }}</p>
                </div>
                
                <div>
                    <p class="text-[10px] uppercase tracking-wide text-gray-500 font-bold mb-1">Total Revenue</p>
                    <p class="text-lg font-mono font-bold text-[#00677d]">Rp {{ number_format($data['data_b']['revenue'] / 1000000, 2) }}M</p>
                </div>
                
                <div>
                    <p class="text-[10px] uppercase tracking-wide text-gray-500 font-bold mb-2">Program Teratas</p>
                    @if(empty($data['data_b']['programs']))
                        <p class="text-xs text-gray-400 italic">Belum ada data program.</p>
                    @else
                        <ul class="space-y-1.5">
                            @foreach($data['data_b']['programs'] as $prog)
                            <li class="flex items-center justify-between text-xs">
                                <span class="text-[#1E293B] truncate pr-2">{{ $prog->name }}</span>
                                <span class="font-mono text-gray-600 shrink-0">{{ $prog->cnt }}</span>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
