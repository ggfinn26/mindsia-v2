<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analitik Member & Program</title>
    <style>
        body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 8.5pt; color: #1E293B; margin: -10px; }
        .header { text-align: center; margin-bottom: 1.5rem; border-bottom: 2px solid #1E293B; padding-bottom: 1rem; position: relative; }
        .logo { position: absolute; left: 0; top: -15px; width: 120px; }
        .title { font-size: 14pt; font-weight: bold; margin-bottom: 0.2rem; color: #1E293B; text-transform: uppercase; letter-spacing: 0.05em; }
        .subtitle { font-size: 9pt; color: #7e7663; }
        .section-title { font-size: 10.5pt; font-weight: bold; margin-top: 1.5rem; margin-bottom: 0.8rem; color: #1E293B; border-bottom: 1px solid #cfc6af; padding-bottom: 0.3rem; text-transform: uppercase; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; page-break-inside: auto; }
        table.data-table tr { page-break-inside: avoid; page-break-after: auto; }
        table.data-table th { background-color: #f9f9f8; font-size: 7.5pt; text-transform: uppercase; color: #7e7663; font-weight: bold; padding: 0.5rem; text-align: left; border-bottom: 1px solid #cfc6af; }
        table.data-table td { padding: 0.5rem; border-bottom: 1px solid #ebebeb; vertical-align: top; }
        table.data-table tbody tr:nth-child(even) { background-color: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .td-numeric { font-family: "Courier New", Courier, monospace; font-weight: bold; font-size: 9pt; }
        
        .clearfix::after { content: ""; clear: both; display: table; }
        .grid-2 { display: table; width: 100%; margin-bottom: 1rem; }
        .grid-col { display: table-cell; width: 50%; padding-right: 0.75rem; vertical-align: top; }
        .grid-col:last-child { padding-right: 0; padding-left: 0.75rem; }
        .grid-3 { display: table; width: 100%; margin-bottom: 1rem; }
        .grid-3 .grid-col { width: 33.33%; }

        .summary-box { background-color: #ffffff; border: 1px solid #cfc6af; border-top: 3px solid #dbb940; padding: 0.75rem; margin-bottom: 0.75rem; }
        .summary-label { font-size: 7.5pt; font-weight: bold; text-transform: uppercase; color: #7e7663; letter-spacing: 0.05em; margin-bottom: 0.3rem; }
        .summary-value { font-family: "Courier New", Courier, monospace; font-size: 15pt; font-weight: bold; margin-bottom: 0.2rem; color: #1a1c1c; }
        .val-positive { color: #00677d; font-weight: bold; }
        .val-negative { color: #ba1a1a; font-weight: bold; }

        .program-list { margin: 0; padding: 0; list-style-type: none; font-size: 7.5pt; }
        .program-list li { margin-bottom: 3px; display: table; width: 100%; border-bottom: 1px dashed #eee; padding-bottom: 2px; }
        .program-name { display: table-cell; width: 75%; }
        .program-count { display: table-cell; width: 25%; text-align: right; font-weight: bold; font-family: "Courier New", monospace; }
        
        .doc-foot { margin-top: 2rem; border-top: 1px solid #cfc6af; padding-top: 0.5rem; font-size: 7pt; color: #7e7663; }
        .foot-left { float: left; }
        .foot-right { float: right; font-weight: bold; }
    </style>
</head>
<body>

<div class="header">
    @if(!empty($logoSrc))
        <img src="{{ $logoSrc }}" alt="Logo" class="logo">
    @endif
    <div class="title">Laporan Peta Segmentasi Member</div>
    <div class="subtitle">Nasional, Area & Cabang • Periode {{ $data['period'] ?? '-' }}</div>
    <div style="font-size: 7.5pt; color: #7e7663; margin-top: 0.2rem; font-family:'Courier New', monospace;">DOC: {{ $docCode ?? '-' }}</div>
</div>

{{-- I. RINGKASAN NASIONAL --}}
<div class="grid-3">
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">Total Member Nasional</div>
            <div class="summary-value">{{ number_format($data['national']['this_month']) }}</div>
        </div>
    </div>
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">vs Bulan Lalu (MoM)</div>
            @php $mom = $data['national']['mom_pct'] ?? 0; @endphp
            <div class="summary-value {{ $mom >= 0 ? 'val-positive' : 'val-negative' }}">{{ $mom >= 0 ? '+' : '' }}{{ $mom }}%</div>
        </div>
    </div>
    <div class="grid-col">
        <div class="summary-box">
            <div class="summary-label">vs Tahun Lalu (YoY)</div>
            @php $yoy = $data['national']['yoy_pct'] ?? 0; @endphp
            <div class="summary-value {{ $yoy >= 0 ? 'val-positive' : 'val-negative' }}">{{ $yoy >= 0 ? '+' : '' }}{{ $yoy }}%</div>
        </div>
    </div>
</div>

{{-- II. PETA SEGMENTASI PROGRAM --}}
<h2 class="section-title">II. Peta Segmentasi Program - Top 4</h2>
@forelse (array_chunk(array_slice($data['by_program'], 0, 4), 2) as $row)
<div class="grid-2" style="margin-bottom: 0;">
    @foreach ($row as $prog)
    <div class="grid-col" style="margin-bottom: 15px;">
        <div class="summary-box" style="padding: 0.8rem; border-top: 3px solid #1E293B;">
            <div style="font-weight:bold; font-size: 11pt; color: #1E293B; margin-bottom: 4px;">{{ $prog['program'] }}</div>
            <div style="display:flex; justify-content:space-between; margin-bottom: 8px; border-bottom: 1px solid #cfc6af; padding-bottom: 6px;">
                <span style="font-size: 8pt; color: #7e7663;">Total Member: <strong style="color:#1E293B; font-family:'Courier New'">{{ number_format($prog['member_count']) }}</strong></span>
                <div style="clear:both"></div>
            </div>
            
            <div style="font-size: 7pt; font-weight: bold; color: #4c4635; margin-bottom: 4px;">Skema Pembayaran:</div>
            <table style="width: 100%; font-size: 7pt; margin-bottom: 8px; font-family:'Courier New', monospace;">
                @forelse($prog['payments'] ?? [] as $pay)
                    <tr>
                        <td style="padding-bottom: 2px;">{{ $pay->payment_status === 'paid_full' ? 'Lunas' : ($pay->payment_status === 'installments' ? 'Cicil '.$pay->installment_type.'x' : 'Blm Lunas') }}</td>
                        <td style="text-align: right; font-weight: bold; padding-bottom: 2px;">{{ $pay->cnt }}</td>
                    </tr>
                @empty
                    <tr><td style="color:#7e7663; font-style:italic;">Belum ada data</td></tr>
                @endforelse
            </table>

            <div style="font-size: 7pt; font-weight: bold; color: #4c4635; margin-bottom: 4px;">Demografi Institusi:</div>
            <table style="width: 100%; font-size: 7pt; font-family:'Courier New', monospace;">
                @forelse($prog['demographics'] ?? [] as $dem)
                    <tr>
                        <td style="padding-bottom: 2px;">{{ $dem->jenjang_institution ?: 'TIDAK DIKETAHUI' }}</td>
                        <td style="text-align: right; font-weight: bold; padding-bottom: 2px;">{{ $dem->cnt }}</td>
                    </tr>
                @empty
                    <tr><td style="color:#7e7663; font-style:italic;">Belum ada data</td></tr>
                @endforelse
            </table>
        </div>
    </div>
    @endforeach
    @if (count($row) === 1)
    <div class="grid-col"></div>
    @endif
</div>
@empty
<div style="text-align:center; font-size:8pt; color:#7e7663; width:100%; padding: 20px;">Tidak ada data program aktif.</div>
@endforelse
<div class="clearfix"></div>

{{-- III. PER AREA --}}
<h2 class="section-title">III. Analitik per Area & Pembayaran</h2>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th>Area</th>
            <th style="width: 8%;" class="text-center">Cabang</th>
            <th class="text-right">Member</th>
            <th class="text-center">MoM</th>
            <th class="text-center">YoY</th>
            <th>Rincian Skema Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data['by_area'] as $i => $area)
            <tr>
                <td class="text-center" style="color:#7e7663; font-family:'Courier New',monospace;">{{ $i + 1 }}</td>
                <td><strong>{{ $area['name'] }}</strong></td>
                <td class="text-center td-numeric">{{ $area['branch_count'] }}</td>
                <td class="text-right td-numeric">{{ number_format($area['this_month']) }}</td>
                <td class="text-center td-numeric {{ ($area['mom_pct'] ?? 0) >= 0 ? 'val-positive' : 'val-negative' }}">
                    {{ ($area['mom_pct'] ?? 0) >= 0 ? '+' : '' }}{{ $area['mom_pct'] ?? 0 }}%
                </td>
                <td class="text-center td-numeric {{ ($area['yoy_pct'] ?? 0) >= 0 ? 'val-positive' : 'val-negative' }}">
                    {{ ($area['yoy_pct'] ?? 0) >= 0 ? '+' : '' }}{{ $area['yoy_pct'] ?? 0 }}%
                </td>
                <td>
                    <ul class="program-list">
                        @forelse ($area['payments'] ?? [] as $pay)
                            <li>
                                <span class="program-name">{{ $pay->payment_status === 'paid_full' ? 'Lunas' : ($pay->payment_status === 'installments' ? 'Cicil '.$pay->installment_type.'x' : 'Blm Lunas') }}</span>
                                <span class="program-count">{{ $pay->cnt }}</span>
                            </li>
                        @empty
                            <li style="color:#7e7663; font-style:italic;">Belum ada data</li>
                        @endforelse
                    </ul>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center" style="color:#7e7663;">Tidak ada data area.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- IV. DETAIL PROGRAM PER CABANG --}}
<h2 class="section-title">IV. Detail Program per Cabang</h2>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 4%;">No</th>
            <th style="width: 25%;">Cabang & Area</th>
            <th class="text-right" style="width: 15%;">Total Member</th>
            <th>Distribusi Program (Semua)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data['by_branch'] as $i => $branch)
            @if ($branch['this_month'] > 0)
                <tr>
                    <td class="text-center" style="color:#7e7663; font-family:'Courier New',monospace;">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $branch['name'] }}</strong><br>
                        <span style="font-size:6.5pt; color:#7e7663;">{{ $branch['area_name'] }}</span>
                    </td>
                    <td class="text-right td-numeric" style="font-weight:bold;">{{ number_format($branch['this_month']) }}</td>
                    <td>
                        <ul class="program-list">
                        @forelse ($branch['programs'] ?? [] as $prog)
                            <li>
                                <span class="program-name">{{ $prog['name'] }}</span>
                                <span class="program-count">{{ $prog['count'] }}</span>
                            </li>
                        @empty
                            <li style="color:#7e7663; font-style:italic;">Belum ada data program</li>
                        @endforelse
                        </ul>
                    </td>
                </tr>
            @endif
        @empty
            <tr><td colspan="4" class="text-center" style="color:#7e7663;">Tidak ada data cabang.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- V. DETAIL PROGRAM PER AREA --}}
<h2 class="section-title">V. Detail Program per Area</h2>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 4%;">No</th>
            <th style="width: 25%;">Area</th>
            <th class="text-right" style="width: 15%;">Total Member</th>
            <th>Distribusi Program (Semua)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data['by_area'] as $i => $area)
            @if ($area['this_month'] > 0)
                <tr>
                    <td class="text-center" style="color:#7e7663; font-family:'Courier New',monospace;">{{ $i + 1 }}</td>
                    <td><strong>{{ $area['name'] }}</strong></td>
                    <td class="text-right td-numeric" style="font-weight:bold;">{{ number_format($area['this_month']) }}</td>
                    <td>
                        <ul class="program-list">
                        @forelse ($area['programs'] ?? [] as $prog)
                            <li>
                                <span class="program-name">{{ $prog['name'] }}</span>
                                <span class="program-count">{{ $prog['count'] }}</span>
                            </li>
                        @empty
                            <li style="color:#7e7663; font-style:italic;">Belum ada data program</li>
                        @endforelse
                        </ul>
                    </td>
                </tr>
            @endif
        @empty
            <tr><td colspan="4" class="text-center" style="color:#7e7663;">Tidak ada data area.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- VI. DETAIL PROGRAM PER DEMOGRAFI --}}
<h2 class="section-title">VI. Detail Program per Demografi Institusi</h2>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 4%;">No</th>
            <th style="width: 25%;">Demografi (Jenjang)</th>
            <th class="text-right" style="width: 15%;">Total Member</th>
            <th>Distribusi Program (Semua)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data['by_demografi'] ?? [] as $i => $demo)
            @if ($demo['this_month'] > 0)
                <tr>
                    <td class="text-center" style="color:#7e7663; font-family:'Courier New',monospace;">{{ $i + 1 }}</td>
                    <td><strong>{{ $demo['name'] }}</strong></td>
                    <td class="text-right td-numeric" style="font-weight:bold;">{{ number_format($demo['this_month']) }}</td>
                    <td>
                        <ul class="program-list">
                        @forelse ($demo['programs'] ?? [] as $prog)
                            <li>
                                <span class="program-name">{{ $prog['name'] }}</span>
                                <span class="program-count">{{ $prog['count'] }}</span>
                            </li>
                        @empty
                            <li style="color:#7e7663; font-style:italic;">Belum ada data program</li>
                        @endforelse
                        </ul>
                    </td>
                </tr>
            @endif
        @empty
            <tr><td colspan="4" class="text-center" style="color:#7e7663;">Tidak ada data demografi.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- FOOTER --}}
<div class="doc-foot">
    <div class="foot-left">Dokumen ini diterbitkan secara digital oleh Sistem MINDSIA dan sah untuk keperluan internal perusahaan.</div>
    <div class="foot-right">HAL. 1</div>
    <div class="clearfix"></div>
</div>

</body>
</html>