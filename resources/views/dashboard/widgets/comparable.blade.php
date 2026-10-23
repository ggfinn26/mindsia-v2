<div class="rounded-2xl shadow-sm overflow-hidden font-sans w-full"
     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
     x-data="{
    branchA: '{{ $data['branch_a']['id'] ?? '' }}',
    branchB: '{{ $data['branch_b']['id'] ?? '' }}',
    programId: '{{ $data['filters']['program_id'] ?? '' }}',
    monthStart: '{{ $data['filters']['month_start'] }}',
    monthEnd: '{{ $data['filters']['month_end'] }}',
    year: '{{ $data['filters']['year'] }}'
}">
    {{-- Header --}}
    <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
         style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wide font-jakarta" style="color: var(--dash-text-heading);">Komparasi Cabang</h3>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                Bandingkan performa antar cabang • {{ $data['period_label'] }}
                @if(!empty($data['program']))
                    • <span class="font-bold" style="color: var(--dash-text-heading);">{{ $data['program']['program_name'] }}</span>
                @endif
            </p>
        </div>
        
        {{-- Export / Actions --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard.widget-export', ['widgetKey' => 'comparable', 'format' => 'pdf', 'comparable_branch_a' => $data['branch_a']['id'] ?? '', 'comparable_branch_b' => $data['branch_b']['id'] ?? '', 'comparable_program_id' => $data['filters']['program_id'] ?? '', 'comparable_month_start' => $data['filters']['month_start'], 'comparable_month_end' => $data['filters']['month_end'], 'comparable_year' => $data['filters']['year']]) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
               style="color: var(--dash-text-body); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[14px]">download</span>
                <span>Unduh Laporan</span>
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="p-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);">
        <form class="flex flex-wrap items-end gap-3 text-xs" x-on:change="
            const url = new URL(window.location.href);
            url.searchParams.set('comparable_branch_a', branchA);
            url.searchParams.set('comparable_branch_b', branchB);
            url.searchParams.set('comparable_program_id', programId);
            url.searchParams.set('comparable_month_start', monthStart);
            url.searchParams.set('comparable_month_end', monthEnd);
            url.searchParams.set('comparable_year', year);
            window.location.href = url.toString();
        " onsubmit="event.preventDefault()">
            <div class="flex flex-col gap-1">
                <label class="font-medium text-[11px] uppercase tracking-wide" style="color: var(--dash-text-muted);">Cabang A</label>
                <select x-model="branchA" class="rounded-lg py-1 pl-2 pr-6 text-xs focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    @foreach($data['branches'] as $b)
                        <option value="{{ $b['id'] }}">{{ $b['branch_name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="font-medium text-[11px] uppercase tracking-wide" style="color: var(--dash-text-muted);">Cabang B</label>
                <select x-model="branchB" class="rounded-lg py-1 pl-2 pr-6 text-xs focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    @foreach($data['branches'] as $b)
                        <option value="{{ $b['id'] }}">{{ $b['branch_name'] }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-px h-8 mx-1 hidden sm:block" style="background-color: var(--dash-card-border);"></div>

            <div class="flex flex-col gap-1">
                <label class="font-medium text-[11px] uppercase tracking-wide" style="color: var(--dash-text-muted);">Program</label>
                <select x-model="programId" class="rounded-lg py-1 pl-2 pr-6 text-xs focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Program</option>
                    @foreach($data['programs'] as $p)
                        <option value="{{ $p['id'] }}">{{ $p['program_name'] }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-px h-8 mx-1 hidden sm:block" style="background-color: var(--dash-card-border);"></div>
            
            <div class="flex flex-col gap-1">
                <label class="font-medium text-[11px] uppercase tracking-wide" style="color: var(--dash-text-muted);">Bulan Mulai</label>
                <select x-model="monthStart" class="rounded-lg py-1 pl-2 pr-6 text-xs focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'] as $m => $name)
                        <option value="{{ $m }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="font-medium text-[11px] uppercase tracking-wide" style="color: var(--dash-text-muted);">Sampai</label>
                <select x-model="monthEnd" class="rounded-lg py-1 pl-2 pr-6 text-xs focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
                    @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'] as $m => $name)
                        <option value="{{ $m }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="font-medium text-[11px] uppercase tracking-wide" style="color: var(--dash-text-muted);">Tahun</label>
                <select x-model="year" class="rounded-lg py-1 pl-2 pr-6 text-xs focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'">
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
        <div class="pr-4" style="border-right: 1px solid var(--dash-card-border);">
            <h4 class="text-sm font-bold mb-4 truncate" style="color: var(--dash-text-heading);">{{ $data['branch_a']['branch_name'] ?? 'Pilih Cabang A' }}</h4>
            
            <div class="space-y-4">
                <div>
                    <p class="text-[10px] uppercase tracking-wide font-bold mb-1" style="color: var(--dash-text-muted);">Total Member</p>
                    <p class="text-lg font-mono font-bold" style="color: var(--dash-text-heading);">{{ number_format($data['data_a']['members']) }}</p>
                </div>
                
                <div>
                    <p class="text-[10px] uppercase tracking-wide font-bold mb-1" style="color: var(--dash-text-muted);">Total Revenue</p>
                    <p class="text-lg font-mono font-bold" style="color: var(--dash-primary-text);">Rp {{ number_format($data['data_a']['revenue'] / 1000000, 2) }} Juta</p>
                </div>
                
                <div>
                    <p class="text-[10px] uppercase tracking-wide font-bold mb-2" style="color: var(--dash-text-muted);">Program Teratas</p>
                    @if(empty($data['data_a']['programs']))
                        <p class="text-xs italic" style="color: var(--dash-text-faint);">Belum ada data program.</p>
                    @else
                        <ul class="space-y-1.5">
                            @foreach($data['data_a']['programs'] as $prog)
                            <li class="flex items-center justify-between text-xs">
                                <span class="truncate pr-2" style="color: var(--dash-text-heading);">{{ data_get($prog, 'name') }}</span>
                                <span class="font-mono shrink-0" style="color: var(--dash-text-muted);">{{ data_get($prog, 'cnt') }}</span>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Cabang B --}}
        <div class="pl-2">
            <h4 class="text-sm font-bold mb-4 truncate" style="color: var(--dash-text-heading);">{{ $data['branch_b']['branch_name'] ?? 'Pilih Cabang B' }}</h4>
            
            <div class="space-y-4">
                <div>
                    <p class="text-[10px] uppercase tracking-wide font-bold mb-1" style="color: var(--dash-text-muted);">Total Member</p>
                    <p class="text-lg font-mono font-bold" style="color: var(--dash-text-heading);">{{ number_format($data['data_b']['members']) }}</p>
                </div>
                
                <div>
                    <p class="text-[10px] uppercase tracking-wide font-bold mb-1" style="color: var(--dash-text-muted);">Total Revenue</p>
                    <p class="text-lg font-mono font-bold" style="color: var(--dash-primary-text);">Rp {{ number_format($data['data_b']['revenue'] / 1000000, 2) }} Juta</p>
                </div>
                
                <div>
                    <p class="text-[10px] uppercase tracking-wide font-bold mb-2" style="color: var(--dash-text-muted);">Program Teratas</p>
                    @if(empty($data['data_b']['programs']))
                        <p class="text-xs italic" style="color: var(--dash-text-faint);">Belum ada data program.</p>
                    @else
                        <ul class="space-y-1.5">
                            @foreach($data['data_b']['programs'] as $prog)
                            <li class="flex items-center justify-between text-xs">
                                <span class="truncate pr-2" style="color: var(--dash-text-heading);">{{ data_get($prog, 'name') }}</span>
                                <span class="font-mono shrink-0" style="color: var(--dash-text-muted);">{{ data_get($prog, 'cnt') }}</span>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
